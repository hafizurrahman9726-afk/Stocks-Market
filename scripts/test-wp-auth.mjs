// scripts/test-wp-auth.mjs
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

async function login() {
  console.log("Attempting login to https://promptsyra.com/wp-login.php...");
  const loginUrl = 'https://promptsyra.com/wp-login.php';
  
  // Step 1: GET login page to get initial cookies
  const step1 = await request(loginUrl, { method: 'GET' });
  const cookies = [];
  if (step1.headers['set-cookie']) {
    step1.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }
  
  // Step 2: POST credentials
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

  console.log("Login POST status:", step2.statusCode, "Location:", step2.headers.location);
  
  // Check access to wp-admin
  const adminCheck = await request('https://promptsyra.com/wp-admin/', {
    headers: { 'Cookie': cookies.join('; ') }
  });
  console.log("wp-admin status:", adminCheck.statusCode);
  const loggedIn = adminCheck.body.includes('wpadminbar') || adminCheck.body.includes('howdy');
  console.log("Logged in successfully?", loggedIn);
  return { loggedIn, cookies };
}

login().catch(console.error);
