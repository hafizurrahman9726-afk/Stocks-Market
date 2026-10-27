// scripts/inspect-form.mjs
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
    pwd: 'Siyra4%4rahman',
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

  const res = await request('https://promptsyra.com/wp-admin/theme-editor.php?file=functions.php&theme=promptsyra', {
    headers: { 'Cookie': cookieHeader }
  });

  const formMatch = res.body.match(/<form[^>]*id="template"[^>]*>([\s\S]*?)<\/form>/);
  if (formMatch) {
    // Print all input tags
    const inputs = [...formMatch[1].matchAll(/<input[^>]+>/g)];
    console.log("Form inputs:", inputs.map(i => i[0]));
  } else {
    console.log("No form with id=template found");
    const anyInputs = [...res.body.matchAll(/<input[^>]+_wpnonce[^>]+>/g)];
    console.log("Any nonce inputs:", anyInputs.map(i => i[0]));
  }
}

run().catch(console.error);
