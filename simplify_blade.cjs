const fs = require('fs');
const path = require('path');

function walk(dir) {
    let results = [];
    const list = fs.readdirSync(dir);
    list.forEach(function(file) {
        file = dir + '/' + file;
        const stat = fs.statSync(file);
        if (stat && stat.isDirectory()) { 
            results = results.concat(walk(file));
        } else { 
            if (file.endsWith('.blade.php')) results.push(file);
        }
    });
    return results;
}

const viewsDir = 'c:/Users/shaur/Downloads/Laravel Project 1 - Copy/resources/views';
const files = walk(viewsDir);

files.forEach(file => {
    let content = fs.readFileSync(file, 'utf8');
    let originalContent = content;

    // Handle layouts/app.blade.php
    if (file.endsWith('layouts/app.blade.php')) {
        content = content.replace(/@isset\(\$header\)/g, "@hasSection('header')");
        content = content.replace(/\{\{\s*\$header\s*\}\}/g, "@yield('header')");
        content = content.replace(/@endisset/g, "@endif");
        content = content.replace(/\{\{\s*\$slot\s*\}\}/g, "@yield('content')");
    }
    
    // Handle layouts/guest.blade.php
    if (file.endsWith('layouts/guest.blade.php')) {
        content = content.replace(/\{\{\s*\$slot\s*\}\}/g, "@yield('content')");
    }

    // Replace <x-app-layout> and <x-guest-layout> with @extends
    if (content.includes('<x-app-layout>') || content.includes('<x-guest-layout>')) {
        let layoutName = content.includes('<x-app-layout>') ? 'app' : 'guest';
        let layoutTag = content.includes('<x-app-layout>') ? 'x-app-layout' : 'x-guest-layout';

        if (content.includes('<x-slot name="header">')) {
            content = content.replace(new RegExp('<' + layoutTag + '>\\s*<x-slot name="header">', 'g'), "@extends('layouts." + layoutName + "')\n\n@section('header')");
            content = content.replace(/<\/x-slot>/g, "@endsection\n\n@section('content')");
        } else {
            content = content.replace(new RegExp('<' + layoutTag + '>', 'g'), "@extends('layouts." + layoutName + "')\n\n@section('content')");
        }
        content = content.replace(new RegExp('<\/' + layoutTag + '>', 'g'), "@endsection");
    }

    if (content !== originalContent) {
        fs.writeFileSync(file, content, 'utf8');
        console.log('Processed: ' + file);
    }
});
