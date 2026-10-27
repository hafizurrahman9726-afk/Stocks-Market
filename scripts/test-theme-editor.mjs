// scripts/test-theme-editor.mjs
import https from 'https';

async function request(url, options = {}, postData = null) {
  return new Promise((resolve, reject) => {
    const req = https.request(url, options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => {
        resolve({
          statusCode: res.statusCode,
          headers: res.headers,
          body: data
        });
      });
    });
    req.on('error', reject);
    if (postData) {
      req.write(postData);
    }
    req.end();
  });
}

async function run() {
  const loginUrl = 'https://promptsyra.com/wp-login.php';
  const step1 = await request(loginUrl, { method: 'GET' });
  const cookies = [];
  if (step1.headers['set-cookie']) {
    step1.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }
  
  const postParams = new URLSearchParams({
    log: 'promptsyra',
    pwd: process.env.WP_PASSWORD || '',
    'wp-submit': 'Log In',
    redirect_to: 'https://promptsyra.com/wp-admin/',
    testcookie: '1'
  }).toString();

  const step2 = await request(loginUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(postParams),
      'Cookie': cookies.join('; ')
    }
  }, postParams);

  if (step2.headers['set-cookie']) {
    step2.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }

  const cookieHeader = cookies.join('; ');

  // Fetch theme editor for functions.php
  const res = await request('https://promptsyra.com/wp-admin/theme-editor.php?file=functions.php&theme=promptsyra', {
    headers: { 'Cookie': cookieHeader }
  });

  console.log("theme-editor status:", res.statusCode);
  
  // Search form fields
  const fileNonceMatch = res.body.match(/name="_wpnonce"\s+value="([^"]+)"/);
  console.log("Nonce:", fileNonceMatch ? fileNonceMatch[1] : 'null');
  
  const actionMatch = res.body.match(/name="action"\s+value="([^"]+)"/);
  console.log("Action:", actionMatch ? actionMatch[1] : 'null');
  
  const newcontentMatch = res.body.match(/<textarea[^>]*name="newcontent"[^>]*>([\s\S]*?)<\/textarea>/);
  console.log("Found textarea content length:", newcontentMatch ? newcontentMatch[1].length : 0);
  if (newcontentMatch) {
    console.log("Sample content snippet:", newcontentMatch[1].substring(0, 150));
  }
}

run().catch(console.error);
