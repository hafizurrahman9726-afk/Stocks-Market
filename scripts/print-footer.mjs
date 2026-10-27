// scripts/print-footer.mjs
import fs from 'fs';

const footer = fs.readFileSync('wordpress-theme/promptsyra/template-parts/footer-promptsyra.php', 'utf8');
console.log(footer);
