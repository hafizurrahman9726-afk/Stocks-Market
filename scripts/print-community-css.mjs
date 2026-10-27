// scripts/print-community-css.mjs
import fs from 'fs';

const css = fs.readFileSync('wordpress-theme/promptsyra/assets/css/promptsyra-home.css', 'utf8');
const lines = css.split('\n');
let capturing = false;
let output = [];

lines.forEach((line, i) => {
  if (line.includes('ps-community')) {
    capturing = true;
  }
  if (capturing) {
    output.push(`${i+1}: ${line}`);
    if (output.length > 80) capturing = false;
  }
});

console.log(output.join('\n'));
