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
    'bg-gray-50': 'bg-gray-50 dark:bg-slate-800',
    'bg-gray-100': 'bg-gray-100 dark:bg-slate-800/80',
    'border-gray-200': 'border-gray-200 dark:border-slate-700',
    'border-gray-100': 'border-gray-100 dark:border-slate-700/60',
    'text-gray-900': 'text-gray-900 dark:text-slate-100',
    'text-gray-800': 'text-gray-800 dark:text-slate-200',
    'text-gray-700': 'text-gray-700 dark:text-slate-300',
    'text-gray-600': 'text-gray-600 dark:text-slate-400',
    'text-gray-500': 'text-gray-500 dark:text-slate-400',
    'divide-gray-200': 'divide-gray-200 dark:divide-slate-700',
    'divide-slate-200': 'divide-slate-200 dark:divide-slate-700',
    'divide-gray-100': 'divide-gray-100 dark:divide-slate-700/60',
    'divide-slate-100': 'divide-slate-100 dark:divide-slate-700/60',
    'bg-blue-100': 'bg-blue-100 dark:bg-indigo-900/30',
    'text-blue-600': 'text-blue-600 dark:text-indigo-400',
    'border-blue-500': 'border-blue-500 dark:border-indigo-500',
    'bg-red-100': 'bg-red-100 dark:bg-rose-900/30',
    'text-red-600': 'text-red-600 dark:text-rose-400',
    'border-green-500': 'border-green-500 dark:border-emerald-500',
    'border-red-500': 'border-red-500 dark:border-rose-500',
};

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let original = content;

    for (const [oldClass, newClass] of Object.entries(classMap)) {
        const regex = new RegExp(`(?<![a-zA-Z0-9_-])(${oldClass.split('/').join('\\\\/')})(?![a-zA-Z0-9_-])`, 'g');
        content = content.replace(regex, (match) => {
            return newClass;
        });
    }

    for (const newClass of Object.values(classMap)) {
        const darkPart = newClass.split(' ')[1];
        const doubleDark = new RegExp(`${darkPart.split('/').join('\\\\/')}\\s+${darkPart.split('/').join('\\\\/')}`, 'g');
        content = content.replace(doubleDark, darkPart);
    }

    if (content !== original) {
        fs.writeFileSync(file, content, 'utf8');
    }
});
console.log('Applied gray dark mode classes');
