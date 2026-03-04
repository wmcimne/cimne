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

/*================================================
#Google Tag Manager - Head
================================================*/

function CIMNE_widget_website_tag_manager_head() { 

    // No cargar GA4 en admin
    if ( is_admin() ) {
        return;
    }


    // No medir páginas cuyo referrer venga del admin
    if ( isset($_SERVER['HTTP_REFERER']) && strpos($_SERVER['HTTP_REFERER'], '/wp-admin/') !== false ) {
        return;
    }


    ?>

    <!-- Google tag (gtag.js) -->
    <script async src="https://www.googletagmanager.com/gtag/js?id=G-04QLKB6K5K"></script>
    <script>
    window.dataLayer = window.dataLayer || [];
    function gtag(){dataLayer.push(arguments);}
    gtag('js', new Date());

    gtag('config', 'G-04QLKB6K5K');
    </script>



    <?php }
add_action('wp_head', 'CIMNE_widget_website_tag_manager_head');

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

    console_log($parentCategory);

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
#Crear archivo HTML con formato email
#Abre un lienzo con opciones para enviar por email
#Opciones para abrir cliente de email y copiar al portapapeles el codigo HTML
#Incluye imagen destacada si existe
#Usa estilos inline y estructura en tablas para compatibilidad con clientes de email
======================================================================================*/
function render_event_custom_fields_email($post_ID , $corporate_color = "#f9f9f9") {

    // language detection
    $url_lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : null;
    if ($url_lang) {
        $locale = $url_lang;
    } else {   
        $locale = 'en';
    }
    
    // Definir etiquetas según idioma
    if ( strpos( $locale, 'es' ) === 0 ) {
        $fields = [
            //'type_of_event'    => 'Tipo de evento',
            'date'             => 'Fecha',
            'time'             => 'Hora',
            'place'            => 'Lugar',
            'online_streaming' => 'Transmisión en línea',
            'registration'     => 'Registro'
        ];
		$days_of_week = [ 'Domingo'  , 'Lunes'    , 'Martes'   , 'Miércoles', 'Jueves'  , 'Viernes'  , 'Sábado'];
		$month_of_year = [ 'Enero'    , 'Febrero'  , 'Marzo'    , 'Abril'    , 'Mayo'    , 'Junio'    , 'Julio'   , 'Agosto'   , 'Septiembre', 'Octubre' , 'Noviembre', 'Diciembre'];

    } elseif ( strpos( $locale, 'ca' ) === 0 ) {
        $fields = [
            //'type_of_event'    => 'Tipus d\'event',
            'date'             => 'Data',
            'time'             => 'Hora',
            'place'            => 'Lloc',
            'online_streaming' => 'Transmissió en línia',
            'registration'     => 'Registre'
        ];
		$days_of_week = ['Diumenge', 'Dilluns', 'Dimarts', 'Dimecres', 'Dijous', 'Divendres', 'Dissabte'];
		$month_of_year = [ 'Gener'    , 'Febrer'   , 'Març'     , 'Abril'    , 'Maig'    , 'Juny'     , 'Juliol'  , 'Agost'    , 'Setembre'  , 'Octubre' , 'Novembre' , 'Desembre'];

    } else {
        // English (default)
        $fields = [
            //'type_of_event'    => 'Type of event',
            'date'             => 'Date',
            'time'             => 'Time',
            'place'            => 'Place',
            'online_streaming' => 'Online streaming',
            'registration'     => 'Registration'
        ];
		$days_of_week = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
		$month_of_year = [ 'January'  , 'February' , 'March'    , 'April'    , 'May'     , 'June'     , 'July'    , 'August'   , 'September' , 'October' , 'November' , 'December'];

    }

    $rows = '';
	
    
    
    $date = get_post_meta($post_ID, 'date', true);
    $date_obj = DateTime::createFromFormat("Ymd", $date);

    if ( $date_obj ) {
        // Use the DateTime created from the post meta so we don't show the current date
        $day_index = (int) $date_obj->format('w');
        $day_name = $days_of_week[$day_index];
        $day_number = $date_obj->format('d');
        $month_number = (int) $date_obj->format('n');
        $month_name = $month_of_year[$month_number - 1];
        $year = $date_obj->format('Y');
        $formatted_date = "{$day_name}, {$day_number} {$month_name} {$year}";
        // also set a human readable $date string (kept for compatibility)
        $date = $date_obj->format("d M Y");
    } else {
        // Fallback: keep whatever value was stored (could be empty or already formatted)
        $formatted_date = $date;
        console_log("Final formatted date (no valid meta): " . $formatted_date);
    }
    $time = esc_html(get_post_meta($post_ID, 'time', true));
    $time = preg_replace('/:\d{2}$/', '', $time); // Remove seconds if present
    $place = esc_html(get_post_meta($post_ID, 'place', true));
    //$attendance = esc_html(get_post_meta($post_ID, 'attendance', true));
    $online_streaming = get_post_meta($post_ID, 'online_streaming', true);
    $registration = get_post_meta($post_ID, 'registration', true);
    $icon_color = str_replace('#', '', $corporate_color);

    $rows .= "
            <tr>
                <td style='width: 10%; text-align:center; padding:5px 15px;'>
                    <img src='https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/icon_calendar{$icon_color}.png' alt='Date' style='width:22px;height:22px;vertical-align: bottom;'/>
                </td>
                <td style='padding:5px 15px;font-size:16px;color:#333333;font-weight:bold;'>
                    {$formatted_date}
                </td>
            </tr>
            <tr>
                <td style='width: 10%; text-align:center; padding:5px 15px;'>
                    <img src='https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/icon_clock{$icon_color}.png' alt='Time' style='width:22px;height:22px;vertical-align: bottom;'/>
                </td>
                <td style='padding:5px 15px;font-size:16px;color:#333333;font-weight:bold;'>
                    {$time}
                </td>
            </tr>
            <tr>
                <td style='width: 10%; text-align:center; padding:5px 15px;'>
                    <img src='https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/icon_pin_alt{$icon_color}.png' alt='Place' style='width:22px;height:22px;vertical-align: bottom;'/>
                </td>
                <td style='padding:5px 15px;font-size:16px;color:#333333;font-weight:bold;'>
                    {$place}
                </td>
            </tr>";
    if (!empty($online_streaming)) {
        $rows .= "
            <tr>
                <td style='width: 10%; text-align:center; padding:5px 15px;'>
                    <img src='https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/icon_laptop{$icon_color}.png' alt='Online streaming' style='width:22px;height:22px;vertical-align: bottom;'/>
                </td>
                <td style='padding:5px 15px;font-size:16px;color:#333333;font-weight:bold;'>
                    <a href='" . esc_url($online_streaming) . "' target='_blank'
                       style='color:#0057b8;text-decoration:none;font-weight:bold;'>
                       {$fields['online_streaming']}
                    </a>
                </td>
            </tr>";
    }
    if (!empty($registration)) {
        $rows .= "
            <tr>
                <td style='width: 10%; text-align:center; padding:5px 15px;'>
                    <img src='https://web.cimne.upc.edu/groups/publicacions/mails/img/icons/icon_pencil-edit{$icon_color}.png' alt='Registration' style='width:22px;height:22px;vertical-align: bottom;'/>
                </td>
                <td style='padding:5px 15px;font-size:16px;color:#333333;font-weight:bold;'>
                    <a href='" . esc_url($registration) . "' target='_blank'
                       style='color:#0057b8;text-decoration:none;font-weight:bold;'>
                       {$fields['registration']}
                    </a>
                </td>
            </tr>";
    }

    // Si no hay ningún campo, no renderizar nada
    if (empty($rows)) {
        return '';
    }

    return "
    <!-- BLOQUE EVENTO -->
    <table width='100%' cellpadding='0' cellspacing='0' border='0' style='margin-bottom:30px; border: 2px solid {$corporate_color};vertical-align: bottom;'>

        {$rows}
    </table>";
}

function limpiar_html_divi_para_email($html,  $corporate_bg_color = "#f9f9f9") {

    // Procesar shortcodes y formato
    $html = apply_filters('the_content', $html);
    $html = do_shortcode($html);
    $html = wpautop($html);

    // Eliminar shortcodes Divi
    $html = preg_replace('/\[\/?et_pb_[^\]]*\]/', '', $html);

    // Eliminar comentarios HTML (incluidos de Divi)
    $html = preg_replace('/<!--(.*?)-->/', '', $html);

    // Reemplazar iframes con enlaces
    $html = preg_replace_callback(
        '/<iframe[^>]*src=["\']([^"\']+)["\'][^>]*><\/iframe>/i',
        function($matches) {
            $src = esc_url($matches[1]);
            return '<p><a href="' . $src . '" target="_blank" style="color:#0057b8;text-decoration:none;font-weight:bold;">Video</a></p>';
        },
        $html
    );

    // Eliminar DIVs con clases Divi pero mantener contenido interno
    $html = preg_replace('/<div[^>]*class="[^"]*et_pb_[^"]*"[^>]*>/i', '<div>', $html);

    // Eliminar atributos innecesarios para email
    $html = preg_replace('/\s(class|id|data-[^=]*|aria-[^=]*|role)="[^"]*"/i', '', $html);

    // Eliminar DIVs con atributos style e id específicos (como wp-caption)
    $html = preg_replace('/<div[^>]*>/i', '<div>', $html);

    // Normalizar divs vacíos
    $html = preg_replace('/<div>\s*<\/div>/', '', $html);

    // Convertir DIV a P
    $html = str_replace('<div>', '<p>', $html);
    $html = str_replace('</div>', '</p>', $html);

    // Limpiar dobles P
    $html = preg_replace('/<p>\s*<p>/', '<p>', $html);
    $html = preg_replace('/<\/p>\s*<\/p>/', '</p>', $html);


    // Tipografía corporativa inline
    $html = preg_replace('/<p>/', '<p style="font-size:14px;line-height:1.7;color:#333333;margin:0 0 20px 0;">', $html);
    $html = preg_replace('/<h1>/', '<h1 style="font-size:26px;color:#1a1a1a;margin-bottom:20px;">', $html);
    $html = preg_replace('/<h2>/', '<h2 style="font-size:22px;color:#1a1a1a;margin:25px 0 15px;">', $html);
    $html = preg_replace('/<h3>/', '<h3 style="font-size:18px;color:#1a1a1a;margin:20px 0 10px;">', $html);
    $html = preg_replace('/<h4>/', '<h4 style="font-size:16px;color:#1a1a1a;margin:20px 0 10px;">', $html);
    $html = preg_replace('/<h5>/', '<h5 style="font-size:16px;color:#1a1a1a;margin:20px 0 10px;">', $html);

    // Imágenes optimizadas para email
    $html = preg_replace_callback(
        '/<img([^>]*?)>/i',
        function($matches) {
            $img_tag = $matches[1];
            $style = 'max-width:100%;height:auto;display:block;margin:20px 0;';
            if (preg_match('/style="([^"]*)"/i', $img_tag, $style_match)) {
                $img_tag = preg_replace('/style="[^"]*"/i', 'style="' . $style_match[1] . $style . '"', $img_tag);
            } else {
                $img_tag .= ' style="' . $style . '"';
            }
            return '<img' . $img_tag . ' />';
        },
        $html
    );

    return trim($html);
}


// Agregar enlace de envío por email en la lista de posts en el admin
add_filter('post_row_actions', 'agregar_enlace_send_email', 10, 2);
function agregar_enlace_send_email($actions, $post) {

    if ($post->post_type === 'post') {

        $lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : 'en';

        $url = add_query_arg([
            'action'  => 'send_post_email',
            'lang'    => $lang,
            'post_id' => $post->ID,
        ], admin_url('admin-post.php'));

        $actions['send_email'] = '<a href="' . esc_url($url) . '">Send Email</a>';
    }

    return $actions;
}




add_action('admin_post_send_post_email', 'pantalla_envio_email_post');
function pantalla_envio_email_post() {

    // Verificar permisos
    if (!current_user_can('edit_posts')) {
        wp_die('No tienes permisos para descargar este archivo.');
    }
    // Verificar ID del post
    if (!isset($_GET['post_id'])) {
        wp_die('Falta el ID del post.');
    }
    // Obtener post
    $post_ID = intval($_GET['post_id']);
    $post = get_post($post_ID);
    if (!$post) {
        wp_die('El post no existe.');
    }
   
    // Obtener tipo de evento desde la categoría hija de Events
    $categories = get_the_category($post_ID);
    $event_type = '';
    
    foreach ($categories as $category) {
        // Verificar si la categoría es hija de Events (categoría padre)
        $parent = get_category($category->parent);
        if ($parent && ($parent->slug === 'events' || $parent->slug === 'eventos' || $parent->slug === 'esdeveniments')) {
            $event_type = $category->name;
            break;
        }
    }

    // language detection
    $url_lang = isset($_GET['lang']) ? sanitize_text_field($_GET['lang']) : null;
    if ($url_lang) {
        $locale = $url_lang;
    } else {   
        $locale = 'en';
    }

    // Definir etiquetas según idioma
    if ( strpos( $locale, 'es' ) === 0 ) {
        $strings = [
            'button'             => 'Leer más',
            'si_no_puedes_leer' => 'Si no puedes leer este correo, haz clic',
            'aqui'               => 'aquí',
            'nota_de_prensa'     => 'Nota de prensa'
        ];
    } elseif ( strpos( $locale, 'ca' ) === 0 ) {
        $strings = [
            'button'             => 'Llegir més',
            'si_no_puedes_leer' => 'Si no pots llegir aquest correu, fes clic',
            'aqui'               => 'aquí',
            'nota_de_prensa'     => 'Nota de premsa'
        ];
    } else {
        // English (default)
        $strings = [
            'button'             => 'Read more',
            'si_no_puedes_leer' => 'If you cannot read this email, click',
            'aqui'               => 'here',
            'nota_de_prensa'     => 'Press release'
        ];
    }

    // Definir colores e imagen de cabecera según tipo de evento
    if (!empty($event_type)) {
        console_log("Event type: " . $event_type);
        switch (strtolower($event_type)) {
            case 'coffee talk':
                console_log("coffee talk");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_coffee_talks_md.jpg';
                $corporate_color = "#02a0a5"; // Blue
                $corporate_bg_color = "rgba(2, 160, 165, 0.2)"; 
                break;
            case 'seminars':
            case 'seminaris':
            case 'seminarios':
                console_log("color orange for seminar");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_seminars_md.jpg';
                $corporate_color = "#f3921a"; // Orange
                $corporate_bg_color = "rgba(243, 146, 26, 0.2)"; // Orange
                break;
            case 'conferences':
            case 'conferències':
            case 'conferencias':
                console_log("color default for conference");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_default.jpg';
                $corporate_color = "#0057b8"; // default
                $corporate_bg_color = "rgba(128, 0, 128, 0.2)"; // default
                break;
            case 'congress':
            case 'congrés':
            case 'congreso':
                console_log("color default for congress");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_default.jpg';
                $corporate_color = "#0057b8"; // default
                $corporate_bg_color = "#F8E7DF"; // default
                break;
            case 'thesis defense':
            case 'defensa de tesi':
            case 'defensa de tesis':
            console_log("color dark blue for thesis defense");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_tesis_md_rec.jpg';
                $corporate_color = "#004996"; // Dark Blue
                $corporate_bg_color = "rgba(0, 73, 150, 0.2)"; // Dark Blue
                break;
            case 'workshops':
                console_log("color default for workshop");
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_default.jpg';
                $corporate_color = "#0057b8"; // default
                $corporate_bg_color = "rgba(0, 128, 128, 0.2)"; // default
                break;
            default:
                $corporate_color = "#0057b8";// Color corporativo CIMNE
                $corporate_bg_color = "rgba(0, 87, 184, 0.2)"; // CIMNE Blue
                $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_default.jpg';
        }

        $header_htlm = "
                    <!-- HEADER IMAGE -->
                    <tr>
                        <td class='title' style='font-size:28px; font-weight:bold;
                                   margin-bottom:20px; line-height:1.3;'>
                                   <div class='contenedor-imagen' style='position: relative; display: inline-block; overflow: hidden;'>
                                        <img src='{$header_image}' style='display:block;width:100%;height:auto;' />
                                        <div class='texto-superpuesto' style='position: absolute; top: 30%; padding: 15px;'>
                                            <h1 style='margin:0;font-size:24px;line-height: 1;'>{$post->post_title}</h1>
                                        </div>
                                    </div>

                            <!--<img width='600' height='auto' src='{$header_image}' 
                                 style='display:block;margin-bottom:30px;
                                        max-width:100%;height:auto;' />-->
                        </td>
                    </tr>
                    

                    <!-- TÍTULO -->
                    <!--<tr>
                        <td class='title' style='font-size:28px; font-weight:bold; color: {$corporate_color};
                                   margin-bottom:20px; line-height:1.3;'>
                            {$post->post_title}
                        </td>

                    </tr>
                    <tr height='10'>
                        <td><br/></td>
                    </tr>-->
                    ";

        // Campos personalizados del evento
        $event_fields_html = render_event_custom_fields_email($post_ID, $corporate_color);  

    } else {
        $corporate_color = "#0057b8";// Color corporativo CIMNE
        $corporate_bg_color = "rgba(0, 87, 184, 0.2)"; // CIMNE Blue
        $header_image = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/banner_nota_de_premsa_md.jpg';
        $header_logo = 'https://web.cimne.upc.edu/groups/publicacions/mails/2026/plantilla/logo-color-cimne-web-sm.png';
        $header_default_style = 'color: #0057b8;';
        $event_fields_html = '';


        $header_htlm = "
                    <!-- HEADER IMAGE -->
                    <tr>
                        <td class='title' style='font-size:28px; font-weight:bold;
                                   margin-bottom:20px; line-height:1.3;'>

                            <img width='600' height='auto' src='{$header_image}' 
                                 style='display:block;margin-bottom:30px;
                                        max-width:100%;height:auto;' />
                        </td>
                    </tr>
                    

                    <!-- TÍTULO -->
                    <tr>
                        <td style='padding-bottom:20px;'>
                            <table width='100%' cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                    <td width='180' valign='top'>
                                        <img width='180' height='auto' src='{$header_logo}' 
                                             style='display:block;max-width:100%;height:auto;' />
                                    </td>
                                    <td width='20'></td>
                                    <td valign='top' style='font-size:28px; font-weight:bold; color: #000; line-height:1.3; border-bottom: 2px solid #000; padding-bottom:5px;'>
                                        {$strings['nota_de_prensa']}
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td class='title' style='font-size:28px; font-weight:bold; color: {$corporate_color};
                                   margin-bottom:20px; line-height:1.3;'>
                            {$post->post_title}
                        </td>
                    </tr>
                    <tr height='10'>
                        <td><br/></td>
                    </tr>
                    ";
    }

    // Sanitizar Divi → HTML email friendly
    $contenido_html = limpiar_html_divi_para_email($post->post_content, $corporate_bg_color);

    // URL del post (para el botón)
    $url_post = get_permalink($post_ID);

    // Imagen destacada procesada para email
    $imagen_destacada = '';
    if (has_post_thumbnail($post_ID)) {
        $img_src = get_the_post_thumbnail_url($post_ID, 'large');
        $imagen_destacada = "<img src='{$img_src}' width='100%' 
            style='display:block;border-radius:8px;margin-bottom:25px;max-width:100%;height:auto;' />";
    }

    // === PLANTILLA CORPORATIVA ===
    $html_email = "
    <!DOCTYPE html>
    <html>
    <head>
        <meta charset='UTF-8' />
        <meta name='viewport' content='width=device-width, initial-scale=1.0' />
        <title>{$post->post_title}</title>

    </head>
    
    <body style='margin:0; padding:0; background-color:#eef1f4; font-family:Arial, sans-serif;'>

    <table class='table-wrapper' style='width:100%; padding:40px 0; background-color:#eef1f4;' cellpadding='0' cellspacing='0' border='0'>
        <tr>
            <td align='center'>

                <table class='main' style='background-color: #ffffff; padding:40px;' width='650' cellpadding='0' cellspacing='0' border='0'>
                    <tr>
                        <td class='title' style='font-size:28px; font-weight:bold; color: {$corporate_color};
                                   margin-bottom:20px; line-height:1.3;'>
                            <p style='font-size:14px;color:#999999;
                                      margin-bottom:30px;'>
                                {$strings['si_no_puedes_leer']}
                                <a href='" . esc_url( $url_post ) . "' target='_blank'
                                   style='color:#0057b8;text-decoration:none;font-weight:bold;'>
                                    {$strings['aqui']}
                                </a>.
                            </p>
                        </td>
                    </tr>
                    {$header_htlm}

                    <!-- CUSTOM FIELDS EVENTO -->
                    <tr>
                        <td>
                            {$event_fields_html}
                        </td>
                    </tr>
                    <tr height='10'>
                        <td><br/></td>
                    </tr>

                    <!-- CONTENIDO LIMPIO -->
                    <tr>
                        <td style='font-size:16px;line-height:1.7;color:#333333;padding-bottom:35px;'>
                            {$contenido_html}
                        </td>
                    </tr>

                    <!-- BOTÓN CORPORATIVO -->
                    <tr>
                        <td align='center' style='padding-bottom:40px;'>
                            <table cellpadding='0' cellspacing='0' border='0'>
                                <tr>
                                    <td align='center'
                                        style='border: 2px solid {$corporate_color};'>
                                        <a href='{$url_post}' target='_blank'
                                           style='display:inline-block;padding:15px 32px;
                                                  font-size:17px;color:{$corporate_color};
                                                  text-decoration:none;font-weight:bold;
                                                  font-family:Arial,sans-serif;'>
                                            {$strings['button']}
                                        </a>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    </table>
                    
                    <!-- FOOTER -->
                    <table style='table-layout:fixed; background-color:{$corporate_color}' class='footer' cellspacing='0' cellpadding='0' width='650'>
                        <tbody>
                            <tr height='20'>
                            <td width='20'><br>
                            </td>
                            <td width='600'><br>
                            </td>
                            <td width='20'><br>
                            </td>
                            </tr>
                            <tr>
                            <td width='20'><br>
                            </td>
                            <td>
                                <table style='table-layout:fixed;' class='footer' cellspacing='0' cellpadding='0'>
                                <tbody>
                                    <tr>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td>
                                        <p style='color:#ffffff; font-weight:bold;'>
                                        <font class='title' face='Arial'>About CIMNE</font>
                                        </p>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td>
                                        <p style='color:#ffffff; font-weight:bold;'>
                                        <font class='title' face='Arial'>Contact</font>
                                        </p>
                                    </td>
                                    </tr>
                                    <tr height='10'>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td width='90'><img class='logo'
                                        src='https://web.cimne.upc.edu/groups/publicacions/mails/2025/seminars/img/logo-blanco-cimne.png'
                                        width='90'></td>
                                    <td width='10'><br>
                                    </td>
                                    <td width='330'>
                                        <p style='color:#ffffff;'>
                                        <font face='Arial'>CIMNE is a public R+D
                                            centre in computational engineering with a
                                            strong focus on knowledge transfer.</font>
                                        </p>
                                    </td>
                                    <td width='10'><br>
                                    </td>
                                    <td width='160'>
                                        <p style='color:#ffffff;'>
                                        <font face='Arial'>+34 93 401 74 95<br>
                                            CIMNE Building C1<br>
                                            Campus Nord UPC<br>
                                            C/ Gran Capità S/N<br>
                                            08034 Barcelona, Spain</font>
                                        </p>
                                    </td>
                                    </tr>
                                    <tr height='10'>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td class='title' style='color:#ffffff;'>Follow us on</td>
                                    <td><br>
                                    </td>
                                    <td> <a href='https://www.facebook.com/cimne' alt='' target='_blank' moz-do-not-send='true'><img
                                            src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-facebook.png'
                                            alt='facebook' moz-do-not-send='true'></a> <a href='https://www.linkedin.com/company/cimne'
                                        alt='linkedin' target='_blank' moz-do-not-send='true'><img
                                            src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-linkedin.png'
                                            moz-do-not-send='true'></a> <a href='https://twitter.com/cimne' alt='twitter' target='_blank'
                                        moz-do-not-send='true'><img
                                            src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-twitter.png'
                                            moz-do-not-send='true'></a> <a href='https://www.youtube.com/cimneMC' alt='CIMNEMC' target='_blank'
                                        moz-do-not-send='true'><img
                                            src='https://web.cimne.upc.edu/groups/publicacions/mails/2022/coffee-talks/img/social-youtube.png'
                                            moz-do-not-send='true'></a> </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    </tr>
                                    <tr height='10'>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    </tr>
                                    <tr>
                                    <td><br>
                                    </td>
                                    <td><br>
                                    </td>
                                    <td class='links'><a href='mailto:cimne@cimne.upc.edu' class='moz-txt-link-freetext'
                                        moz-do-not-send='true' style='color:#ffffff;'>cimne@cimne.upc.edu</a>
                                        | <a href='http://www.cimne.com' moz-do-not-send='true' style='color:#ffffff;'>www.cimne.com</a></td>
                                    <td><br>
                                    </td>
                                    <td style='color:#ffffff;'>Copyright © 2026 CIMNE.<br>
                                        All rights reserved.</td>
                                    </tr>
                                </tbody>
                                </table>
                            </td>
                            <td width='20'><br>
                            </td>
                            </tr>
                            <tr height='20'>
                            <td width='20'><br>
                            </td>
                            <td width='600'><br>
                            </td>
                            <td width='20'><br>
                            </td>
                            </tr>
                        </tbody>
                    </table>
            </td>
        </tr>
    </table>

    </body>
    </html>
    ";

    // Nombre del archivo
    // $file_name = 'post-' . $post_ID . '-' . sanitize_title($post->post_title) . '.html';

    // header('Content-Type: text/html; charset=UTF-8');
    // header('Content-Disposition: attachment; filename="' . $file_name . '"');

    // echo $html_email;
    // exit;

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
        style="padding:12px 20px;font-size:16px;
               background:#0057b8;color:#fff;border:0;
               border-radius:6px;cursor:pointer;">
        Copiar HTML
    </button>

    <span id="status" style="margin-left:15px;color:green;display:none;">
        ✔ HTML copiado
    </span>

    <textarea id="htmlContent"
        style="width:100%;height:300px;margin-top:20px;">
<?= esc_textarea($html_email); ?>
    </textarea>
    
    <button id="volverBtn"
        style="margin-top:20px;padding:10px 15px;
               font-size:14px;background:#eee;
               border:1px solid #ccc;
               border-radius:4px;
               cursor:pointer;"
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
            const ok = document.execCommand('copy');
            return ok;
        } catch (e) {
            return false;
        }
    }

    btn.addEventListener('click', async () => {
        let copied = false;

        // 1️⃣ Intentar API moderna
        if (navigator.clipboard && window.isSecureContext) {
            try {
                await navigator.clipboard.writeText(content.value);
                copied = true;
            } catch (e) {
                copied = false;
            }
        }

        // 2️⃣ Fallback clásico
        if (!copied) {
            copied = legacyCopy();
        }

        // 3️⃣ Resultado
        if (copied) {
            status.style.display = 'inline';

            setTimeout(() => {
                window.location.href = "mailto:?subject=<?= $subject ?>";
            }, 600);

        } else {
            alert(
                'No fue posible copiar automáticamente.\n\n' +
                'Selecciona el contenido y copia manualmente (Ctrl+C / Cmd+C).'
            );
        }
    });
</script>

</body>
</html>
<?php
    exit;
}
