<?php
get_header();
$search_term = get_search_query();
$year = sanitize_text_field($_GET['year'] ?? '');
$subject = sanitize_text_field($_GET['subject'] ?? '');
$type = sanitize_text_field($_GET['type'] ?? '');
$sort = sanitize_text_field($_GET['sort'] ?? 'relevance');

$args = [
    'post_type' => $type ? [$type] : ['cem_lesson', 'cem_exercise', 'cem_exam'],
    's' => $search_term,
    'posts_per_page' => 10,
    'paged' => max(1, (int) get_query_var('paged')),
];

$tax_query = [];
if ($year) {
    $tax_query[] = [
        'taxonomy' => 'cem_year',
        'field' => 'slug',
        'terms' => $year,
    ];
}
if ($subject) {
    $tax_query[] = [
        'taxonomy' => 'cem_subject',
        'field' => 'slug',
        'terms' => $subject,
    ];
}
if ($tax_query) {
    $args['tax_query'] = $tax_query;
}

if ($sort === 'newest') {
    $args['orderby'] = 'date';
    $args['order'] = 'DESC';
}

$query = new WP_Query($args);
?>
<section class="cem-card">
    <h1>نتائج البحث</h1>
    <form method="get" action="<?php echo esc_url(home_url('/')); ?>">
        <input type="hidden" name="s" value="<?php echo esc_attr($search_term); ?>">
        <div class="cem-grid">
            <label>السنة
                <select name="year">
                    <option value="">الكل</option>
                    <?php foreach (get_terms(['taxonomy' => 'cem_year', 'hide_empty' => false]) as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($term->slug, $year); ?>><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>المادة
                <select name="subject">
                    <option value="">الكل</option>
                    <?php foreach (get_terms(['taxonomy' => 'cem_subject', 'hide_empty' => false]) as $term) : ?>
                        <option value="<?php echo esc_attr($term->slug); ?>" <?php selected($term->slug, $subject); ?>><?php echo esc_html($term->name); ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>النوع
                <select name="type">
                    <option value="">الكل</option>
                    <option value="cem_lesson" <?php selected('cem_lesson', $type); ?>>درس</option>
                    <option value="cem_exercise" <?php selected('cem_exercise', $type); ?>>تمرين</option>
                    <option value="cem_exam" <?php selected('cem_exam', $type); ?>>امتحان</option>
                </select>
            </label>
            <label>الترتيب
                <select name="sort">
                    <option value="relevance" <?php selected('relevance', $sort); ?>>الأكثر صلة</option>
                    <option value="newest" <?php selected('newest', $sort); ?>>الأحدث</option>
                </select>
            </label>
        </div>
        <button class="cem-button cem-button-primary" type="submit">تحديث النتائج</button>
    </form>
</section>

<section>
    <div class="cem-grid">
        <?php if ($query->have_posts()) : ?>
            <?php while ($query->have_posts()) : $query->the_post(); ?>
                <?php
                $excerpt = wp_trim_words(get_the_excerpt(), 22);
                if ($search_term) {
                    $excerpt = preg_replace('/(' . preg_quote($search_term, '/') . ')/iu', '<mark>$1</mark>', $excerpt);
                }
                ?>
                <a class="cem-card" href="<?php the_permalink(); ?>">
                    <h2><?php the_title(); ?></h2>
                    <p><?php echo wp_kses_post($excerpt); ?></p>
                </a>
            <?php endwhile; ?>
        <?php else : ?>
            <div class="cem-card">لا توجد نتائج مطابقة.</div>
        <?php endif; ?>
    </div>
    <div class="cem-card">
        <?php
        echo paginate_links([
            'total' => $query->max_num_pages,
        ]);
        ?>
    </div>
</section>
<?php
wp_reset_postdata();
get_footer();
