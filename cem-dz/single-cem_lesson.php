<?php
get_header();
while (have_posts()) :
    the_post();
    $intro = get_post_meta(get_the_ID(), 'cem_intro', true);
    $bullets = get_post_meta(get_the_ID(), 'cem_bullets', true);
    $examples = get_post_meta(get_the_ID(), 'cem_examples', true);
    $mcq = get_post_meta(get_the_ID(), 'cem_mcq', true);
    ?>
    <article class="cem-card" data-content-id="<?php echo esc_attr(get_the_ID()); ?>" data-content-type="lesson">
        <h1><?php the_title(); ?></h1>
        <div class="cem-actions">
            <button class="cem-button" type="button" data-toggle-complete>تم إنجاز الدرس</button>
            <button class="cem-button" type="button" data-toggle-favorite>إضافة للمفضلة</button>
            <a class="cem-button cem-button-primary" href="#exercises">ابدأ التمارين</a>
        </div>
        <section>
            <h2>مقدمة</h2>
            <p><?php echo esc_html($intro); ?></p>
        </section>
        <section>
            <h2>النقاط الأساسية</h2>
            <ul>
                <?php foreach ((array) $bullets as $bullet) : ?>
                    <li><?php echo esc_html($bullet); ?></li>
                <?php endforeach; ?>
            </ul>
        </section>
        <section>
            <h2>أمثلة محلولة</h2>
            <ol>
                <?php foreach ((array) $examples as $example) : ?>
                    <li><?php echo esc_html($example); ?></li>
                <?php endforeach; ?>
            </ol>
        </section>
        <section>
            <h2>اختبار سريع</h2>
            <div data-mcq><?php echo esc_html($mcq); ?></div>
        </section>
        <section class="cem-card">
            <h2>وضع الدراسة</h2>
            <label>حجم الخط <input type="range" min="14" max="22" value="16" data-font-size></label>
            <label>تباعد الأسطر <input type="range" min="1.4" max="2.2" step="0.1" value="1.7" data-line-height></label>
            <label><input type="checkbox" data-distraction-free> وضع بدون تشتيت</label>
        </section>
    </article>
    <section id="exercises">
        <h2>التمارين المرتبطة</h2>
        <div class="cem-grid">
            <?php
            $exercise_query = new WP_Query([
                'post_type' => 'cem_exercise',
                'posts_per_page' => 6,
                'meta_query' => [
                    [
                        'key' => 'cem_linked_lesson_id',
                        'value' => get_the_ID(),
                        'compare' => '=',
                    ],
                ],
            ]);
            while ($exercise_query->have_posts()) : $exercise_query->the_post(); ?>
                <a class="cem-card" href="<?php the_permalink(); ?>">
                    <h3><?php the_title(); ?></h3>
                    <p><?php echo esc_html(get_the_excerpt()); ?></p>
                </a>
            <?php endwhile; wp_reset_postdata(); ?>
        </div>
    </section>
<?php
endwhile;
get_footer();
