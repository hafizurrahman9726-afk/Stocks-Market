// scripts/check-theme-and-pages.mjs
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

async function check() {
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

  // Check theme editor
  const themeEditorRes = await request('https://promptsyra.com/wp-admin/theme-editor.php', {
    headers: { 'Cookie': cookieHeader }
  });
  console.log("Theme editor status:", themeEditorRes.statusCode);
  
  // Extract active theme and nonce if present
  const themeMatch = themeEditorRes.body.match(/name="theme"\s+value="([^"]+)"/);
  console.log("Theme in editor:", themeMatch ? themeMatch[1] : "not found");
  
  const nonceMatch = themeEditorRes.body.match(/name="_wpnonce"\s+value="([^"]+)"/);
  console.log("Nonce in editor:", nonceMatch ? nonceMatch[1] : "not found");

  // Check REST API nonce
  const adminPage = await request('https://promptsyra.com/wp-admin/edit.php?post_type=page', {
    headers: { 'Cookie': cookieHeader }
  });
  const restNonceMatch = adminPage.body.match(/wpApiSettings\s*=\s*\{"root":"[^"]*","nonce":"([^"]+)"\}/);
  console.log("REST API nonce:", restNonceMatch ? restNonceMatch[1] : "not found");

  // Check existing published pages via REST API
  const pagesRes = await request('https://promptsyra.com/wp-json/wp/v2/pages?per_page=50', {
    headers: { 
      'Cookie': cookieHeader,
      ...(restNonceMatch ? { 'X-WP-Nonce': restNonceMatch[1] } : {})
    }
  });
  console.log("Pages endpoint status:", pagesRes.statusCode);
  try {
    const pages = JSON.parse(pagesRes.body);
    console.log("Found pages count:", pages.length);
    pages.forEach(p => console.log(` - ID: ${p.id}, Slug: ${p.slug}, Title: ${p.title.rendered}, Status: ${p.status}`));
  } catch (e) {
    console.log("Error parsing pages JSON:", e.message);
  }
}

check().catch(console.error);
