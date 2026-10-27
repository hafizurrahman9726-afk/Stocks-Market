// scripts/find-files.mjs
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

  const res = await request('https://promptsyra.com/wp-admin/theme-editor.php', {
    headers: { 'Cookie': cookieHeader }
  });

  const templatesideMatch = res.body.match(/<div[^>]*id="templateside"[^>]*>([\s\S]*?)<\/div>/);
  if (templatesideMatch) {
    const fileMatches = [...templatesideMatch[1].matchAll(/file=([^&"']+)/g)];
    const files = [...new Set(fileMatches.map(m => decodeURIComponent(m[1])))];
    console.log("Templateside files:", files);
  } else {
    console.log("No templateside div found");
    const anyFileMatches = [...res.body.matchAll(/theme-editor\.php\?file=([^"'\s>]+)/g)];
    console.log("Any theme-editor file links:", anyFileMatches.map(m => m[1]));
  }
}

run().catch(console.error);
