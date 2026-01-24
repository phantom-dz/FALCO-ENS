<?php

if (!defined('ABSPATH')) {
    exit;
}

function cem_dz_admin_menu(): void
{
    add_menu_page(
        'CEM DZ',
        'CEM DZ',
        'manage_options',
        'cem-dz-admin',
        'cem_dz_render_admin_page',
        'dashicons-welcome-learn-more',
        20
    );
}
add_action('admin_menu', 'cem_dz_admin_menu');

function cem_dz_render_admin_page(): void
{
    if (!current_user_can('manage_options')) {
        return;
    }

    ?>
    <div class="wrap">
        <h1>لوحة إدارة CEM DZ</h1>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('cem_dz_seed', 'cem_dz_seed_nonce'); ?>
            <input type="hidden" name="action" value="cem_dz_seed_content">
            <p>تثبيت محتوى تجريبي شامل لكل السنوات والمواد.</p>
            <p><button class="button button-primary" type="submit">تثبيت المحتوى التجريبي</button></p>
        </form>

        <hr>

        <h2>تصدير المحتوى</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>">
            <?php wp_nonce_field('cem_dz_export', 'cem_dz_export_nonce'); ?>
            <input type="hidden" name="action" value="cem_dz_export_content">
            <p><button class="button" type="submit">تصدير JSON</button></p>
        </form>

        <h2>استيراد المحتوى</h2>
        <form method="post" action="<?php echo esc_url(admin_url('admin-post.php')); ?>" enctype="multipart/form-data">
            <?php wp_nonce_field('cem_dz_import', 'cem_dz_import_nonce'); ?>
            <input type="hidden" name="action" value="cem_dz_import_content">
            <p><input type="file" name="cem_dz_import_file" accept="application/json" required></p>
            <p>
                <label><input type="radio" name="cem_dz_import_mode" value="merge" checked> دمج</label>
                <label><input type="radio" name="cem_dz_import_mode" value="replace"> استبدال</label>
            </p>
            <p><button class="button button-primary" type="submit">استيراد</button></p>
        </form>
    </div>
    <?php
}

function cem_dz_seed_content(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('غير مسموح');
    }

    check_admin_referer('cem_dz_seed', 'cem_dz_seed_nonce');

    $years = get_terms(['taxonomy' => 'cem_year', 'hide_empty' => false]);
    $subjects = get_terms(['taxonomy' => 'cem_subject', 'hide_empty' => false]);

    foreach ($years as $year) {
        foreach ($subjects as $subject) {
            $unit_ids = [];
            for ($i = 1; $i <= 3; $i++) {
                $unit_name = $subject->name . ' - الوحدة ' . $i;
                $unit_term = term_exists($unit_name, 'cem_unit');
                if (!$unit_term) {
                    $unit_term = wp_insert_term($unit_name, 'cem_unit');
                }
                if (is_array($unit_term)) {
                    $unit_ids[] = (int) $unit_term['term_id'];
                }
            }

            $lesson_ids = [];
            $lesson_count = 0;
            foreach ($unit_ids as $unit_id) {
                for ($l = 1; $l <= 2; $l++) {
                    $lesson_count++;
                    $lesson_id = wp_insert_post([
                        'post_type' => 'cem_lesson',
                        'post_title' => 'درس ' . $lesson_count . ' - ' . $subject->name,
                        'post_status' => 'publish',
                        'post_content' => 'هذا محتوى تمهيدي للدرس مع شرح مبسط ومقدمة تعليمية.',
                    ]);
                    if ($lesson_id) {
                        wp_set_object_terms($lesson_id, [$year->term_id], 'cem_year');
                        wp_set_object_terms($lesson_id, [$subject->term_id], 'cem_subject');
                        wp_set_object_terms($lesson_id, [$unit_id], 'cem_unit');
                        update_post_meta($lesson_id, 'cem_intro', 'مقدمة قصيرة تساعدك على فهم الدرس.');
                        update_post_meta($lesson_id, 'cem_bullets', ['نقطة أساسية 1', 'نقطة أساسية 2']);
                        update_post_meta($lesson_id, 'cem_examples', ['مثال محلول 1', 'مثال محلول 2']);
                        $lesson_ids[] = $lesson_id;
                    }
                }
            }

            foreach ($lesson_ids as $lesson_id) {
                for ($e = 1; $e <= 2; $e++) {
                    $exercise_id = wp_insert_post([
                        'post_type' => 'cem_exercise',
                        'post_title' => 'تمرين ' . $e . ' للدرس ' . get_the_title($lesson_id),
                        'post_status' => 'publish',
                        'post_content' => 'نص التمرين مع المطلوب بالتفصيل.',
                    ]);
                    if ($exercise_id) {
                        wp_set_object_terms($exercise_id, [$year->term_id], 'cem_year');
                        wp_set_object_terms($exercise_id, [$subject->term_id], 'cem_subject');
                        update_post_meta($exercise_id, 'cem_linked_lesson_id', $lesson_id);
                        update_post_meta($exercise_id, 'cem_hint', 'تلميح يساعد على حل التمرين.');
                        update_post_meta($exercise_id, 'cem_solution_steps', ['خطوة 1', 'خطوة 2']);
                    }
                }
            }

            $exam_titles = ['اختبار الفصل الأول', 'اختبار الفصل الثاني', 'اختبار الفصل الثالث'];
            foreach ($exam_titles as $exam_title) {
                $exam_id = wp_insert_post([
                    'post_type' => 'cem_exam',
                    'post_title' => $exam_title . ' - ' . $subject->name,
                    'post_status' => 'publish',
                    'post_content' => 'تعليمات الامتحان والأسئلة الرئيسية.',
                ]);
                if ($exam_id) {
                    wp_set_object_terms($exam_id, [$year->term_id], 'cem_year');
                    wp_set_object_terms($exam_id, [$subject->term_id], 'cem_subject');
                    update_post_meta($exam_id, 'cem_duration', '01:30');
                    update_post_meta($exam_id, 'cem_grading', '10 نقاط للأجزاء النظرية، 10 نقاط للتطبيق.');
                    update_post_meta($exam_id, 'cem_sections', ['قسم 1: أسئلة عامة', 'قسم 2: مسائل تطبيقية']);
                    update_post_meta($exam_id, 'cem_answer_key', ['إجابة 1', 'إجابة 2']);
                }
            }

            if ($year->name === 'السنة الرابعة متوسط' && in_array($subject->name, ['الرياضيات', 'الفيزياء والتكنولوجيا', 'العلوم الطبيعية', 'اللغة العربية'], true)) {
                wp_insert_post([
                    'post_type' => 'cem_exam',
                    'post_title' => 'نموذج BEM تجريبي - ' . $subject->name,
                    'post_status' => 'publish',
                    'post_content' => 'نموذج شامل للتدريب على الامتحان النهائي.',
                ]);
            }
        }
    }

    wp_safe_redirect(admin_url('admin.php?page=cem-dz-admin&seeded=1'));
    exit;
}
add_action('admin_post_cem_dz_seed_content', 'cem_dz_seed_content');

function cem_dz_export_content(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('غير مسموح');
    }

    check_admin_referer('cem_dz_export', 'cem_dz_export_nonce');

    $posts = get_posts([
        'post_type' => ['cem_lesson', 'cem_exercise', 'cem_exam'],
        'numberposts' => -1,
        'post_status' => 'publish',
    ]);

    $data = [];
    foreach ($posts as $post) {
        $data[] = [
            'post_type' => $post->post_type,
            'title' => $post->post_title,
            'content' => $post->post_content,
            'meta' => get_post_meta($post->ID),
            'tax' => [
                'cem_year' => wp_get_object_terms($post->ID, 'cem_year', ['fields' => 'names']),
                'cem_subject' => wp_get_object_terms($post->ID, 'cem_subject', ['fields' => 'names']),
                'cem_unit' => wp_get_object_terms($post->ID, 'cem_unit', ['fields' => 'names']),
            ],
        ];
    }

    wp_send_json($data);
}
add_action('admin_post_cem_dz_export_content', 'cem_dz_export_content');

function cem_dz_import_content(): void
{
    if (!current_user_can('manage_options')) {
        wp_die('غير مسموح');
    }

    check_admin_referer('cem_dz_import', 'cem_dz_import_nonce');

    if (empty($_FILES['cem_dz_import_file']['tmp_name'])) {
        wp_safe_redirect(admin_url('admin.php?page=cem-dz-admin&import=missing'));
        exit;
    }

    $file = wp_unslash(file_get_contents($_FILES['cem_dz_import_file']['tmp_name']));
    $payload = json_decode($file, true);

    if (!is_array($payload)) {
        wp_safe_redirect(admin_url('admin.php?page=cem-dz-admin&import=invalid'));
        exit;
    }

    $mode = isset($_POST['cem_dz_import_mode']) && $_POST['cem_dz_import_mode'] === 'replace' ? 'replace' : 'merge';

    if ($mode === 'replace') {
        $existing = get_posts([
            'post_type' => ['cem_lesson', 'cem_exercise', 'cem_exam'],
            'numberposts' => -1,
        ]);
        foreach ($existing as $post) {
            wp_delete_post($post->ID, true);
        }
    }

    foreach ($payload as $item) {
        if (empty($item['post_type']) || empty($item['title'])) {
            continue;
        }
        $post_id = wp_insert_post([
            'post_type' => sanitize_text_field($item['post_type']),
            'post_title' => sanitize_text_field($item['title']),
            'post_content' => wp_kses_post($item['content'] ?? ''),
            'post_status' => 'publish',
        ]);
        if (!$post_id) {
            continue;
        }
        if (!empty($item['meta'])) {
            foreach ($item['meta'] as $meta_key => $meta_value) {
                update_post_meta($post_id, sanitize_key($meta_key), $meta_value[0] ?? $meta_value);
            }
        }
        if (!empty($item['tax'])) {
            foreach ($item['tax'] as $tax => $terms) {
                if (taxonomy_exists($tax) && is_array($terms)) {
                    wp_set_object_terms($post_id, array_map('sanitize_text_field', $terms), $tax);
                }
            }
        }
    }

    wp_safe_redirect(admin_url('admin.php?page=cem-dz-admin&import=done'));
    exit;
}
add_action('admin_post_cem_dz_import_content', 'cem_dz_import_content');

function cem_dz_register_metaboxes(): void
{
    add_meta_box('cem_lesson_meta', 'بيانات الدرس', 'cem_dz_render_lesson_meta', 'cem_lesson', 'normal', 'default');
    add_meta_box('cem_exercise_meta', 'بيانات التمرين', 'cem_dz_render_exercise_meta', 'cem_exercise', 'normal', 'default');
    add_meta_box('cem_exam_meta', 'بيانات الامتحان', 'cem_dz_render_exam_meta', 'cem_exam', 'normal', 'default');
}
add_action('add_meta_boxes', 'cem_dz_register_metaboxes');

function cem_dz_render_lesson_meta(
    WP_Post $post
): void {
    wp_nonce_field('cem_dz_save_lesson', 'cem_dz_lesson_nonce');
    $intro = get_post_meta($post->ID, 'cem_intro', true);
    $bullets = get_post_meta($post->ID, 'cem_bullets', true);
    $examples = get_post_meta($post->ID, 'cem_examples', true);
    $mcq = get_post_meta($post->ID, 'cem_mcq', true);
    $linked_exercises = get_posts([
        'post_type' => 'cem_exercise',
        'numberposts' => -1,
        'meta_query' => [
            [
                'key' => 'cem_linked_lesson_id',
                'value' => $post->ID,
                'compare' => '=',
            ],
        ],
    ]);
    ?>
    <p><label>مقدمة</label><br><textarea name="cem_intro" rows="3" style="width:100%;"><?php echo esc_textarea($intro); ?></textarea></p>
    <p><label>النقاط الأساسية (سطر لكل نقطة)</label><br><textarea name="cem_bullets" rows="3" style="width:100%;"><?php echo esc_textarea(is_array($bullets) ? implode("\n", $bullets) : ''); ?></textarea></p>
    <p><label>أمثلة محلولة (سطر لكل مثال)</label><br><textarea name="cem_examples" rows="3" style="width:100%;"><?php echo esc_textarea(is_array($examples) ? implode("\n", $examples) : ''); ?></textarea></p>
    <p><label>اختبار سريع (JSON)</label><br><textarea name="cem_mcq" rows="5" style="width:100%;"><?php echo esc_textarea(is_string($mcq) ? $mcq : ''); ?></textarea></p>
    <h4>التمارين المرتبطة</h4>
    <?php if ($linked_exercises) : ?>
        <p>حدد التمارين ثم اختر الإجراء المناسب:</p>
        <?php foreach ($linked_exercises as $exercise) : ?>
            <label>
                <input type="checkbox" name="cem_linked_exercises[]" value="<?php echo esc_attr($exercise->ID); ?>">
                <?php echo esc_html($exercise->post_title); ?>
            </label><br>
        <?php endforeach; ?>
        <p>
            <button class="button" type="submit" name="cem_exercise_action" value="delete">حذف التمارين التابعة</button>
            <button class="button" type="submit" name="cem_exercise_action" value="unlink">فصل الربط</button>
        </p>
    <?php else : ?>
        <p>لا توجد تمارين مرتبطة بهذا الدرس.</p>
    <?php endif; ?>
    <?php
}

function cem_dz_render_exercise_meta(WP_Post $post): void
{
    wp_nonce_field('cem_dz_save_exercise', 'cem_dz_exercise_nonce');
    $hint = get_post_meta($post->ID, 'cem_hint', true);
    $solution = get_post_meta($post->ID, 'cem_solution_steps', true);
    $linked = get_post_meta($post->ID, 'cem_linked_lesson_id', true);
    ?>
    <p><label>تلميح</label><br><textarea name="cem_hint" rows="3" style="width:100%;"><?php echo esc_textarea($hint); ?></textarea></p>
    <p><label>خطوات الحل (سطر لكل خطوة)</label><br><textarea name="cem_solution_steps" rows="4" style="width:100%;"><?php echo esc_textarea(is_array($solution) ? implode("\n", $solution) : ''); ?></textarea></p>
    <p><label>الدرس المرتبط</label><br><input type="number" name="cem_linked_lesson_id" value="<?php echo esc_attr($linked); ?>" /></p>
    <?php
}

function cem_dz_render_exam_meta(WP_Post $post): void
{
    wp_nonce_field('cem_dz_save_exam', 'cem_dz_exam_nonce');
    $duration = get_post_meta($post->ID, 'cem_duration', true);
    $grading = get_post_meta($post->ID, 'cem_grading', true);
    $sections = get_post_meta($post->ID, 'cem_sections', true);
    $answer_key = get_post_meta($post->ID, 'cem_answer_key', true);
    ?>
    <p><label>المدة</label><br><input type="text" name="cem_duration" value="<?php echo esc_attr($duration); ?>" /></p>
    <p><label>التنقيط</label><br><textarea name="cem_grading" rows="3" style="width:100%;"><?php echo esc_textarea($grading); ?></textarea></p>
    <p><label>الأقسام (سطر لكل قسم)</label><br><textarea name="cem_sections" rows="4" style="width:100%;"><?php echo esc_textarea(is_array($sections) ? implode("\n", $sections) : ''); ?></textarea></p>
    <p><label>سلم الإجابة (سطر لكل إجابة)</label><br><textarea name="cem_answer_key" rows="4" style="width:100%;"><?php echo esc_textarea(is_array($answer_key) ? implode("\n", $answer_key) : ''); ?></textarea></p>
    <?php
}

function cem_dz_save_post_meta(int $post_id): void
{
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }
    if (!current_user_can('edit_post', $post_id)) {
        return;
    }

    if (isset($_POST['cem_dz_lesson_nonce']) && wp_verify_nonce($_POST['cem_dz_lesson_nonce'], 'cem_dz_save_lesson')) {
        update_post_meta($post_id, 'cem_intro', sanitize_textarea_field(wp_unslash($_POST['cem_intro'] ?? '')));
        $bullets = array_filter(array_map('sanitize_text_field', explode("\n", wp_unslash($_POST['cem_bullets'] ?? ''))));
        update_post_meta($post_id, 'cem_bullets', $bullets);
        $examples = array_filter(array_map('sanitize_text_field', explode("\n", wp_unslash($_POST['cem_examples'] ?? ''))));
        update_post_meta($post_id, 'cem_examples', $examples);
        update_post_meta($post_id, 'cem_mcq', wp_unslash($_POST['cem_mcq'] ?? ''));
    }

    if (!empty($_POST['cem_exercise_action']) && !empty($_POST['cem_linked_exercises']) && is_array($_POST['cem_linked_exercises'])) {
        $action = sanitize_text_field(wp_unslash($_POST['cem_exercise_action']));
        $exercise_ids = array_map('intval', wp_unslash($_POST['cem_linked_exercises']));
        foreach ($exercise_ids as $exercise_id) {
            if ($action === 'delete') {
                wp_delete_post($exercise_id, true);
            }
            if ($action === 'unlink') {
                update_post_meta($exercise_id, 'cem_linked_lesson_id', 0);
            }
        }
    }

    if (isset($_POST['cem_dz_exercise_nonce']) && wp_verify_nonce($_POST['cem_dz_exercise_nonce'], 'cem_dz_save_exercise')) {
        update_post_meta($post_id, 'cem_hint', sanitize_textarea_field(wp_unslash($_POST['cem_hint'] ?? '')));
        $solutions = array_filter(array_map('sanitize_text_field', explode("\n", wp_unslash($_POST['cem_solution_steps'] ?? ''))));
        update_post_meta($post_id, 'cem_solution_steps', $solutions);
        update_post_meta($post_id, 'cem_linked_lesson_id', (int) ($_POST['cem_linked_lesson_id'] ?? 0));
    }

    if (isset($_POST['cem_dz_exam_nonce']) && wp_verify_nonce($_POST['cem_dz_exam_nonce'], 'cem_dz_save_exam')) {
        update_post_meta($post_id, 'cem_duration', sanitize_text_field(wp_unslash($_POST['cem_duration'] ?? '')));
        update_post_meta($post_id, 'cem_grading', sanitize_textarea_field(wp_unslash($_POST['cem_grading'] ?? '')));
        $sections = array_filter(array_map('sanitize_text_field', explode("\n", wp_unslash($_POST['cem_sections'] ?? ''))));
        update_post_meta($post_id, 'cem_sections', $sections);
        $answer_key = array_filter(array_map('sanitize_text_field', explode("\n", wp_unslash($_POST['cem_answer_key'] ?? ''))));
        update_post_meta($post_id, 'cem_answer_key', $answer_key);
    }
}
add_action('save_post', 'cem_dz_save_post_meta');
