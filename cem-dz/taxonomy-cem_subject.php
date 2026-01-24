<?php
get_header();
$subject = get_queried_object();
$year_slug = sanitize_text_field($_GET['cem_year'] ?? '');
$year = $year_slug ? get_term_by('slug', $year_slug, 'cem_year') : null;
?>
<section class="cem-card">
    <h1><?php echo esc_html($subject->name); ?></h1>
    <p><?php echo $year ? esc_html($year->name) : 'كل السنوات'; ?></p>
</section>

<div class="cem-tabs" role="tablist">
    <button class="cem-tab" type="button" role="tab" aria-selected="true" data-tab="lessons">الدروس</button>
    <button class="cem-tab" type="button" role="tab" aria-selected="false" data-tab="exercises">التمارين</button>
    <button class="cem-tab" type="button" role="tab" aria-selected="false" data-tab="exams">الامتحانات الشاملة</button>
</div>

<section data-tab-panel="lessons">
    <?php
    $units = get_terms(['taxonomy' => 'cem_unit', 'hide_empty' => false]);
    foreach ($units as $unit) :
        $lesson_query = new WP_Query([
            'post_type' => 'cem_lesson',
            'posts_per_page' => 6,
            'tax_query' => array_filter([
                [
                    'taxonomy' => 'cem_subject',
                    'field' => 'term_id',
                    'terms' => $subject->term_id,
                ],
                $year ? [
                    'taxonomy' => 'cem_year',
                    'field' => 'term_id',
                    'terms' => $year->term_id,
                ] : null,
                [
                    'taxonomy' => 'cem_unit',
                    'field' => 'term_id',
                    'terms' => $unit->term_id,
                ],
            ]),
        ]);
        if (!$lesson_query->have_posts()) {
            continue;
        }
        ?>
        <div class="cem-card cem-accordion">
            <button type="button" data-accordion="toggle" aria-expanded="false">
                <?php echo esc_html($unit->name); ?>
            </button>
            <div class="cem-accordion-content" data-accordion="content">
                <div class="cem-grid">
                    <?php while ($lesson_query->have_posts()) : $lesson_query->the_post(); ?>
                        <a class="cem-card" href="<?php the_permalink(); ?>">
                            <h3><?php the_title(); ?></h3>
                            <p><?php echo esc_html(get_the_excerpt()); ?></p>
                        </a>
                    <?php endwhile; wp_reset_postdata(); ?>
                </div>
            </div>
        </div>
    <?php endforeach; ?>
</section>

<section data-tab-panel="exercises" hidden>
    <div class="cem-grid">
        <?php
        $exercise_query = new WP_Query([
            'post_type' => 'cem_exercise',
            'posts_per_page' => 12,
            'tax_query' => array_filter([
                [
                    'taxonomy' => 'cem_subject',
                    'field' => 'term_id',
                    'terms' => $subject->term_id,
                ],
                $year ? [
                    'taxonomy' => 'cem_year',
                    'field' => 'term_id',
                    'terms' => $year->term_id,
                ] : null,
            ]),
        ]);
        while ($exercise_query->have_posts()) : $exercise_query->the_post(); ?>
            <a class="cem-card" href="<?php the_permalink(); ?>">
                <h3><?php the_title(); ?></h3>
                <p><?php echo esc_html(get_the_excerpt()); ?></p>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>

<section data-tab-panel="exams" hidden>
    <div class="cem-grid">
        <?php
        $exam_query = new WP_Query([
            'post_type' => 'cem_exam',
            'posts_per_page' => 6,
            'tax_query' => array_filter([
                [
                    'taxonomy' => 'cem_subject',
                    'field' => 'term_id',
                    'terms' => $subject->term_id,
                ],
                $year ? [
                    'taxonomy' => 'cem_year',
                    'field' => 'term_id',
                    'terms' => $year->term_id,
                ] : null,
            ]),
        ]);
        while ($exam_query->have_posts()) : $exam_query->the_post(); ?>
            <a class="cem-card" href="<?php the_permalink(); ?>">
                <h3><?php the_title(); ?></h3>
                <p><?php echo esc_html(get_the_excerpt()); ?></p>
            </a>
        <?php endwhile; wp_reset_postdata(); ?>
    </div>
</section>
<?php
get_footer();
