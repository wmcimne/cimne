<?php
// Exit if accessed directly
if ( !defined( 'ABSPATH' ) ) exit;

/*================================================
#Load custom Blog Module
================================================*/
// function divi_custom_blog_module() {
//     get_template_part( '/includes/Blog' ); 
//     $dcfm = new custom_ET_Builder_Module_Blog();
//     remove_shortcode( 'et_pb_blog' );
//     add_shortcode( 'et_pb_blog', array( $dcfm, '_shortcode_callback' ) ); 
//   }
//   add_action( 'et_builder_ready', 'divi_custom_blog_module' );
//   function divi_custom_blog_class( $classlist ) {
//     // Blog Module 'classname' overwrite.
//     $classlist['et_pb_blog'] = array( 'classname' => 'custom_ET_Builder_Module_Blog',);
//     return $classlist;
//   }
//   add_filter( 'et_module_classes', 'divi_custom_blog_class' );

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



/*=====================================================================
 * Google Consent Mode v2 manual para Complianz Free
 * Adaptado para activar señales de marketing si el usuario acepta, 
 * solucionando el aviso de "Señales inactivas" en GA4.
 *====================================================================*/

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
# Incluedes para Ajax Listings
================================================*/

require_once get_stylesheet_directory() . '/includes/ajax-listings/helpers.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/pagination.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/event-meta.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/assets.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/events.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/news.php';
require_once get_stylesheet_directory() . '/includes/ajax-listings/workshops.php';


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

