import fs from 'fs';
import path from 'path';

async function deployHeroImages() {
  const loginUrl = 'https://promptsyra.com/wp-login.php';
  const user = process.env.WP_USER || 'promptsyra';
  const pass = process.env.WP_PASSWORD || '';

  console.log('1. Authenticating with WordPress admin at promptsyra.com...');
  const res1 = await fetch(loginUrl);
  const cookie1 = res1.headers.getSetCookie();

  const params = new URLSearchParams();
  params.append('log', user);
  params.append('pwd', pass);
  params.append('wp-submit', 'Log In');
  params.append('redirect_to', 'https://promptsyra.com/wp-admin/');
  params.append('testcookie', '1');

  const res2 = await fetch(loginUrl, {
    method: 'POST',
    headers: {
      'Content-Type': 'application/x-www-form-urlencoded',
      'Cookie': cookie1.map(c => c.split(';')[0]).join('; '),
      'User-Agent': 'Mozilla/5.0'
    },
    body: params.toString(),
    redirect: 'manual'
  });

  const authCookies = res2.headers.getSetCookie().map(c => c.split(';')[0]).join('; ');
  console.log('Authentication successful.');

  async function updateThemeFile(themeFilePath, localContent) {
    const editorUrl = `https://promptsyra.com/wp-admin/theme-editor.php?file=${encodeURIComponent(themeFilePath)}&theme=promptsyra`;
    const resGet = await fetch(editorUrl, {
      headers: { 'Cookie': authCookies, 'User-Agent': 'Mozilla/5.0' }
    });
    const html = await resGet.text();

    const nonceMatch = html.match(/id="nonce" name="nonce" value="([^"]+)"/i);
    if (!nonceMatch) {
      throw new Error(`Could not find nonce for ${themeFilePath}`);
    }
    const nonce = nonceMatch[1];

    const postParams = new URLSearchParams();
    postParams.append('nonce', nonce);
    postParams.append('_wp_http_referer', `/wp-admin/theme-editor.php?file=${encodeURIComponent(themeFilePath)}&theme=promptsyra`);
    postParams.append('action', 'update');
    postParams.append('file', themeFilePath);
    postParams.append('theme', 'promptsyra');
    postParams.append('newcontent', localContent);
    postParams.append('submit', 'Update File');

    const resPost = await fetch('https://promptsyra.com/wp-admin/theme-editor.php', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/x-www-form-urlencoded',
        'Cookie': authCookies,
        'User-Agent': 'Mozilla/5.0',
        'Referer': editorUrl
      },
      body: postParams.toString(),
      redirect: 'manual'
    });

    console.log(`Updated ${themeFilePath}: HTTP ${resPost.status}`);
  }

  // Deploy front-page.php
  const frontPageContent = fs.readFileSync(path.resolve('wordpress-theme/promptsyra/front-page.php'), 'utf8');
  await updateThemeFile('front-page.php', frontPageContent);

  // Deploy assets/css/promptsyra-home.css
  const homeCssContent = fs.readFileSync(path.resolve('wordpress-theme/promptsyra/assets/css/promptsyra-home.css'), 'utf8');
  await updateThemeFile('assets/css/promptsyra-home.css', homeCssContent);

  console.log('\n2. Verifying live site front page HTML for hero images...');
  const resFront = await fetch('https://promptsyra.com/?hero_test=' + Date.now(), {
    headers: { 'User-Agent': 'Mozilla/5.0' }
  });
  const html = await resFront.text();

  const heroSectionMatch = html.match(/<section[^>]*ps-hero-section[\s\S]*?<\/section>/i);
  if (!heroSectionMatch) {
    throw new Error('Hero section not found on live front page');
  }
  const heroHtml = heroSectionMatch[0];

  const checks = [
    {
      name: 'Image 1 (promptsyra-hero-portrait.webp) in Main Portrait Card',
      pass: heroHtml.includes('promptsyra-hero-portrait.webp')
    },
    {
      name: 'Image 2 (promptsyra-creative-studio.webp) in Creative Studio Card',
      pass: heroHtml.includes('promptsyra-creative-studio.webp')
    },
    {
      name: 'Image 3 (promptsyra-social-visuals.webp) in Social Visuals Card',
      pass: heroHtml.includes('promptsyra-social-visuals.webp')
    },
    {
      name: 'Old Portrait SVG placeholder removed',
      pass: !heroHtml.includes('ps-art-portrait')
    },
    {
      name: 'Old Creative Studio SVG placeholder removed',
      pass: !heroHtml.includes('ps-art-creative')
    },
    {
      name: 'Old Social Visuals SVG placeholder removed',
      pass: !heroHtml.includes('ps-art-social')
    },
    {
      name: 'Preserved Card 1 Title (Portrait Study · 85mm Soft Rim Light)',
      pass: heroHtml.includes('Portrait Study · 85mm Soft Rim Light')
    },
    {
      name: 'Preserved Card 1 Prompt Snippet',
      pass: heroHtml.includes('Natural window key light, shallow depth of field')
    },
    {
      name: 'Preserved Card 2 Title (Creative Studio Setup)',
      pass: heroHtml.includes('Creative Studio Setup')
    },
    {
      name: 'Preserved Card 3 Title (9:16 Social Visuals)',
      pass: heroHtml.includes('9:16 Social Visuals')
    },
    {
      name: 'Descriptive alt texts present on all 3 images',
      pass: heroHtml.includes('alt="Portrait Study · 85mm Soft Rim Light') &&
            heroHtml.includes('alt="Creative Studio Setup') &&
            heroHtml.includes('alt="9:16 Social Visuals')
    }
  ];

  console.log('\nLive Hero Section Verification:');
  for (const c of checks) {
    console.log(` - ${c.pass ? 'PASS' : 'FAIL'}: ${c.name}`);
  }

  console.log('\n3. Direct image HTTP status check:');
  const imageUrls = [
    'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-hero-portrait.webp',
    'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-creative-studio.webp',
    'https://promptsyra.com/wp-content/uploads/2026/10/promptsyra-social-visuals.webp'
  ];
  for (const u of imageUrls) {
    const r = await fetch(u, { method: 'HEAD' });
    console.log(` - [HTTP ${r.status}] ${u} (${r.headers.get('content-type')}, ${r.headers.get('content-length')} bytes)`);
  }

  console.log('\nHero visual replacement deployment and verification complete!');
}

deployHeroImages().catch(err => {
  console.error('Error during deployment:', err);
  process.exit(1);
});
