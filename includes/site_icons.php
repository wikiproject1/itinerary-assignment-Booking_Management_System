<?php
// Prevent direct access to this file
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}

// Generate icons if they don't exist
require_once 'assets/icons/generate_icons.php';
?>

<!-- Site Icons and Favicon -->
<link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
<link rel="icon" type="image/svg+xml" sizes="16x16" href="assets/icons/icon-16x16.svg">
<link rel="icon" type="image/svg+xml" sizes="32x32" href="assets/icons/icon-32x32.svg">
<link rel="icon" type="image/svg+xml" sizes="48x48" href="assets/icons/icon-48x48.svg">
<link rel="apple-touch-icon" type="image/svg+xml" href="assets/icons/apple-touch-icon.svg">

<!-- PWA Support -->
<link rel="manifest" href="assets/icons/site.webmanifest">
<meta name="theme-color" content="#005bea">
<meta name="msapplication-TileColor" content="#005bea">
<meta name="msapplication-TileImage" content="assets/icons/icon-144x144.svg">

<!-- Fallback for older browsers -->
<link rel="icon" type="image/svg+xml" href="assets/icons/favicon.svg">
<link rel="shortcut icon" type="image/svg+xml" href="assets/icons/favicon.svg">

<!-- iOS specific -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<meta name="apple-mobile-web-app-title" content="Travel System">

<!-- Windows specific -->
<meta name="application-name" content="Travel System">
<meta name="msapplication-config" content="none"/>

<style>
    /* Preload icons to prevent flicker */
    body::after {
        position: absolute;
        width: 0;
        height: 0;
        overflow: hidden;
        z-index: -1;
        content: url(assets/icons/favicon.svg)
                 url(assets/icons/icon-16x16.svg)
                 url(assets/icons/icon-32x32.svg)
                 url(assets/icons/apple-touch-icon.svg);
    }
</style> 