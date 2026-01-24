<?php
get_header();
while (have_posts()) :
    the_post();
    $duration = get_post_meta(get_the_ID(), 'cem_duration', true);
    $grading = get_post_meta(get_the_ID(), 'cem_grading', true);
    $sections = get_post_meta(get_the_ID(), 'cem_sections', true);
    $answer_key = get_post_meta(get_the_ID(), 'cem_answer_key', true);
    $years = wp_get_object_terms(get_the_ID(), 'cem_year', ['fields' => 'names']);
    $subjects = wp_get_object_terms(get_the_ID(), 'cem_subject', ['fields' => 'names']);
    ?>
    <article class="cem-card" data-content-id="<?php echo esc_attr(get_the_ID()); ?>" data-content-type="exam">
        <div class="cem-print-header">
            <strong>CEM DZ</strong>
            <span><?php echo esc_html(implode(' - ', array_filter([$years[0] ?? '', $subjects[0] ?? '']))); ?></span>
        </div>
        <h1><?php the_title(); ?></h1>
        <div class="cem-actions">
            <button class="cem-button" type="button" onclick="window.print()">طباعة</button>
            <button class="cem-button" type="button" data-toggle-complete>تمت المعاينة</button>
        </div>
        <section>
            <h2>التعليمات</h2>
            <p>المدة: <?php echo esc_html($duration); ?></p>
            <p><?php echo esc_html($grading); ?></p>
        </section>
        <section>
            <h2>الأقسام والأسئلة</h2>
            <ol>
                <?php foreach ((array) $sections as $section) : ?>
                    <li><?php echo esc_html($section); ?></li>
                <?php endforeach; ?>
            </ol>
            <?php the_content(); ?>
        </section>
        <section>
            <button class="cem-button" type="button" data-accordion="toggle" aria-expanded="false">عرض سلم الإجابة</button>
            <div class="cem-accordion-content" data-accordion="content">
                <ol>
                    <?php foreach ((array) $answer_key as $answer) : ?>
                        <li><?php echo esc_html($answer); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    </article>
<?php
endwhile;
get_footer();
