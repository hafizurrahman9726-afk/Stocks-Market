// scripts/inspect-redirect.mjs
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

  // Fetch https://promptsyra.com/wp-admin/theme-editor.php?a=1&theme=promptsyra&file=functions.php
  const res = await request('https://promptsyra.com/wp-admin/theme-editor.php?a=1&theme=promptsyra&file=functions.php', {
    headers: { 'Cookie': cookieHeader }
  });

  // Check notice in HTML
  const notices = [...res.body.matchAll(/<div[^>]*class="[^"]*notice[^"]*"[^>]*>([\s\S]*?)<\/div>/g)];
  console.log("Notices found:", notices.map(n => n[1].trim().replace(/\s+/g, ' ')));

  // Also check wp-admin/theme-editor.php file permissions or if DISALLOW_FILE_EDIT is active or anything
  console.log("Submit button present?", res.body.includes('id="submit"'));
  console.log("Textarea readonly?", res.body.includes('readonly'));
}

run().catch(console.error);
