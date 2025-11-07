<?php
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

function ksc404_get_color_themes() {
    return [
        'default-blue' => [
            'name' => 'Default Blue',
            'description' => '白背景にWordPress標準の青を基調とした、標準的で見やすい配色。',
            'colors' => [
                '--ksc404-bg-color' => '#f0f4f8',
                '--ksc404-container-bg-color' => '#FFFFFF',
                '--ksc404-text-color' => '#333333',
                '--ksc404-link-color' => '#0073aa',
                '--ksc404-link-hover-color' => '#005a87',
                '--ksc404-button-bg-color' => '#0073aa',
                '--ksc404-button-hover-bg-color' => '#005a87',
                '--ksc404-button-text-color' => '#FFFFFF',
                '--ksc404-error-color' => '#D9534F',
                '--ksc404-accent-bg-color' => '#eef5fa',
                '--ksc404-accent-border-color' => '#d0e4f2',
                '--ksc404-accent-text-color' => '#31708f',
                '--ksc404-section-border-color' => '#e0e0e0',
                '--ksc404-image-placeholder-bg' => '#f0f0f0',
                '--ksc404-footer-bg-color' => '#f0f4f8',
                '--ksc404-footer-text-color' => '#555555',
                '--ksc404-search-input-border-color' => '#ccd0d4',
            ]
        ],
        'midnight-calm' => [
            'name' => 'Midnight Calm',
            'description' => 'ダークモード。黒に近い背景に明るいテキストと青系のアクセントカラー。',
            'colors' => [
                '--ksc404-bg-color' => '#0d1117',
                '--ksc404-container-bg-color' => '#161b22',
                '--ksc404-text-color' => '#c9d1d9',
                '--ksc404-link-color' => '#58a6ff',
                '--ksc404-link-hover-color' => '#79c0ff',
                '--ksc404-button-bg-color' => '#238636',
                '--ksc404-button-hover-bg-color' => '#2ea043',
                '--ksc404-button-text-color' => '#FFFFFF',
                '--ksc404-error-color' => '#f85149',
                '--ksc404-accent-bg-color' => '#21262d',
                '--ksc404-accent-border-color' => '#30363d',
                '--ksc404-accent-text-color' => '#8b949e',
                '--ksc404-section-border-color' => '#30363d',
                '--ksc404-image-placeholder-bg' => '#21262d',
                '--ksc404-footer-bg-color' => '#0d1117',
                '--ksc404-footer-text-color' => '#8b949e',
                '--ksc404-search-input-border-color' => '#30363d',
            ]
        ],
        'crisp-contrast' => [
            'name' => 'Crisp Contrast',
            'description' => 'ハイコントラスト。白背景に黒文字、はっきりした青と赤を使用し、視認性を高めた配色。',
            'colors' => [
                '--ksc404-bg-color' => '#e0e0e0',
                '--ksc404-container-bg-color' => '#FFFFFF',
                '--ksc404-text-color' => '#000000',
                '--ksc404-link-color' => '#0000EE',
                '--ksc404-link-hover-color' => '#00008B',
                '--ksc404-button-bg-color' => '#0000EE',
                '--ksc404-button-hover-bg-color' => '#00008B',
                '--ksc404-button-text-color' => '#FFFFFF',
                '--ksc404-error-color' => '#D8000C',
                '--ksc404-accent-bg-color' => '#FEF9E7',
                '--ksc404-accent-border-color' => '#F7DC6F',
                '--ksc404-accent-text-color' => '#000000',
                '--ksc404-section-border-color' => '#a0a0a0',
                '--ksc404-image-placeholder-bg' => '#cccccc',
                '--ksc404-footer-bg-color' => '#e0e0e0',
                '--ksc404-footer-text-color' => '#000000',
                '--ksc404-search-input-border-color' => '#808080',
            ]
        ],
        'forest-bath' => [
            'name' => 'Forest Bath',
            'description' => 'ナチュラル系。オフホワイトの背景に、緑や茶色を基調とした目に優しい落ち着いた配色。',
            'colors' => [
                '--ksc404-bg-color' => '#f5f5f0',
                '--ksc404-container-bg-color' => '#FFFFFF',
                '--ksc404-text-color' => '#384238',
                '--ksc404-link-color' => '#2E7D32',
                '--ksc404-link-hover-color' => '#1B5E20',
                '--ksc404-button-bg-color' => '#2E7D32',
                '--ksc404-button-hover-bg-color' => '#1B5E20',
                '--ksc404-button-text-color' => '#FFFFFF',
                '--ksc404-error-color' => '#BF5700',
                '--ksc404-accent-bg-color' => '#E8F5E9',
                '--ksc404-accent-border-color' => '#C8E6C9',
                '--ksc404-accent-text-color' => '#1B5E20',
                '--ksc404-section-border-color' => '#d0d0c8',
                '--ksc404-image-placeholder-bg' => '#e0e0d8',
                '--ksc404-footer-bg-color' => '#f5f5f0',
                '--ksc404-footer-text-color' => '#4a544a',
                '--ksc404-search-input-border-color' => '#c0c0b8',
            ]
        ],
        'sunset-glow' => [
            'name' => 'Sunset Glow (Citrus)',
            'description' => '柑橘系。クリーム色の背景に、オレンジ、薄オレンジを組み合わせた暖かみのある配色。',
            'colors' => [
                '--ksc404-bg-color' => '#FFF9E6', // Lightest cream
                '--ksc404-container-bg-color' => '#FFFFFF',
                '--ksc404-text-color' => '#6D4C41', // Brown
                '--ksc404-link-color' => '#FF8F00', // Amber Darken-2
                '--ksc404-link-hover-color' => '#FF6F00', // Amber Darken-4
                '--ksc404-button-bg-color' => '#FF9800', // Orange
                '--ksc404-button-hover-bg-color' => '#FB8C00', // Orange Darken-1
                '--ksc404-button-text-color' => '#FFFFFF',
                '--ksc404-error-color' => '#E65100', // Orange Darken-4
                '--ksc404-accent-bg-color' => '#FFFDE7', // Lightest Yellow
                '--ksc404-accent-border-color' => '#FFF59D', // Pale Yellow
                '--ksc404-accent-text-color' => '#A1887F', // Brown Lighten-1
                '--ksc404-section-border-color' => '#FFE0B2', // Light Orange
                '--ksc404-image-placeholder-bg' => '#FFF8E1', // Light Cream
                '--ksc404-footer-bg-color' => '#FFF9E6',
                '--ksc404-footer-text-color' => '#6D4C41',
                '--ksc404-search-input-border-color' => '#FFCC80', // Lighter Orange
            ]
        ],
    ];
}

function ksc404_get_default_settings() {
    return [
        'latest_posts_count' => 5,
        'related_posts_count' => 3,
        'latest_post_types' => ['post'],
        'color_theme' => 'default-blue',
        'redirect_found_action' => 'show_404_canonical',
        'redirect_not_found_action' => 'show_default_404',
        'redirect_not_found_custom_url' => '',
    ];
}
?>