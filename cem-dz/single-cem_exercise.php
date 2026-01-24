<?php
get_header();
while (have_posts()) :
    the_post();
    $hint = get_post_meta(get_the_ID(), 'cem_hint', true);
    $steps = get_post_meta(get_the_ID(), 'cem_solution_steps', true);
    ?>
    <article class="cem-card" data-content-id="<?php echo esc_attr(get_the_ID()); ?>" data-content-type="exercise">
        <h1><?php the_title(); ?></h1>
        <div class="cem-actions">
            <button class="cem-button" type="button" data-toggle-complete>تم الحل</button>
            <button class="cem-button" type="button" data-toggle-favorite>إضافة للمفضلة</button>
        </div>
        <section>
            <h2>نص التمرين</h2>
            <?php the_content(); ?>
        </section>
        <section>
            <button class="cem-button" type="button" data-accordion="toggle" aria-expanded="false">عرض التلميح</button>
            <div class="cem-accordion-content" data-accordion="content">
                <p><?php echo esc_html($hint); ?></p>
            </div>
        </section>
        <section>
            <button class="cem-button" type="button" data-accordion="toggle" aria-expanded="false">عرض الحل</button>
            <div class="cem-accordion-content" data-accordion="content">
                <ol>
                    <?php foreach ((array) $steps as $step) : ?>
                        <li><?php echo esc_html($step); ?></li>
                    <?php endforeach; ?>
                </ol>
            </div>
        </section>
    </article>
<?php
endwhile;
get_footer();
