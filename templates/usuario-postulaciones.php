<?php
/**
 * Template name: Usuario - Postulaciones
 */
global $wp;

$url = home_url();

// Si el usuario NO está logueado, redirige sin importar el rol
if ( !is_user_logged_in() ) {
    $url = home_url("login");
    wp_safe_redirect($url);
    exit;
}

if ( current_user_can('administrator') ) {
    wp_safe_redirect( home_url() );
    exit;
}

if ( locate_template('header-full.php') ) {
   get_header('full'); 
} else {
   get_header(); 
}
$order_by = isset($_REQUEST['orderby']) ? sanitize_text_field($_REQUEST['orderby']) : 'date';
$order_type = isset($_REQUEST['order']) ? sanitize_text_field($_REQUEST['order']) : 'DESC';
$page = isset($_REQUEST['pag']) ? absint($_REQUEST['pag']) : 1;

global $wpdb;

$current_user_id = get_current_user_id();

// Obtener los IDs de oportunidades para este usuario
$table_name = $wpdb->prefix . 'opt_inscripciones'; // safe456_opt_inscripciones
$oportunidad_ids = $wpdb->get_col( $wpdb->prepare(
    "SELECT id_oportunidades FROM $table_name WHERE id_usuario = %d",
    $current_user_id
) );

$items_per_page = 10;


if ( ! empty($oportunidad_ids) ) {
    $query_args = [
        'post_type' => 'oportunidad',
        'posts_per_page' => $items_per_page,
        'paged' => $page,
        'orderby' => $order_by,
        'order' => $order_type,
        'post__in' => $oportunidad_ids, // Filtrar solo esos posts
    ];
} else {
  $query_args = [
    ];
}

$query = new WP_Query($query_args);
$items = $query->posts;
$total_items = $query->found_posts;
$total_pages = $query->max_num_pages;

?>

<style>
    
    .pagination-bar{
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }
    .pagination-bar a{
        height: 30px;
        width: 30px;
        display: flex;
        align-items: center;
        justify-content: center;
        text-align: center;
    }
    .pagination-bar a.active{
        background: #02006C;
        border-radius: 999px;
        color: #fff;
    }
</style>
<div class="turimet-account">
    <aside class="turimet-account__aside no-mobile">
        <ul class="turimet-account__menu">
          <?php include(plugin_dir_path(__FILE__) . '../template-parts/menu-cuenta.php'); ?>
      </ul>
  </aside>
  <main class="turimet-account__main">
    <div class="row">

        <h2 class="c-blue">Postulaciones</h2>
        <a href="<?=home_url('oportunidades/')?>" class="btn" style="color: #fff;">Buscar oportunidades</a>
        <br><br>
        <div class="ao-messages">
            <div class="ao-message"><span>Filtra las oportunidades a las que postulaste</span></div>
        </div>
        <div class="ao-buttons">

            <form method="get">
                <select name="orderby">
                    <option value="">Ordenar</option>
                    <?php
                    $options = ['date' => 'Fecha','status' => 'Estado','company' => 'Empresa'];

                    foreach($options as $k => $v){
                        printf( '<option value="%2$s"%3$s>%1$s</option>', $v, $k, (isset($_REQUEST['orderby']) && $k==$_REQUEST['orderby']) ? ' selected="selected"': '' );
                    }
                    ?>
                </select>
                <select name="order">
                    <?php
                    $options = ['asc' => 'Ascendente', 'desc' => 'Descendente'];

                    foreach($options as $k => $v){
                        printf( '<option value="%2$s"%3$s>%1$s</option>', $v, $k, (isset($_REQUEST['order']) && $k==$_REQUEST['order']) ? ' selected="selected"': '' );
                    }
                    ?>
                </select>
                <input type="submit" value="Ordenar" class="btn btn-primary" />
            </form>
        </div>
        <div class="ao-wrap">
            <h3>Postulaciones realizadas</h3>
            <?php if ($items): ?>
                <div class="oplist">
                    <?php foreach ($items as $post): setup_postdata($post); ?>
                        <?php
                        $post_author_id = $post->post_author;
                        $user_info = get_userdata($post_author_id);
                        $empresa_nombre = get_user_meta($post_author_id, 'empresa_nombre', true);
                        $empresa_pais = get_user_meta($post_author_id, 'empresa_pais', true);
                        $empresa_icono = get_user_meta($post_author_id, 'empresa_icono', true);
                        $modalidad = get_post_meta($post->ID, '_modalidad', true);
                        $_salario_max = get_post_meta($post->ID, '_salario_min', true);
                        $_salario_min = get_post_meta($post->ID, '_salario_max', true);
                        $code = 'pe';
                        $url = get_permalink($post);
                        $urlautor = get_author_posts_url($post_author_id);
                        ?>
                        <div class="oplist__item">
                            <figure class="fig-contain oplist__image">
                                <!-- <a href="<?= $urlautor ?>" target="_blank"> -->
                                    <img src="<?= esc_url($empresa_icono) ?>" alt="" />
                                    <!-- </a> -->
                                </figure>
                                <div class="oplist__main">
                                    <div class="oplist__main--company">
                                        <!-- <a href="<?= $urlautor ?>" target="_blank"> -->
                                            <?= esc_html($empresa_nombre) ?>
                                            <!-- </a> -->
                                        </div>
                                        <h3 class="oplist__main--job">
                                            <!-- <a href="<?= esc_url($url) ?>"> -->
                                                <?= esc_html($post->post_title) ?>
                                                <!-- </a> -->
                                            </h3>
                                            <div class="oplist__main--country">
                                                <div class="iti__flag iti__<?= $code ?>"></div>
                                                <?= esc_html($empresa_pais) ?>
                                            </div>
                                        </div>
                                        <div class="oplist__side">
                                            <ul>
                                                <li class="oplist__side--creation"><?= get_the_date('Y-m-d H:i:s', $post) ?></li>
                                                <li class="oplist__side--modality"><?= esc_html($modalidad) ?></li>
                                            </ul>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                                <?php wp_reset_postdata(); ?>
                            </div>
                        <?php else: ?>
                            <p>No se encontraron oportunidades.</p>
                        <?php endif; ?>

                    </div>
                    <div class="pagination-bar">
                        <?php
                        $base_url = remove_query_arg('pag');
                        $query_args = [
                            'orderby' => $order_by,
                            'order' => $order_type
                        ];

                        for ($i = 1; $i <= $total_pages; $i++) {
                            $url = add_query_arg(array_merge($query_args, ['pag' => $i]), $base_url);
                            $active = ($i == $page) ? ' class="active"' : '';
                            echo "<a href='{$url}'{$active}>{$i}</a> ";
                        }
                        ?>

                    </div>
                </div>
            </main>
        </div>

<?php include('footer-simple.php'); ?>