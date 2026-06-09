<?php
$post_id = get_the_ID();
//console_log("Rendering event card for post ID: {$post_id}");

$event_type = '';
$attendance = get_field('attendance', $post_id);

/**
 * Event type from child categories of Events Compo parent category.
 */
$categories = get_the_terms($post_id, 'category');
console_log("Categories for post ID {$post_id}: " . print_r($categories, true));

if (!empty($categories) && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        /* 
            Category parent IDs for Events Compo EN, CA, ES: 95, 107, 108 
        */
        if ((int) $category->parent === 95 || (int) $category->parent === 107 || (int) $category->parent === 108) {
            $event_type = $category->name;
            console_log("Event type for post ID {$post_id}: {$event_type}");
            break;
        }
    }
}

/**
 * Normalize attendance.
 */
if (is_array($attendance)) {
    $attendance = reset($attendance);
}

$attendance_class = !empty($attendance) ? sanitize_title($attendance) : '';


/**
 * Dates.
 *
 * Logic:
 * - start_date is required and used as visible start date.
 * - finish_date is optional.
 * - If finish_date exists, append it.
 * - date_timestamp uses finish_date when available, otherwise start_date.
 */
$start_date_raw  = get_post_meta($post_id, 'date', true);
$finish_date_raw = get_post_meta($post_id, 'finish_date', true);

$start_date  = cimne_parse_event_date($start_date_raw);
$finish_date = cimne_parse_event_date($finish_date_raw);

$date_visible = '';


if ($start_date) {
    $date_visible = $start_date->format('d M Y');
    if ($finish_date) {
        $date_visible .= ' - ' . $finish_date->format('d M Y');
    }
} 
?>

<article id="post-<?php echo esc_attr($post_id); ?>"
    <?php post_class('et_pb_post clearfix et_pb_no_thumb et_pb_blog_item_0_0'); ?>
    style="display: block; position: relative; padding-top: 50px;">

    <h5 class="entry-title">
        <a href="<?php the_permalink(); ?>" role="link">
            <?php the_title(); ?>
        </a>
    </h5>

    <?php if (!empty($event_type)) : ?>
        <div class="type-of-event rectangle">
            <span class="rectangle-text">
                <?php echo esc_html($event_type); ?>
            </span>
        </div>
    <?php endif; ?>

    <div class="post-content">

        <?php if (!empty($date_visible)) : ?>
            <div class="date">
                <?php echo esc_html($date_visible); ?>
            </div>
        <?php endif; ?>

        <?php if (!empty($attendance)) : ?>
            <div class="attendance <?php echo esc_attr($attendance_class); ?>">
                <?php echo esc_html($attendance); ?>
            </div>
        <?php endif; ?>

        <!--Bloque para el caso que se quiera el contenido del post, actualmente se muestra un extracto con un máximo de 28 palabras.
        <div class="post-content-inner et_multi_view_hidden">
            <p>
                <?php echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?>
            </p>
        </div> -->

    </div>

</article>