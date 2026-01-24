<?php

if (!defined('ABSPATH')) {
    exit;
}

function cem_dz_get_user_state(): void
{
    check_ajax_referer('cem_dz_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'غير مسجل'], 401);
    }

    $user_id = get_current_user_id();
    $keys = ['completedLessons', 'completedExercises', 'viewedExams', 'favorites', 'recent', 'activityLog', 'themePreference'];
    $data = [];

    foreach ($keys as $key) {
        $data[$key] = get_user_meta($user_id, 'cem_dz_' . $key, true);
    }

    wp_send_json_success($data);
}
add_action('wp_ajax_cem_dz_get_user_state', 'cem_dz_get_user_state');

function cem_dz_update_user_state(): void
{
    check_ajax_referer('cem_dz_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'غير مسجل'], 401);
    }

    $payload = json_decode(wp_unslash($_POST['payload'] ?? ''), true);
    if (!is_array($payload)) {
        wp_send_json_error(['message' => 'بيانات غير صالحة'], 400);
    }

    $user_id = get_current_user_id();
    foreach ($payload as $key => $value) {
        update_user_meta($user_id, 'cem_dz_' . sanitize_key($key), $value);
    }

    wp_send_json_success(['message' => 'تم التحديث']);
}
add_action('wp_ajax_cem_dz_update_user_state', 'cem_dz_update_user_state');

function cem_dz_sync_from_local(): void
{
    check_ajax_referer('cem_dz_nonce', 'nonce');

    if (!is_user_logged_in()) {
        wp_send_json_error(['message' => 'غير مسجل'], 401);
    }

    $payload = json_decode(wp_unslash($_POST['payload'] ?? ''), true);
    if (!is_array($payload)) {
        wp_send_json_error(['message' => 'بيانات غير صالحة'], 400);
    }

    $user_id = get_current_user_id();
    foreach ($payload as $key => $value) {
        $meta_key = 'cem_dz_' . sanitize_key($key);
        $current = get_user_meta($user_id, $meta_key, true);
        if (is_array($current) && is_array($value)) {
            $merged = array_unique(array_merge($current, $value), SORT_REGULAR);
            update_user_meta($user_id, $meta_key, array_values($merged));
        } else {
            update_user_meta($user_id, $meta_key, $value);
        }
    }

    wp_send_json_success(['message' => 'تمت المزامنة']);
}
add_action('wp_ajax_cem_dz_sync_from_local', 'cem_dz_sync_from_local');
