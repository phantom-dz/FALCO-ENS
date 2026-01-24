<?php

if (!defined('ABSPATH')) {
    exit;
}

function cem_dz_register_cpts(): void
{
    register_post_type('cem_lesson', [
        'labels' => [
            'name' => 'الدروس',
            'singular_name' => 'درس',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-welcome-learn-more',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);

    register_post_type('cem_exercise', [
        'labels' => [
            'name' => 'التمارين',
            'singular_name' => 'تمرين',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-edit',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);

    register_post_type('cem_exam', [
        'labels' => [
            'name' => 'الامتحانات',
            'singular_name' => 'امتحان',
        ],
        'public' => true,
        'has_archive' => true,
        'menu_icon' => 'dashicons-welcome-write-blog',
        'supports' => ['title', 'editor', 'thumbnail', 'excerpt'],
        'show_in_rest' => true,
    ]);
}
add_action('init', 'cem_dz_register_cpts');

function cem_dz_register_taxonomies(): void
{
    register_taxonomy('cem_year', ['cem_lesson', 'cem_exercise', 'cem_exam'], [
        'labels' => [
            'name' => 'السنة',
            'singular_name' => 'سنة',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    ]);

    register_taxonomy('cem_subject', ['cem_lesson', 'cem_exercise', 'cem_exam'], [
        'labels' => [
            'name' => 'المادة',
            'singular_name' => 'مادة',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    ]);

    register_taxonomy('cem_unit', ['cem_lesson'], [
        'labels' => [
            'name' => 'الوحدة/المحور',
            'singular_name' => 'وحدة',
        ],
        'public' => true,
        'hierarchical' => true,
        'show_in_rest' => true,
    ]);
}
add_action('init', 'cem_dz_register_taxonomies');

function cem_dz_seed_tax_terms(): void
{
    $years = [
        'السنة الأولى متوسط',
        'السنة الثانية متوسط',
        'السنة الثالثة متوسط',
        'السنة الرابعة متوسط',
    ];

    $subjects = [
        'اللغة العربية',
        'اللغة الفرنسية',
        'اللغة الإنجليزية',
        'الرياضيات',
        'العلوم الطبيعية',
        'الفيزياء والتكنولوجيا',
        'التاريخ والجغرافيا',
        'التربية الإسلامية',
        'التربية المدنية',
        'الإعلام الآلي',
        'اللغة الأمازيغية',
    ];

    foreach ($years as $year) {
        if (!term_exists($year, 'cem_year')) {
            wp_insert_term($year, 'cem_year');
        }
    }

    foreach ($subjects as $subject) {
        if (!term_exists($subject, 'cem_subject')) {
            wp_insert_term($subject, 'cem_subject');
        }
    }
}
add_action('init', 'cem_dz_seed_tax_terms');
