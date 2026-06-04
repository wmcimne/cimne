<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*================================================
#Load custom Blog Module
================================================*/
function divi_custom_blog_module() {
    get_template_part( '/includes/Blog' ); 
    $dcfm = new custom_ET_Builder_Module_Blog();
    remove_shortcode( 'et_pb_blog' );
    add_shortcode( 'et_pb_blog', array( $dcfm, '_shortcode_callback' ) ); 
  }
  add_action( 'et_builder_ready', 'divi_custom_blog_module' );
  function divi_custom_blog_class( $classlist ) {
    // Blog Module 'classname' overwrite.
    $classlist['et_pb_blog'] = array( 'classname' => 'custom_ET_Builder_Module_Blog',);
    return $classlist;
  }
  add_filter( 'et_module_classes', 'divi_custom_blog_class' );

/*================================================
# ACF shortcode enable
================================================*/

function set_acf_settings() {
    acf_update_setting( 'enable_shortcode', true );
}
add_action( 'acf/init', 'set_acf_settings' );

/*================================================
#ENQUEUE PARENT ACTION
================================================*/
// AUTO GENERATED - Do not modify or remove comment markers above or below:

if ( !function_exists( 'chld_thm_cfg_locale_css' ) ):
    function chld_thm_cfg_locale_css( $uri ){
        if ( empty( $uri ) && is_rtl() && file_exists( get_template_directory() . '/rtl.css' ) )
            $uri = get_template_directory_uri() . '/rtl.css';
        return $uri;
    }
endif;
add_filter( 'locale_stylesheet_uri', 'chld_thm_cfg_locale_css' );
// END ENQUEUE PARENT ACTION

/*================================================
#maintenance Mode
================================================*/
// Activate WordPress Maintenance Mode
// function aulas_maintenance_mode(){

//     if(!current_user_can('edit_themes') || !is_user_logged_in()){

//     wp_die('<h1 style="color:red">Aulas CIMNE Website under Maintenance</h1><br />We are performing scheduled maintenance. We will be back on-line shortly!');

//     }
// }
// add_action('get_header', 'aulas_maintenance_mode');
// END MANTENANCE MODE



/**
 * Google Consent Mode v2 manual para Complianz Free
 * Adaptado para activar señales de marketing si el usuario acepta, 
 * solucionando el aviso de "Señales inactivas" en GA4.
 */

add_action('wp_head', function () {
    if (is_admin()) {
        return;
    }
    ?>
    <script>
    window.dataLayer = window.dataLayer || [];

    function gtag() {
      window.dataLayer.push(arguments);
    }

    // Función para leer las cookies de Complianz
    function cmplzCookieIsAllowed(cookieName) {
      return document.cookie
        .split('; ')
        .some(function(cookie) {
          return cookie.indexOf(cookieName + '=allow') === 0;
        });
    }

    function updateGoogleConsentFromComplianz() {
      var statisticsAllowed = cmplzCookieIsAllowed('cmplz_statistics');
      console.log('Updating Google Consent statistics...' + statisticsAllowed);
      console.log('Statistics consent: ' + (statisticsAllowed ? 'granted' : 'denied'));
      var marketingAllowed = cmplzCookieIsAllowed('cmplz_marketing');
      console.log('Updating Google Consent marketing...' + marketingAllowed);
      console.log('Marketing consent: ' + (marketingAllowed ? 'granted' : 'denied'));

      gtag('consent', 'update', {
        'analytics_storage': statisticsAllowed ? 'granted' : 'denied',
        'ad_storage': marketingAllowed ? 'granted' : 'denied',
        'ad_user_data': marketingAllowed ? 'granted' : 'denied',
        'ad_personalization': marketingAllowed ? 'granted' : 'denied',
        'functionality_storage': 'granted',
        'security_storage': 'granted'
      });

    //   gtag('consent', 'update', {
    //     'analytics_storage': statisticsAllowed ? 'granted' : 'denied',
    //     'ad_storage': 'denied',
    //     'ad_user_data': 'denied',
    //     'ad_personalization': 'denied',
    //     'functionality_storage': 'granted',
    //     'security_storage': 'granted'
    //   });
    }

    // Estado por defecto (Default)
    // gtag('consent', 'default', {
    //   'analytics_storage': cmplzCookieIsAllowed('cmplz_statistics') ? 'granted' : 'denied',
    //   'ad_storage': cmplzCookieIsAllowed('cmplz_marketing') ? 'granted' : 'denied',
    //   'ad_user_data': cmplzCookieIsAllowed('cmplz_marketing') ? 'granted' : 'denied',
    //   'ad_personalization': cmplzCookieIsAllowed('cmplz_marketing') ? 'granted' : 'denied',
    //   'functionality_storage': 'granted',
    //   'security_storage': 'granted',
    //   'wait_for_update': 500
    // });

    // Estado por defecto (Default) - Forzamos 'denied' siempre al arrancar
    gtag('consent', 'default', {
    'analytics_storage': 'denied',
    'ad_storage': 'denied',
    'ad_user_data': 'denied',
    'ad_personalization': 'denied',
    'functionality_storage': 'granted',
    'security_storage': 'granted',
    'wait_for_update': 500
    });

    // Escuchar eventos de Complianz
    document.addEventListener('cmplz_event_statistics', updateGoogleConsentFromComplianz);
    document.addEventListener('cmplz_event_marketing', updateGoogleConsentFromComplianz);
    document.addEventListener('cmplz_event_all', updateGoogleConsentFromComplianz);
    document.addEventListener('cmplz_status_change', updateGoogleConsentFromComplianz);

    // Re-verificación por carga o retraso
    window.addEventListener('load', updateGoogleConsentFromComplianz);
    setTimeout(updateGoogleConsentFromComplianz, 1000);
    setTimeout(updateGoogleConsentFromComplianz, 3000);
    </script>
    <?php
}, 0);



/*====================================================
# Imagen de cabecera aleatoria en la página de inicio
=======================================================*/
add_action('wp_head', function() {
    // Solo se ejecuta si es la página de inicio configurada
    if ( is_front_page() ) {
		
		console_log("Front page");
        
        $imagenes = array(
            '/wp-content/uploads/2025/05/header3-1.jpg',
            '/wp-content/uploads/2025/05/header2dark-1.jpg',
            '/wp-content/uploads/2025/05/header1-1.jpg'
        );

        $imagen_aleatoria = $imagenes[array_rand($imagenes)];
		
		console_log("Front page image: ". $imagen_aleatoria );

        echo '<style type="text/css">
            .home-header-image {
                background-image: url("' . esc_url($imagen_aleatoria) . '") !important;
            }
        </style>';
    }
});

/*====================================================
#Mostrar título del padre en páginas hijas - Shortcode
=======================================================*/
function mostrar_titulo_padre_shortcode() {
    $parent_id = wp_get_post_parent_id( get_the_ID() );
    if ( $parent_id ) {
        return get_the_title( $parent_id );
    }
    return ''; // No devuelve nada si no tiene padre
}
add_shortcode( 'titulo_padre', 'mostrar_titulo_padre_shortcode' );

/*================================================
#CONSOLE LOG FUNCTION
================================================*/
function console_log( $data ){
    echo '<script>';
    echo 'console.log('. json_encode( $data ) .')';
    echo '</script>';
  }

/*================================================
#Is Page Child Of Function
================================================*/
function is_page_child_of($parent_slug) {
    global $post;

    if ( !has_post_parent( $post ) ) {
        console_log( 'No parent' );
        return false;
    }

    // if ( is_home() || is_front_page() ) {
    //     console_log( 'home' );
    //     return false;
    // }
    $parent = get_post_parent( $post );
    $slug = $parent->post_name;     
    if ($slug === $parent_slug) {
        console_log( 'true' );
        return true;
    }else{
        console_log( 'false' );
        return false;
    }
}

/*================================================
#remove WP Version Info
================================================*/
remove_action('wp_head', 'wp_generator');

/*================================================
#remove Widget Block Editor Support
================================================*/
function cimne_theme_support() {
    remove_theme_support( 'widgets-block-editor');
}
add_action( 'after_setup_theme', 'cimne_theme_support');

/*================================================
#Custom Login Page
================================================*/
function my_login_page() {
    wp_enqueue_style( 'login-custom-style', get_bloginfo('stylesheet_directory'). '/login.css', array('login') );
    }
    
    add_action( 'login_enqueue_scripts', 'my_login_page' );
    
    /* Change the Login Logo URL */
    function my_login_logo_url() {
        return get_bloginfo( 'url' );
    }
    add_filter( 'login_headerurl', 'my_login_logo_url' );
    
    function my_login_logo_url_title() {
        return get_bloginfo( 'name' ) . ' | ' . get_bloginfo( 'description' );
    }
    add_filter( 'login_headertitle', 'my_login_logo_url_title' );
    
    /*Hide the Login Error Message*/
    function login_error_override()
    {
        return __( 'Incorrect login details.', 'targetimc' );
    }
    add_filter('login_errors', 'login_error_override');
    
    /*Change the Redirect URL */
    function admin_login_redirect( $redirect_to, $request, $user ) {
        global $user;
            if( isset( $user->roles ) && is_array( $user->roles ) ) {
                if( in_array( "administrator", $user->roles ) ) {
                    return $redirect_to;
                } else {
                    return home_url();
                }
            }
            else
            {
            return $redirect_to;
            }
    }
    add_filter("login_redirect", "admin_login_redirect", 10, 3);
    
// END OF CUSTOM LOGIN PAGE

/*================================================
#List Filter Shortcode for News and Events
#Usage: [filtro_eventos_noticias id="X"]
#Where X is the parent category ID
#Example: [filtro_eventos_noticias id="5"]
#Or for thesis pages: [filtro_eventos_noticias]
================================================*/
function cimne_filtro_eventos_noticias($parent, $content = null) {
    // Get current locale and debug info
    $idioma_actual = get_locale();
    console_log($idioma_actual);
    console_log($parent);

    // Determine locale once and set translated strings (supports en/es/ca with locale prefixes)
    $locale = $idioma_actual;
    // Default English
    $text_type_something = 'Type something';
    $text_from = 'From';
    $text_to = 'To';
    if ( strpos( $locale, 'es' ) === 0 ) {
        $text_type_something = 'Escribe algo';
        $text_from = 'Desde';
        $text_to = 'Hasta';
    } elseif ( strpos( $locale, 'ca' ) === 0 ) {
        $text_type_something = 'Escriu alguna cosa';
        $text_from = 'Des de';
        $text_to = 'Fins a';
    }

    // Special case: thesis pages
    if ( is_page( 'phd-theses' ) || is_page( 'tesis-doctorales') || is_page( 'tesis-doctorals' )) {
        $result = '<form class="thesis-form "><div class="form-group"><input id="searchBox" class="form-control" type="text" pattern="" maxlength="70" placeholder="'. esc_attr( $text_type_something ) .'"></div>';
        $result .=  ' <div class="form-group form-flex-fields">';
        $result .= '<div class="from">';
        $result .= '<input type="text" id="start-date" placeholder="'. esc_attr( $text_from ) .'" onfocus="this.type=\'date\'" onblur="this.type=\'text\'"  onchange="console.log(this.value)">';
        $result .= '</div>';
        $result .= '<div class="to">';
        $result .= '<input type="text" id="end-date" placeholder="'. esc_attr( $text_to ) .'" onfocus="this.type=\'date\'" onblur="this.type=\'text\'"  onchange="console.log(this.value)">';
        $result .= '</div></div>';
        $result .= '</form>';

        return $result;
    }

    // Fallback: category-based form
    $parentCategory = shortcode_atts( array (
        'id' => '0'
        ), $parent );

    console_log("Parent Category: " . $parentCategory['id']);

    $result = '<form class="'.strtolower(get_the_category_by_ID($parentCategory['id'])).'-form "><div class="form-group"><input id="search-box" class="form-control" type="text" pattern="" maxlength="70" placeholder="'. esc_attr( $text_type_something ) .'"></div>';

    $args = array(
        'taxonomy'    => "category",
        'parent'    => $parentCategory['id'],
        'hide_empty' => 0
    );

    $categories = get_categories($args);

    console_log($categories);
    

    $result .= '<div class="form-group">';
    $result .= '<select id="publication" class="form-control">';
    $result .= ' <option value="" class="dropdown-header">'.get_the_category_by_ID($parentCategory['id']).'</option>';

    foreach($categories as $category){
        $result .= '<option value="'.$category->name .'">'. $category->name .'</option>';    
    }
    $result .= '</select></div>';
    $result .=  ' <div class="form-group form-flex-fields">';
    $result .= '<div class="from">';
    $result .= '<input type="text" id="start-date" placeholder="'. esc_attr( $text_from ) .'" onfocus="this.type=\'date\'" onblur="this.type=\'text\'"  onchange="console.log(this.value)">';
    $result .= '</div>';
    $result .= '<div class="to">';
    $result .= '<input type="text" id="end-date" placeholder="'. esc_attr( $text_to ) .'" onfocus="this.type=\'date\'" onblur="this.type=\'text\'"  onchange="console.log(this.value)">';
    $result .= '</div></div>';
    $result .= '</form>';
 
    return $result;

}

add_shortcode('filtro_eventos_noticias', 'cimne_filtro_eventos_noticias');


/*================================================
#Insert JS Files
================================================*/
function cimne_insertar_js(){

    wp_enqueue_script(
        'cimne_main_scripts', 
        get_stylesheet_directory_uri(). '/js/functions.js', 
        array('jquery'), 
        '1.0',
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );
    
    wp_enqueue_script(
        'cimne_translate', 
        get_stylesheet_directory_uri(). '/js/locale.js',
        array(), '1.0',
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );

    wp_enqueue_script(
        'cimne_views_scripts', 
        get_stylesheet_directory_uri(). '/js/views.js', 
        array(), 
        '1.0', 
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );

    wp_enqueue_script(
        'cimne_services_scripts', 
        get_stylesheet_directory_uri(). '/js/services.js', 
        array(), 
        '1.0', 
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );

    wp_enqueue_script(
        'cimne_crypt', 
        get_stylesheet_directory_uri(). '/js/crypt.js', 
        array(), 
        '1.0', 
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );

    wp_enqueue_script(
        'cimne_slick', 
        get_stylesheet_directory_uri(). '/js/slick.min.js', 
        array(), 
        '1.0', 
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );


    wp_enqueue_script(
        'cimne_data_from_gdrive', 
        get_stylesheet_directory_uri(). '/js/gsheets_data.js', 
        array(), 
        '1.0', 
        array( 'strategy' => 'defer',
                'in_footer' => true
        )
    );
}
add_action('wp_enqueue_scripts', 'cimne_insertar_js');




/*=====================================================================================
#Cambiar el orden de las entradas en el módulo de blog de Divi (solo en categoría 'events')
#Ordenar por campo personalizado 'date' (numérico, formato YYYYMMDD)
#Orden descendente (más reciente primero)
======================================================================================*/

// Para la búsqueda general
function date_search_filter($query) {

    if ( ! is_admin() && $query->is_main_query() ) {

        if ($query->is_search) {
        //console_log('Ording search results by date');

            $query->set( 'orderby', 'post_date');

        }
    }

}
add_action( 'pre_get_posts', 'date_search_filter' );

// Para la pagina de eventos
function change_query_order( $query ) {

    // Evitar modificaciones en el admin y asegurarse de la consulta principal
    if ( is_admin() || ! $query->is_main_query() ) {
        return;
    }

    // Aplicar solo cuando se está viendo la categoría with slug 'events'
    if ( $query->is_category( 'events' ) || $query->is_category( 'eventos' ) || $query->is_category( 'esdeveniments' ) ) {
        // Ordenar por el metadato 'date' de forma numérica ascendente
        //console_log('Modifying query order for events category');
        $query->set( 'meta_key', 'date' );
        $query->set( 'orderby', 'meta_value_num' );
        $query->set( 'order', 'DESC' );
    }
}
add_action( 'pre_get_posts', 'change_query_order' );

// Para el módulo de blog de la página de inicio
function custom_blog_filter_and_order($query, $args) {

    // --- PARÁMETROS DE CONFIGURACIÓN ---
    $meta_key_orden = 'date'; // Reemplaza con el campo que quieres usar para ORDENAR (ej: precio, prioridad)
    $meta_key_fecha = 'date';    // Reemplaza con el campo que contiene la FECHA (importante: debe estar en formato YYYY-MM-DD)
    $orden_ascendente = 'ASC';             // O 'DESC' para descendente
    $orden_descendente = 'DESC';           // O 'ASC' para descendente
    $tipo_de_meta = 'meta_value_num';         // 'meta_value_num' si es un número, 'meta_value' si es texto

    // --- LÓGICA DE FILTRADO DE FECHA ---
    // Obtener la fecha actual en formato YYYY-MM-DD
    $today = date('Y-m-d');

    // Definir la consulta de metadatos (meta_query)
    $meta_query_home_events = array(
        array(
            'key' => $meta_key_fecha, // El campo de fecha que quieres comparar
            'value' => $today,
            'compare' => '>=',        // Muestra solo los posts con fecha mayor o igual a hoy
            'type' => 'DATE',         // Le dice a WP que compare como fechas
        ),
    );
    $meta_query_events_upcoming_events = array(
        array(
            'key' => $meta_key_fecha, // El campo de fecha que quieres comparar
            'value' => $today,
            'compare' => '>=',        // Muestra solo los posts con fecha mayor o igual a hoy
            'type' => 'DATE',         // Le dice a WP que compare como fechas
        ),
    );
    $meta_query_events_past_events = array(
        array(
            'key' => $meta_key_fecha, // El campo de fecha que quieres comparar
            'value' => $today,
            'compare' => '<',        // Muestra solo los posts con fecha mayor o igual a hoy
            'type' => 'DATE',         // Le dice a WP que compare como fechas
        ),
    );

    if (isset($args['module_id']) && ($args['module_id'] === 'home-upcoming-events-left' || $args['module_id'] === 'home-upcoming-events-right')) {

        console_log('Custom Blog Module - Upcoming Events: ' . $args['module_id']);

        // --- APLICAR ORDENACIÓN Y FILTRADO ---
        // Aplicar los nuevos argumentos a la consulta
        $query->query_vars['meta_query'] = $meta_query_home_events;
        $query->query_vars['orderby'] = $tipo_de_meta;
        $query->query_vars['meta_key'] = $meta_key_orden;
        $query->query_vars['order'] = $orden_ascendente;

        // Crear y devolver la nueva consulta
        $query = new WP_Query($query->query_vars);
    }

    if (isset($args['module_id']) && $args['module_id'] === 'events-upcoming-events') {

        console_log('Custom Blog Module - Upcoming Events: ' . $args['module_id']);

        // --- APLICAR ORDENACIÓN Y FILTRADO ---
        // Aplicar los nuevos argumentos a la consulta
        $query->query_vars['meta_query'] = $meta_query_events_upcoming_events;
        $query->query_vars['orderby'] = $tipo_de_meta;
        $query->query_vars['meta_key'] = $meta_key_orden;
        $query->query_vars['order'] = $orden_ascendente;

        // Crear y devolver la nueva consulta
        $query = new WP_Query($query->query_vars);
    }

    if (isset($args['module_id']) && $args['module_id'] === 'events-past-events') {

        console_log('Custom Blog Module - Past Events: ' . $args['module_id']);

        // --- APLICAR ORDENACIÓN Y FILTRADO ---
        // Aplicar los nuevos argumentos a la consulta
        $query->query_vars['meta_query'] = $meta_query_events_past_events;
        $query->query_vars['orderby'] = $tipo_de_meta;
        $query->query_vars['meta_key'] = $meta_key_orden;
        $query->query_vars['order'] = $orden_descendente;

        // Crear y devolver la nueva consulta
        $query = new WP_Query($query->query_vars);
    }
    
    return $query;
}


add_filter('et_builder_blog_query', 'custom_blog_filter_and_order', 10, 2);

/*=====================================================================================
#Establece la primera imagen del contenido como destacada si no hay una destacada

 No FUNCIONA BIEN, revisar
======================================================================================*/
// function auto_featured_image() {
//     if ( !is_singular() ) {
//         return;
//     }
//     if ( !has_post_thumbnail() ) {
//         global $post;
//         $attachment = get_posts( array(
//             'post_type' => 'attachment',
//             'posts_per_page' => 1,
//             'post_mime_type' => 'image',
//             'post_parent' => $post->ID
//         ) );
//         if ( !empty( $attachment ) ) {
//             set_post_thumbnail( $post, $attachment[0]->ID );
//         }
//     }
// }
// add_action( 'the_post', 'auto_featured_image' );


/*=====================================================================================
# Crear archivo HTML con formato email
# Abre un lienzo con opciones para enviar por email
# Opciones para abrir cliente de email, copiar al portapapeles o descargar el codigo HTML
# Incluye imagen destacada si existe
# Usa estilos inline y estructura en tablas para compatibilidad con clientes de email
======================================================================================*/

/* ============================================================
 * Helpers
 * ============================================================ */

function cimne_email_log($message) {
    if (defined('WP_DEBUG') && WP_DEBUG) {
        error_log('[CIMNE Email] ' . $message);
    }
}

function cimne_get_request_lang() {
    return isset($_GET['lang']) ? sanitize_text_field(wp_unslash($_GET['lang'])) : 'en';
}

function cimne_get_email_strings($locale) {
    if (strpos($locale, 'es') === 0) {
        return [
            'button' => 'Leer más',
            'cannot_read' => 'Si no puedes leer este correo, haz clic',
            'here' => 'aquí',
            'press_release' => 'Nota de prensa',
            'send_email' => 'Send Email',
        ];
    }

    if (strpos($locale, 'ca') === 0) {
        return [
            'button' => 'Llegir més',
            'cannot_read' => 'Si no pots llegir aquest correu, fes clic',
            'here' => 'aquí',
            'press_release' => 'Nota de premsa',
            'send_email' => 'Send Email',
        ];
    }

    return [
        'button' => 'Read more',
        'cannot_read' => 'If you cannot read this email, click',
        'here' => 'here',
        'press_release' => 'Press release',
        'send_email' => 'Send Email',
    ];
}

function cimne_get_event_labels($locale) {
    if (strpos($locale, 'es') === 0) {
        return [
            'fields' => [
                'date' => 'Fecha',
                'time' => 'Hora',
                'place' => 'Lugar',
                'online_streaming' => 'Transmisión en línea',
                'registration' => 'Registro',
            ],
            'days' => ['Domingo', 'Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'],
            'months' => ['Enero', 'Febrero', 'Marzo', 'Abril', 'Mayo', 'Junio', 'Julio', 'Agosto', 'Septiembre', 'Octubre', 'Noviembre', 'Diciembre'],
        ];
    }

    if (strpos($locale, 'ca') === 0) {
        return [
            'fields' => [
                'date' => 'Data',
                'time' => 'Hora',
                'place' => 'Lloc',
                'online_streaming' => 'Transmissió en línia',
                'registration' => 'Registre',
            ],
            'days' => ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'],
            'months' => ['Gener', 'Febrer', 'Març', 'Abril', 'Maig', 'Juny', 'Juliol', 'Agost', 'Setembre', 'Octubre', 'Novembre', 'Desembre'],
        ];
    }

    return [
        'fields' => [
            'date' => 'Date',
            'time' => 'Time',
            'place' => 'Place',
            'online_streaming' => 'Online streaming',
            'registration' => 'Registration',
        ],
        'days' => ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'],
        'months' => ['January', 'February', 'March', 'April', 'May', 'June', 'July', 'August', 'September', 'October', 'November', 'December'],
    ];
}

function cimne_sanitize_email_color($color, $fallback = '#0057b8') {
    $color = sanitize_hex_color($color);
    return $color ?: $fallback;
}

function cimne_get_icon_color($corporate_color) {
    return preg_replace('/[^a-fA-F0-9]/', '', str_replace('#', '', $corporate_color));
}

/* ============================================================
 * Tipo de entrada / evento
 * ============================================================ */

function cimne_get_post_types_for_email($post_ID) {
    $categories = get_the_category($post_ID);

    $event_type = '';
    $entry_type = '';

    foreach ($categories as $category) {
        $parent = $category->parent ? get_category($category->parent) : null;

        if ($parent && in_array($parent->slug, ['events', 'eventos', 'esdeveniments'], true)) {
            $event_type = $category->name;
            break;
        }

        if (!$entry_type) {
            $entry_type = $category->name;
        }
    }

    return [
        'event_type' => $event_type,
        'entry_type' => $entry_type,
    ];
}

function cimne_get_email_theme($event_type, $entry_type) {
    $type_key = strtolower(trim($event_type ?: $entry_type));

    $theme = [
        'corporate_color' => '#0057b8',
        'corporate_bg_color' => '#d9e8f8',
        'header_image' => 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_default.jpg',
    ];

    switch ($type_key) {
        case 'coffee talk':
        case 'internal coffee talks':
            $theme['corporate_color'] = '#02a0a5';
            $theme['corporate_bg_color'] = '#d9f2f3';
            $theme['header_image'] = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_coffee_talks_md.jpg';
            break;

        case 'seminars':
        case 'seminaris':
        case 'seminarios':
            $theme['corporate_color'] = '#f3921a';
            $theme['corporate_bg_color'] = '#fdebd1';
            $theme['header_image'] = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_seminars_md.jpg';
            break;

        case 'thesis defense':
        case 'defensa de tesi':
        case 'defensa de tesis':
            $theme['corporate_color'] = '#004996';
            $theme['corporate_bg_color'] = '#d9e4f2';
            $theme['header_image'] = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_tesis_md_rec.jpg';
            break;

        case 'congress':
        case 'congrés':
        case 'congreso':
            $theme['corporate_bg_color'] = '#F8E7DF';
            break;
    }

    $theme['corporate_color'] = cimne_sanitize_email_color($theme['corporate_color']);

    return $theme;
}

/* ============================================================
 * Campos de evento
 * ============================================================ */

function cimne_format_event_date($post_ID, $locale) {
    $labels = cimne_get_event_labels($locale);

    $date = get_post_meta($post_ID, 'date', true);
    $date_obj = DateTime::createFromFormat('Ymd', $date);

    if (!$date_obj) {
        return esc_html($date);
    }

    $day_name = $labels['days'][(int) $date_obj->format('w')];
    $day_number = $date_obj->format('d');
    $month_name = $labels['months'][(int) $date_obj->format('n') - 1];
    $year = $date_obj->format('Y');

    return esc_html("{$day_name}, {$day_number} {$month_name} {$year}");
}

function cimne_render_event_row($icon, $alt, $content, $corporate_color) {
    $icon_color = cimne_get_icon_color($corporate_color);
    $icon_url = esc_url("https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/{$icon}{$icon_color}.png");

    return "
        <tr>
            <td style='width:10%;text-align:center;padding:5px 15px;'>
                <img src='{$icon_url}' alt='" . esc_attr($alt) . "' style='width:22px;height:22px;vertical-align:bottom;' />
            </td>
            <td style='padding:5px 15px;font-size:14px;color:#333333;font-weight:bold;'>
                {$content}
            </td>
        </tr>";
}

function cimne_render_event_custom_fields_email($post_ID, $locale, $corporate_color = '#0057b8') {
    $labels = cimne_get_event_labels($locale);
    $fields = $labels['fields'];

    $formatted_date = cimne_format_event_date($post_ID, $locale);

    $time = esc_html(get_post_meta($post_ID, 'time', true));
    $time = preg_replace('/:\d{2}$/', '', $time);

    $place = esc_html(get_post_meta($post_ID, 'place', true));
    $online_streaming = get_post_meta($post_ID, 'online_streaming', true);
    $registration = get_post_meta($post_ID, 'registration', true);

    $rows = '';

    if ($formatted_date) {
        $rows .= cimne_render_event_row('icon_calendar', 'Date', $formatted_date, $corporate_color);
    }

    if ($time) {
        $rows .= cimne_render_event_row('icon_clock', 'Time', $time, $corporate_color);
    }

    if ($place) {
        $rows .= cimne_render_event_row('icon_pin_alt', 'Place', $place, $corporate_color);
    }

    if (!empty($online_streaming)) {
        $content = "<a href='" . esc_url($online_streaming) . "' target='_blank' style='color:#0057b8;text-decoration:none;font-weight:bold;'>" .
            esc_html($fields['online_streaming']) .
        '</a>';

        $rows .= cimne_render_event_row('icon_laptop', 'Online streaming', $content, $corporate_color);
    }

    if (!empty($registration)) {
        $content = "<a href='" . esc_url($registration) . "' target='_blank' style='color:#0057b8;text-decoration:none;font-weight:bold;'>" .
            esc_html($fields['registration']) .
        '</a>';

        $rows .= cimne_render_event_row('icon_pencil-edit', 'Registration', $content, $corporate_color);
    }

    if (!$rows) {
        return '';
    }

    return "
        <tr>
            <td>
                <table width='100%' cellpadding='0' cellspacing='0' border='0' style='margin-bottom:30px;border:2px solid " . esc_attr($corporate_color) . ";vertical-align:bottom;'>
                        {$rows}
                </table>
            </td>
        </tr>";
}

/* ============================================================
 * Limpieza contenido Divi
 * ============================================================ */

function cimne_clean_divi_html_for_email($html, $corporate_bg_color = '#d9e8f8', $entry_type = 'default') {
    $html = apply_filters('the_content', $html);
    $html = do_shortcode($html);
    $html = wpautop($html);

    $html = preg_replace('/\[\/?et_pb_[^\]]*\]/', '', $html);
    $html = preg_replace('/<!--(.*?)-->/s', '', $html);

    $html = preg_replace_callback(
        '/<iframe[^>]*src=["\']([^"\']+)["\'][^>]*><\/iframe>/i',
        function ($matches) {
            $src = esc_url($matches[1]);
            return '<p><a href="' . $src . '" target="_blank" style="color:#0057b8;text-decoration:none;font-weight:bold;">Video</a></p>';
        },
        $html
    );

    $html = preg_replace('/<div[^>]*class="[^"]*et_pb_[^"]*"[^>]*>/i', '<div>', $html);
    $html = preg_replace('/\s(class|id|data-[^=]*|aria-[^=]*|role)="[^"]*"/i', '', $html);
    $html = preg_replace('/<div[^>]*>/i', '<div>', $html);
    $html = preg_replace('/<div>\s*<\/div>/', '', $html);

    $html = str_replace(['<div>', '</div>'], ['<p>', '</p>'], $html);
    $html = preg_replace('/<p>\s*<p>/', '<p>', $html);
    $html = preg_replace('/<\/p>\s*<\/p>/', '</p>', $html);

    if (strtolower($entry_type) === 'newsletter') {
        $replacements = [
            '/<p>/' => '<p style="font-size:14px;line-height:1.6;color:#222222;margin:0 0 18px 0;">',
            '/<h1>/' => '<h1 style="font-size:26px;color:#0057b8;margin-bottom:22px;">',
            '/<h2>/' => '<h2 style="font-size:22px;color:#fff;background-color:#0057b8;padding:20px 0 5px 12px;">',
            '/<h3>/' => '<h3 style="font-size:18px;color:#f3921a;margin:20px 0 14px;">',
            '/<h4>/' => '<h4 style="font-size:16px;color:#0057b8;margin:18px 0 12px;">',
            '/<h5>/' => '<h5 style="font-size:16px;color:#0057b8;margin:16px 0 10px;">',
            '/<a(?![^>]*style=)/' => '<a style="color:#02A0A5;"',
        ];
    } else {
        $replacements = [
            '/<p>/' => '<p style="font-size:14px;line-height:1.7;color:#333333;margin:0 0 20px 0;">',
            '/<h1>/' => '<h1 style="font-size:26px;color:#1a1a1a;margin-bottom:20px;">',
            '/<h2>/' => '<h2 style="font-size:22px;color:#1a1a1a;margin:25px 0 15px;">',
            '/<h3>/' => '<h3 style="font-size:18px;color:#1a1a1a;margin:20px 0 10px;">',
            '/<h4>/' => '<h4 style="font-size:16px;color:#1a1a1a;margin:20px 0 10px;">',
            '/<h5>/' => '<h5 style="font-size:16px;color:#1a1a1a;margin:20px 0 10px;">',
        ];
    }

    foreach ($replacements as $pattern => $replacement) {
        $html = preg_replace($pattern, $replacement, $html);
    }

    // Ajusta las etiquetas <img> del HTML para que sean compatibles con email.
    $html = preg_replace_callback(
        '/<img([^>]*?)>/i',
        function ($matches) {
            // Contenido dentro de la etiqueta img sin el nombre de la etiqueta.
            $img_tag = $matches[1];
            // Estilo por defecto para imágenes en email.
            $style = 'max-width:100%;height:auto;display:block;margin:20px 0;';
            $set_fixed_width = false;
            $original_width = null;
            $original_height = null;

            // Detecta ancho original y decide si forzar ancho fijo a 600px.
            if (preg_match('/\bwidth\s*=\s*("|\')?(\d+)(?:\1)?/i', $img_tag, $width_match)) {
                $original_width = (int) $width_match[2];
                $set_fixed_width = $original_width > 600;
            }

            // Detecta altura original si está presente.
            if (preg_match('/\bheight\s*=\s*(?:"([^"]*)"|\'([^\']*)\'|([^\s>]+))/i', $img_tag, $height_match)) {
                $original_height = $height_match[1] ?? $height_match[2] ?? $height_match[3];
            }

            // Elimina los atributos width/height originales para evitar conflictos.
            $img_tag = preg_replace('/\s*(width|height)\s*=\s*("|\')[^\2]*?\2/i', '', $img_tag);
            $img_tag = trim($img_tag);

            // Añade o reemplaza el estilo existente con el estilo por defecto.
            if (preg_match('/style="([^"]*)"/i', $img_tag, $style_match)) {
                $img_tag = preg_replace('/style="[^"]*"/i', 'style="' . esc_attr($style_match[1] . $style) . '"', $img_tag);
            } else {
                $img_tag .= ' style="' . esc_attr($style) . '"';
            }

            // Si el ancho original es mayor de 600px, forzamos un ancho fijo de 600px.
            if ($set_fixed_width) {
                $img_tag .= ' width="600" height="auto"';
            } elseif ($original_width !== null) {
                // Si no se fuerza ancho fijo, restauramos las dimensiones originales.
                $img_tag .= ' width="' . esc_attr($original_width) . '"';
                if ($original_height !== null) {
                    $img_tag .= ' height="' . esc_attr($original_height) . '"';
                }
            }

            return '<img ' . $img_tag . ' />';
        },
        $html
    );

    // Imágenes optimizadas para email
    // $html = preg_replace_callback(
    //     '/<img([^>]*?)>/i',
    //     function($matches) {
    //         $img_tag = $matches[1];
    //         $style = 'max-width:100%;height:auto;display:block;margin:20px 0;';
    //         if (preg_match('/style="([^"]*)"/i', $img_tag, $style_match)) {
    //             $img_tag = preg_replace('/style="[^"]*"/i', 'style="' . $style_match[1] . $style . '"', $img_tag);
    //         } else {
    //             $img_tag .= ' style="' . $style . '"';
    //         }
    //         return '<img' . $img_tag . ' />';
    //     },
    //     $html
    // );
    return trim($html);
    // return trim(wp_kses_post($html));
}

/* ============================================================
 * Header y botones
 * ============================================================ */

function cimne_render_event_header($post, $header_image) {
    return '
        <tr>
            <td class="title" style="font-size:28px;font-weight:bold;margin-bottom:20px;line-height:1.3;">
                <div class="contenedor-imagen" style="position:relative;display:inline-block;overflow:hidden;">
                    <img src="' . esc_url($header_image) . '" style="display:block;width:100%;height:auto;" />
                    <div class="texto-superpuesto" style="position:absolute;top:30%;padding:15px;">
                        <h1 style="margin:0;font-size:24px;line-height:1;">' . esc_html($post->post_title) . '</h1>
                    </div>
                </div>
            </td>
        </tr>';
}

function cimne_render_default_header($post, $entry_type, $strings, $corporate_color) {
    $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_nota_de_premsa_md.jpg';
    $header_logo = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/logo-color-cimne-web-sm.png';

    $is_newsletter = strtolower($entry_type) === 'newsletter';
    console_log('Entry type: ' . $entry_type . ' | Is newsletter: ' . ($is_newsletter ? 'Yes' : 'No'));
    $header_title = $is_newsletter ? $post->post_title : $strings['press_release'];
    $post_title = $is_newsletter ? '' : $post->post_title;
    $post_title_row = !$is_newsletter
        ? '
        <tr>
            <td style="font-size:28px;font-weight:bold;color:' . esc_attr($corporate_color) . ';margin-bottom:20px;line-height:1.3;">
                ' . esc_html($post_title) . '
            </td>
        </tr>'
        : '';

    return '
        <tr>
            <td style="font-size:28px;font-weight:bold;margin-bottom:20px;line-height:1.3;">
                <img width="600" height="auto" src="' . esc_url($header_image) . '" style="display:block;margin-bottom:30px;max-width:100%;height:auto;" />
            </td>
        </tr>
        <tr>
            <td style="padding-bottom:20px;">
                <table width="100%" cellpadding="0" cellspacing="0" border="0">
                    <tr>
                        <td width="180" valign="top">
                            <img width="180" height="auto" src="' . esc_url($header_logo) . '" style="display:block;max-width:100%;height:auto;" />
                        </td>
                        <td width="20"></td>
                        <td valign="top" style="font-size:22px;font-weight:bold;color:#000;line-height:1.3;border-bottom:2px solid #000;padding-bottom:5px;">
                            ' . esc_html($header_title) . '
                        </td>
                    </tr>
                </table>
            </td>
        </tr>' . $post_title_row;
}

function cimne_render_read_more_button($url, $text, $corporate_color) {
    return '
        <table align="center" cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td align="center" style="border:2px solid ' . esc_attr($corporate_color) . ';">
                    <a href="' . esc_url($url) . '" target="_blank"
                       style="display:inline-block;padding:15px 32px;font-size:17px;color:' . esc_attr($corporate_color) . ';text-decoration:none;font-weight:bold;font-family:Arial,sans-serif;">
                        ' . esc_html($text) . '
                    </a>
                </td>
            </tr>
        </table>';
}

function cimne_render_newsletter_previous_editions($post_ID, $corporate_bg_color, $entry_type) {
    $previous = get_post_meta($post_ID, 'previous_editions', true);

    if (!$previous) {
        return '';
    }

    $previous_html = cimne_clean_divi_html_for_email($previous, $corporate_bg_color, $entry_type);

    return '
        <table cellpadding="0" cellspacing="0" border="0">
            <tr>
                <td style="border:2px solid #f3921a;background-color:#f3921a26;padding:10px 20px;">
                    ' . $previous_html . '
                </td>
            </tr>
        </table>';
}

/* ============================================================
 * Footer
 * ============================================================ */

function cimne_render_email_footer($corporate_color) {
    return "
        <table style='table-layout:fixed;background-color:" . esc_attr($corporate_color) . "' cellspacing='0' cellpadding='0' width='650'>
            <tbody>
                <tr height='20'><td width='20'><br></td><td width='600'><br></td><td width='20'><br></td></tr>
                <tr>
                    <td width='20'><br></td>
                    <td>
                        <table style='table-layout:fixed;' cellspacing='0' cellpadding='0'>
                            <tbody>
                                <tr>
                                    <td></td><td></td>
                                    <td><p style='color:#ffffff;font-weight:bold;'><font face='Arial'>About CIMNE</font></p></td>
                                    <td></td>
                                    <td><p style='color:#ffffff;font-weight:bold;'><font face='Arial'>Contact</font></p></td>
                                </tr>
                                <tr>
                                    <td width='90'>
                                        <img src='https://web.cimne.upc.edu/groups/publicacions/mails/2025/seminars/img/logo-blanco-cimne.png' width='90' alt='CIMNE'>
                                    </td>
                                    <td width='10'></td>
                                    <td width='330'>
                                        <p style='color:#ffffff;'><font face='Arial'>CIMNE is a public R+D centre in computational engineering with a strong focus on knowledge transfer.</font></p>
                                    </td>
                                    <td width='10'></td>
                                    <td width='160'>
                                        <p style='color:#ffffff;'><font face='Arial'>+34 93 401 74 95<br>CIMNE Building C1<br>Campus Nord UPC<br>C/ Gran Capità S/N<br>08034 Barcelona, Spain</font></p>
                                    </td>
                                </tr>
                                <tr>
                                    <td style='color:#ffffff;'>Follow us on</td>
                                    <td></td>
                                    <td>
                                        <a href='https://www.facebook.com/cimne' target='_blank'><img src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-facebook.png' alt='facebook'></a>
                                        <a href='https://www.linkedin.com/company/cimne' target='_blank'><img src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-linkedin.png' alt='linkedin'></a>
                                        <a href='https://twitter.com/cimne' target='_blank'><img src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-twitter.png' alt='twitter'></a>
                                        <a href='https://www.youtube.com/cimneMC' target='_blank'><img src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-youtube.png' alt='youtube'></a>
                                    </td>
                                    <td></td><td></td>
                                </tr>
                                <tr>
                                    <td></td><td></td>
                                    <td style='color:#ffffff;'>
                                        <a href='mailto:cimne@cimne.upc.edu' style='color:#ffffff;'>cimne@cimne.upc.edu</a>
                                        | <a href='http://www.cimne.com' style='color:#ffffff;'>www.cimne.com</a>
                                    </td>
                                    <td></td>
                                    <td style='color:#ffffff;'>Copyright © 2026 CIMNE.<br>All rights reserved.</td>
                                </tr>
                            </tbody>
                        </table>
                    </td>
                    <td width='20'><br></td>
                </tr>
                <tr height='20'><td width='20'><br></td><td width='600'><br></td><td width='20'><br></td></tr>
            </tbody>
        </table>";
}

/* ============================================================
 * Construcción del email
 * ============================================================ */

function cimne_build_email_html($post_ID) {
    $post = get_post($post_ID);

    if (!$post) {
        return '';
    }

    $locale = cimne_get_request_lang();
    $strings = cimne_get_email_strings($locale);

    $types = cimne_get_post_types_for_email($post_ID);
    $event_type = $types['event_type'];
    $entry_type = $types['entry_type'];

    $is_event = !empty($event_type) || strtolower($entry_type) === 'internal coffee talks';

    $theme = cimne_get_email_theme($event_type, $entry_type);
    $corporate_color = $theme['corporate_color'];
    $corporate_bg_color = $theme['corporate_bg_color'];

    $url_post = get_permalink($post_ID);

    if ($is_event) {
        $header_html = cimne_render_event_header($post, $theme['header_image']);
        $event_fields_html = cimne_render_event_custom_fields_email($post_ID, $locale, $corporate_color);
        $button_html = cimne_render_read_more_button($url_post, $strings['button'], $corporate_color);
    } else {
        $header_html = cimne_render_default_header($post, $entry_type, $strings, $corporate_color);
        $event_fields_html = '';
        $button_html = strtolower($entry_type) === 'newsletter'
            ? cimne_render_newsletter_previous_editions($post_ID, $corporate_bg_color, $entry_type)
            : '';
    }

    $content_html = cimne_clean_divi_html_for_email($post->post_content, $corporate_bg_color, $entry_type);
    $footer_html = cimne_render_email_footer($corporate_color);

    return "
<!DOCTYPE html>
<html>
<head>
    <meta charset='UTF-8' />
    <meta name='viewport' content='width=device-width, initial-scale=1.0' />
    <title>" . esc_html($post->post_title) . "</title>
</head>
<body style='margin:0;padding:0;background-color:#eef1f4;font-family:Arial,sans-serif;'>

<table style='width:100%;padding:40px 0;background-color:#eef1f4;' cellpadding='0' cellspacing='0' border='0'>
    <tr>
        <td align='center'>
            <table style='background-color:#ffffff;padding:40px;' width='650' cellpadding='0' cellspacing='0' border='0'>
                <!-- if you can not read this email element -->
                <tr>
                    <td style='font-size:28px;font-weight:bold;color:" . esc_attr($corporate_color) . ";margin-bottom:20px;line-height:1.3;'>
                        <p style='font-size:14px;color:#999999;margin-bottom:30px;'>
                            " . esc_html($strings['cannot_read']) . "
                            <a href='" . esc_url($url_post) . "' target='_blank' style='color:#0057b8;text-decoration:none;font-weight:bold;'>
                                " . esc_html($strings['here']) . "
                            </a>.
                        </p>
                    </td>
                </tr>
                <!-- header -->
                {$header_html}
                <!-- event custom fields (date, time, place...) -->
                {$event_fields_html}
                <!-- content -->
                <tr>
                    <td style='font-size:14px;line-height:1.7;color:#333333;padding-bottom:35px;'>
                        {$content_html}
                    </td>
                </tr>
                <!-- read more button -->
                <tr>
                    <td align='center' style='padding-bottom:40px;'>
                        {$button_html}
                    </td>
                </tr>
            </table>
            <!-- footer -->
            {$footer_html}
        </td>
    </tr>
</table>

</body>
</html>";
}

/* ============================================================
 * Acción en listado admin
 * ============================================================ */

add_filter('post_row_actions', 'cimne_add_send_email_row_action', 10, 2);

function cimne_add_send_email_row_action($actions, $post) {
    if ($post->post_type !== 'post') {
        return $actions;
    }

    if (!current_user_can('edit_post', $post->ID)) {
        return $actions;
    }

    $lang = cimne_get_request_lang();

    $url = add_query_arg([
        'action' => 'send_post_email',
        'lang' => $lang,
        'post_id' => $post->ID,
    ], admin_url('admin-post.php'));

    $url = wp_nonce_url($url, 'send_post_email_' . $post->ID);

    $actions['send_email'] = '<a href="' . esc_url($url) . '">Send Email</a>';

    return $actions;
}

/* ============================================================
 * Pantalla de envío
 * ============================================================ */

add_action('admin_post_send_post_email', 'cimne_render_send_email_screen');

function cimne_render_send_email_screen() {
    $post_ID = isset($_GET['post_id']) ? absint($_GET['post_id']) : 0;

    if (!$post_ID) {
        wp_die('Falta el ID del post.');
    }

    if (!current_user_can('edit_post', $post_ID)) {
        wp_die('No tienes permisos para acceder a este contenido.');
    }

    check_admin_referer('send_post_email_' . $post_ID);

    $post = get_post($post_ID);

    if (!$post) {
        wp_die('El post no existe.');
    }

    $html_email = cimne_build_email_html($post_ID);
    $subject = esc_attr($post->post_title);

    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset="UTF-8">
        <title>Enviar email</title>
    </head>
    <body style="font-family:Arial;padding:40px;max-width:900px;">

        <h2>Email listo para enviar</h2>

        <p>
            1️⃣ Pulsa <strong>Copiar HTML</strong><br>
            2️⃣ Se abrirá tu cliente de correo<br>
            3️⃣ Pega el contenido y envía
        </p>

        <button id="copyBtn"
            style="padding:12px 20px;font-size:16px;background:#0057b8;color:#fff;border:0;border-radius:6px;cursor:pointer;">
            Copiar HTML
        </button>

        <button id="downloadBtn"
            style="margin-left:10px;padding:12px 20px;font-size:16px;background:#28a745;color:#fff;border:0;border-radius:6px;cursor:pointer;">
            Descargar HTML
        </button>

        <span id="status" style="margin-left:15px;color:green;display:none;">
            ✔ HTML copiado
        </span>

        <textarea id="htmlContent" style="width:100%;height:300px;margin-top:20px;"><?php echo esc_textarea($html_email); ?></textarea>

        <button id="volverBtn"
            style="margin-top:20px;padding:10px 15px;font-size:14px;background:#eee;border:1px solid #ccc;border-radius:4px;cursor:pointer;"
            onclick="window.history.back();">
            ← Volver
        </button>

        <script>
        const btn = document.getElementById('copyBtn');
        const status = document.getElementById('status');
        const content = document.getElementById('htmlContent');

        function legacyCopy() {
            content.focus();
            content.select();

            try {
                return document.execCommand('copy');
            } catch (e) {
                return false;
            }
        }

        const downloadBtn = document.getElementById('downloadBtn');

        btn.addEventListener('click', async () => {
            let copied = false;

            if (navigator.clipboard && window.isSecureContext) {
                try {
                    await navigator.clipboard.writeText(content.value);
                    copied = true;
                } catch (e) {
                    copied = false;
                }
            }

            if (!copied) {
                copied = legacyCopy();
            }

            if (copied) {
                status.style.display = 'inline';

                setTimeout(() => {
                    window.location.href = "mailto:?subject=<?php echo rawurlencode($subject); ?>";
                }, 600);
            } else {
                alert(
                    'No fue posible copiar automáticamente.\n\n' +
                    'Selecciona el contenido y copia manualmente (Ctrl+C / Cmd+C).'
                );
            }
        });

        downloadBtn.addEventListener('click', () => {
            const html = content.value;
            const blob = new Blob([html], { type: 'text/html;charset=utf-8' });
            const url = URL.createObjectURL(blob);
            const link = document.createElement('a');

            link.href = url;
            link.download = 'email.html';
            document.body.appendChild(link);
            link.click();
            document.body.removeChild(link);
            URL.revokeObjectURL(url);
        });
        </script>

    </body>
    </html>
    <?php

    exit;
}