<?php
$post_id = get_the_ID();

$date_raw        = get_post_meta($post_id, CIMNE_EVENT_DATE_FIELD, true);
$finish_date_raw = get_post_meta($post_id, CIMNE_EVENT_FINISH_DATE_FIELD, true);

$date        = cimne_parse_event_date($date_raw);
$finish_date = cimne_parse_event_date($finish_date_raw);

$date_visible = '';
$date_timestamp = '';

if ($date) {
    $date_visible = $date->format('d M Y');

    if ($finish_date) {
        $date_visible .= ' - ' . $finish_date->format('d M Y');
        $date_timestamp = $finish_date->getTimestamp();
    } else {
        $date_timestamp = $date->getTimestamp();
    }
}
?>

<article id="post-<?php echo esc_attr($post_id); ?>"
    <?php post_class('et_pb_post clearfix et_pb_blog_item_0_0'); ?>
    style="opacity: 1;">

    <?php if (has_post_thumbnail()) : ?>
        <div class="">
            <a href="<?php the_permalink(); ?>" class="entry-featured-image-url">
                <?php the_post_thumbnail('medium_large'); ?>
            </a>
        </div>
    <?php endif; ?>

    <h5 class="entry-title">
        <a href="<?php the_permalink(); ?>">
            <?php the_title(); ?>
        </a>
    </h5>

    <div class="post-content">
        <?php if (!empty($date_visible)) : ?>
            <div class="date" date="<?php echo esc_attr($date_timestamp); ?>">
                <?php echo esc_html($date_visible); ?>
            </div>
        <?php endif; ?>
    </div>

</article>