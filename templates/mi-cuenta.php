<?php
/**
 * Template name: Usuario - Perfil - OPT
 */
$settings = get_option('_turimet_user_settings', []);
$url = home_url();

// Verifica si el usuario está logueado
if (is_user_logged_in()) {

    $current_user = wp_get_current_user();
    // Verifica si el usuario tiene el rol de "subscriber"
    if (in_array('subscriber', (array) $current_user->roles)) {
        $url = home_url("mi-cuenta-opt");
        // Redirige al usuario
        wp_safe_redirect($url);
        exit;
    }
}

get_header('full'); 
$country_list = \Turimet\Control\API::country_list();
$keyword_list = \Turimet\Control\API::keyword_list();
if( is_array($keyword_list) && isset($keyword_list['data']) ){
	$keyword_list = $keyword_list['data'];
} else {
	$keyword_list = false;
}

$current_member = \Turimet\Control\API::user_get_current();

$avatar = false;
if( filter_var($current_member['avatar'], FILTER_VALIDATE_URL) && getimagesize($current_member['avatar']) !== false ){
	$avatar = $current_member['avatar'];
}
?>
<style>
	.turimet-account__alerts {
		position: fixed;
/*  left: 50%;*/
left: 10%;
transform: translateY(50%);
top: 20%;
max-width: 100%;
width: 100%;
z-index: 9;
align-items: center;
justify-content: center;
text-align: center;

}
.account-alert{
	padding: 15px 30px;
	display: flex;
	flex-direction: column;
}
.account-alert ul{
	padding-top: 10px;
	padding-bottom: 10px;
}
.account-alert ul li{
	text-align: left;
}

.select2-container--default .select2-selection--single {
	background-color: var(--tu-surface-1) !important;
	border: .5px solid var(--tu-grey-2) !important;
	border-radius:
	4px !important;
	height: 46px !important;
}
.select2-container{
	width: 100% !important;
}
.select2-container--default .select2-selection--single .select2-selection__rendered {
	color: #444;
	line-height: 28px;
	height: 46px !important;
	display: flex !important;
	align-items: center !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow {
	height: 40px !important;
	position: absolute;
	top: 1px;
	right: 5px !important ;
	width: 20px;
}

.select2-container--default .select2-selection--single .select2-selection__rendered{
	color: #292D3F !important;
}

.select2-container--default .select2-selection--single .select2-selection__placeholder {
	color: #292D3F !important;
}
.select2-container--default .select2-selection--single .select2-selection__arrow b{
	border-width: 7px 5px 0px 5px !important;
	margin-left: -5px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered{
	padding-left: 13px !important;
}
.select2-container .select2-selection--single .select2-selection__rendered{
	white-space: wrap !important;
}
.form .ff-input.error > input, .ff.error .ff__field > :is(input,select,textarea), :is(input,select,textarea).has-error{
	border-width: 2px !important;
}
</style>	
<script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
<!-- CSS -->
<link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
<!-- JS -->
<script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

<div class="turimet-account">
	<aside class="turimet-account__aside no-mobile">
		<ul class="turimet-account__menu">
			<?php wp_nav_menu(['theme_location' => 'account-menu', 'container' => false, 'items_wrap' => '%3$s', 'walker' => new \Turimet\Menu\Account_Menu_Walker ]); ?>
		</ul>
	</aside>
	<main class="turimet-account__main">
		<div class="turimet-account__alerts"></div>
		<div class="row">
			<h2 class="tab-titles c-blue"><span class="active" data-step="1">1. Completa tu perfil</span> <span data-step="2">2. Crea tu CV Turimet</span></h2>

			<div class="ao-messages">
				<div class="ao-message"><span>Completa tu perfil al 100% para postular a las oportunidades laborales de nuestra plataforma.</span></div>
			</div>

			<form action="" id="profile-form" method="post" enctype="multipart/form-data">
				<div class="form2" data-step="1">
					<div class="ff ff-input">
						<div class="ff__info">
							<h3>Foto de Perfil</h3>
							<small>¡Bienvenido! Ingresa una foto para tu CV en nuestra base de datos. Es el primer paso para que te conozcan, y destacar tu perfil.</small>
						</div>
						<div class="ff__field">
							<div class="ff__field-wrap">
								<div class="ff__field-image-upload<?=$avatar?' active':''?>">
									<figure>
										<img src="<?=$avatar?>" alt="" data-input="avatar" />
									</figure>
									<div>
										<label><input type="file" name="avatar" data-input="avatar-upload" id="avatar" class="hidden" /><span>Seleccionar imagen</span></label>
										<small>*Selecciona mínimo 200x200 px</small>
									</div>
								</div>
							</div>
						</div>
					</div>
					<div class="ff ff-input">
						<div class="ff__info">
							<h3>Nombres<abbr class="required" title="Obligatorio">*</abbr></h3>
							<small>Ingresa tus nombres completos.</small>
						</div>
						<div class="ff__field">
							<div class="ff__field-wrap">
								<input type="text" name="first_name" id="first_name" data-validate="name" maxlength="50" value="<?=isset($current_member['first_name'])?$current_member['first_name']:''?>" />
							</div>
						</div>
					</div>
					<div class="ff ff-input">
						<div class="ff__info">
							<h3>Apellidos<abbr class="required" title="Obligatorio">*</abbr></h3>
							<small>Ingresa tus apellidos completos.</small>
						</div>
						<div class="ff__field">
							<div class="ff__field-wrap">
								<input type="text" name="last_name" id="last_name" data-validate="name" maxlength="50" value="<?=isset($current_member['last_name'])?$current_member['last_name']:''?>" />
							</div>
						</div>
					</div>
					<div class="ff ff-input">
						<div class="ff__info">
							<h3>DNI o Documento de identidad<abbr class="required" title="Obligatorio">*</abbr></h3>
							<small>Ingresa tu documento de identidad o Carnet de Extranjería.</small>
						</div>
						<div class="ff__field">
							<div class="ff__field-wrap">
								<div class="ff__field-document">
									<select name="document_type" id="document_type">
										<option value="" selected disabled>Tipo de doc.</option>
										<?php if(is_array(\Turimet\Control\API::document_type_list())) foreach(\Turimet\Control\API::document_type_list() as $item): ?>
										<option value="<?=$item['name']?>"<?=($current_member['document_type']==$item['name'])?' selected':''?>><?=$item['title']?></option>
									<?php endforeach; ?>
								</select>
								<input type="text" name="document_number" id="document_number" data-validate="document_number" maxlength="12" value="<?=isset($current_member['document_number'])?$current_member['document_number']:''?>" maxlength="12" />
							</div>
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Género<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Selecciona tu género</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<select name="gender" id="gender">
								<option value="" selected disabled>Género</option>
								<option value="Femenino"<?=(isset($current_member['gender']) && 'Femenino'==$current_member['gender'])?' selected':''?>>Femenino</option>
								<option value="Masculino"<?=(isset($current_member['gender']) && 'Masculino'==$current_member['gender'])?' selected':''?>>Masculino</option>
							</select>
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Fecha de nacimiento<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Selecciona tu fecha de nacimiento</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<?php
							$currentDate = current_time('mysql');
							$dateTime = new DateTime($currentDate);
							$dateTime->modify('-18 years');
							$newDate = $dateTime->format('Y-m-d');

							$edad = '';
							if( !empty($current_member['born_date']) ){
								$fechaNacimiento = new DateTime($current_member['born_date']);
								$fechaActual = new Datetime(current_time('mysql'));
								$edad = $fechaActual->diff($fechaNacimiento)->y;
							}
							?>
							<input type="date" name="born_date" id="born_date" value="<?=isset($current_member['born_date'])?$current_member['born_date']:''?>" max="<?=$newDate?>" />
							<div class="result-div"><span>Tu edad:</span> <input type="text" value="<?=$edad?>" readonly id="years_old"></div>
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Lugar de nacimiento<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Ingresa el país de nacimiento.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap ff__field-country">
							<select name="country_born" id="country_born" data-input="country">
								<option value="" selected>País</option>
								<?php foreach( $country_list['data'] as $item ): ?>
									<option value="<?=$item['name']?>"<?=(isset($current_member['country']) && $item['name']==$current_member['country'])?' selected':''?>><?=$item['name']?></option>
								<?php endforeach; ?>
							</select>
							<select <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden"':''?> name="region_born" id="region_born" data-input="region">
								<option value="" selected disabled>Departamento</option>
								<?php 
								if('Peru'==$current_member['country'] && !empty($current_member['state'])){
									$options = \Turimet\Control\API::ubigeo_region($current_member['country']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['state']==$option?' selected':'');
									}
								}
								?>
							</select>
							<select  <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden"':''?> name="province_born" 
								id="province_born" data-input="province">
								<option value="" selected disabled>Provincia</option>
								<?php 
								if('Peru'==$current_member['country'] && !empty($current_member['state']) && !empty($current_member['county'])){
									$options = \Turimet\Control\API::ubigeo_province($current_member['country'], $current_member['ubigeo']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['county']==$option?' selected':'');
									}
								}
								?>
							</select>
							<select <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden"':''?> name="city_born" id="city_born" data-input="city">
								<option value="" selected disabled>Distrito</option>
								<?php 
								if('Peru'==$current_member['country'] && !empty($current_member['state']) && !empty($current_member['county']) && !empty($current_member['city'])){
									$options = \Turimet\Control\API::ubigeo_city($current_member['country'], $current_member['ubigeo']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['city']==$option?' selected':'');
									}
								}
								?>
							</select>

							
							<input type="hidden" name="ubigeo_born" id="ubigeo" data-input="ubigeo" value="<?=isset($current_member['ubigeo'])?$current_member['ubigeo']:''?>" />
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Lugar de residencia<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Ingresa el país en el que te encuentras residiendo actualmente.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap ff__field-country">
							<select name="country_res" id="country_res" data-input="country">
								<option value="" selected>País</option>
								<?php foreach( $country_list['data'] as $item ): ?>
									<option value="<?=$item['name']?>"<?=(isset($current_member['country_r']) && $item['name']==$current_member['country_r'])?' selected':''?>><?=$item['name']?></option>
								<?php endforeach; ?>
							</select>
							<select <?=(isset($current_member['state_r']) && 'Peru'!=$current_member['country_r'])?'class="hidden"':''?> name="region_res" id="region_res" data-input="region">
								<option value="" selected disabled>Departamento</option>
								<?php 
								if('Peru'==$current_member['country_r'] && !empty($current_member['state_r'])){
									$options = \Turimet\Control\API::ubigeo_region($current_member['country_r']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['state_r']==$option?' selected':'');
									}
								}
								?>
							</select>
							<select <?=(isset($current_member['county_r']) && 'Peru'!=$current_member['country_r'])?'class="hidden"':''?> name="province_res" id="province_res" data-input="province">
								<option value="" selected disabled>Provincia</option>
								<?php 
								if('Peru'==$current_member['country_r'] && !empty($current_member['state_r']) && !empty($current_member['county_r'])){
									$options = \Turimet\Control\API::ubigeo_province($current_member['country_r'], $current_member['ubigeo_r']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['county_r']==$option?' selected':'');
									}
								}
								?>
							</select>
							<select <?=(isset($current_member['city_r']) && 'Peru'!=$current_member['country_r'])?'class="hidden"':''?> name="city_res" id="city_res" data-input="city">
								<option value="" selected disabled>Distrito</option>
								<?php 
								if('Peru'==$current_member['country_r'] && !empty($current_member['state_r']) && !empty($current_member['county_r']) && !empty($current_member['city_r'])){
									$options = \Turimet\Control\API::ubigeo_city($current_member['country_r'], $current_member['ubigeo_r']);

									foreach($options as $id => $option){
										printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['city_r']==$option?' selected':'');
									}
								}
								?>
							</select>
							<input type="hidden" name="ubigeo_res" data-input="ubigeo" value="<?=isset($current_member['ubigeo_r'])?$current_member['ubigeo_r']:''?>" />
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Dirección<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Ingresa tu cuenta de correo con el que nos podremos comunicar.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<input type="text" name="address" id="address" maxlength="100" data-validate="text" value="<?=isset($current_member['address'])?$current_member['address']:''?>" placeholder="Ingresa la dirección donde vives actualmente" />
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Correo<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Ingresa tu cuenta de correo con el que nos podremos comunicar.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<input type="text" name="email" id="email" data-validate="email" maxlength="80" value="<?=isset($current_member['email'])?$current_member['email']:''?>" />
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Celular<abbr class="required" title="Obligatorio">*</abbr></h3>
						<small>Ingresa tu numero de celular con el que te podemos contactar.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<input type="text" name="mobile_visible" id="mobile_visible" maxlength="15" value="<?=isset($current_member['mobile'])?'+'.trim($current_member['mobile']):''?>" />
							<input type="hidden" name="mobile" id="mobile" maxlength="15" value="<?=isset($current_member['mobile'])?'+'.trim($current_member['mobile']):''?>" />
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Colegiatura</h3>
						<small>Ingresa los datos de tu colegio si te encuentras colegiado actualmente.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<div class="ff__field--college">
								<select name="has_colegiatura" id="has_colegiatura">
									<option value="" selected disabled>Elegir Si/No</option>
									<option value="1"<?=(isset($current_member['has_colegiatura']) && '1'==$current_member['has_colegiatura'])?' selected':''?>>Si</option>
									<option value="0"<?=(isset($current_member['has_colegiatura']) && '1'!=$current_member['has_colegiatura'])?' selected':''?>>No</option>
								</select>
								<div<?=(isset($current_member['has_colegiatura']) && '1'==$current_member['has_colegiatura'])?'':' class="hidden"'?>>
									<input type="text" name="colegiatura_school" maxlength="60" id="colegiatura_school" data-validate="text" placeholder="Nombre del colegio" value="<?=isset($current_member['colegiatura_school'])?esc_attr($current_member['colegiatura_school']):''?>" />
									<input type="text" name="colegiatura_number" maxlength="20" id="colegiatura_number" placeholder="Ingresa tu número de colegiatura" value="<?=isset($current_member['colegiatura_number'])?esc_attr($current_member['colegiatura_number']):''?>" />
								</div>
							</div>
						</div>
					</div>
				</div>
				<div class="ff ff-input ff-rrss">
					<div class="ff__info">
						<h3>Perfiles de redes sociales</h3>
						<small>Estar presente en las redes sociales incrementa tus oportunidades. Compártenos las redes sociales que tienes.</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap ff__field-wrap-2col">
							<div class="ff__social">
								<label for="linkedin">Linkedin Companypage</label>
								<span>https://linkedin.com/</span>
								<input name="linkedin" id="linkedin" data-validate="username" type="text" maxlength="50" placeholder="company/tuempresa" value="<?=isset($current_member['linkedin'])?esc_attr($current_member['linkedin']):''?>" />
							</div>
							<div class="ff__social">
								<label for="twitter">Twitter</label>
								<span>https://twitter.com/</span>
								<input name="twitter" id="twitter" data-validate="username" type="text" maxlength="32" placeholder="usuario sin @" value="<?=isset($current_member['twitter'])?esc_attr($current_member['twitter']):''?>" />
							</div>
							<div class="ff__social">
								<label for="facebook">Facebook Fanpage</label>
								<span>https://facebook.com/</span>
								<input name="facebook" id="facebook" data-validate="username" type="text" maxlength="50" placeholder="fanpage" value="<?=isset($current_member['facebook'])?esc_attr($current_member['facebook']):''?>" />
							</div>
							<div class="ff__social">
								<label for="youtube">YouTube</label>
								<span>https://youtube.com/</span>
								<input name="youtube" id="youtube" data-validate="username" type="text" maxlength="50" placeholder="nombre del canal" value="<?=isset($current_member['youtube'])?esc_attr($current_member['youtube']):''?>" />
							</div>
							<div class="ff__social">
								<label for="instagram">Instagram</label>
								<span>https://instagram.com/</span>
								<input name="instagram" id="instagram" data-validate="username" type="text" maxlength="32" placeholder="usuario sin @" value="<?=isset($current_member['instagram'])?esc_attr($current_member['instagram']):''?>" />
							</div>
							<div class="ff__social">
								<label for="tiktok">Tiktok</label>
								<span>https://tiktok.com/</span>
								<input name="tiktok" id="tiktok" data-validate="username" type="text" maxlength="32" placeholder="usuario sin @" value="<?=isset($current_member['tiktok'])?esc_attr($current_member['tiktok']):''?>" />
							</div>
						</div>
					</div>
				</div>
				<div class="ff ff-input">
					<div class="ff__info">
						<h3>Palabras claves</h3>
						<small>Escribe algunas palabras que describan de manera puntual tu perfil profesional. (Ejemplo: Docente de pre grado, Especialista en Planificación Turística y Destinos Turísticos, etc.)</small>
					</div>
					<div class="ff__field">
						<div class="ff__field-wrap">
							<select name="keywords[]" multiple="multiple">
								<?php 
								$member_keys = [];
								if( isset($current_member['key1']) ) $member_keys[] = $current_member['key1'];
								if( isset($current_member['key2']) ) $member_keys[] = $current_member['key2'];
								if( isset($current_member['key3']) ) $member_keys[] = $current_member['key3'];

								if(is_array($keyword_list)) foreach($keyword_list as $kw): ?>
									<option value="<?=$kw['name']?>"<?=in_array($kw['name'], $member_keys)?' selected':''?>><?=$kw['title']?></option>
							<?php endforeach; ?>
						</select>
					</div>
				</div>
			</div>
		</div>
		<div class="form2 hidden" data-step="2">
			<h2 class="ff-heading">Sobre ti</h2>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Descripción de Perfil profesional</h3>
					<small>Ingresa una descripción breve de tu experiencia profesional para que los reclutadores te conozcan.</small>
				</div>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<textarea name="profile" id="profile" rows="3" maxlength="1200" data-validate="text" placeholder="Ingresa una descripción corta sobre ti"><?=isset($current_member['profile'])?esc_textarea($current_member['profile']):''?></textarea>
					</div>
				</div>
			</div>
			<h2 class="ff-heading">Información de Formación</h2>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Ingresa tus estudios<abbr class="required" title="Obligatorio">*</abbr></h3>
					<small>Dale click a “Agregar formación” para completar los datos de tu formación académica.</small>
				</div>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<div data-input="studies">
							<?php
							if( isset($current_member['studies']) && is_array($current_member['studies']) && !empty($current_member['studies']) ){
								foreach($current_member['studies'] as $study){
									get_template_part('template-parts/account', 'studies', ['study' => $study]);
								}
							}
							?>
						</div>
						<a href="javascript:void(0);" data-action="add-studies" class="btn btn-primary" id="add-formation-btn">
							<?php echo (!empty($current_member['studies']) && is_array($current_member['studies'])) ? 'Agregar otra formación' : 'Agregar formación'; ?>
						</a>

					</div>
				</div>
			</div>
			<h2 class="ff-heading">Información de Formación Complementaria</h2>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Especialidad profesional</h3>
					<small>Dale click a “Agregar formación” para completar los datos de tu formación complementaria.</small>
				</div>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<div data-input="complimentary">
							<?php
							if( isset($current_member['complementary_studies']) && is_array($current_member['complementary_studies']) && !empty($current_member['complementary_studies']) ){
								foreach($current_member['complementary_studies'] as $study){
									get_template_part('template-parts/account', 'complementary', ['study' => $study]);
								}
							}
							?>
						</div>
						<a href="javascript:void(0);" data-action="add-complimentary" class="btn btn-primary" id="add-especialidad-btn">
							<?php echo (!empty($current_member['complementary_studies']) && is_array($current_member['complementary_studies'])) ? 'Agregar otra expecialidad' : 'Agregar expecialidad'; ?>
						</a>

					</div>
				</div>
			</div>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Idiomas</h3>
					<small>Selecciona el idioma o idiomas que manejas.</small>
				</div>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<div data-input="langs">
							<?php
							if( isset($current_member['language']) && is_array($current_member['language']) && !empty($current_member['language']) ){
								foreach($current_member['language'] as $item){
									get_template_part('template-parts/account', 'langs', ['lang' => $item]);
								}
							}
							?>
						</div>
						<a href="javascript:void(0);" data-action="add-lang" class="btn btn-primary" id="add-idioma-btn">
							<?php echo (isset($current_member['language']) && is_array($current_member['language']) && !empty($current_member['language'])) ? 'Agregar otro idioma' : 'Agregar idioma'; ?>
						</a>

					</div>
				</div>
			</div>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Conocimientos técnicos y/o habilidades personales, informáticas, otras.</h3>
					<small>Selecciona el conocimiento o habilidades que manejas.</small>
				</div>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<select name="skills[]" id="skills" multiple="multiple">
							<?php 
							$member_keys = [];
							if( isset($current_member['skills']) && is_array($current_member['skills']) && !empty($current_member['skills']) ){
								$member_keys = array_column($current_member['skills'], 'skill');
							}
							
							$skill_list = \Turimet\Control\API::skills_list(true);

							if(is_array($skill_list)) foreach($skill_list as $skill): ?>
								<option value="<?=$skill['name']?>"<?=in_array($skill['name'], $member_keys)?' selected':''?>><?=$skill['title']?></option>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
		<h2 class="ff-heading">Información de Experiencia Profesional</h2>
		<div class="ff ff-input">
			<div class="ff__info">
				<h3>Experiencia profesional</h3>
				<small>Dale click a “Agregar experiencia” para completar los datos de tu experiencia laboral.</small>
			</div>
			<div class="ff__field">
				<div class="ff__field-wrap">
					<div data-input="experience">
						<?php
						if( isset($current_member['experience']) && is_array($current_member['experience']) && !empty($current_member['experience']) ){
							foreach($current_member['experience'] as $item){
								get_template_part('template-parts/account', 'experience', ['exp' => $item]);
							}
						}
						?>
					</div>
					<a href="javascript:void(0);" data-action="add-experience" class="btn btn-primary" id="add-experiencia-btn">
						<?php echo (!empty($current_member['experience']) && is_array($current_member['experience'])) ? 'Agregar otra experiencia' : 'Agregar experiencia'; ?>
					</a>

				</div>
			</div>
		</div>
		<div class="ff ff-input">
			<div class="ff__info">
				<h3>Futuro empleo</h3>
				<small>Dale click para agregar tus intereses laborales e ingresa tu expectativa salarial.</small>
			</div>
			<div class="ff__field">
				<div class="ff__field-wrap ff__field-wrap-3col">
					<select name="sector_interest" id="sector_interest">
						<option value="" selected>Sector de interés</option>
						<?php 
						$list = \Turimet\Control\API::sector_list();
						$list = ( is_array($list) && isset($list['data']) ) ? $list['data'] : false;

						if(is_array($list)) foreach($list as $item): ?>
							<option value="<?=$item['name']?>"<?=(isset($current_member['sector_interest']) && $current_member['sector_interest']==$item['name'])?' selected':''?>><?=$item['title']?></option>
					<?php endforeach; ?>
				</select>
				<select name="availability" id="availability">
					<option value="" selected>Disponibilidad</option>
					<?php 
					$list = \Turimet\Control\API::availability_list();
					$list = ( is_array($list) && isset($list['data']) ) ? $list['data'] : false;

					if(is_array($list)) foreach($list as $item): ?>
						<option value="<?=$item['name']?>"<?=(isset($current_member['availability']) && $current_member['availability']==$item['name'])?' selected':''?>><?=$item['title']?></option>
				<?php endforeach; ?>
			</select>
			<select name="type_day" id="type_day">
				<option value="" selected>Tipo de jornada</option>
				<?php 
				$list = \Turimet\Control\API::type_day_list();
				$list = ( is_array($list) && isset($list['data']) ) ? $list['data'] : false;

				if(is_array($list)) foreach($list as $item): ?>
					<option value="<?=$item['name']?>"<?=(isset($current_member['type_day']) && $current_member['type_day']==$item['name'])?' selected':''?>><?=$item['title']?></option>
			<?php endforeach; ?>
		</select>
		<input type="text" value="<?=esc_attr($current_member['min_salary'])?>" name="min_salary" id="min_salary" data-validate="int" maxlength="6" placeholder="Salario mínimo" />
		<input type="text" value="<?=esc_attr($current_member['max_salary'])?>" name="max_salary" id="max_salary" data-validate="int" maxlength="6" placeholder="Salario máximo" />
	</div>
</div>
</div>
<div class="ff ff-input">
	<div class="ff__info">
		<h3>Sube tu CV</h3>
		<small>Carga tu C.V.</small>
	</div>
	<div class="ff__field">
		<div class="ff__field-upload cv-upload">
			<input type="file" name="cv" id="cv" class="hidden" />
			<label for="cv" class="btn btn-secondary pink"><span>Subir CV</span></label>
			<small>Sube tu CV en formato pdf, .doc o .docx. Peso máximo 2 MB*</small>
		</div>
	</div>
</div>
<div class="ff ff-input">
	<div class="ff__info no-mobile"></div>
	<div class="ff__field">
		<label class="checkbox-label"><input type="checkbox"<?=($current_member['agree_policy']==1)?' checked':''?> name="agree_policy" id="agree_policiy"> <span>Acepto la <a href="/politica-de-privacidad" target="_blank">Política de Privacidad</a>, los <a href="/terminos-y-condiciones/" target="_blank">Términos y Condiciones</a>, y las <a href="/terminos-y-condiciones-generales-de-contratacion/" target="_blank">Condiciones de Contratación.</a> </span></label>
	</div>
</div>
</div>
<div class="form-footer">
	<div>
		<input type="submit" value="Guardar cambios" class="btn" />
		<a href="javascript:void(0);" data-action="toggle-step" class="btn btn-secondary"><span>Siguiente</span></a>
	</div>
	<a href="<?=home_url('mi-cuenta/descargar/')?>" target="_blank" class="btn btn-secondary btn-download-cv"><span>Descargar CV</span></a>
</div>
</form>
</div>
</main>
</div>
<script id="tpl-studies" type="text/template">
	<div class="ff__field-group" data-key="{{key}}">
		<h3 class="ff__field-group--title" data-input="group-title"><span>Nueva formación</span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
		<div class="ff__field-group--content">
			<div class="ff__subfield">
				<div class="ff__subfield--caption">
					<label for="studies_{{key}}_grade">Grado de instrucción</label>
					<small>Selecciona tu grado académico.</small>
				</div>
				<div class="ff__subfield--field">
					<select name="studies[{{key}}][grade]" class="studies_grade_" id="studies_{{key}}_grade" data-input="grade" class="studies_grade_">
						<option value="" selected disabled>Selecciona tu grado de instrucción</option>
						<?php if(is_array(\Turimet\Control\API::grade_list())) foreach(\Turimet\Control\API::grade_list() as $item): ?>
						<option value="<?=$item['name']?>"><?=$item['title']?></option>
					<?php endforeach; ?>
				</select>
			</div>
		</div>
		<div class="ff__subfield">
			<div class="ff__subfield--caption">
				<label for="studies_{{key}}_specialty">Especialidad</label>
				<small>Escribe el nombre de tu especialidad/profesión.</small>
			</div>
			<div class="ff__subfield--field">
				<select name="studies[{{key}}][specialty]" class="studies_specialty_" id="studies_{{key}}_specialty" data-input="specialty" class="studies_specialty_">
					<option value="" selected disabled>Selecciona tu especialidad</option>
					<?php if(is_array(\Turimet\Control\API::specialty_list())) foreach(\Turimet\Control\API::specialty_list() as $item): ?>
					<option value="<?=$item['name']?>"><?=$item['title']?></option>
				<?php endforeach; ?>
			</select>
		</div>
	</div>
	<div class="ff__subfield">
		<div class="ff__subfield--caption">
			<label for="studies_{{key}}_institution">Centro de estudios</label>
			<small>Escribe el nombre de tu centro de estudios.</small>
		</div>
		<div class="ff__subfield--field">
			<select name="studies[{{key}}][institution]" class="studies__institution_" id="studies_{{key}}_institution" class="studies__institution_">
				<option value="" selected disabled>Selecciona el centro de estudios</option>
				<?php if(is_array(\Turimet\Control\API::institution_list())) foreach(\Turimet\Control\API::institution_list() as $item): ?>
				<option value="<?=$item['name']?>"><?=$item['title']?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
<div class="ff__subfield">
	<div class="ff__subfield--caption">
		<label for="studies_{{key}}_year_start">Año de ingreso y egreso</label>
		<small>Selecciona los años de ingreso y egreso de estudios.</small>
	</div>
	<div class="ff__subfield--field">
		<div class="ff__subfield--field-25-25-50">
			<select name="studies[{{key}}][year_start]" id="studies_{{key}}_year_start">
				<option value="" selected disabled>Ingreso</option>
				<?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
				<option value="<?=$item?>"><?=$item?></option>
			<?php endforeach; ?>
		</select>
		<select name="studies[{{key}}][year_end]" id="studies_{{key}}_year_end">
			<option value="" selected disabled>Egreso</option>
			<?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
			<option value="<?=$item?>"><?=$item?></option>
		<?php endforeach; ?>
	</select>
	<label class="checkbox-label"><input type="checkbox" name="studies[{{key}}][study_now]" id="experience_{{key}}_study_now"> <span>Estudiando actualmente</span></label>
</div>
</div>
</div>
</div>
</div>
</script>
<script id="tpl-complimentary" type="text/template">
	<div class="ff__field-group" data-key="{{key}}">
		<h3 class="ff__field-group--title" data-input="group-title"><span>Nueva formación</span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
		<div class="ff__field-group--content">
			<div class="ff__subfield">
				<select name="complementary_studies[{{key}}][type_course]" id="complementary_{{key}}_type" data-input="type_course">
					<option value="" selected disabled>Curso/Diplomado</option>
					<?php if(is_array(\Turimet\Control\API::type_course_list())) foreach(\Turimet\Control\API::type_course_list() as $item): ?>
					<option value="<?=$item['name']?>"><?=$item['title']?></option>
				<?php endforeach; ?>
			</select>
			<input type="text" name="complementary_studies[{{key}}][course]" id="complementary_{{key}}_course" placeholder="Nombre de la especialidad" data-validate="text" data-input="complementary_course">
			<input type="text" name="complementary_studies[{{key}}][hours]" id="complementary_{{key}}_hours" maxlength="3" data-validate="int" placeholder="Nro de horas">
			<input type="text" name="complementary_studies[{{key}}][institution]" id="complementary_{{key}}_institution" data-validate="text" placeholder="Institución u Organización">
			<select name="complementary_studies[{{key}}][year]" id="complementary_{{key}}_year">
				<option value="" selected disabled>Año</option>
				<?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
				<option value="<?=$item?>"><?=$item?></option>
			<?php endforeach; ?>
		</select>
	</div>
</div>
</div>
</script>
<script id="tpl-experience" type="text/template">
	<?php
	$currentDate = current_time('mysql');
	$dateTime = new DateTime($currentDate);
	$toDate = $dateTime->format('Y-m-d');
	?>
	<div class="ff__field-group" data-key="{{key}}">
		<h3 class="ff__field-group--title" data-input="group-title"><span>Nueva experiencia</span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
		<div class="ff__field-group--content">
			<div class="ff__subfield">
				<div class="ff__subfield--caption">
					<label for="experience_{{key}}_grade">Cargo, Rol y Tiempo</label>
					<small>Ingresa el nombre de tu cargo, fecha de inicio y de cierre, si corresponde.</small>
				</div>
				<div class="ff__subfield--field">
					<div class="ff__subfield--field-exp">
						<select name="experience[{{key}}][type_position]" id="experience_{{key}}_type_position" class="experiencepp">
							<option value="" selected disabled>Tipo de cargo</option>
							<?php if(is_array(\Turimet\Control\API::type_position_list())) foreach(\Turimet\Control\API::type_position_list() as $item): ?>
							<option value="<?=$item['name']?>"><?=$item['title']?></option>
						<?php endforeach; ?>
					</select>
					<input type="text" maxlength="50" name="experience[{{key}}][position]" id="experience_{{key}}_position" data-validate="text" placeholder="Ingresa el nombre del cargo" data-input="position">
					<input type="date" name="experience[{{key}}][date_initial]" id="experience_{{key}}_date_initial" placeholder="Fecha de inicio" max="<?=$toDate?>"/>
					<input type="date" name="experience[{{key}}][date_final]" id="experience_{{key}}_date_final" placeholder="Fecha de cierre" max="<?=$toDate?>"/>
					<label class="checkbox-label"><input type="checkbox" name="experience[{{key}}][currently_work]" id="experience_{{key}}_currently_work"> <span>Trabajo actualmente</span></label>
					<div class="result-div"><span>Tiempo en el cargo:</span> <input type="text" value="<?=$edad?>" readonly id="experience_{{key}}_time"></div>
				</div>
			</div>
		</div>
		<div class="ff__subfield">
			<div class="ff__subfield--caption">
				<label for="studies_{{key}}_specialty">Organización y sector</label>
				<small>Ingresa nombre de la empresa u organizacion.</small>
			</div>
			<div class="ff__subfield--field">
				<div class="ff__subfield--field-exp2">
					<input type="text" maxlength="60" name="experience[{{key}}][company]" id="experience_{{key}}_company" data-validate="text" placeholder="Ingresa el nombre de la empresa u organización" data-input="company">
					<select name="experience[{{key}}][sector]" id="experience_{{key}}_sector">
						<option value="" selected disabled>Selecciona el sector</option>
						<?php if(is_array(\Turimet\Control\API::sector_list(true))) foreach(\Turimet\Control\API::sector_list(true) as $item): ?>
						<option value="<?=$item['name']?>"><?=$item['title']?></option>
					<?php endforeach; ?>
				</select>
				<input type="text" maxlength="12" name="experience[{{key}}][salary]" id="experience_{{key}}_salary" placeholder="Ingresa el salario que percibiste en este puesto">
				<label class="checkbox-label"><input type="checkbox" name="experience[{{key}}][not_share_salary]" id="experience_{{key}}_not_share_salary"> <span>No deseo compartir esta información</span></label>
			</div>
		</div>
	</div>
	<div class="ff__subfield">
		<div class="ff__subfield--caption">
			<label for="studies_{{key}}_institution">Descripción de experiencia laboral</label>
			<small>Describe brevemente las funciones y logros más importantes desarrollados.</small>
		</div>
		<div class="ff__subfield--field">
			<textarea name="experience[{{key}}][description]" maxlength="1200" id="experience_{{key}}_description" rows="4" data-validate="text" placeholder="Ingresar descripción"></textarea>
		</div>
	</div>
</div>
</div>
</script>
<script id="tpl-languages" type="text/template">
	<div class="ff__field-wrap--lang" data-input="repeater-item">
		<select name="language[{{key}}][language]" class="language_language_" id="language_{{key}}_language">
			<?php if(is_array(\Turimet\Control\API::language_list())) foreach(\Turimet\Control\API::language_list() as $item): ?>
			<option value="<?=$item['name']?>"><?=$item['title']?></option>
		<?php endforeach; ?>
	</select>
	<select name="language[{{key}}][level]" id="language_{{key}}_level">
		<?php if(is_array(\Turimet\Control\API::level_list())) foreach(\Turimet\Control\API::level_list() as $item): ?>
		<option value="<?=$item['name']?>"><?=$item['title']?></option>
	<?php endforeach; ?>
</select>
<a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
</div>
</script>
<script>
	$(document).ready(function() {
		$('#country_born').select2({
			placeholder: "País",
			allowClear: true
		}).on('select2:select select2:clear', function () {
			this.dispatchEvent(new Event('change', { bubbles: true }));
		});
		$('#country_res').select2({
			placeholder: "País",
			allowClear: true
		}).on('select2:select select2:clear', function () {
			this.dispatchEvent(new Event('change', { bubbles: true }));
		});


		var country_bornval = $('#country_born').val();
		var country_resval = $('#country_res').val();
		if (country_bornval == 'Peru') {
			$('#region_born').select2({
				placeholder: "Departamento",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('#province_born').select2({
				placeholder: "Provincia",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('#city_born').select2({
				placeholder: "Distrito",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}

		if (country_resval == 'Peru') {
			$('#region_res').select2({
				placeholder: "Departamento",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('#province_res').select2({
				placeholder: "Provincia",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('#city_res').select2({
				placeholder: "Distrito",
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}

	});



	document.addEventListener('DOMContentLoaded', function () {
		if (document.querySelector('.studies_grade_ ')) {

			$('.studies_grade_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('.studies_specialty_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('.studies__institution_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('.studies_year_start_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
			$('.studies_year_end_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}
		if (document.querySelector('.language_language_ ')) {
			$('.language_language_').select2({
				allowClear: true
			}).on('select2:select select2:clear', function () {
				this.dispatchEvent(new Event('change', { bubbles: true }));
			});
		}

if (document.querySelector('.experiencepp')) {
		$('.experiencepp').select2({
					allowClear: true
				}).on('select2:select select2:clear', function () {
					this.dispatchEvent(new Event('change', { bubbles: true }));
				});
		}



	});

</script>

<?php get_footer('hidden'); ?>