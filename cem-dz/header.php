<?php
if (!defined('ABSPATH')) {
    exit;
}
?><!doctype html>
<html <?php language_attributes(); ?> dir="rtl">
<head>
    <meta charset="<?php bloginfo('charset'); ?>">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <?php wp_head(); ?>
</head>
<body <?php body_class(); ?>>
<a class="screen-reader-text" href="#main">تخطي إلى المحتوى</a>
<header class="cem-header" role="banner">
    <div class="cem-brand">
        <a href="<?php echo esc_url(home_url('/')); ?>">CEM DZ</a>
    </div>
    <nav class="cem-nav" aria-label="التنقل الرئيسي">
        <?php
        wp_nav_menu([
            'theme_location' => 'primary',
            'container' => false,
            'fallback_cb' => '__return_false',
            'items_wrap' => '%3$s',
        ]);
        ?>
        <button class="cem-button" type="button" data-theme-toggle aria-label="تبديل الوضع الليلي">
            <span aria-hidden="true"><?php echo cem_dz_render_svg_icon('moon'); ?></span>
        </button>
    </nav>
</header>
<main id="main" class="cem-container">
