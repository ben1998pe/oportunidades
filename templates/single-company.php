 <?php
get_header(); 

$author_id = get_queried_object_id();
$user_info = get_userdata($author_id);

$registered_date = $user_info->user_registered; // yyyy-mm-dd hh:mm:ss

// Convertimos a formato legible e internacionalizado
$date_formatted = date_i18n('F j, Y', strtotime($registered_date));


$code = ''; // Inicializamos
$empresa_pais = get_user_meta($author_id, 'empresa_pais', true);
foreach ($countries as $country) {
    if (strcasecmp($country['name'], $empresa_pais) === 0) {
        $code = $country['code'];
        break;
    }
}


$company = [
   'company_url'         => $user_info ? $user_info->user_nicename : '',
   'company'        => get_user_meta($author_id, 'empresa_nombre', true),
   'name'                 =>$user_info ? $user_info->user_nicename : '',
   'company_name'  => get_user_meta($author_id, 'empresa_nombre', true),
   'country'       => get_user_meta($author_id, 'empresa_pais', true),
    'company_image' => get_user_meta($author_id, 'empresa_icono', true), // aquí tienes URL
    'company_background' => get_user_meta($author_id, 'empresa_fondo', true), // URL
    'url'   => get_author_posts_url($author_id),
    'description'   => get_user_meta($author_id, 'empresa_description', true),
   'registered_date'     => $date_formatted, // Aquí queda la fecha formateada
   'code'                => $code,
   'country_code'                => $code,
   'twitter'                => get_user_meta($author_id, 'empresa_twitter', true),
   'facebook'                => get_user_meta($author_id, 'empresa_facebook', true),
   'instagram'                => get_user_meta($author_id, 'empresa_instagram', true),
   'video_youtube'                => get_user_meta($author_id, 'empresa_video_youtube', true),
   'linkedin'                => '',
];

?>
<div class="sub-header section-1 section-p-xs">
    <div class="row">
        <a href="<?=home_url('oportunidades/')?>" class="link-back">regresar</a>
    </div>
</div>
<?php 

?>
<article class="company-single">
    <section class="section-3 hero-sop">
        <div class="section__background">
            <figure class="fig-cover">
                <img src="<?php echo OPT_CUSTOM_PLUGIN_URL . 'assets/images/hero-single-company.jpg'; ?>" alt="">
            </figure>
        </div>
        <div class="row-xs">
            <ul class="oportunity-single__company">
                <li><figure class="fig-contain"><img src="<?=$company['company_image']?>" /></figure></li>    
                <li>
                    <a href="<?=$company['company_url']?>" style="pointer-events: none;"><?=$company['company']?></a>
                    <span class="oportunity-single__country"><div class="iti__flag iti__<?=$company['country_code']?>"></div> <?=$company['country']?></span>
                </li>
            </ul>
            <h1 class="event-single__title"><?=$company['company']?></h1>
            <ul class="company-single__meta">
                <!-- <?php if( filter_var($company['qty_workers'], FILTER_VALIDATE_INT) ): ?><li class="user">Más de <?=number_format($company['qty_workers'])?> colaboradores</li><?php endif; ?> -->
                <?php if( filter_var($company['url'], FILTER_VALIDATE_URL) ): ?><li class="web"><a href="<?=$company['url']?>" target="_blank" rel="nofollow">Web</a></li><?php endif; ?>
                <?php if( filter_var($company['linkedin'], FILTER_VALIDATE_URL) ): ?><li class="linkedin"><a href="<?=$company['linkedin']?>" target="_blank" rel="nofollow">LinkedIn</a></li><?php endif; ?>
                <li class="creation">Se unió en <?=$date_formatted?></li>
            </ul>
            <div class="company-single__buttons">
                <a href="<?=home_url('oportunidades/?empresa=' . $company['name'] .'/')?>" class="btn btn-primary-pink">Ver oportunidades laborales</a>
            </div>
        </div>
    </section>
    <section class="section-1 pb-xl">
        <div class="row-xs ta-c">
            <h3 class="c-blue">Sobre nosotros</h3>
            <div class="company-single__content"><?=$company['description']?></div>
            <?php if( filter_var($company['video_youtube'], FILTER_VALIDATE_URL) ): ?>
                <div class="company-single__video-wrapper">
                    <div class="companny-single__video"><?=wp_oembed_get($company['video_youtube'])?></div>
                </div>
            <?php endif; ?>
            <?php if( 
                filter_var($company['twitter'], FILTER_VALIDATE_URL) ||
                filter_var($company['facebook'], FILTER_VALIDATE_URL) ||
                filter_var($company['instagram'], FILTER_VALIDATE_URL)
            ): ?>
            <div class="company-single__social">
                <strong>Síguenos en:</strong>
                <?php if( filter_var($company['twitter'], FILTER_VALIDATE_URL) ): ?><a href="<?=$company['twitter']?>" target="_blank" rel="nofollow" class="twitter">Twitter</a><?php endif; ?>
                <?php if( filter_var($company['facebook'], FILTER_VALIDATE_URL) ): ?><a href="<?=$company['facebook']?>" target="_blank" rel="nofollow" class="facebook">Facebook</a><?php endif; ?>
                <?php if( filter_var($company['instagram'], FILTER_VALIDATE_URL) ): ?><a href="<?=$company['instagram']?>" target="_blank" rel="nofollow" class="instagram">Instagram</a><?php endif; ?>
            </div>
        <?php endif; ?>
    </div>
</section>
</article>

<?php 

if ( locate_template('footer-simple.php') ) {
    get_footer('simple');
} else {
    get_footer();
}
 ?>