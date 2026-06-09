<?php
/*==========================================================
# Workshops AJAX filtering
==========================================================*/

add_action('wp_ajax_cimne_filter_workshops_events', 'cimne_filter_workshops_events');
add_action('wp_ajax_nopriv_cimne_filter_workshops_events', 'cimne_filter_workshops_events');

function cimne_filter_workshops_events() {
    check_ajax_referer('cimne_workshops_nonce', 'nonce');

    $paged = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;

    $args = [
        'post_type'      => 'post',
        'posts_per_page' => 9,
        'paged'          => $paged,
        'post_status'    => 'publish',
        'category_name'  => 'workshops',
        'meta_key'       => CIMNE_EVENT_DATE_FIELD,
        'orderby'        => 'meta_value_num',
        'order'          => 'DESC',
        'meta_query'     => [
            [
                'key'     => CIMNE_EVENT_DATE_FIELD,
                'compare' => 'EXISTS',
            ],
        ],
    ];

    $workshops = new WP_Query($args);

    ob_start();

    if ($workshops->have_posts()) {
        while ($workshops->have_posts()) {
            $workshops->the_post();
            get_template_part('template-parts/workshop-event-card');
        }
    } else {
        echo '<p class="cimne-workshops-empty">No workshops found.</p>';
    }

    $html = ob_get_clean();

    $pagination = cimne_build_ajax_pagination(
        $paged,
        $workshops->max_num_pages,
        'cimne-workshops-pagination-nav'
    );

    wp_reset_postdata();

    wp_send_json_success([
        'html'       => $html,
        'pagination' => $pagination,
    ]);
}