<?php
/*
Template Name: لوحة التحكم
*/
get_header();
?>
<section class="cem-card">
    <h1>لوحة التحكم</h1>
    <p>نظرة شاملة على تقدمك وإحصاءاتك.</p>
</section>

<section class="cem-grid">
    <div class="cem-card">
        <h2>التقدم حسب السنة</h2>
        <svg data-chart="year-progress" width="100%" height="180" aria-label="مخطط التقدم حسب السنة"></svg>
    </div>
    <div class="cem-card">
        <h2>النشاط خلال 7 أيام</h2>
        <svg data-chart="activity" width="100%" height="180" aria-label="مخطط النشاط"></svg>
    </div>
</section>

<section class="cem-grid">
    <div class="cem-card">
        <h2>دروس غير مكتملة</h2>
        <div data-dashboard="incomplete">...</div>
    </div>
    <div class="cem-card">
        <h2>المفضلة</h2>
        <div data-dashboard="favorites">...</div>
    </div>
    <div class="cem-card">
        <h2>آخر ما شاهدت</h2>
        <div data-dashboard="recent">...</div>
    </div>
</section>
<?php
get_footer();
