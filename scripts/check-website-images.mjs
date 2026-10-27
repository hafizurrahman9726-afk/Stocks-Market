// scripts/check-website-images.mjs
import https from 'https';

async function fetchUrl(url) {
  return new Promise((resolve, reject) => {
    https.get(url, (res) => {
      let data = '';
      res.on('data', chunk => data += chunk);
      res.on('end', () => resolve({ statusCode: res.statusCode, headers: res.headers, body: data }));
    }).on('error', reject);
  });
}

async function headUrl(url) {
  return new Promise((resolve) => {
    try {
      const u = new URL(url);
      const req = https.request({
        hostname: u.hostname,
        path: u.pathname + u.search,
        method: 'HEAD',
        headers: { 'User-Agent': 'Mozilla/5.0 (PromptSyraImageChecker/1.0)' }
      }, (res) => {
        resolve({ statusCode: res.statusCode, contentType: res.headers['content-type'], length: res.headers['content-length'] });
      });
      req.on('error', (err) => resolve({ statusCode: 0, error: err.message }));
      req.setTimeout(5000, () => { req.abort(); resolve({ statusCode: 408, error: 'Timeout' }); });
      req.end();
    } catch(e) {
      resolve({ statusCode: 0, error: e.message });
    }
  });
}

async function run() {
  console.log('Fetching homepage HTML from https://promptsyra.com/...');
  const home = await fetchUrl('https://promptsyra.com/');
  console.log('Homepage status:', home.statusCode);

  // Extract all <img ... src="...">, srcset, and background images / SVG references
  const imgSrcRegex = /<img[^>]+src=["']([^"']+)["']/gi;
  const images = new Set();
  let match;
  while ((match = imgSrcRegex.exec(home.body)) !== null) {
    images.add(match[1]);
  }

  // Also check inline style url(...) or custom wp-content uploads
  const uploadRegex = /https?:\/\/[^"'\s)]+\/wp-content\/uploads\/[^"'\s)]+/gi;
  while ((match = uploadRegex.exec(home.body)) !== null) {
    images.add(match[0]);
  }

  console.log(`Found ${images.size} unique image URLs on homepage:`);
  for (const img of images) {
    const res = await headUrl(img);
    console.log(`- [${res.statusCode}] ${img} (${res.contentType || res.error || ''}, size: ${res.length || 'unknown'})`);
  }

  // Also check single post if available
  const postUrl = 'https://promptsyra.com/couple-eye-photo-prompts/';
  console.log('\nChecking single post:', postUrl);
  try {
    const post = await fetchUrl(postUrl);
    console.log('Post status:', post.statusCode);
    const postImages = new Set();
    while ((match = imgSrcRegex.exec(post.body)) !== null) {
      postImages.add(match[1]);
    }
    while ((match = uploadRegex.exec(post.body)) !== null) {
      postImages.add(match[0]);
    }
    console.log(`Found ${postImages.size} unique images on single post:`);
    for (const img of postImages) {
      const res = await headUrl(img);
      console.log(`- [${res.statusCode}] ${img} (${res.contentType || res.error || ''})`);
    }
  } catch(e) {
    console.log('Post check error:', e.message);
  }
}

run().catch(console.error);
