<?php
// Function to create SVG icon
function createSVGIcon($size, $filename) {
    // Calculate center and radius once
    $center = $size/2;
    $fontSize = $size/2;

    // Define SVG elements once
    $defs = '
    <defs>
        <linearGradient id="grad" x1="0%" y1="0%" x2="100%" y2="100%">
            <stop offset="0%" style="stop-color:#00c6fb;stop-opacity:1" />
            <stop offset="100%" style="stop-color:#005bea;stop-opacity:1" />
        </linearGradient>
        <linearGradient id="highlight" x1="0%" y1="0%" x2="0%" y2="100%">
            <stop offset="0%" style="stop-color:rgba(255,255,255,0.7);stop-opacity:1" />
            <stop offset="100%" style="stop-color:rgba(255,255,255,0);stop-opacity:1" />
        </linearGradient>
        <filter id="shadow">
            <feGaussianBlur in="SourceAlpha" stdDeviation="2"/>
            <feOffset dx="0" dy="4" result="offsetblur"/>
            <feComponentTransfer>
                <feFuncA type="linear" slope="0.4"/>
            </feComponentTransfer>
            <feMerge>
                <feMergeNode/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
        <filter id="inner-shadow">
            <feOffset dx="0" dy="2"/>
            <feGaussianBlur stdDeviation="1"/>
            <feComposite operator="out" in="SourceGraphic"/>
            <feComponentTransfer>
                <feFuncA type="linear" slope="0.3"/>
            </feComponentTransfer>
            <feMerge>
                <feMergeNode/>
                <feMergeNode in="SourceGraphic"/>
            </feMerge>
        </filter>
    </defs>';

    // Create circle and text elements
    $circle = '<circle cx="'.$center.'" cy="'.$center.'" r="'.$center.'" fill="url(#grad)" filter="url(#shadow)"/>';
    $highlight = '<circle cx="'.$center.'" cy="'.$center.'" r="'.$center.'" fill="url(#highlight)" opacity="0.5"/>';
    $text = '<text x="50%" y="50%" text-anchor="middle" dy=".3em" 
        fill="white" font-family="Arial" font-weight="bold" 
        font-size="'.$fontSize.'"
        style="text-shadow: 0 2px 4px rgba(0,0,0,0.3), 
                          0 4px 8px rgba(0,0,0,0.2), 
                          0 -1px 2px rgba(255,255,255,0.5);"
        filter="url(#inner-shadow)">TS</text>';

    // Combine all elements
    $svg = '<?xml version="1.0" encoding="UTF-8" standalone="no"?>
<svg width="'.$size.'" height="'.$size.'" viewBox="0 0 '.$size.' '.$size.'" xmlns="http://www.w3.org/2000/svg">
    '.$defs.'
    '.$circle.'
    '.$highlight.'
    '.$text.'
</svg>';

    file_put_contents($filename, $svg);
}

// Create icons directory if it doesn't exist
$iconDir = __DIR__;
if (!file_exists($iconDir)) {
    mkdir($iconDir, 0777, true);
}

// Define icon sizes and their purposes
$iconSizes = [
    'favicon.svg' => 32,
    'apple-touch-icon.svg' => 180,
    'icon-16x16.svg' => 16,
    'icon-32x32.svg' => 32,
    'icon-48x48.svg' => 48,
    'icon-96x96.svg' => 96,
    'icon-144x144.svg' => 144,
    'icon-192x192.svg' => 192,
    'icon-512x512.svg' => 512
];

// Generate all icons
foreach ($iconSizes as $filename => $size) {
    createSVGIcon($size, $iconDir . '/' . $filename);
}

// Create site.webmanifest
$manifest = [
    'name' => 'Travel System',
    'short_name' => 'TS',
    'icons' => [
        [
            'src' => 'icon-192x192.svg',
            'sizes' => '192x192',
            'type' => 'image/svg+xml'
        ],
        [
            'src' => 'icon-512x512.svg',
            'sizes' => '512x512',
            'type' => 'image/svg+xml'
        ]
    ],
    'theme_color' => '#005bea',
    'background_color' => '#ffffff',
    'display' => 'standalone',
    'start_url' => '/',
    'orientation' => 'portrait'
];

file_put_contents($iconDir . '/site.webmanifest', json_encode($manifest, JSON_PRETTY_PRINT)); 