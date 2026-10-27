// scripts/publish-social-pages.mjs
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

// Full rich HTML content for Instagram page
const instagramHtml = `
<div class="ps-editorial-intro-block">
  <p class="ps-page-lead">Welcome to the official PromptSyra Instagram hub. Follow <strong>@promptsyra</strong> to discover visual AI photo prompt ideas, studio lighting setups, portrait framing inspiration, and daily creative concepts crafted for modern creators.</p>
  <p>Our Instagram profile serves as a visual companion to PromptSyra.com, transforming structured text prompts into eye-catching image specimens so you can immediately see how subtle lighting cues, camera lens choices, and composition rules translate into realistic AI art.</p>
</div>

<div class="ps-social-cta-banner ps-instagram-banner">
  <div class="ps-social-cta-content">
    <span class="ps-badge ps-badge-instagram">Official Instagram</span>
    <h2>Follow @promptsyra on Instagram</h2>
    <p>Get daily AI photo prompt inspiration, portrait lighting setups, and visual art concepts directly in your Instagram feed and stories.</p>
    <div class="ps-social-cta-actions">
      <a href="https://www.instagram.com/promptsyra" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-instagram">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><rect width="20" height="20" x="2" y="2" rx="5" ry="5"/><path d="M16 11.37A4 4 0 1 1 12.63 8 4 4 0 0 1 16 11.37z"/><line x1="17.5" x2="17.51" y1="6.5" y2="6.5"/></svg>
        <span>Follow on Instagram (@promptsyra)</span>
      </a>
      <span class="ps-social-cta-note">Opens official Instagram profile in a new tab</span>
    </div>
  </div>
</div>

<div class="ps-about-pillars">
  <div class="ps-about-pillar">
    <h3>Visual Prompt Inspiration</h3>
    <p>Discover ready-to-use photo prompts featuring soft 85mm rim lighting, atmospheric studio portraits, candid street realism, and editorial fashion compositions.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Behind-the-Prompt Breakdowns</h3>
    <p>Learn how specific keywords like "catchlight in eyes", "subtle volumetric haze", or "split complementary palette" influence your image generator's output.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Creator Community & Trends</h3>
    <p>Explore emerging aesthetics, seasonal visual themes, and community-tested variations across ChatGPT, Midjourney, Google Gemini, and Stable Diffusion.</p>
  </div>
</div>

<h2>How to Use Prompts from Our Instagram</h2>
<p>Every post on our Instagram profile is linked to a practical creative concept. Here is the recommended workflow to adapt prompts from our posts to your own generative tools:</p>
<ol class="ps-steps-list">
  <li><strong>Read the Caption:</strong> Review the core prompt formula provided in the post caption, noting the subject, environment, lighting style, and camera specification.</li>
  <li><strong>Swap the Subject:</strong> Replace the subject details (wardrobe, age, ethnicity, or expression) with your specific creative requirement while keeping the lighting and lens descriptors intact.</li>
  <li><strong>Choose Your Aspect Ratio:</strong> Use <code>--ar 9:16</code> for vertical social stories or reels, <code>--ar 4:5</code> for classic portrait posts, or <code>--ar 16:9</code> for wide banners.</li>
  <li><strong>Refine in Steps:</strong> If your generator adds unwanted details, remove conflicting adjectives and emphasize camera framing (such as medium close-up or eye-level composition).</li>
</ol>

<div class="ps-editorial-callout">
  <h3>PromptSyra Editorial Quality Guarantee</h3>
  <p>We test lighting phrasing and camera angles to ensure they produce coherent, aesthetic imagery. We do not use automated bot reposting or low-quality prompt spam. Every visual specimen on our profile is designed to spark genuine creative ideas for photographers, designers, and hobbyists alike.</p>
</div>

<h2>Frequently Asked Questions</h2>
<div class="ps-faq-grid">
  <div class="ps-faq-card">
    <h4>Do I need an Instagram account to view PromptSyra posts?</h4>
    <p>You can view our public profile directly through any modern desktop or mobile browser. However, following with an active Instagram account allows you to receive prompt updates directly in your daily feed and save posts into your personal collections.</p>
  </div>
  <div class="ps-faq-card">
    <h4>How often is new prompt inspiration posted?</h4>
    <p>We publish fresh prompt breakdowns, portrait experiments, and trending aesthetic showcases multiple times each week, coordinating with in-depth articles published on PromptSyra.com.</p>
  </div>
  <div class="ps-faq-card">
    <h4>Can I request a specific prompt theme or lighting setup?</h4>
    <p>Yes! Leave a comment on any recent post or send us a direct message on Instagram. Our editorial team frequently turns community requests into full prompt guides on the website.</p>
  </div>
</div>

<div class="ps-connect-footer-bar">
  <p>Looking for more ways to connect? Explore our <a href="/facebook/">Official Facebook Page</a> and join our private <a href="/whatsapp-channel/">WhatsApp Channel</a> for direct mobile alerts.</p>
</div>
`;

// Full rich HTML content for Facebook page
const facebookHtml = `
<div class="ps-editorial-intro-block">
  <p class="ps-page-lead">Welcome to the official PromptSyra Facebook community page. Follow us on Facebook to stay connected with our latest AI photo prompt guides, editorial articles, creative discussions, and website announcements.</p>
  <p>Our Facebook page connects creative enthusiasts, digital creators, and photographers who use AI image generation to craft realistic portraits, trending visuals, and social media graphics. It serves as an active community hub where we share full prompt breakdowns, highlight author articles, and answer reader questions.</p>
</div>

<div class="ps-social-cta-banner ps-facebook-banner">
  <div class="ps-social-cta-content">
    <span class="ps-badge ps-badge-facebook">Official Facebook</span>
    <h2>Follow PromptSyra on Facebook</h2>
    <p>Join our Facebook community to get article releases, creative prompt breakdowns, and discussion threads in your news feed.</p>
    <div class="ps-social-cta-actions">
      <a href="https://www.facebook.com/people/PromptSyra/61595584906422/" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-facebook">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M18 2h-3a5 5 0 0 0-5 5v3H7v4h3v8h4v-8h3l1-4h-4V7a1 1 0 0 1 1-1h3z"/></svg>
        <span>Follow PromptSyra on Facebook</span>
      </a>
      <span class="ps-social-cta-note">Official verified Facebook profile: ID 61595584906422</span>
    </div>
  </div>
</div>

<div class="ps-about-pillars">
  <div class="ps-about-pillar">
    <h3>Article &amp; Guide Releases</h3>
    <p>Be the first to read our comprehensive prompt collections as soon as they are published on PromptSyra.com, complete with direct links and copyable text.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Community Discussions</h3>
    <p>Engage with fellow creators in post comments. Share your generated results, compare model behaviors, and discover new keyword combinations.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Platform Updates &amp; Tips</h3>
    <p>Receive practical alerts whenever major AI tools (ChatGPT, Midjourney, Gemini, Grok) release updates that impact prompt syntax or image realism.</p>
  </div>
</div>

<h2>How the Facebook Page Relates to PromptSyra.com</h2>
<p>PromptSyra.com is our central editorial library containing categorized, searchable prompt collections with one-click copy buttons and structured customization tables. Our Facebook page functions as the community companion:</p>
<ul>
  <li><strong>Quick Highlights:</strong> We summarize key takeaways from new articles so you can decide if a prompt collection matches your project needs.</li>
  <li><strong>Direct Feedback:</strong> Post comments provide a direct line to our editorial contributors, including Syra Ansari, Arjun Patel, and Neha Joshi.</li>
  <li><strong>Creative Challenges:</strong> Periodic prompt challenges where readers adapt a weekly lighting setup to their own creative styles.</li>
</ul>

<h2>Frequently Asked Questions</h2>
<div class="ps-faq-grid">
  <div class="ps-faq-card">
    <h4>Is the Facebook page free to follow?</h4>
    <p>Yes, completely free. Simply click the "Follow" or "Like" button on our official Facebook page to start seeing updates in your feed.</p>
  </div>
  <div class="ps-faq-card">
    <h4>Where can I find the full prompt text shared in Facebook posts?</h4>
    <p>Each Facebook post includes either the exact prompt formula directly in the post text or a direct link to the corresponding detailed article on PromptSyra.com where you can copy the full prompt with one click.</p>
  </div>
  <div class="ps-faq-card">
    <h4>How do I ensure PromptSyra posts appear in my feed?</h4>
    <p>After clicking "Follow" on our Facebook page, you can select "Favorites" in your follow settings to prioritize PromptSyra updates at the top of your feed.</p>
  </div>
</div>

<div class="ps-connect-footer-bar">
  <p>Connect with our other official channels: Explore our <a href="/instagram/">Instagram Visual Gallery</a> or subscribe to the private <a href="/whatsapp-channel/">PromptSyra WhatsApp Channel</a>.</p>
</div>
`;

// Full rich HTML content for WhatsApp Channel page
const whatsappHtml = `
<div class="ps-editorial-intro-block">
  <p class="ps-page-lead">Welcome to the official PromptSyra WhatsApp Channel. Subscribe to our channel for instant, direct-to-mobile prompt alerts, creative drops, trending photo ideas, and website updates delivered right inside your WhatsApp Updates tab.</p>
  <p>WhatsApp Channels offer a streamlined, distraction-free way to stay informed without relying on social media algorithms or crowded email inboxes. Every broadcast contains carefully selected prompt specimens and actionable guidance.</p>
</div>

<div class="ps-social-cta-banner ps-whatsapp-banner">
  <div class="ps-social-cta-content">
    <span class="ps-badge ps-badge-whatsapp">Official WhatsApp Channel</span>
    <h2>Join the PromptSyra WhatsApp Channel</h2>
    <p>Receive instant prompt drops, portrait lighting setups, and creative ideas directly on your phone with complete personal privacy.</p>
    <div class="ps-social-cta-actions">
      <a href="https://whatsapp.com/channel/0029VbDkVtP7T8bZzLpWMq2d" target="_blank" rel="noopener noreferrer" class="ps-btn ps-btn-whatsapp">
        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"><path d="M3 21l1.65-3.8a9 9 0 1 1 3.4 2.9L3 21"/><path d="M9 10a.5.5 0 0 0 1 0V9a.5.5 0 0 0-1 0v1a5 5 0 0 0 5 5h1a.5.5 0 0 0 0-1h-1a.5.5 0 0 0 0 1"/></svg>
        <span>Join PromptSyra WhatsApp Channel</span>
      </a>
      <span class="ps-social-cta-note">100% Free · One-Way Broadcast · Private &amp; Secure</span>
    </div>
  </div>
</div>

<div class="ps-privacy-callout">
  <div class="ps-privacy-callout-icon" aria-hidden="true">
    <svg width="28" height="28" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect width="18" height="11" x="3" y="11" rx="2" ry="2"/><path d="M7 11V7a5 5 0 0 1 10 0v4"/></svg>
  </div>
  <div class="ps-privacy-callout-text">
    <h3>Your Privacy is Fully Protected</h3>
    <p>WhatsApp Channels are designed with built-in privacy. As a channel follower, <strong>your phone number, profile photo, and real name are completely hidden</strong> from other followers and even from the PromptSyra channel administrators. Nobody in the channel can view your contact details, message you, or see which updates you read.</p>
  </div>
</div>

<div class="ps-about-pillars">
  <div class="ps-about-pillar">
    <h3>Instant Mobile Drops</h3>
    <p>Receive curated prompts directly on your mobile device, formatted so you can easily copy and paste them into ChatGPT, Midjourney, or your favorite mobile AI app.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Zero Clutter &amp; Spam</h3>
    <p>We respect your attention. We send only high-value notifications: new article releases, weekly prompt highlights, and major visual trend alerts.</p>
  </div>
  <div class="ps-about-pillar">
    <h3>Simple One-Click Access</h3>
    <p>Find our channel anytime under the "Updates" tab in WhatsApp alongside your regular status feeds, completely separated from your personal chats.</p>
  </div>
</div>

<h2>How to Follow &amp; Enable Notifications</h2>
<p>To make sure you never miss a new prompt collection, follow these simple steps:</p>
<ol class="ps-steps-list">
  <li>Click the green <strong>"Join PromptSyra WhatsApp Channel"</strong> button above to open the channel in WhatsApp.</li>
  <li>Tap the <strong>Follow</strong> button in the upper-right corner of the channel header.</li>
  <li><strong>Important:</strong> Tap the <strong>Bell icon</strong> (notifications) at the top of the channel to unmute updates so you get notified when a new prompt drop arrives.</li>
</ol>

<h2>Frequently Asked Questions</h2>
<div class="ps-faq-grid">
  <div class="ps-faq-card">
    <h4>Is the WhatsApp Channel free to join?</h4>
    <p>Yes, following our WhatsApp Channel is 100% free and requires no subscription or personal information.</p>
  </div>
  <div class="ps-faq-card">
    <h4>Can other channel followers see my phone number?</h4>
    <p>No. WhatsApp Channels are private one-way broadcasts. Neither other subscribers nor PromptSyra admins can see your phone number, name, or profile picture.</p>
  </div>
  <div class="ps-faq-card">
    <h4>Can I leave or mute the channel at any time?</h4>
    <p>Yes. You have full control. You can mute notifications or unfollow the channel at any moment directly from the channel options menu in WhatsApp.</p>
  </div>
</div>

<div class="ps-connect-footer-bar">
  <p>Also explore our <a href="/instagram/">Instagram Visual Gallery</a> and our <a href="/facebook/">Facebook Community Page</a> for more prompt inspiration.</p>
</div>
`;

async function main() {
  console.log("=== PUBLISHING SOCIAL PAGES ON PROMPTSYRA.COM ===");
  
  // 1. Authenticate with WordPress
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

  // Get REST Nonce from post-new.php
  const postNewRes = await request('https://promptsyra.com/wp-admin/post-new.php?post_type=page', {
    headers: { 'Cookie': cookieHeader }
  });

  const apiMatch = postNewRes.body.match(/wpApiSettings\s*=\s*(\{.+?\});/);
  if (!apiMatch) {
    throw new Error("Could not find wpApiSettings on post-new.php");
  }
  const apiSettings = JSON.parse(apiMatch[1]);
  const restNonce = apiSettings.nonce;
  console.log("Acquired REST Nonce:", restNonce);

  // 2. Fetch existing pages to check if they already exist
  const existingPagesRes = await request('https://promptsyra.com/wp-json/wp/v2/pages?per_page=100', {
    headers: {
      'Cookie': cookieHeader,
      'X-WP-Nonce': restNonce
    }
  });

  const existingPages = JSON.parse(existingPagesRes.body);
  const existingBySlug = {};
  existingPages.forEach(p => {
    existingBySlug[p.slug] = p;
  });

  console.log("Current existing pages on site:", Object.keys(existingBySlug));

  // 3. Define the three target pages
  const targetPages = [
    {
      slug: 'instagram',
      title: 'Follow PromptSyra on Instagram',
      content: instagramHtml
    },
    {
      slug: 'facebook',
      title: 'Follow PromptSyra on Facebook',
      content: facebookHtml
    },
    {
      slug: 'whatsapp-channel',
      title: 'Join the PromptSyra WhatsApp Channel',
      content: whatsappHtml
    }
  ];

  for (const target of targetPages) {
    const existing = existingBySlug[target.slug];
    if (existing) {
      console.log(`Page '${target.slug}' exists (ID: ${existing.id}). Updating content and ensuring publish status...`);
      const updateData = JSON.stringify({
        title: target.title,
        content: target.content,
        status: 'publish'
      });
      const updateRes = await request(`https://promptsyra.com/wp-json/wp/v2/pages/${existing.id}`, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Content-Length': Buffer.byteLength(updateData),
          'Cookie': cookieHeader,
          'X-WP-Nonce': restNonce
        }
      }, updateData);

      console.log(`Update ${target.slug} response status:`, updateRes.statusCode);
      const updated = JSON.parse(updateRes.body);
      console.log(`Updated page URL: ${updated.link}`);
    } else {
      console.log(`Page '${target.slug}' does NOT exist. Creating new published page...`);
      const createData = JSON.stringify({
        slug: target.slug,
        title: target.title,
        content: target.content,
        status: 'publish'
      });
      const createRes = await request('https://promptsyra.com/wp-json/wp/v2/pages', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json',
          'Content-Length': Buffer.byteLength(createData),
          'Cookie': cookieHeader,
          'X-WP-Nonce': restNonce
        }
      }, createData);

      console.log(`Create ${target.slug} response status:`, createRes.statusCode);
      const created = JSON.parse(createRes.body);
      console.log(`Created page ID: ${created.id}, URL: ${created.link}`);
    }
  }

  // 4. Verify public access to all three pages
  console.log("\n=== VERIFYING PUBLIC HTTP ACCESS TO SOCIAL PAGES ===");
  for (const target of targetPages) {
    const publicUrl = `https://promptsyra.com/${target.slug}/`;
    const checkRes = await request(publicUrl, { method: 'GET' });
    console.log(`Public URL: ${publicUrl} -> Status: ${checkRes.statusCode}, Title found: ${checkRes.body.includes(target.title)}`);
  }
}

main().catch(console.error);
