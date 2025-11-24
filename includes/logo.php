<?php
// Prevent direct access to this file
if (!defined('BASEPATH')) {
    exit('No direct script access allowed');
}
?>

<!-- Logo Component -->
<div class="brand me-4 d-flex align-items-center">
    <div class="logo-container me-2">
        <div class="logo-circle">
            <span class="logo-text">TS</span>
        </div>
    </div>
    <h1 class="header-title mb-0 d-flex align-items-center">
        Travel System
    </h1>
</div>

<style>
    /* Logo Styles */
    .logo-container {
        display: flex;
        align-items: center;
    }

    .logo-circle {
        width: 40px;
        height: 40px;
        background: linear-gradient(135deg, #00c6fb 0%, #005bea 100%);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.2);
        position: relative;
        overflow: hidden;
        transition: all 0.3s ease;
    }

    .logo-circle::before {
        content: '';
        position: absolute;
        top: -50%;
        left: -50%;
        width: 200%;
        height: 200%;
        background: linear-gradient(
            45deg,
            transparent 0%,
            rgba(255, 255, 255, 0.1) 50%,
            transparent 100%
        );
        transform: rotate(45deg);
        animation: shine 3s infinite;
    }

    .logo-text {
        color: white;
        font-size: 1.5rem;
        font-weight: 700;
        letter-spacing: 1px;
        text-shadow: 1px 1px 2px rgba(0, 0, 0, 0.2);
        font-family: 'Segoe UI', sans-serif;
    }

    .logo-circle:hover {
        transform: scale(1.05) rotate(5deg);
        box-shadow: 0 6px 12px rgba(0, 0, 0, 0.3);
    }

    @keyframes shine {
        0% {
            transform: translateX(-100%) rotate(45deg);
        }
        100% {
            transform: translateX(100%) rotate(45deg);
        }
    }

    /* Responsive adjustments */
    @media (max-width: 576px) {
        .logo-circle {
            width: 35px;
            height: 35px;
        }
        
        .logo-text {
            font-size: 1.2rem;
        }
    }
</style> 