<?php
// $variant = 'clair' pour un logo blanc (fond sombre, ex: sidebar admin).
$variant ??= 'sombre';
$fill = $variant === 'clair' ? '#ffffff' : '#2A2F6D';
?>
<a href="<?= e(url('/')) ?>" class="brand">
    <svg class="brand-mark" width="30" height="30" viewBox="0 0 64 64" fill="none" xmlns="http://www.w3.org/2000/svg">
        <rect width="64" height="64" rx="16" fill="<?= $fill ?>"/>
        <path d="M22 24c0-5 5-8 10-8s9 3 9 7c0 4-3 5.5-8 7-6 1.7-9 3.4-9 7.5 0 4.3 4 7.5 10 7.5 4.4 0 8-1.6 9.6-4.3" stroke="#DD9636" stroke-width="5" stroke-linecap="round" fill="none"/>
    </svg>
    <span>Skolea</span>
</a>
