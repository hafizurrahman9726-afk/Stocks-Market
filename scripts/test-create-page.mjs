// scripts/test-create-page.mjs
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

  // Fetch post-new.php?post_type=page
  const postNewRes = await request('https://promptsyra.com/wp-admin/post-new.php?post_type=page', {
    headers: { 'Cookie': cookieHeader }
  });

  console.log("post-new.php status:", postNewRes.statusCode);

  // Search for wpApiSettings nonce in HTML
  const apiMatch = postNewRes.body.match(/wpApiSettings\s*=\s*(\{.+?\});/);
  if (apiMatch) {
    try {
      const apiSettings = JSON.parse(apiMatch[1]);
      console.log("REST Nonce found:", apiSettings.nonce);
      return { cookies: cookieHeader, restNonce: apiSettings.nonce };
    } catch (e) {
      console.log("JSON parse error:", e);
    }
  }

  // Also check for _wpnonce
  const nonceMatches = [...postNewRes.body.matchAll(/name="_wpnonce"\s+value="([^"]+)"/g)];
  console.log("Found _wpnonces:", nonceMatches.map(m => m[1]));

  // Also check for wp_rest nonce in wp-admin
  const wpRestMatch = postNewRes.body.match(/"nonce":"([a-f0-9]+)"/);
  if (wpRestMatch) {
    console.log("Potential nonce:", wpRestMatch[1]);
  }
}

run().catch(console.error);
