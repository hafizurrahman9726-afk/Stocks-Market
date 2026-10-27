// scripts/inspect-footer.mjs
import fs from 'fs';

const footer = fs.readFileSync('wordpress-theme/promptsyra/template-parts/footer-promptsyra.php', 'utf8');
console.log("Footer file length:", footer.length);

// Search for social links or urls
const socialMatch = footer.match(/social|instagram|facebook|twitter|whatsapp/i);
console.log("Has social mentions:", Boolean(socialMatch));

// Print lines with social
const lines = footer.split('\n');
lines.forEach((l, idx) => {
  if (/social|instagram|facebook|whatsapp|follow|community/i.test(l)) {
    console.log(`Line ${idx + 1}: ${l.trim()}`);
  }
});
