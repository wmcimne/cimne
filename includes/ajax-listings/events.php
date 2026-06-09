<?php

/*==========================================================
# Events AJAX filtering
==========================================================*/

add_action('wp_ajax_cimne_filter_events', 'cimne_filter_events');
add_action('wp_ajax_nopriv_cimne_filter_events', 'cimne_filter_events');

function cimne_filter_events() {
    check_ajax_referer('cimne_events_nonce', 'nonce');

    $mode = sanitize_text_field($_POST['mode'] ?? '');

    if ($mode === 'home_upcoming') {
        cimne_filter_home_upcoming_events();
        return;
    }

    $status           = sanitize_text_field($_POST['status'] ?? 'upcoming');
    $search           = sanitize_text_field($_POST['search'] ?? '');
    $event_type       = sanitize_text_field($_POST['event_type'] ?? '');
    $start_date       = sanitize_text_field($_POST['start_date'] ?? '');
    $end_date         = sanitize_text_field($_POST['end_date'] ?? '');
    $paged            = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;
    $current_category = sanitize_key($_POST['current_category'] ?? 'events');

    $today = current_time('Ymd');

    /**
     * Category logic:
     * - current_category comes from the current URL.
     * - event_type comes from the dropdown.
     * - Both are combined with AND.
     */
    $tax_query = [
        'relation' => 'AND',
    ];

    if (!empty($current_category)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => [$current_category],
        ];
    }

    if (!empty($event_type)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => [sanitize_key($event_type)],
        ];
    }

    /**
     * Date logic:
     *
     * event_compare_date must contain:
     * - finish_date if finish_date exists
     * - date if finish_date is empty
     *
     * Upcoming:
     * - event_compare_date >= today
     *
     * Past:
     * - event_compare_date < today
     *
     * Manual date range:
     * - From uses event_compare_date >= selected start
     * - To uses date <= selected end
     *
     * This means:
     * - event end >= filter start
     * - event start <= filter end
     */
    $meta_query = [
        'relation' => 'AND',
        [
            'key'     => CIMNE_EVENT_COMPARE_DATE_FIELD,
            'value'   => $today,
            'compare' => $status === 'past' ? '<' : '>=',
            'type'    => 'NUMERIC',
        ],
    ];

    if (!empty($start_date)) {
        $filter_start = date('Ymd', strtotime($start_date));

        $meta_query[] = [
            'key'     => CIMNE_EVENT_COMPARE_DATE_FIELD,
            'value'   => $filter_start,
            'compare' => '>=',
            'type'    => 'NUMERIC',
        ];
    }

    if (!empty($end_date)) {
        $filter_end = date('Ymd', strtotime($end_date));

        $meta_query[] = [
            'key'     => CIMNE_EVENT_DATE_FIELD,
            'value'   => $filter_end,
            'compare' => '<=',
            'type'    => 'NUMERIC',
        ];
    }

    $args = [
        'post_type'              => 'post',
        'posts_per_page'         => 12,
        'paged'                  => $paged,
        'post_status'            => 'publish',
        's'                      => $search,
        'meta_key'               => CIMNE_EVENT_COMPARE_DATE_FIELD,
        'orderby'                => 'meta_value_num',
        'order'                  => $status === 'past' ? 'DESC' : 'ASC',
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'meta_query'             => $meta_query,
        'tax_query'              => $tax_query,
    ];

    $events = new WP_Query($args);

    ob_start();

    if ($events->have_posts()) {
        while ($events->have_posts()) {
            $events->the_post();

            get_template_part('template-parts/event-card');
        }
    } else {
        echo '<p class="cimne-events-empty">No events found.</p>';
    }

    $html = ob_get_clean();

    $pagination = cimne_build_ajax_pagination(
        $paged,
        (int) $events->max_num_pages,
        'cimne-events-pagination-nav'
    );

    wp_reset_postdata();

    wp_send_json_success([
        'html'       => $html,
        'pagination' => $pagination,
    ]);
}



function cimne_filter_home_upcoming_events() {
    $today = current_time('Ymd');

    $args = [
        'post_type'              => 'post',
        'posts_per_page'         => 2,
        'post_status'            => 'publish',
        'category_name'          => 'events',
        'meta_key'               => CIMNE_EVENT_COMPARE_DATE_FIELD,
        'orderby'                => 'meta_value_num',
        'order'                  => 'ASC',
        'no_found_rows'          => true,
        'update_post_meta_cache' => true,
        'update_post_term_cache' => true,
        'meta_query'             => [
            [
                'key'     => CIMNE_EVENT_COMPARE_DATE_FIELD,
                'value'   => $today,
                'compare' => '>=',
                'type'    => 'NUMERIC',
            ],
        ],
    ];

    $events = new WP_Query($args);

    $right_html = '';
    $left_html  = '';
    $index      = 0;

    if ($events->have_posts()) {
        while ($events->have_posts()) {
            $events->the_post();

            ob_start();

            get_template_part('template-parts/event-card');

            $card_html = ob_get_clean();

            if ($index === 0) {
                $right_html = $card_html;
            }

            if ($index === 1) {
                $left_html = $card_html;
            }

            $index++;
        }
    }

    wp_reset_postdata();

    wp_send_json_success([
        'right' => $right_html,
        'left'  => $left_html,
    ]);
}