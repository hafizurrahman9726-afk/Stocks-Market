// scripts/deploy-screenshot.mjs
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
  const nonce = await getNonceForFile(cookieHeader, filename);
  if (!nonce) {
    throw new Error(`Failed to acquire nonce for ${filename}`);
  }

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

  const isSuccess = updateRes.headers.location && (updateRes.headers.location.includes('a=1') || updateRes.headers.location.includes('updated=true'));
  return isSuccess;
}

async function main() {
  console.log('=== STEP 1: Logging in to WordPress ===');
  const cookieHeader = await login();
  console.log('Login successful.');

  // Fetch current live functions.php
  console.log('Fetching live functions.php...');
  const editorRes = await request('https://promptsyra.com/wp-admin/theme-editor.php?file=functions.php&theme=promptsyra', {
    headers: { 'Cookie': cookieHeader }
  });
  const match = editorRes.body.match(/<textarea[^>]*name="newcontent"[^>]*>([\s\S]*?)<\/textarea>/);
  if (!match) {
    throw new Error('Could not find textarea content for functions.php');
  }

  // Decode basic HTML entities that WordPress encodes in textarea
  let currentFunctions = match[1]
    .replace(/&lt;/g, '<')
    .replace(/&gt;/g, '>')
    .replace(/&amp;/g, '&')
    .replace(/&quot;/g, '"')
    .replace(/&#039;/g, "'");

  // Save clean backup
  fs.writeFileSync('wordpress-theme/promptsyra/legacy-backup/functions.before_screenshot.php', currentFunctions);
  console.log('Saved pristine backup of functions.php (length:', currentFunctions.length, ')');

  // Temporary deployer hook
  const hookMarker = '/* PROMPTSYRA_SCREENSHOT_DEPLOYER_HOOK */';
  const deployerHook = `
${hookMarker}
add_action('init', function() {
    if (isset($_GET['ps_write_screenshot']) && $_GET['ps_write_screenshot'] === 'ps_write_2026') {
        if (!current_user_can('manage_options')) {
            wp_die('Unauthorized');
        }
        $dest = get_stylesheet_directory() . '/screenshot.png';
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['screenshot_b64'])) {
            $data = base64_decode($_POST['screenshot_b64']);
            $bytes = file_put_contents($dest, $data);
            wp_send_json_success(array(
                'bytes' => $bytes,
                'filesize' => file_exists($dest) ? filesize($dest) : 0,
                'dest' => $dest
            ));
        }
    }
});
`;

  console.log('=== STEP 2: Injecting deployer hook into functions.php ===');
  const modifiedFunctions = currentFunctions + '\n' + deployerHook;
  const update1 = await updateThemeFile(cookieHeader, 'functions.php', modifiedFunctions);
  console.log('Injected deployer hook update success:', update1);

  console.log('=== STEP 3: Reading screenshot.png and sending binary upload ===');
  const screenshotBuffer = fs.readFileSync('wordpress-theme/promptsyra/screenshot.png');
  const screenshotB64 = screenshotBuffer.toString('base64');
  console.log('Screenshot local buffer size:', screenshotBuffer.length, 'bytes, Base64 length:', screenshotB64.length);

  const uploadParams = new URLSearchParams({
    screenshot_b64: screenshotB64
  }).toString();

  const uploadRes = await request('https://promptsyra.com/?ps_write_screenshot=ps_write_2026', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Content-Length': Buffer.byteLength(uploadParams),
      'Cookie': cookieHeader
    }
  }, uploadParams);

  console.log('Upload response status:', uploadRes.statusCode);
  console.log('Upload response body:', uploadRes.body);

  console.log('=== STEP 4: Restoring original functions.php without hook ===');
  const cleanFunctions = currentFunctions; // without the hook
  const update2 = await updateThemeFile(cookieHeader, 'functions.php', cleanFunctions);
  console.log('Clean functions.php restored success:', update2);

  console.log('=== STEP 5: Verifying live screenshot.png ===');
  const checkRes = await request('https://promptsyra.com/wp-content/themes/promptsyra/screenshot.png', {
    method: 'HEAD'
  });
  console.log('Live screenshot.png status:', checkRes.statusCode);
  console.log('Content-Type:', checkRes.headers['content-type']);
  console.log('Content-Length:', checkRes.headers['content-length']);

  console.log('=== STEP 6: Verifying wp-admin/themes.php screenshot recognition ===');
  const themesRes = await request('https://promptsyra.com/wp-admin/themes.php', {
    headers: { 'Cookie': cookieHeader }
  });
  const themeMatches = themesRes.body.match(/_wpThemeSettings\s*=\s*({[\s\S]*?});/);
  if (themeMatches) {
    try {
      const settings = JSON.parse(themeMatches[1]);
      if (settings.themes) {
        settings.themes.forEach(t => {
          console.log(`Theme: ${t.id} | Name: ${t.name} | Screenshot: ${JSON.stringify(t.screenshot)} | Active: ${t.active}`);
        });
      }
    } catch(e) {
      console.log('Error parsing themes json:', e.message);
    }
  }
}

main().catch(console.error);
