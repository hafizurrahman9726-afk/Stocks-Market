// scripts/inspect-css.mjs
import fs from 'fs';

const css = fs.readFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-home.css', 'utf8');
console.log("CSS file length:", css.length);

const hasCommunity = css.includes('ps-community');
console.log("Already has ps-community?", hasCommunity);
