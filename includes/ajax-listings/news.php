<?php
/*==========================================================
# News AJAX filtering
==========================================================*/

add_action('wp_ajax_cimne_filter_news', 'cimne_filter_news');
add_action('wp_ajax_nopriv_cimne_filter_news', 'cimne_filter_news');

function cimne_filter_news() {
    // Verificar el nonce para seguridad (evita peticiones CSRF).
    check_ajax_referer('cimne_news_nonce', 'nonce');

    // --- Sanitizar entradas recibidas por POST ---
    // Texto de búsqueda libre
    $search     = sanitize_text_field($_POST['search'] ?? '');
    // Tipo de noticia (slug de categoría)
    $news_type  = sanitize_text_field($_POST['news_type'] ?? '');
    // Rango de fechas: inicio y fin (opcional)
    $start_date = sanitize_text_field($_POST['start_date'] ?? '');
    $end_date   = sanitize_text_field($_POST['end_date'] ?? '');
    // Página solicitada para paginación (por defecto 1)
    $paged      = isset($_POST['paged']) ? max(1, absint($_POST['paged'])) : 1;
    $current_category = sanitize_key($_POST['current_category'] ?? 'news');

    // Construir la parte de consulta por fecha si se proporcionan dates
    $date_query = [];
    if (!empty($start_date)) {
        // 'after' acepta una fecha legible por WP ('YYYY-MM-DD' u otros formatos válidos)
        $date_query['after'] = $start_date;
    }
    if (!empty($end_date)) {
        // 'before' limita la fecha máxima
        $date_query['before'] = $end_date;
    }
    if (!empty($date_query)) {
        // Incluir los límites de forma inclusiva (incluye los días extremos)
        $date_query['inclusive'] = true;
    }

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

    if (!empty($news_type)) {
        $tax_query[] = [
            'taxonomy' => 'category',
            'field'    => 'slug',
            'terms'    => [sanitize_key($news_type)],
        ];
}

    // --- Argumentos principales para WP_Query ---
    $args = [
        'post_type'      => 'post',          // tipo de contenido
        'posts_per_page' => 12,              // número de items por página
        'paged'          => $paged,          // página actual
        'post_status'    => 'publish',       // solo publicados
        's'              => $search,         // búsqueda por texto
        'orderby'        => 'date',          // ordenar por fecha
        'order'          => 'DESC',          // más recientes primero
        'tax_query' => $tax_query, 
    ];

    // Añadir filtro por rango de fechas si procede
    if (!empty($date_query)) {
        $args['date_query'] = [$date_query];
    }



    // Ejecutar la consulta
    $news = new WP_Query($args);

    // Capturar la salida HTML de los template parts
    ob_start();

    if ($news->have_posts()) {
        while ($news->have_posts()) {
            $news->the_post();
            // Renderizar cada noticia usando el template 'template-parts/news-card'
            get_template_part('template-parts/news-card');
        }
    } else {
        // Mensaje cuando no hay resultados
        echo '<p class="cimne-news-empty">No news found.</p>';
    }

    // Obtener HTML generado
    $html = ob_get_clean();

    // Generar la paginación basada en el número máximo de páginas de la consulta
    $pagination = cimne_build_ajax_pagination(
        $paged,
        $news->max_num_pages,
        'cimne-news-pagination-nav'
    );

    // Restaurar datos globales de post
    wp_reset_postdata();

    // Enviar respuesta JSON con éxito: HTML + fragmento de paginación
    wp_send_json_success([
        'html'       => $html,
        'pagination' => $pagination,
    ]);
}