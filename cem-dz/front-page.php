<?php
get_header();
$years = get_terms(['taxonomy' => 'cem_year', 'hide_empty' => false]);
?>
<section class="cem-hero">
    <div class="cem-card">
        <h1>مرحباً بك في CEM DZ</h1>
        <p>اختر سنتك الدراسية وابدأ التعلم بخطوات واضحة.</p>
        <div class="cem-search">
            <input type="search" id="cem-search-input" placeholder="ابحث عن درس أو تمرين أو امتحان" aria-label="بحث">
            <a class="cem-button cem-button-primary" href="<?php echo esc_url(home_url('/?s=')); ?>">بحث</a>
        </div>
    </div>
    <div class="cem-card">
        <h2>ابدأ من حيث توقفت</h2>
        <p>سيتم عرض آخر محتوى فتحته هنا.</p>
        <button class="cem-button cem-button-primary" type="button" data-resume>ابدأ الآن</button>
    </div>
</section>

<section>
    <h2>خريطة السنوات</h2>
    <div class="cem-grid">
        <?php foreach ($years as $index => $year) : ?>
            <a class="cem-card" href="<?php echo esc_url(get_term_link($year)); ?>" data-year="<?php echo esc_attr($year->term_id); ?>">
                <div class="cem-chip"><?php echo esc_html($year->name); ?></div>
                <p>نسبة التقدم</p>
                <svg class="cem-progress-ring" viewBox="0 0 36 36" data-progress="0" aria-hidden="true">
                    <path d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#e4e7f0" stroke-width="3"/>
                    <path class="cem-progress-value" d="M18 2.0845a 15.9155 15.9155 0 0 1 0 31.831a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#3a6ff8" stroke-width="3" stroke-dasharray="0, 100"/>
                </svg>
            </a>
        <?php endforeach; ?>
    </div>
</section>

<section>
    <h2>آخر ما شاهدته</h2>
    <div class="cem-grid" data-recent-list>
        <div class="cem-card">سيتم تحميل العناصر قريباً.</div>
    </div>
</section>
<?php
get_footer();
