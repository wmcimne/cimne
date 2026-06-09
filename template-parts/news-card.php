<?php
// Obtener el ID del post actual.
$post_id = get_the_ID();

// Inicializar la variable para el tipo de noticia.
$news_type = '';

// Obtener las categorías asignadas al post.
$categories = get_the_terms($post_id, 'category');

// Verificar que existan categorías y que no haya errores.
if (!empty($categories) && !is_wp_error($categories)) {
    foreach ($categories as $category) {
        // Comprobar si la categoría pertenece a alguno de los tipos relevantes.
        if (in_array($category->slug, ['in-depth', 'innovation-news', 'institutional', 'research-news'], true)) {
            // Usar el nombre de la categoría encontrada como tipo de noticia.
            $news_type = $category->name;
            break;
        }
    }
}

// Si no se encontró ningún tipo de noticia válido, usar el valor por defecto.
if (empty($news_type)) {
    $news_type = 'News';
}
?>

<article id="post-<?php echo esc_attr($post_id); ?>"
    <?php post_class('et_pb_post clearfix et_pb_blog_item_0_0 turquoise'); ?>
    style="display: block;">

    <?php if (has_post_thumbnail()) : ?>
        <a href="<?php the_permalink(); ?>" class="entry-featured-image-url" role="link">

            <?php if (!empty($news_type)) : ?>
                <div class="rectangle">
                    <span class="rectangle-text">
                        <?php echo esc_html($news_type); ?>
                    </span>
                </div>
            <?php endif; ?>

            <?php // Mostrar la imagen destacada del post.
            the_post_thumbnail('large'); ?>

            <!-- Imagen decorativa fija con cambio de fuente mediante toggle-src -->
            <img
                src="/wp-content/uploads/2025/02/cross-bg-transparent.png"
                toggle-src="/wp-content/uploads/2025/03/dash-bg-transparent.png"
                alt="square-cross-form"
                class="square-form-img"
            >

        </a>
    <?php endif; ?>

    <h5 class="entry-title">
        <a href="<?php the_permalink(); ?>" role="link">
            <?php the_title(); ?>
        </a>
    </h5>

    <p class="post-meta">
        <span class="published">
            <?php // Mostrar la fecha de publicación en formato Año/Mes/Día.
            echo esc_html(get_the_date('Y/m/d')); ?>
        </span>
    </p>
    <!--
    // Bloque para el caso que se quiera el contenido del post, actualmente se muestra un extracto con un máximo de 28 palabras.
    // <div class="post-content">
    //     <div class="date" date=""></div>

    //     <div class="post-content-inner et_multi_view_hidden">
    //         <p>
    //             <?php // Mostrar un extracto del contenido del post con un máximo de 28 palabras.
    //             echo esc_html(wp_trim_words(get_the_excerpt(), 28)); ?>
    //         </p>
    //     </div>
    // </div>
    -->
</article>