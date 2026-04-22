const fs = require('fs');
let css = fs.readFileSync('resources/css/app.css', 'utf8');

// The theme mapping
const themeBlock = `@theme {
  --color-teal-50: #eef2ff;
  --color-teal-100: #e0e7ff;
  --color-teal-200: #c7d2fe;
  --color-teal-300: #a5b4fc;
  --color-teal-400: #818cf8;
  --color-teal-500: #6366f1;
  --color-teal-600: #4f46e5;
  --color-teal-700: #4338ca;
  --color-teal-800: #3730a3;
  --color-teal-900: #312e81;
  --color-teal-950: #1e1b4b;

  --color-sky-50: #f5f3ff;
  --color-sky-100: #ede9fe;
  --color-sky-200: #ddd6fe;
  --color-sky-300: #c4b5fd;
  --color-sky-400: #a78bfa;
  --color-sky-500: #8b5cf6;
  --color-sky-600: #7c3aed;
  --color-sky-700: #6d28d9;
  --color-sky-800: #5b21b6;
  --color-sky-900: #4c1d95;
  --color-sky-950: #2e1065;

  --color-cyan-50: #faf5ff;
  --color-cyan-100: #f3e8ff;
  --color-cyan-200: #e9d5ff;
  --color-cyan-300: #d8b4fe;
  --color-cyan-400: #c084fc;
  --color-cyan-500: #a855f7;
  --color-cyan-600: #9333ea;
  --color-cyan-700: #7e22ce;
  --color-cyan-800: #6b21a8;
  --color-cyan-900: #581c87;
  --color-cyan-950: #3b0764;

  --color-orange-50: #fff1f2;
  --color-orange-100: #ffe4e6;
  --color-orange-200: #fecdd3;
  --color-orange-300: #fda4af;
  --color-orange-400: #fb7185;
  --color-orange-500: #f43f5e;
  --color-orange-600: #e11d48;
  --color-orange-700: #be123c;
  --color-orange-800: #9f1239;
  --color-orange-900: #881337;
  --color-orange-950: #4c0519;
}

`;

if (!css.includes('@theme {')) {
  // Insert the theme block
  css = css.replace('@import "tailwindcss";', '@import "tailwindcss";\n\n' + themeBlock);
}

// Hex Replacements
css = css.replace(/#0f766e/gi, '#4338ca'); // teal-700 -> indigo-700
css = css.replace(/#0284c7/gi, '#7c3aed'); // sky-600 -> violet-600
css = css.replace(/#f97316/gi, '#f43f5e'); // orange-500 -> rose-500
css = css.replace(/#06b6d4/gi, '#a855f7'); // cyan-500 -> purple-500
css = css.replace(/#14b8a6/gi, '#6366f1'); // teal-500 -> indigo-500
css = css.replace(/#38bdf8/gi, '#a78bfa'); // sky-400 -> violet-400
css = css.replace(/#0ea5e9/gi, '#8b5cf6'); // sky-500 -> violet-500

// RGBA Replacements
// Teal
css = css.replace(/15,\s*118,\s*110/g, '67, 56, 202'); // teal-700
css = css.replace(/13,\s*148,\s*136/g, '79, 70, 229'); // teal-600
css = css.replace(/20,\s*184,\s*166/g, '99, 102, 241'); // teal-500

// Sky
css = css.replace(/2,\s*132,\s*199/g, '124, 58, 237'); // sky-600
css = css.replace(/14,\s*165,\s*233/g, '139, 92, 246'); // sky-500
css = css.replace(/56,\s*189,\s*248/g, '167, 139, 250'); // sky-400

// Cyan
css = css.replace(/14,\s*116,\s*144/g, '126, 34, 206'); // cyan-700 -> purple-700
css = css.replace(/8,\s*145,\s*178/g, '147, 51, 234'); // cyan-600 -> purple-600
css = css.replace(/6,\s*182,\s*212/g, '168, 85, 247'); // cyan-500 -> purple-500
css = css.replace(/103,\s*232,\s*249/g, '216, 180, 254'); // cyan-300 -> purple-300

// Orange
css = css.replace(/249,\s*115,\s*22/g, '244, 63, 94'); // orange-500 -> rose-500
css = css.replace(/245,\s*158,\s*11/g, '251, 113, 133'); // amber-500 -> rose-400 (if amber is used somewhere)

// Write back
fs.writeFileSync('resources/css/app.css', css);
console.log('Theme updated');
