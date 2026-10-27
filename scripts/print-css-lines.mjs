// scripts/print-css-lines.mjs
import fs from 'fs';

const css = fs.readFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-home.css', 'utf8');
const lines = css.split('\n');
console.log(lines.slice(1390, 1460).map((l, i) => `${i + 1391}: ${l}`).join('\n'));
