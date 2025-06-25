<?php
get_header();
global $wp_query;
global $categoria_list;

$category_list         = $GLOBALS['categoria_list'];

$settings = get_option('_turimet_oportunity_settings', []);

$btn_url = '#';
if( ! is_user_logged_in() ){
	$btn_url = "/login";
}

global $post;

$post_author_id = (int) $post->post_author;
$user_info = get_userdata($post_author_id);

setlocale(LC_TIME, 'es_ES.UTF-8'); // Solo para entornos que respetan locale
$date_string = get_the_date('F j, Y', $post); // Esto da: "February 14, 2025"
$date_string = ucfirst(date_i18n('F j, Y', strtotime(get_the_date('Y-m-d H:i:s', $post))));

 $min_salary 	=  get_post_meta($post->ID, '_salario_min', true);
        $max_salary		= get_post_meta($post->ID, '_salario_max', true);
        $currency 		=  get_post_meta($post->ID, '_currency', true);

 $currency_symbol = ''; // Aquí puedes usar una función propia para obtener símbolo si quieres
 switch($currency) {
 	case 'USD': $currency_symbol = '$/'; break;
 	case 'EUR': $currency_symbol = '€/'; break;
 	case 'SOL': $currency_symbol = 'S/'; break;
            // etc
 	default: $currency_symbol = '';
 }

 if (floatval($min_salary) == 0 && floatval($max_salary) != 0) {
 	$salary = $currency_symbol . number_format(floatval($max_salary), 2);
 } elseif (floatval($min_salary) != 0 && floatval($max_salary) == 0) {
 	$salary = $currency_symbol . number_format(floatval($min_salary), 2);
 } else {
 	$salary = sprintf('%3$s%1$s a %3$s%2$s', number_format(floatval($min_salary), 2), number_format(floatval($max_salary), 2), $currency_symbol);
 }

$code = ''; // Inicializamos
$empresa_pais = get_user_meta($post_author_id, 'empresa_pais', true);
foreach ($countries as $country) {
	if (strcasecmp($country['name'], $empresa_pais) === 0) {
		$code = $country['code'];
		break;
	}
}

$oportunity = [
    'ID' => $post->ID,
    'title' => get_the_title($post),
    'url' => get_permalink($post),
    'created_at' => ucfirst(date_i18n('F j, Y', strtotime(get_the_date('Y-m-d', $post)))),
    'description' => apply_filters('the_content', $post->post_content),
    'modality' => get_post_meta($post->ID, '_modalidad', true),
    'currency' => get_post_meta($post->ID, '_currency', true),
    'category' => get_post_meta($post->ID, '_categoria', true),
    'type_day' => get_post_meta($post->ID, '_jornada', true),
    'type_contract' => get_post_meta($post->ID, '_contrato', true),
    'enlace_reportar' => get_post_meta($post->ID, '_enlace_reportar', true),
    'salary' => $salary,

    // Información del autor / empresa
    'author_id' => $post_author_id,
    'company_url' => $user_info ? $user_info->user_nicename : '',
    'company_name'  => get_user_meta($post_author_id, 'empresa_nombre', true),
    'country'       => get_user_meta($post_author_id, 'empresa_pais', true),
    'company_image' => get_user_meta($post_author_id, 'empresa_icono', true), // aquí tienes URL
    'company_background' => get_user_meta($post_author_id, 'empresa_fondo', true), // URL
    'company_url'  => get_author_posts_url($post_author_id),
    'code' => $code,
];
?>
<style>
	a.btn[data-action]{
		display: flex;
		align-items: center;
	}
	.oportunity-single button, .oportunity-single .btn, .oportunity-single li:is(.btn-primary, .oportunity-single .btn-secondary, .oportunity-single .btn-third) > a ,.oportunity-single  a.btn[data-action]{
    padding: .8em 1.5em;
    position: relative;
    background-color: var(--btn-background);
    border: 1px solid var(--btn-background);
    border-radius: 8px;
    color: var(--btn-color);
    display: inline-flex
;
    letter-spacing: 0.5px;
    user-select: none;
    cursor: pointer;
}
</style>
<div class="sub-header section-1 section-p-xs">
	<div class="row">
		<a href="<?=home_url('oportunidades/')?>" class="link-back">regresar</a>
	</div>
</div>
<article class="oportunity-single">
	<div class="turimet-account__alerts"></div>
	<section class="section-3 hero-sop section-yp-xl">
		<div class="section__background">
			<figure class="fig-cover">
				<img src="<?php echo OPT_CUSTOM_PLUGIN_URL . 'assets/images/hero-single-oportunity.jpg'; ?>" alt="">
			</figure>
		</div>
		<div class="row cols-2 t-cols-1 m-cols-1">
			<div>
				<ul class="oportunity-single__company">
					<li><figure class="fig-contain"><img src="<?=$oportunity['company_image']?>" /></figure></li>    
					<li>
						<a href="<?=$oportunity['company_url']?>"><?=$oportunity['company_name']?></a>
						<span class="oportunity-single__country"><div class="iti__flag iti__<?=$oportunity['code']?>"></div> <?=$oportunity['country']?></span>
					</li>
				</ul>
				<h2 class="oportunity-single__title"><?=$oportunity['title']?></h2>
				<div class="oportunity-entry__header-salary">Salario: <strong><?=$oportunity['salary']?></strong></div>
				<div class="oportunity-single__buttons">
					<?php
                    if ( is_user_logged_in() ) {
                        $url = $btn_url;
                        $id = $oportunity['ID'];
                        $extra_attrs = 'data-action="postulate" data-id="'.$id.'"';
                    } else {
                        $url = home_url('/iniciar-sesion');  // O la URL que uses para el login
                        $extra_attrs = 'target="_blank"';
                    }
                ?>
                <a href="<?= esc_url($url) ?>" <?= $extra_attrs ?> class="btn btn-primary pink" >Postular</a>
					<div class="share-select" data-url="<?=$oportunity['url']?>">
						<select>
							<option value="">Compartir</option>
								<option value="email">Correo</option>
								<option value="whatsapp">Whatsapp</option>
								<option value="facebook">Facebook</option>
								<option value="linkedin">LinkedIn</option>
								<option value="clipboard">Copiar enlace</option>
						</select>
					</div>
				</div>
			</div>
		</div>
	</section>
	<section class="oportunity-single__content-wrap">
		<div class="row">
			<div class="oportunity-single__content">
				<aside>
					<ul class="oportunity-entry__meta">
						<li class="creation"><?=$oportunity['created_at']?></li>
						<li class="category"><?=$oportunity['category']?></li>
						<li class="day"><?=$oportunity['type_day']?></li>
						<li class="contract"><?=$oportunity['type_contract']?></li>
						<li class="modality"><?=$oportunity['modality']?></li>
					</ul>
					<div class="oportunity-single__match no-mobile">
						<h4>Haz match con Turimet</h4>
					
						<?php
                    if ( is_user_logged_in() ) {
                        $url = $btn_url;
                        $id = $oportunity['ID'];
                        $extra_attrs = 'data-action="postulate" data-id="'.$id.'"';
                    } else {
                        $url = home_url('/iniciar-sesion');  // O la URL que uses para el login
                        $extra_attrs = 'target="_blank"';
                    }
                ?>
                <a href="<?= esc_url($url) ?>" <?= $extra_attrs ?> class="btn btn-primary pink" style="min-height: 56px;">Postular</a>
						<div class="share-select" data-url="<?=$oportunity['url']?>">
							<select>
								<option value="">Compartir</option>
								<option value="email">Correo</option>
								<option value="whatsapp">Whatsapp</option>
								<option value="facebook">Facebook</option>
								<option value="linkedin">LinkedIn</option>
								<option value="clipboard">Copiar enlace</option>
							</select>
						</div>
					</div>
				</aside>
				<div>
					<h3>Descripción del puesto</h3>
					<?=$oportunity['description']?>
				</div>
				<div class="oportunity-single__more">
					<?php if ($oportunity['enlace_reportar'] != '') { ?>
						<a href="<?php echo $oportunity['enlace_reportar']; ?>" target="_blank" rel="nofollow" class="link-report">Reportar oportunidad</a>
					<?php } ?>
					
					<div class="oportunity-single__tags no-mobile">
    <?php foreach( $category_list as $item ): ?>
        <a href="<?= home_url('oportunidades/?category=' . urlencode($item['name']) . '&Filtrar=Filtrar') ?>">
            <?= $item['title'] ?>
        </a>
    <?php endforeach; ?>
</div>

				</div>
			</div>
		</div>
		<div class="row">
			<a href="<?=$oportunity['company_url']?>" class="oportunity-single__company--a">
			<h4>Acerca de <?=$oportunity['company_name']?></h4>

			<ul class="oportunity-single__company">
				<li><figure class="fig-contain"><img src="<?=$oportunity['company_image']?>" /></figure></li>    
				<li>
					<?=@$company['short_description']?>
				</li>
			</ul>
			</a>
		</div>
	</section>
</article>
<script>
  document.addEventListener('DOMContentLoaded', function () {
 
  document.querySelectorAll('.share-select select').forEach(select => {
		let choices = new Choices(select, {itemSelectText: '',searchEnabled:false,shouldSort:false});

		choices.passedElement.element.addEventListener('change', evt => {
			let selectedOption = choices.getValue().value;
			let current_url = select.closest('.share-select').dataset.url;

			console.log(selectedOption);

			switch(selectedOption){
				case 'clipboard':
					let tempInput = document.createElement('input');
					tempInput.setAttribute('value', current_url);
					document.body.appendChild(tempInput);
					tempInput.select();
					document.execCommand('copy');
					document.body.removeChild(tempInput);
				break;
				case 'linkedin':
					window.open('https://www.linkedin.com/shareArticle?mini=true&url=' + encodeURIComponent(current_url), '_blank');
				break;
				case 'whatsapp':
					window.open('https://api.whatsapp.com/send?text=' + encodeURIComponent(current_url), '_blank');
				break;
				case 'email':
					var emailSubject = 'Echa un vistazo a este enlace';
					var emailBody = 'Hola,\n\nMira este enlace: ' + current_url;
					window.open('mailto:?subject=' + emailSubject + '&body=' + emailBody, '_blank');
				break;
			}
			
			choices.setChoiceByValue(['']);
		})
	})
    });
</script>
<?php 
if ( locate_template('footer-simple.php') ) {
    get_footer('simple');
} else {
    get_footer();
}
 ?>