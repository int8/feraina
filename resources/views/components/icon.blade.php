@props(['name', 'class' => 'size-6'])
@php
$icons = [
    'globe' => '<circle cx="12" cy="12" r="9"/><path d="M3 12h18M12 3a14 14 0 0 1 0 18M12 3a14 14 0 0 0 0 18"/>',
    'megaphone' => '<path d="M3 10v4a1 1 0 0 0 1 1h3l6 4V5L7 9H4a1 1 0 0 0-1 1z"/><path d="M17 8a5 5 0 0 1 0 8"/>',
    'chat' => '<path d="M4 5h16v11H9l-5 4z"/>',
    'flag' => '<path d="M5 21V4"/><path d="M5 4h12l-2 4 2 4H5"/>',
    'card' => '<rect x="3" y="6" width="18" height="12" rx="2"/><path d="M3 10h18"/>',
    'invoice' => '<path d="M6 3h9l4 4v14H6z"/><path d="M15 3v4h4M9 12h6M9 16h6"/>',
    'shield' => '<path d="M12 3l8 3v6c0 5-3.5 8-8 9-4.5-1-8-4-8-9V6z"/><path d="m9 12 2 2 4-4"/>',
    'check' => '<path d="m5 12 5 5L20 7"/>',
    'wallet' => '<rect x="3" y="7" width="18" height="12" rx="2"/><path d="M16 13h2M3 10V6a2 2 0 0 1 2-2h11"/>',
    'undo' => '<path d="M9 14 4 9l5-5"/><path d="M4 9h11a5 5 0 0 1 0 10h-3"/>',
    'hand' => '<path d="M8 13V5a1.5 1.5 0 0 1 3 0v6M11 10V4a1.5 1.5 0 0 1 3 0v7M14 11V6a1.5 1.5 0 0 1 3 0v8a6 6 0 0 1-6 6h-1a6 6 0 0 1-5-3l-2.5-4a1.5 1.5 0 0 1 2.6-1.5L8 14"/>',
    'power' => '<path d="M12 3v9"/><path d="M6.5 7a8 8 0 1 0 11 0"/>',
    'menu' => '<path d="M4 7h16M4 12h16M4 17h16"/>',
    'close' => '<path d="M6 6l12 12M18 6 6 18"/>',
    'linkedin' => '<rect x="3" y="3" width="18" height="18" rx="2"/><path d="M8 10v7M8 7v.5M12 17v-4a2 2 0 0 1 4 0v4M12 10v7"/>',
    'chevron' => '<path d="m6 9 6 6 6-6"/>',
];
@endphp
<svg {{ $attributes->merge(['class' => $class]) }} viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true" focusable="false">{!! $icons[$name] ?? '' !!}</svg>
