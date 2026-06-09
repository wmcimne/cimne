<?php 

function cimne_ajax_listings_enqueue_scripts(){

    $current_url = $_SERVER['REQUEST_URI'];
    
    $is_events_page = strpos($current_url, '/category/events/') !== false || strpos($current_url, '/category/esdeveniments/') !== false || strpos($current_url, '/category/eventos/') !== false;

    if ( $is_events_page || is_category('seminars') || is_front_page()) {
        wp_enqueue_style(
            'cimne-events',
            get_stylesheet_directory_uri() . '/css/cimne-events.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'cimne-events',
            get_stylesheet_directory_uri() . '/js/cimne-events.js',
            ['jquery'],
            '1.0.0',
            true
        );

        $current_category = get_queried_object();

        wp_localize_script('cimne-events', 'CimneEvents', [
            'ajax_url'         => admin_url('admin-ajax.php'),
            'nonce'            => wp_create_nonce('cimne_events_nonce'),
            'current_category' => is_category() && !empty($current_category->slug)
                ? $current_category->slug
                : 'events',
        ]);
    }

    $is_news_page = strpos($current_url, '/category/news/') !== false || strpos($current_url, '/category/noticias/') !== false || strpos($current_url, '/category/noticies/') !== false;

    if ( $is_news_page ) {
        wp_enqueue_style(
            'cimne-news',
            get_stylesheet_directory_uri() . '/css/cimne-news.css',
            [],
            '1.0.0'
        );

        wp_enqueue_script(
            'cimne-news',
            get_stylesheet_directory_uri() . '/js/cimne-news.js',
            ['jquery'],
            '1.0.0',
            true
        );

        $current_category = get_queried_object();

        wp_localize_script('cimne-news', 'CimneNews', [
            'ajax_url'         => admin_url('admin-ajax.php'),
            'nonce'            => wp_create_nonce('cimne_news_nonce'),
            'current_category' => is_category() && !empty($current_category->slug)
                ? $current_category->slug
                : 'news',
        ]);
    }

    if (is_page('short-training')) {
        wp_enqueue_style(
            'cimne-workshops',
            get_stylesheet_directory_uri() . '/css/cimne-workshops.css',
            [],
            '1.0.0'
        );
        wp_enqueue_script(
            'cimne-workshops',
            get_stylesheet_directory_uri() . '/js/cimne-workshops.js',
            ['jquery'],
            '1.0.0',
            true
        );

        wp_localize_script('cimne-workshops', 'CimneWorkshops', [
            'ajax_url' => admin_url('admin-ajax.php'),
            'nonce'    => wp_create_nonce('cimne_workshops_nonce'),
        ]);
        
    }
}

add_action('wp_enqueue_scripts', 'cimne_ajax_listings_enqueue_scripts');