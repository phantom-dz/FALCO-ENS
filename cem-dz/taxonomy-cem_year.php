<?php
get_header();
$term = get_queried_object();
$subjects = get_terms(['taxonomy' => 'cem_subject', 'hide_empty' => false]);
?>
<section class="cem-card">
    <h1><?php echo esc_html($term->name); ?></h1>
    <p>اختر المادة المناسبة وتابع تقدمك بشكل واضح.</p>
</section>

<section>
    <h2>المواد</h2>
    <div class="cem-grid">
        <?php foreach ($subjects as $subject) :
            $link = add_query_arg([
                'cem_year' => $term->slug,
                'cem_subject' => $subject->slug,
            ], get_term_link($subject));
            ?>
            <a class="cem-card" href="<?php echo esc_url($link); ?>" data-subject="<?php echo esc_attr($subject->term_id); ?>">
                <h3><?php echo esc_html($subject->name); ?></h3>
                <p class="cem-badge" data-progress-badge>0% تقدم</p>
                <p class="cem-badge" data-favorites-count>0 مفضلة</p>
                <p class="cem-badge" data-updated>جديد</p>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section class="cem-card">
    <h2>مسار مقترح</h2>
    <p>ابدأ بوحدة تمهيدية ثم انتقل إلى التمارين قبل الامتحان الشامل.</p>
</section>
<?php
get_footer();
