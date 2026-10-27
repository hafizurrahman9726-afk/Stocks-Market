// scripts/generate-theme-screenshot.mjs
import sharp from 'sharp';
import fs from 'fs';

async function createScreenshot() {
  const WIDTH = 1200;
  const HEIGHT = 900;

  console.log('Preparing card images...');
  
  // Resize hero images with rounded corners
  // 1. Hero main portrait (left large card image: ~320x330)
  const heroPortrait = await sharp('temp_assets/hero-portrait.webp')
    .resize(320, 320, { fit: 'cover', position: 'top' })
    .toBuffer();

  // 2. Creative studio (top right card image: ~220x135)
  const creativeStudio = await sharp('temp_assets/creative-studio.webp')
    .resize(220, 135, { fit: 'cover' })
    .toBuffer();

  // 3. Social visuals (bottom right card image: ~220x135)
  const socialVisuals = await sharp('temp_assets/social-visuals.webp')
    .resize(220, 135, { fit: 'cover' })
    .toBuffer();

  // 4. Logo icon
  const logo = await sharp('temp_assets/logo.webp')
    .resize(44, 44)
    .toBuffer();

  // SVG background and layout
  const svgOverlay = Buffer.from(`
    <svg width="${WIDTH}" height="${HEIGHT}" viewBox="0 0 ${WIDTH} ${HEIGHT}" xmlns="http://www.w3.org/2000/svg">
      <defs>
        <linearGradient id="bgGrad" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%" stop-color="#070a12" />
          <stop offset="50%" stop-color="#0d1424" />
          <stop offset="100%" stop-color="#080c16" />
        </linearGradient>
        <linearGradient id="headerGrad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#0b101d" />
          <stop offset="100%" stop-color="#070a12" />
        </linearGradient>
        <linearGradient id="purpleGrad" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#6366f1" />
          <stop offset="50%" stop-color="#8b5cf6" />
          <stop offset="100%" stop-color="#d946ef" />
        </linearGradient>
        <linearGradient id="blueGrad" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#3b82f6" />
          <stop offset="100%" stop-color="#06b6d4" />
        </linearGradient>
        <linearGradient id="roseGrad" x1="0" y1="0" x2="1" y2="0">
          <stop offset="0%" stop-color="#f43f5e" />
          <stop offset="100%" stop-color="#fb923c" />
        </linearGradient>
        <linearGradient id="cardGrad" x1="0" y1="0" x2="0" y2="1">
          <stop offset="0%" stop-color="#161f33" stop-opacity="0.9" />
          <stop offset="100%" stop-color="#0d1424" stop-opacity="0.95" />
        </linearGradient>
        <filter id="glow" x="-20%" y="-20%" width="140%" height="140%">
          <feGaussianBlur stdDeviation="35" result="blur" />
          <feComposite in="SourceGraphic" in2="blur" operator="over" />
        </filter>
      </defs>

      <!-- Background Canvas -->
      <rect width="${WIDTH}" height="${HEIGHT}" fill="url(#bgGrad)" />

      <!-- Ambient Glow Orbs -->
      <circle cx="180" cy="240" r="220" fill="#6366f1" opacity="0.12" filter="url(#glow)" />
      <circle cx="1020" cy="260" r="240" fill="#a855f7" opacity="0.10" filter="url(#glow)" />
      <circle cx="600" cy="740" r="200" fill="#06b6d4" opacity="0.08" filter="url(#glow)" />

      <!-- Top Header Bar -->
      <rect x="0" y="0" width="${WIDTH}" height="80" fill="url(#headerGrad)" />
      <line x1="0" y1="80" x2="${WIDTH}" y2="80" stroke="#1e293b" stroke-width="1.2" />

      <!-- Brand Text -->
      <text x="100" y="47" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="22" font-weight="800" fill="#ffffff">Prompt<tspan fill="#a855f7">Syra</tspan><tspan fill="#64748b" font-size="16" font-weight="600">.com</tspan></text>
      <text x="100" y="62" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="10" font-weight="600" fill="#94a3b8" letter-spacing="1.2">AI PHOTO PROMPTS &amp; CREATIVE STUDIO</text>

      <!-- Header Navigation -->
      <g font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13.5" font-weight="600">
        <text x="410" y="46" fill="#a855f7">Home</text>
        <line x1="410" y1="54" x2="450" y2="54" stroke="#a855f7" stroke-width="2.5" stroke-linecap="round" />
        <text x="475" y="46" fill="#e2e8f0">AI Photo Prompts</text>
        <text x="620" y="46" fill="#e2e8f0">Trending Prompts</text>
        <text x="765" y="46" fill="#e2e8f0">Social Media Prompts</text>
        <text x="935" y="46" fill="#e2e8f0">Important Pages ▾</text>
      </g>

      <!-- Header CTA Button -->
      <rect x="1075" y="22" width="90" height="36" rx="18" fill="url(#purpleGrad)" />
      <text x="1120" y="45" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="700" fill="#ffffff" text-anchor="middle">Explore →</text>

      <!-- Hero Left Column -->
      <!-- Eyebrow Pill -->
      <rect x="52" y="130" width="265" height="30" rx="15" fill="#1e1b4b" stroke="#4338ca" stroke-width="1.2" />
      <text x="68" y="150" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11.5" font-weight="700" fill="#a5b4fc">✨ AI PHOTO PROMPTS &amp; STUDIO LIGHTING</text>

      <!-- Main Headline -->
      <text x="52" y="215" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="44" font-weight="900" fill="#ffffff" letter-spacing="-1">
        Turn Your Ideas Into
      </text>
      <text x="52" y="268" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="44" font-weight="900" fill="url(#purpleGrad)" letter-spacing="-1">
        Stunning AI Creations
      </text>

      <!-- Subtitle Description -->
      <text x="52" y="315" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="15.5" font-weight="400" fill="#94a3b8">
        Curated prompt formulas, exact photographic camera settings,
      </text>
      <text x="52" y="338" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="15.5" font-weight="400" fill="#94a3b8">
        and studio lighting references designed for real creators.
      </text>

      <!-- Search Bar -->
      <rect x="52" y="375" width="480" height="52" rx="26" fill="#0f172a" stroke="#334155" stroke-width="1.5" />
      <circle cx="78" cy="401" r="7" stroke="#64748b" stroke-width="2" fill="none" />
      <line x1="83" y1="406" x2="90" y2="413" stroke="#64748b" stroke-width="2" stroke-linecap="round" />
      <text x="102" y="407" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13.5" fill="#64748b">Search photo prompts (portraits, 85mm, studio...)</text>
      <rect x="424" y="381" width="100" height="40" rx="20" fill="url(#purpleGrad)" />
      <text x="474" y="406" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="700" fill="#ffffff" text-anchor="middle">Search</text>

      <!-- Trust Badges / Value Props -->
      <g transform="translate(52, 455)">
        <rect x="0" y="0" width="480" height="58" rx="12" fill="#101726" stroke="#1f293d" stroke-width="1" />
        <g transform="translate(18, 17)">
          <circle cx="12" cy="12" r="10" fill="#4338ca" />
          <path d="M8 12l3 3 5-5" stroke="#ffffff" stroke-width="2" fill="none" stroke-linecap="round" />
          <text x="30" y="16" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="700" fill="#f1f5f9">Structured Prompts</text>
        </g>
        <g transform="translate(175, 17)">
          <circle cx="12" cy="12" r="10" fill="#0284c7" />
          <path d="M8 12l3 3 5-5" stroke="#ffffff" stroke-width="2" fill="none" stroke-linecap="round" />
          <text x="30" y="16" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="700" fill="#f1f5f9">Studio Lighting</text>
        </g>
        <g transform="translate(325, 17)">
          <circle cx="12" cy="12" r="10" fill="#7c3aed" />
          <path d="M8 12l3 3 5-5" stroke="#ffffff" stroke-width="2" fill="none" stroke-linecap="round" />
          <text x="30" y="16" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="700" fill="#f1f5f9">Tested Models</text>
        </g>
      </g>

      <!-- Right Visual Grid Cards -->
      <!-- Card 1: Main Portrait Card Frame -->
      <rect x="568" y="125" width="344" height="425" rx="18" fill="url(#cardGrad)" stroke="#334155" stroke-width="1.5" />
      <rect x="580" y="137" width="125" height="22" rx="11" fill="#4338ca" opacity="0.9" />
      <text x="642" y="152" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="10" font-weight="800" fill="#e0e7ff" text-anchor="middle">PORTRAIT STUDY</text>
      <!-- Image placed here via Sharp composite at (580, 168) -->
      <text x="584" y="515" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="15" font-weight="800" fill="#ffffff">85mm Soft Rim Light</text>
      <text x="584" y="534" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="500" fill="#94a3b8">Golden hour natural diffusion with bokeh</text>

      <!-- Card 2: Creative Studio Card Frame -->
      <rect x="918" y="125" width="236" height="205" rx="16" fill="url(#cardGrad)" stroke="#334155" stroke-width="1.5" />
      <!-- Image placed here at (926, 137) -->
      <text x="928" y="296" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="800" fill="#ffffff">Creative Studio Setup</text>
      <text x="928" y="314" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11" font-weight="500" fill="#94a3b8">Multi-point lighting prompts</text>

      <!-- Card 3: Social Visuals Card Frame -->
      <rect x="918" y="345" width="236" height="205" rx="16" fill="url(#cardGrad)" stroke="#334155" stroke-width="1.5" />
      <!-- Image placed here at (926, 357) -->
      <text x="928" y="516" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="800" fill="#ffffff">9:16 Social Visuals</text>
      <text x="928" y="534" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11" font-weight="500" fill="#94a3b8">Vertical reel &amp; feed styles</text>

      <!-- Divider Section -->
      <line x1="52" y1="580" x2="1148" y2="580" stroke="#1e293b" stroke-width="1.2" />

      <!-- Bottom Category Showcase Triptych -->
      <g transform="translate(52, 610)">
        <!-- Column 1: AI Photo Prompts -->
        <rect x="0" y="0" width="345" height="152" rx="16" fill="#0f172a" stroke="#334155" stroke-width="1.2" />
        <rect x="20" y="20" width="44" height="44" rx="12" fill="#1e1b4b" stroke="#4338ca" stroke-width="1" />
        <!-- Camera SVG Icon -->
        <g transform="translate(32, 32)" stroke="#818cf8" stroke-width="2" fill="none">
          <path d="M2 7a2 2 0 0 1 2-2h2.5l1.5-2h8l1.5 2H20a2 2 0 0 1 2 2v10a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2z"/>
          <circle cx="12" cy="12" r="3.5"/>
        </g>
        <text x="76" y="42" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="16" font-weight="800" fill="#ffffff">AI Photo Prompts</text>
        <text x="76" y="60" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="600" fill="#818cf8">Category Archive</text>
        <text x="20" y="94" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">Structured portrait, studio lighting, and creative</text>
        <text x="20" y="112" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">photography prompt templates.</text>
        <text x="20" y="136" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11.5" font-weight="700" fill="#a855f7">View Category →</text>

        <!-- Column 2: Trending Prompts -->
        <rect x="375" y="0" width="345" height="152" rx="16" fill="#0f172a" stroke="#334155" stroke-width="1.2" />
        <rect x="395" y="20" width="44" height="44" rx="12" fill="#2d133b" stroke="#8b5cf6" stroke-width="1" />
        <!-- Trend / Flame SVG Icon -->
        <g transform="translate(407, 32)" stroke="#c084fc" stroke-width="2" fill="none">
          <polyline points="20 4 13 11 9 7 2 14" stroke-linecap="round" stroke-linejoin="round" />
          <polyline points="15 4 20 4 20 9" stroke-linecap="round" stroke-linejoin="round" />
        </g>
        <text x="451" y="42" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="16" font-weight="800" fill="#ffffff">Trending Prompts</text>
        <text x="451" y="60" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="600" fill="#a78bfa">Category Archive</text>
        <text x="395" y="94" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">Popular visual concepts, seasonal styles, and</text>
        <text x="395" y="112" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">current photography ideas.</text>
        <text x="395" y="136" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11.5" font-weight="700" fill="#a855f7">View Category →</text>

        <!-- Column 3: Social Media Prompts -->
        <rect x="750" y="0" width="345" height="152" rx="16" fill="#0f172a" stroke="#334155" stroke-width="1.2" />
        <rect x="770" y="20" width="44" height="44" rx="12" fill="#3b1129" stroke="#ec4899" stroke-width="1" />
        <!-- Smartphone SVG Icon -->
        <g transform="translate(782, 32)" stroke="#f472b6" stroke-width="2" fill="none">
          <rect x="5" y="2" width="14" height="20" rx="2" ry="2"/>
          <line x1="12" y1="18" x2="12.01" y2="18" stroke-linecap="round"/>
        </g>
        <text x="826" y="42" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="16" font-weight="800" fill="#ffffff">Social Media Prompts</text>
        <text x="826" y="60" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="600" fill="#f472b6">Category Archive</text>
        <text x="770" y="94" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">Visual prompt ideas formatted for reels,</text>
        <text x="770" y="112" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="12" font-weight="400" fill="#94a3b8">thumbnails, stories, and feed posts.</text>
        <text x="770" y="136" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="11.5" font-weight="700" fill="#a855f7">View Category →</text>
      </g>

      <!-- Bottom Status & Version Footer Bar -->
      <rect x="0" y="845" width="${WIDTH}" height="55" fill="#050811" />
      <line x1="0" y1="845" x2="${WIDTH}" y2="845" stroke="#1e293b" stroke-width="1" />
      <text x="52" y="878" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="600" fill="#64748b">
        PromptSyra WordPress Theme · Production Theme v2.4.1
      </text>
      <text x="${WIDTH - 52}" y="878" font-family="-apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif" font-size="13" font-weight="600" fill="#94a3b8" text-anchor="end">
        https://promptsyra.com
      </text>
    </svg>
  `);

  console.log('Compositing theme screenshot...');
  const result = await sharp({
    create: {
      width: WIDTH,
      height: HEIGHT,
      channels: 4,
      background: { r: 7, g: 10, b: 18, alpha: 1 }
    }
  })
  .composite([
    // Vector overlay base
    { input: svgOverlay, top: 0, left: 0 },
    // Logo at header
    { input: logo, top: 18, left: 46 },
    // Hero main portrait inside card 1 (rounded corners via Sharp)
    { 
      input: await sharp(heroPortrait)
        .composite([{
          input: Buffer.from(`<svg><rect x="0" y="0" width="320" height="320" rx="14" ry="14" /></svg>`),
          blend: 'dest-in'
        }])
        .png()
        .toBuffer(),
      top: 168,
      left: 580
    },
    // Creative studio inside card 2
    { 
      input: await sharp(creativeStudio)
        .composite([{
          input: Buffer.from(`<svg><rect x="0" y="0" width="220" height="135" rx="12" ry="12" /></svg>`),
          blend: 'dest-in'
        }])
        .png()
        .toBuffer(),
      top: 137,
      left: 926
    },
    // Social visuals inside card 3
    { 
      input: await sharp(socialVisuals)
        .composite([{
          input: Buffer.from(`<svg><rect x="0" y="0" width="220" height="135" rx="12" ry="12" /></svg>`),
          blend: 'dest-in'
        }])
        .png()
        .toBuffer(),
      top: 357,
      left: 926
    }
  ])
  .png({ compressionLevel: 8 })
  .toFile('wordpress-theme/promptsyra/screenshot.png');

  console.log('Polished screenshot generated successfully:', result);
}

createScreenshot().catch(console.error);
