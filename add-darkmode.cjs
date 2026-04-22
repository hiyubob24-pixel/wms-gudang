const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = path.join(dir, file);
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if(file.endsWith('.blade.php')) results.push(file);
        }
    });
    return results;
}

const files = walk('resources/views');

const classMap = {
    'bg-white': 'bg-white dark:bg-slate-900',
    'bg-white/95': 'bg-white/95 dark:bg-slate-900/95',
    'bg-slate-50': 'bg-slate-50 dark:bg-slate-800',
    'bg-slate-100': 'bg-slate-100 dark:bg-slate-800/80',
    'border-slate-200': 'border-slate-200 dark:border-slate-700',
    'border-slate-100': 'border-slate-100 dark:border-slate-700/60',
    'text-slate-900': 'text-slate-900 dark:text-slate-100',
    'text-slate-800': 'text-slate-800 dark:text-slate-200',
    'text-slate-700': 'text-slate-700 dark:text-slate-300',
    'text-slate-600': 'text-slate-600 dark:text-slate-400',
    'text-slate-500': 'text-slate-500 dark:text-slate-400',
    'shadow-sm': 'shadow-sm dark:shadow-none',
    'shadow-xl': 'shadow-xl dark:shadow-none',
    'bg-indigo-50': 'bg-indigo-50 dark:bg-indigo-900/30',
    'text-indigo-700': 'text-indigo-700 dark:text-indigo-300',
    'border-indigo-200': 'border-indigo-200 dark:border-indigo-700/50',
    'bg-violet-50': 'bg-violet-50 dark:bg-violet-900/30',
    'text-violet-700': 'text-violet-700 dark:text-violet-300',
    'border-violet-200': 'border-violet-200 dark:border-violet-700/50',
    'bg-sky-50': 'bg-sky-50 dark:bg-sky-900/30',
    'text-sky-700': 'text-sky-700 dark:text-sky-300',
    'border-sky-200': 'border-sky-200 dark:border-sky-700/50',
    'bg-emerald-50': 'bg-emerald-50 dark:bg-emerald-900/30',
    'text-emerald-700': 'text-emerald-700 dark:text-emerald-300',
    'border-emerald-200': 'border-emerald-200 dark:border-emerald-700/50',
    'text-rose-600': 'text-rose-600 dark:text-rose-400',
    'bg-rose-50': 'bg-rose-50 dark:bg-rose-900/40',
    'border-rose-200': 'border-rose-200 dark:border-rose-700/50',
};

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    // We process class attributes specifically, or just match exactly bounded string
    for (const [oldClass, newClass] of Object.entries(classMap)) {
        // Find oldClass not already followed by dark equivalent
        // Match oldClass bounded by space, quote, or newline
        const regex = new RegExp(`(?<![a-zA-Z0-9_-])(${oldClass.split('/').join('\\\\/')})(?![a-zA-Z0-9_-])`, 'g');
        content = content.replace(regex, (match) => {
            // Prevent duplicate dark mode injection if it's already there
            // Actually, we can just replace and then clean up.
            return newClass;
        });
    }

    // Cleanup duplicated dark classes
    for (const newClass of Object.values(classMap)) {
        const darkPart = newClass.split(' ')[1]; // the dark:...
        // Replace "bg-white dark:bg-slate-900 dark:bg-slate-900" with just "bg-white dark:bg-slate-900"
        const doubleDark = new RegExp(`${darkPart.split('/').join('\\\\/')}\\s+${darkPart.split('/').join('\\\\/')}`, 'g');
        content = content.replace(doubleDark, darkPart);
    }
    
    // some manual fixes for existing classes that might have been processed previously
    content = content.replace(/dark:bg-slate-900\s+dark:bg-slate-900/g, 'dark:bg-slate-900');

    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
    }
});
console.log('Applied smart dark mode classes');
