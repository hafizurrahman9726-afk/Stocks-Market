// scripts/fetch-live-file.mjs
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

async function getLiveThemeFile(filename) {
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

  const res = await request(`https://promptsyra.com/wp-admin/theme-editor.php?file=${encodeURIComponent(filename)}&theme=promptsyra`, {
    headers: { 'Cookie': cookieHeader }
  });

  const textareaMatch = res.body.match(/<textarea[^>]*name="newcontent"[^>]*>([\s\S]*?)<\/textarea>/);
  if (textareaMatch) {
    // decode basic html entities
    let decoded = textareaMatch[1]
      .replace(/&lt;/g, '<')
      .replace(/&gt;/g, '>')
      .replace(/&amp;/g, '&')
      .replace(/&quot;/g, '"')
      .replace(/&#039;/g, "'");
    return decoded;
  }
  return null;
}

async function run() {
  const footerContent = await getLiveThemeFile('template-parts/footer-promptsyra.php');
  if (footerContent) {
    console.log("Got footer content, length:", footerContent.length);
    fs.writeFileSync('wordpress-theme/promptsyra/template-parts/footer-promptsyra.php', footerContent);
    console.log("Saved to wordpress-theme/promptsyra/template-parts/footer-promptsyra.php");
  } else {
    console.log("Could not find textarea for footer-promptsyra.php");
  }

  const headerContent = await getLiveThemeFile('template-parts/header-promptsyra.php');
  if (headerContent) {
    console.log("Got header content, length:", headerContent.length);
    fs.writeFileSync('wordpress-theme/promptsyra/template-parts/header-promptsyra.php', headerContent);
    console.log("Saved to wordpress-theme/promptsyra/template-parts/header-promptsyra.php");
  }
}

run().catch(console.error);
