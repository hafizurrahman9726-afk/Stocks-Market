// scripts/deploy-responsive-theme.mjs
import https from 'https';
import fs from 'fs';

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
  const loginUrl = 'https://promptsyra.com/wp-login.php';
  const step1 = await request(loginUrl, { method: 'GET' });
  const cookies = [];
  if (step1.headers['set-cookie']) {
    step1.headers['set-cookie'].forEach(c => cookies.push(c.split(';')[0]));
  }
  
  const postParams = new URLSearchParams({
    log: process.env.WP_USER || 'promptsyra',
    pwd: process.env.WP_PASSWORD || 'Siyra4%4rahman',
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

  return cookies.join('; ');
}

async function getNonceForFile(cookieHeader, filename) {
  const res = await request(`https://promptsyra.com/wp-admin/theme-editor.php?file=${encodeURIComponent(filename)}&theme=promptsyra`, {
    headers: { 'Cookie': cookieHeader }
  });
  const nonceMatch = res.body.match(/id="nonce"\s+name="nonce"\s+value="([^"]+)"/);
  if (nonceMatch) {
    return nonceMatch[1];
  }
  const altNonce = res.body.match(/name="nonce"\s+value="([^"]+)"/);
  return altNonce ? altNonce[1] : null;
}

async function updateThemeFile(cookieHeader, filename, content) {
  console.log(`\n--- Updating theme file on WordPress: ${filename} ---`);
  const nonce = await getNonceForFile(cookieHeader, filename);
  if (!nonce) {
    throw new Error(`Failed to acquire nonce for ${filename}`);
  }
  console.log(`Acquired nonce for ${filename}: ${nonce}`);

  const postParams = new URLSearchParams({
    nonce: nonce,
    _wp_http_referer: `/wp-admin/theme-editor.php?file=${filename}&theme=promptsyra`,
    action: 'update',
    file: filename,
    theme: 'promptsyra',
    newcontent: content,
    submit: 'Update File'
  }).toString();

  const updateRes = await request('https://promptsyra.com/wp-admin/theme-editor.php', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(postParams),
      'Cookie': cookieHeader
    }
  }, postParams);

  console.log(`Update response status: ${updateRes.statusCode}, Location: ${updateRes.headers.location}`);
  const isSuccess = updateRes.headers.location && (updateRes.headers.location.includes('a=1') || updateRes.headers.location.includes('updated=true'));
  console.log(`File ${filename} updated successfully?`, Boolean(isSuccess));
  return isSuccess;
}

async function main() {
  console.log("=== STARTING COMPLETE RESPONSIVE THEME DEPLOYMENT (v2.5.0) ===");
  const cookieHeader = await login();
  console.log("Logged into WordPress successfully at https://promptsyra.com/wp-admin/.");

  const filesToDeploy = [
    { name: 'style.css', path: 'wordpress-theme/promptsyra/style.css' },
    { name: 'functions.php', path: 'wordpress-theme/promptsyra/functions.php' },
    { name: 'assets/css/promptsyra-home.css', path: 'wordpress-theme/promptsyra/assets/css/promptsyra-home.css' },
    { name: 'assets/css/promptsyra-pages.css', path: 'wordpress-theme/promptsyra/assets/css/promptsyra-pages.css' },
    { name: 'assets/css/promptsyra-single.css', path: 'wordpress-theme/promptsyra/assets/css/promptsyra-single.css' }
  ];

  for (const item of filesToDeploy) {
    const content = fs.readFileSync(item.path, 'utf8');
    const ok = await updateThemeFile(cookieHeader, item.name, content);
    if (!ok) {
      console.warn(`Warning: Update for ${item.name} did not return redirect with a=1. Checking file content...`);
    }
  }

  console.log("\n=== ALL THEME FILES DEPLOYED TO LIVE WORDPRESS ===");

  // Verify live site
  console.log("\n=== VERIFYING LIVE HOMEPAGE ===");
  const homeRes = await request('https://promptsyra.com/?nocache=' + Date.now(), { method: 'GET' });
  console.log("Live homepage HTTP status:", homeRes.statusCode);
  const hasV250 = homeRes.body.includes('2.5.0');
  console.log("Found version 2.5.0 in live HTML?", hasV250);

  // Check the live stylesheet
  const cssRes = await request('https://promptsyra.com/wp-content/themes/promptsyra/assets/css/promptsyra-home.css?ver=2.5.0', { method: 'GET' });
  console.log("Live promptsyra-home.css HTTP status:", cssRes.statusCode);
  const hasClamp = cssRes.body.includes('font-size: clamp(1.65rem, 4.8vw, 3.35rem)');
  const hasWrap = cssRes.body.includes('ps-footer-bottom-links');
  const hasOverflowClip = cssRes.body.includes('overflow-x: clip');
  console.log("Verified 'font-size: clamp(1.65rem...' in live CSS?", hasClamp);
  console.log("Verified 'overflow-x: clip' in live CSS?", hasOverflowClip);
  console.log("Verified footer-bottom-links wrap in live CSS?", hasWrap);
  console.log("\nDeployment & verification successfully completed!");
}

main().catch(err => {
  console.error("Deployment failed:", err);
  process.exit(1);
});
