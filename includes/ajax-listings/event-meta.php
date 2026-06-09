<?php

if (!defined('ABSPATH')) {
    exit;
}

add_action('save_post_post', 'cimne_update_event_compare_date', 20, 3);

function cimne_update_event_compare_date($post_id, $post, $update) {
    if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) {
        return;
    }

    if (wp_is_post_revision($post_id)) {
        return;
    }

    if ($post->post_type !== 'post') {
        return;
    }

    $date = get_post_meta($post_id, CIMNE_EVENT_DATE_FIELD, true);

    if (empty($date)) {
        delete_post_meta($post_id, CIMNE_EVENT_COMPARE_DATE_FIELD);
        return;
    }

    $compare_date = cimne_get_event_compare_date($post_id);

    if (!empty($compare_date)) {
        update_post_meta($post_id, CIMNE_EVENT_COMPARE_DATE_FIELD, $compare_date);
    }
}


// add_action('admin_init', 'cimne_run_event_compare_date_backfill');

// function cimne_run_event_compare_date_backfill() {
//     if (!current_user_can('manage_options')) {
//         return;
//     }

//     if (get_option('cimne_event_compare_date_backfilled')) {
//         return;
//     }

//     $events = new WP_Query([
//         'post_type'      => 'post',
//         'posts_per_page' => -1,
//         'post_status'    => ['publish', 'draft', 'future', 'private'],
//         'fields'         => 'ids',
//         'tax_query'      => [
//             [
//                 'taxonomy'         => 'category',
//                 'field'            => 'slug',
//                 'terms'            => ['events'],
//                 'include_children' => true,
//             ],
//         ],
//     ]);

//     foreach ($events->posts as $post_id) {
//         $date = get_post_meta($post_id, CIMNE_EVENT_DATE_FIELD, true);

//         if (empty($date)) {
//             delete_post_meta($post_id, CIMNE_EVENT_COMPARE_DATE_FIELD);
//             continue;
//         }

//         update_post_meta(
//             $post_id,
//             CIMNE_EVENT_COMPARE_DATE_FIELD,
//             cimne_get_event_compare_date($post_id)
//         );
//     }

//     update_option('cimne_event_compare_date_backfilled', 1);
// }

// add_action('admin_init', function () {
//     if (!current_user_can('manage_options')) {
//         return;
//     }

//     if (!isset($_GET['check_event_compare_date'])) {
//         return;
//     }

//     $events = get_posts([
//         'post_type'      => 'post',
//         'posts_per_page' => 20,
//         'category_name'  => 'events',
//         'post_status'    => 'publish',
//     ]);

//     echo '<pre>';

//     foreach ($events as $event) {
//         echo $event->ID . ' - ' . $event->post_title . "\n";
//         echo 'date: ' . get_post_meta($event->ID, 'date', true) . "\n";
//         echo 'finish_date: ' . get_post_meta($event->ID, 'finish_date', true) . "\n";
//         echo 'event_compare_date: ' . get_post_meta($event->ID, 'event_compare_date', true) . "\n\n";
//     }

//     echo '</pre>';
//     exit;
// });