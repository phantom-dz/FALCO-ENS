<?php

if (!defined('ABSPATH')) {
    exit;
}

require_once get_template_directory() . '/inc/cpt-tax.php';
require_once get_template_directory() . '/inc/admin.php';
require_once get_template_directory() . '/inc/ajax.php';

function cem_dz_setup(): void
{
    add_theme_support('title-tag');
    add_theme_support('post-thumbnails');
    add_theme_support('html5', ['search-form', 'comment-form', 'comment-list', 'gallery', 'caption']);
    register_nav_menus([
        'primary' => 'القائمة الرئيسية',
    ]);
}
add_action('after_setup_theme', 'cem_dz_setup');

function cem_dz_enqueue_assets(): void
{
    $theme_version = wp_get_theme()->get('Version');

    wp_enqueue_style('cem-dz-style', get_template_directory_uri() . '/assets/css/style.css', [], $theme_version);
    wp_enqueue_style('cem-dz-print', get_template_directory_uri() . '/assets/css/print.css', [], $theme_version, 'print');

    wp_enqueue_script('cem-dz-ui', get_template_directory_uri() . '/assets/js/ui.js', [], $theme_version, true);
    wp_enqueue_script('cem-dz-charts', get_template_directory_uri() . '/assets/js/charts.js', [], $theme_version, true);
    wp_enqueue_script('cem-dz-app', get_template_directory_uri() . '/assets/js/app.js', ['cem-dz-ui'], $theme_version, true);

    wp_localize_script('cem-dz-app', 'cemDz', [
        'ajaxUrl' => admin_url('admin-ajax.php'),
        'nonce' => wp_create_nonce('cem_dz_nonce'),
        'isLoggedIn' => is_user_logged_in(),
        'userId' => get_current_user_id(),
    ]);
}
add_action('wp_enqueue_scripts', 'cem_dz_enqueue_assets');

function cem_dz_get_meta_array(int $post_id, string $key): array
{
    $value = get_post_meta($post_id, $key, true);
    return is_array($value) ? $value : [];
}

function cem_dz_render_svg_icon(string $name): string
{
    $icons = [
        'star' => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="m12 2 3 6 6 .9-4.5 4.4 1.1 6.3L12 16.9 6.4 19.6l1.1-6.3L3 8.9 9 8z"/></svg>',
        'check' => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="m9.2 16.2-3.4-3.4 1.4-1.4 2 2 5.6-5.6 1.4 1.4z"/></svg>',
        'moon' => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M21 14.5A8.5 8.5 0 0 1 9.5 3a9 9 0 1 0 11.5 11.5Z"/></svg>',
        'sun' => '<svg viewBox="0 0 24 24" width="20" height="20" aria-hidden="true"><path fill="currentColor" d="M12 18a6 6 0 1 1 0-12 6 6 0 0 1 0 12Zm0-16h1v3h-1V2Zm0 17h1v3h-1v-3ZM2 11h3v1H2v-1Zm17 0h3v1h-3v-1ZM4.2 4.2l2.1 2.1-.7.7-2.1-2.1.7-.7Zm11.5 11.5 2.1 2.1-.7.7-2.1-2.1.7-.7ZM4.2 19.8l-.7-.7 2.1-2.1.7.7-2.1 2.1Zm11.5-11.5-.7-.7 2.1-2.1.7.7-2.1 2.1Z"/></svg>',
    ];

    return $icons[$name] ?? '';
}
