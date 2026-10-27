// scripts/inspect-themes-page.mjs
import https from 'https';

async function request(url, options = {}, postData = null) {
  return new Promise((resolve, reject) => {
    const req = https.request(url, options, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ statusCode: res.statusCode, headers: res.headers, body: data }));
    });
    req.on('error', reject);
    if (postData) req.write(postData);
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

  const themesRes = await request('https://promptsyra.com/wp-admin/themes.php', {
    headers: { 'Cookie': cookieHeader }
  });

  console.log('themes.php status:', themesRes.statusCode);
  const themeMatches = themesRes.body.match(/_wpThemeSettings\s*=\s*({[\s\S]*?});/);
  if (themeMatches) {
    try {
      const settings = JSON.parse(themeMatches[1]);
      if (settings.themes) {
        settings.themes.forEach(t => {
          console.log('Theme:', t.id, '| Name:', t.name, '| Screenshot:', t.screenshot, '| Active:', t.active);
        });
      }
    } catch(e) {
      console.log('JSON parse error:', e.message);
    }
  } else {
    console.log('No _wpThemeSettings found. Searching html snippet...');
    const themeSnippet = themesRes.body.match(/<div class="theme[^>]*>[\s\S]*?<\/div>/g);
    console.log('Themes found count:', themeSnippet ? themeSnippet.length : 0);
  }
}

run().catch(console.error);
