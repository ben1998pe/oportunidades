<?php
/**
 * Template name: Usuario - Perfil
 */
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

global $countries;
global $fecha;
global $categoria_list;
global $modality_list;
global $type_day_list;
global $type_contract_list;
global $range_salary;
global $language_list;
global $level_list;

$countries              = $GLOBALS['countries'];
$fecha                  = $GLOBALS['fecha'];
$category_list          = $GLOBALS['categoria_list'];
$modality_list          = $GLOBALS['modality_list'];
$type_contract_list     = $GLOBALS['type_contract_list'];
$range_salary           = $GLOBALS['range_salary'];
$language_list          = $GLOBALS['language_list'];
$level_list             = $GLOBALS['level_list'];


$country_list['data'] = $countries;
$current_user_id = get_current_user_id();

$current_member = [
	'first_name'             => get_user_meta($current_user_id, 'first_name', true),
	'last_name'              => get_user_meta($current_user_id, 'last_name', true),
	'document_type'          => get_user_meta($current_user_id, 'document_type', true),
	'document_number'        => get_user_meta($current_user_id, 'document_number', true),
    'email'                  => get_userdata($current_user_id)->user_email, // Email se obtiene de otra forma
    'gender'                 => get_user_meta($current_user_id, 'gender', true),
    'born_date'              => get_user_meta($current_user_id, 'born_date', true),
    'country'                => get_user_meta($current_user_id, 'country', true),
    'ubigeo'                 => get_user_meta($current_user_id, 'ubigeo', true),
    'state'                  => get_user_meta($current_user_id, 'state', true),
    'county'                 => get_user_meta($current_user_id, 'county', true),
    'city'                   => get_user_meta($current_user_id, 'city', true),
    'country_r'              => get_user_meta($current_user_id, 'country_r', true),
    'ubigeo_r'               => get_user_meta($current_user_id, 'ubigeo_r', true),
    'state_r'                => get_user_meta($current_user_id, 'state_r', true),
    'county_r'               => get_user_meta($current_user_id, 'county_r', true),
    'city_r'                 => get_user_meta($current_user_id, 'city_r', true),
    'address'                => get_user_meta($current_user_id, 'address', true),
    'mobile'                 => get_user_meta($current_user_id, 'mobile', true),
    'has_colegiatura'        => get_user_meta($current_user_id, 'has_colegiatura', true),
    'colegiatura_school'     => get_user_meta($current_user_id, 'colegiatura_school', true),
    'colegiatura_number'     => get_user_meta($current_user_id, 'colegiatura_number', true),
    'linkedin'               => get_user_meta($current_user_id, 'linkedin', true),
    'twitter'                => get_user_meta($current_user_id, 'twitter', true),
    'facebook'               => get_user_meta($current_user_id, 'facebook', true),
    'youtube'                => get_user_meta($current_user_id, 'youtube', true),
    'instagram'              => get_user_meta($current_user_id, 'instagram', true),
    'tiktok'                 => get_user_meta($current_user_id, 'tiktok', true),
    'profile'                => get_user_meta($current_user_id, 'profile', true),
    'keywords'               => get_user_meta($current_user_id, 'keywords', true) ?: [],
    'key1'                	 => get_user_meta($current_user_id, 'key1', true),
    'key2'                	 => get_user_meta($current_user_id, 'key2', true),
    'key3'                	 => get_user_meta($current_user_id, 'key3', true),
    'studies'                => get_user_meta($current_user_id, 'studies', true) ?: [],
    'complementary_studies' => get_user_meta($current_user_id, 'complementary_studies', true) ?: [],
    'language'               => get_user_meta($current_user_id, 'language', true) ?: [],
    'experience'             => get_user_meta($current_user_id, 'experience', true) ?: [],
    'skills'                 => get_user_meta($current_user_id, 'skills', true) ?: [],
    'availability'           => get_user_meta($current_user_id, 'availability', true),
    'sector_interest'        => get_user_meta($current_user_id, 'sector_interest', true),
    'type_day'               => get_user_meta($current_user_id, 'type_day', true),
    'min_salary'             => get_user_meta($current_user_id, 'min_salary', true),
    'max_salary'             => get_user_meta($current_user_id, 'max_salary', true),
    'agree_policy'           => get_user_meta($current_user_id, 'agree_policy', true),
    'avatar'           	     => get_user_meta($current_user_id, 'avatar', true),
];


$avatar = false;
if( filter_var($current_member['avatar'], FILTER_VALIDATE_URL) && getimagesize($current_member['avatar']) !== false ){
	$avatar = $current_member['avatar'];
}


global $grade_list;
global $specialty_list;
global $institution_list;
global $keyword_list;
global $type_position_list;
global $sector_list;
global $skill_list;

$grade_list              = $GLOBALS['grade_list'];
$specialty_list 		 = $GLOBALS['specialty_list'];
$institution_list 		 = $GLOBALS['institution_list'];
$keyword_list 			 = $GLOBALS['keyword_list'];
$type_position_list 	 = $GLOBALS['type_position_list'];
$sector_list 		     = $GLOBALS['sector_list'];
$skill_list 		     = $GLOBALS['skill_list'];

$currentDate 			 = current_time('mysql');
$dateTime 				 = new DateTime($currentDate);
$toDate 				 = $dateTime->format('Y-m-d');

?>
<style>
	.select2-selection__clear{
		display: none;
	}
	.turimet-account__alerts {
		position: fixed;
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
	.choices[data-type*=select-multiple] .choices__button, .choices[data-type*=text] .choices__button{
		border: none;
	}
	.choices[data-type*=select-multiple] .choices__button:hover{
		background-color: inherit;
	}
	.choices[data-type*=select-multiple] .choices__button, .choices[data-type*=text] .choices__button{
		border-left: 1px solid #008fa1 !important;
	}
	.choices__list--multiple .choices__item, .form2 .ff__field .choices[data-type="select-multiple"] .choices__list--multiple .choices__item{
		padding: 4px 10px !important;
		padding-right: 5px !important;
		margin-right: 3.75px !important;
		margin-bottom: 3.75px !important;
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
			<?php include(plugin_dir_path(__FILE__) . '../template-parts/menu-cuenta.php'); ?>
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
										<option value="" selected="" disabled="">Tipo de doc.</option>
										<option value="RUC" <?= (isset($current_member['document_type']) && $current_member['document_type'] === 'RUC') ? 'selected' : '' ?>>RUC</option>
    <option value="Pasaporte" <?= (isset($current_member['document_type']) && $current_member['document_type'] === 'Pasaporte') ? 'selected' : '' ?>>Pasaporte</option>
    <option value="Carnet Extrangería" <?= (isset($current_member['document_type']) && $current_member['document_type'] === 'Carnet Extrangería') ? 'selected' : '' ?>>Carnet Extrangería</option>
    <option value="DNI" <?= (isset($current_member['document_type']) && $current_member['document_type'] === 'DNI') ? 'selected' : '' ?>>DNI</option>
									</select>
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

							<select id="departamento_born" style="display:none; width: 100%;">
								<option value="">Departamento</option>
							</select>
							<select id="provincia_born" style="display:none; width: 100%;">
								<option value="">Provincia</option>
							</select>
							<select id="distrito_born" style="display:none; width: 100%;">
								<option value="">Distrito</option>
							</select>
							<script>
								document.addEventListener('DOMContentLoaded', function () {
									const basePath = '/wp-content/plugins/opt-custom/assets/';
									let departamentos = [], provincias = [], distritos = [];

									Promise.all([
										fetch(basePath + 'ubigeo_peru_2016_departamentos.json').then(r => r.json()),
										fetch(basePath + 'ubigeo_peru_2016_provincias.json').then(r => r.json()),
										fetch(basePath + 'ubigeo_peru_2016_distritos.json').then(r => r.json())
									]).then(([deps, provs, dists]) => {
										departamentos = deps;
										provincias = provs;
										distritos = dists;
									});

									const countrySel = document.getElementById('country_born');
									const depSel = document.getElementById('departamento_born');
									const provSel = document.getElementById('provincia_born');
									const distSel = document.getElementById('distrito_born');
									let choicesDep, choicesProv, choicesDist; // Instancias de Choices

									countrySel.addEventListener('change', () => {
										if (countrySel.value.trim() === 'Peru') {
											depSel.style.display = 'block';
											provSel.style.display = 'block';
											distSel.style.display = 'block';

											depSel.innerHTML = '<option value="">Departamento</option>';
											departamentos.forEach(dep => {
												depSel.innerHTML += `<option value="${dep.name.trim()}">${dep.name.trim()}</option>`;
											});
										} else {
											depSel.style.display = 'none';
											provSel.style.display = 'none';
											distSel.style.display = 'none';
										}
									});

									depSel.addEventListener('change', () => {
										const nombreDep = depSel.value.trim();
										const depSeleccionado = departamentos.find(dep => dep.name.trim() === nombreDep);
										if (!depSeleccionado) return;

										provSel.innerHTML = '<option value="">Provincia</option>';
										distSel.innerHTML = '<option value="">Distrito</option>';

										provincias
										.filter(prov => prov.department_id === depSeleccionado.id)
										.forEach(prov => {
											provSel.innerHTML += `<option value="${prov.name.trim()}">${prov.name.trim()}</option>`;
										});
									});

									provSel.addEventListener('change', () => {
										const nombreProv = provSel.value.trim();
										const provSeleccionado = provincias.find(prov => prov.name.trim() === nombreProv);
										if (!provSeleccionado) return;

										distSel.innerHTML = '<option value="">Distrito</option>';

										distritos
										.filter(dist => dist.province_id === provSeleccionado.id)
										.forEach(dist => {
											distSel.innerHTML += `<option value="${dist.name.trim()}">${dist.name.trim()}</option>`;
										});
									});
								});

							</script>



<!-- <input type="hidden" name="ubigeo_born" id="ubigeo" data-input="ubigeo" value="<?=isset($current_member['ubigeo'])?$current_member['ubigeo']:''?>" /> -->
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


			<select id="departamento_res" style="display:none; width: 100%;">
				<option value="">Departamento</option>
			</select>
			<select id="provincia_res" style="display:none; width: 100%;">
				<option value="">Provincia</option>
			</select>
			<select id="distrito_res" style="display:none; width: 100%;">
				<option value="">Distrito</option>
			</select>
			<script>
				const basePathRes = '/wp-content/plugins/opt-custom/assets/';
				let departamentos_res = [], provincias_res = [], distritos_res = [];

				Promise.all([
					fetch(basePathRes + 'ubigeo_peru_2016_departamentos.json').then(r => r.json()),
					fetch(basePathRes + 'ubigeo_peru_2016_provincias.json').then(r => r.json()),
					fetch(basePathRes + 'ubigeo_peru_2016_distritos.json').then(r => r.json())
				]).then(([deps, provs, dists]) => {
					departamentos_res = deps;
					provincias_res = provs;
					distritos_res = dists;
				});

				const countrySel_res = document.getElementById('country_res');
				const depSel_res = document.getElementById('departamento_res');
				const provSel_res = document.getElementById('provincia_res');
				const distSel_res = document.getElementById('distrito_res');

				countrySel_res.addEventListener('change', () => {
					if (countrySel_res.value.trim() === 'Peru') {
						depSel_res.style.display = 'block';
						provSel_res.style.display = 'block';
						distSel_res.style.display = 'block';

						depSel_res.innerHTML = '<option value="">Departamento</option>';
						departamentos_res.forEach(dep => {
							depSel_res.innerHTML += `<option value="${dep.name.trim()}">${dep.name.trim()}</option>`;
						});
					} else {
						depSel_res.style.display = 'none';
						provSel_res.style.display = 'none';
						distSel_res.style.display = 'none';
					}
				});

				depSel_res.addEventListener('change', () => {
					const nombreDep = depSel_res.value.trim();
					const depSeleccionado = departamentos_res.find(dep => dep.name.trim() === nombreDep);
					if (!depSeleccionado) return;

					provSel_res.innerHTML = '<option value="">Provincia</option>';
					distSel_res.innerHTML = '<option value="">Distrito</option>';

					provincias_res
					.filter(prov => prov.department_id === depSeleccionado.id)
					.forEach(prov => {
						provSel_res.innerHTML += `<option value="${prov.name.trim()}">${prov.name.trim()}</option>`;
					});
				});

				provSel_res.addEventListener('change', () => {
					const nombreProv = provSel_res.value.trim();
					const provSeleccionado = provincias_res.find(prov => prov.name.trim() === nombreProv);
					if (!provSeleccionado) return;

					distSel_res.innerHTML = '<option value="">Distrito</option>';

					distritos_res
					.filter(dist => dist.province_id === provSeleccionado.id)
					.forEach(dist => {
						distSel_res.innerHTML += `<option value="${dist.name.trim()}">${dist.name.trim()}</option>`;
					});
				});



			</script>
			<!-- FALTA -->
			<!-- <input type="hidden" name="ubigeo_res" data-input="ubigeo" value="<?=isset($current_member['ubigeo_r'])?$current_member['ubigeo_r']:''?>" /> -->
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
					<div data-input="studies">
						<?php
// 1. Asegurar que la variable es un array (deserializar si es necesario)
						$studies = maybe_unserialize($current_member['studies']);

						if (is_array($studies) && !empty($studies)) :
							foreach ($studies as $study) {
								$key = uniqid('s');
								?>
								<div class="ff__field-group" data-key="<?= esc_attr($key) ?>">
									<h3 class="ff__field-group--title" data-input="group-title">
										<span><?php echo $study['grade']; ?></span>
										<a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
									</h3>
									<div class="ff__field-group--content">

										<!-- Grado de instrucción -->
										<div class="ff__subfield">
											<div class="ff__subfield--caption">
												<label for="studies_<?= $key ?>_grade">Grado de instrucción</label>
												<small>Selecciona tu grado académico.</small>
											</div>
											<div class="ff__subfield--field">
												<select name="studies[<?= $key ?>][grade]" id="studies_<?= $key ?>_grade" class="studies_grade_">
													<option value="" disabled selected>Selecciona tu grado de instrucción</option>
													<?php foreach ($grade_list as $item): ?>
														<option value="<?= esc_attr($item['name']) ?>" <?= ($study['grade'] ?? '') == $item['name'] ? 'selected' : '' ?>>
															<?= esc_html($item['title']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>

										<!-- Especialidad -->
										<div class="ff__subfield">
											<div class="ff__subfield--caption">
												<label for="studies_<?= $key ?>_specialty">Especialidad</label>
												<small>Escribe el nombre de tu especialidad/profesión.</small>
											</div>
											<div class="ff__subfield--field">
												<select name="studies[<?= $key ?>][specialty]" id="studies_<?= $key ?>_specialty" class="studies_specialty_">
													<option value="" disabled selected>Selecciona tu especialidad</option>
													<?php foreach ($specialty_list as $item): ?>
														<option value="<?= esc_attr($item['name']) ?>" <?= ($study['specialty'] ?? '') == $item['name'] ? 'selected' : '' ?>>
															<?= esc_html($item['title']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>

										<!-- Institución -->
										<div class="ff__subfield">
											<div class="ff__subfield--caption">
												<label for="studies_<?= $key ?>_institution">Centro de estudios</label>
												<small>Escribe el nombre de tu centro de estudios.</small>
											</div>
											<div class="ff__subfield--field">
												<select name="studies[<?= $key ?>][institution]" id="studies_<?= $key ?>_institution" class="studies__institution_">
													<option value="" disabled selected>Selecciona el centro de estudios</option>
													<?php foreach ($institution_list as $item): ?>
														<option value="<?= esc_attr($item['name']) ?>" <?= ($study['institution'] ?? '') == $item['name'] ? 'selected' : '' ?>>
															<?= esc_html($item['title']) ?>
														</option>
													<?php endforeach; ?>
												</select>
											</div>
										</div>

										<!-- Año de ingreso/egreso -->
										<div class="ff__subfield">
											<div class="ff__subfield--caption">
												<label for="studies_<?= $key ?>_year_start">Año de ingreso y egreso</label>
												<small>Selecciona los años de ingreso y egreso de estudios.</small>
											</div>
											<div class="ff__subfield--field">
												<div class="ff__subfield--field-25-25-50">
													<select name="studies[<?= $key ?>][year_start]" id="studies_<?= $key ?>_year_start">
														<option value="" disabled selected>Ingreso</option>
														<?php
														$years = array_reverse(range(1970, date_i18n('Y')));
														foreach ($years as $year): ?>
															<option value="<?= $year ?>" <?= ($study['year_start'] ?? '') == $year ? 'selected' : '' ?>><?= $year ?></option>
														<?php endforeach; ?>
													</select>

													<select name="studies[<?= $key ?>][year_end]" id="studies_<?= $key ?>_year_end">
														<option value="" disabled selected>Egreso</option>
														<?php foreach ($years as $year): ?>
															<option value="<?= $year ?>" <?= ($study['year_end'] ?? '') == $year ? 'selected' : '' ?>><?= $year ?></option>
														<?php endforeach; ?>
													</select>

													<label class="checkbox-label">
														<input type="checkbox" name="studies[<?= $key ?>][study_now]" value="1" <?= !empty($study['study_now']) ? 'checked' : '' ?>>
														<span>Estudiando actualmente</span>
													</label>
												</div>
											</div>
										</div>

									</div>
								</div>
								<?php
							}
						endif;
						?>
					</div>

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

					$complementary = maybe_unserialize($current_member['complementary_studies'] ?? []);

					if (is_array($complementary) && !empty($complementary)):
						foreach ($complementary as $study):
							$key = uniqid('s');
							$title_parts = [];
							if (!empty($study['type_course'])) $title_parts[] = $study['type_course'];
							if (!empty($study['course'])) $title_parts[] = $study['course'];
							$title = implode(' - ', $title_parts);
							?>
							<div class="ff__field-group" data-key="<?= $key ?>">
								<h3 class="ff__field-group--title" data-input="group-title">
									<span><?= esc_html($title) ?></span>
									<a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
								</h3>
								<div class="ff__field-group--content">
									<div class="ff__subfield">
										<select name="complementary_studies[<?= $key ?>][type_course]" id="complementary_<?= $key ?>_type" data-input="type_course">
											<option value="" disabled <?= empty($study['type_course']) ? 'selected' : '' ?>>Curso/Diplomado</option>
											<option value="Taller" <?= ($study['type_course'] === 'Taller') ? 'selected' : '' ?>>Taller</option>
											<option value="Seminario" <?= ($study['type_course'] === 'Seminario') ? 'selected' : '' ?>>Seminario</option>
											<option value="Curso" <?= ($study['type_course'] === 'Curso') ? 'selected' : '' ?>>Curso</option>
											<option value="Diplomado" <?= ($study['type_course'] === 'Diplomado') ? 'selected' : '' ?>>Diplomado</option>
										</select>

										<input type="text" value="<?= esc_attr($study['course']) ?>" name="complementary_studies[<?= $key ?>][course]" id="complementary_<?= $key ?>_course" data-validate="text" placeholder="Nombre de la especialidad" data-input="complementary_course">
										<input type="text" value="<?= esc_attr($study['hours']) ?>" name="complementary_studies[<?= $key ?>][hours]" maxlength="3" data-validate="int" id="complementary_<?= $key ?>_hours" placeholder="Nro de horas">
										<input type="text" value="<?= esc_attr($study['institution']) ?>" name="complementary_studies[<?= $key ?>][institution]" id="complementary_<?= $key ?>_institution" data-validate="text" placeholder="Institución u Organización">
										<select name="complementary_studies[<?= $key ?>][year]" id="complementary_<?= $key ?>_year">
											<option value="" disabled>Año</option>
											<?php $years = array_reverse(range(1970, date_i18n('Y'))); ?>
											<?php foreach ($years as $item): ?>
												<option value="<?= $item ?>" <?= ($study['year'] == $item) ? 'selected' : '' ?>><?= $item ?></option>
											<?php endforeach; ?>
										</select>
									</div>
								</div>
							</div>
						<?php endforeach; endif; ?>
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
						$languages = maybe_unserialize($current_member['language'] ?? []);
						if (is_array($languages) && !empty($languages)):
							foreach ($languages as $lang):
								$key = uniqid('s');
								$selected_lang = $lang['language'] ?? '';
								$selected_level = $lang['level'] ?? '';
								?>
								<div class="ff__field-wrap--lang" data-input="repeater-item">
									<select name="language[<?= $key ?>][language]" id="language_<?= $key ?>_language" class="language_language_">
										<option value="" disabled <?= empty($selected_lang) ? 'selected' : '' ?>>Seleccione idioma</option>
										<?php foreach ($language_list as $item): ?>
											<option value="<?= $item['name'] ?>" <?= ($selected_lang === $item['name']) ? 'selected' : '' ?>>
												<?= $item['title'] ?>
											</option>
										<?php endforeach; ?>
									</select>

									<select name="language[<?= $key ?>][level]" id="language_<?= $key ?>_level">
										<option value="" disabled <?= empty($selected_level) ? 'selected' : '' ?>>Seleccione nivel</option>
										<?php foreach ($level_list as $item): ?>
											<option value="<?= $item['name'] ?>" <?= ($selected_level === $item['name']) ? 'selected' : '' ?>>
												<?= $item['title'] ?>
											</option>
										<?php endforeach; ?>
									</select>

									<a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
								</div>
							<?php endforeach; endif; ?>
						</div>

						<a href="javascript:void(0);" data-action="add-lang" class="btn btn-primary" id="add-idioma-btn">
							<?= (isset($current_member['language']) && is_array($current_member['language']) && !empty($current_member['language'])) ? 'Agregar otro idioma' : 'Agregar idioma'; ?>
						</a>
					</div>

				</div>
			</div>
			<div class="ff ff-input">
				<div class="ff__info">
					<h3>Conocimientos técnicos y/o habilidades personales, informáticas, otras.</h3>
					<small>Selecciona el conocimiento o habilidades que manejas.</small>
				</div>

				<?php 
					$skills = maybe_unserialize($current_member['skills'] ?? []);
				?>
				<div class="ff__field">
					<div class="ff__field-wrap">
						<select name="skills[]" id="skills" multiple="multiple">
							<?php 
							$member_keys = [];

							if( isset($skills) && is_array($skills) && !empty($skills) ){
								$member_keys = array_column($skills, 'skill');
							}

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
						$experience = maybe_unserialize($current_member['experience'] ?? []);

						?>
						<?php if (!empty($experience) && is_array($experience)): ?>
						<?php foreach ($experience as $exp): ?>
							<?php
							$key = uniqid('s');
							$title_parts = [];

							if (!empty($exp['position'])) $title_parts[] = $exp['position'];
							if (!empty($exp['company'])) $title_parts[] = $exp['company'];

							$title = implode(' - ', $title_parts);

        // Calcular duración
							$currentDate = current_time('mysql');
							if ('1' == $exp['currently_work'] && !empty($exp['date_final'])) {
								$currentDate = $exp['date_final'];
							}

							try {
								$dateInitial = new DateTime($exp['date_initial']);
								$dateFinal = new DateTime($currentDate);
								$diferencia = $dateFinal->diff($dateInitial);

								$anios = $diferencia->y;
								$meses = $diferencia->m;
								$dias = $diferencia->d;

								$resultado = "";
								if ($anios > 0) {
									$resultado .= $anios . ($anios === 1 ? " año, " : " años, ");
								}
								if ($meses > 0) {
									$resultado .= $meses . ($meses === 1 ? " mes, " : " meses, ");
								}
								$resultado .= $dias . ($dias === 1 ? " día" : " días");
							} catch (Exception $e) {
								$resultado = "";
							}

							$maxDate = (new DateTime(current_time('mysql')))->format('Y-m-d');
							?>
							<div class="ff__field-group" data-key="<?= $key ?>">
								<h3 class="ff__field-group--title" data-input="group-title">
									<span><?= esc_html($title ?: 'Nueva experiencia') ?></span>
									<a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
								</h3>
								<div class="ff__field-group--content">
									<!-- Cargo, rol y tiempo -->
									<div class="ff__subfield">
										<div class="ff__subfield--caption">
											<label for="experience_<?= $key ?>_grade">Cargo, Rol y Tiempo</label>
											<small>Ingresa el nombre de tu cargo, fecha de inicio y de cierre, si corresponde.</small>
										</div>
										<div class="ff__subfield--field">
											<div class="ff__subfield--field-exp">
												<select name="experience[<?= $key ?>][type_position]" id="experience_<?= $key ?>_type_position" class="experiencepp">
													<option value="" disabled <?= empty($exp['type_position']) ? 'selected' : '' ?>>Tipo de cargo</option>
													<?php if (is_array($type_position_list)) foreach ($type_position_list as $item): ?>
													<option value="<?= $item['name'] ?>"<?= ($exp['type_position'] === $item['name']) ? ' selected' : '' ?>><?= $item['title'] ?></option>
												<?php endforeach; ?>
											</select>

											<input type="text" maxlength="50" name="experience[<?= $key ?>][position]" id="experience_<?= $key ?>_position"
											value="<?= esc_attr($exp['position']) ?>" placeholder="Ingresa el nombre del cargo" data-validate="text" data-input="position">

											<input type="date" name="experience[<?= $key ?>][date_initial]" id="experience_<?= $key ?>_date_initial"
											max="<?= $maxDate ?>" value="<?= esc_attr($exp['date_initial']) ?>">

											<input type="date" name="experience[<?= $key ?>][date_final]" id="experience_<?= $key ?>_date_final"
											min="<?= esc_attr($exp['date_initial']) ?>" max="<?= $maxDate ?>" value="<?= esc_attr($exp['date_final']) ?>"
											<?= ($exp['currently_work'] == '1') ? 'disabled' : '' ?>>

											<label class="checkbox-label">
												<input type="checkbox" name="experience[<?= $key ?>][currently_work]" id="experience_<?= $key ?>_currently_work"
												<?= ($exp['currently_work'] == '1') ? 'checked' : '' ?>> <span>Trabajo actualmente</span>
											</label>

											<div class="result-div">
												<span>Tiempo en el cargo:</span>
												<input type="text" value="<?= esc_attr($resultado) ?>" readonly id="experience_<?= $key ?>_time">
											</div>
										</div>
									</div>
								</div>

								<!-- Organización y sector -->
								<div class="ff__subfield">
									<div class="ff__subfield--caption">
										<label for="experience_<?= $key ?>_company">Organización y sector</label>
										<small>Ingresa nombre de la empresa u organización.</small>
									</div>
									<div class="ff__subfield--field">
										<div class="ff__subfield--field-exp2">
											<input type="text" maxlength="60" name="experience[<?= $key ?>][company]" id="experience_<?= $key ?>_company"
											value="<?= esc_attr($exp['company']) ?>" placeholder="Nombre de la empresa u organización" data-validate="text" data-input="company">

											<select name="experience[<?= $key ?>][sector]" id="experience_<?= $key ?>_sector">
												<option value="" disabled <?= empty($exp['sector']) ? 'selected' : '' ?>>Selecciona el sector</option>
												<?php if (is_array($sector_list)) foreach ($sector_list as $item): ?>
												<option value="<?= $item['name'] ?>"<?= ($exp['sector'] === $item['name']) ? ' selected' : '' ?>><?= $item['title'] ?></option>
											<?php endforeach; ?>
										</select>

										<input type="text" maxlength="12" name="experience[<?= $key ?>][salary]" id="experience_<?= $key ?>_salary"
										value="<?= esc_attr($exp['not_share_salary'] == '1' ? '' : $exp['salary']) ?>"
										placeholder="Salario recibido" <?= $exp['not_share_salary'] == '1' ? 'disabled' : '' ?>>

										<label class="checkbox-label">
											<input type="checkbox" name="experience[<?= $key ?>][not_share_salary]" id="experience_<?= $key ?>_not_share_salary"
											<?= ($exp['not_share_salary'] == '1') ? 'checked' : '' ?>> <span>No deseo compartir esta información</span>
										</label>
									</div>
								</div>
							</div>

							<!-- Descripción -->
							<div class="ff__subfield">
								<div class="ff__subfield--caption">
									<label for="experience_<?= $key ?>_description">Descripción de experiencia laboral</label>
									<small>Describe brevemente las funciones y logros más importantes desarrollados.</small>
								</div>
								<div class="ff__subfield--field">
									<textarea name="experience[<?= $key ?>][description]" id="experience_<?= $key ?>_description" maxlength="1200" rows="4"
										placeholder="Ingresar descripción" data-validate="text"><?= esc_textarea($exp['description']) ?></textarea>
									</div>
								</div>
							</div>
						</div>
					<?php endforeach; ?>
				<?php endif; ?>

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
				$list = [
					'data' => [
						['name' => 'Privado', 'title' => 'Privado'],
						['name' => 'Público', 'title' => 'Público']
					]
				];
				$list = ( is_array($list) && isset($list['data']) ) ? $list['data'] : false;

				if(is_array($list)) foreach($list as $item): ?>
					<option value="<?=$item['name']?>"<?=(isset($current_member['sector_interest']) && $current_member['sector_interest']==$item['name'])?' selected':''?>><?=$item['title']?></option>
			<?php endforeach; ?>
		</select>
		<select name="availability" id="availability">
			<option value="" selected>Disponibilidad</option>
			<?php 
			$list = [
				'data' => [
					[
						'name' => 'Dentro de 1 mes',
						'title' => 'Dentro de 1 mes'
					],
					[
						'name' => 'Dentro de 1 semana',
						'title' => 'Dentro de 1 semana'
					],
					[
						'name' => 'Inmediata',
						'title' => 'Inmediata'
					]
				]
			];
			$list = ( is_array($list) && isset($list['data']) ) ? $list['data'] : false;

			if(is_array($list)) foreach($list as $item): ?>
				<option value="<?=$item['name']?>"<?=(isset($current_member['availability']) && $current_member['availability']==$item['name'])?' selected':''?>><?=$item['title']?></option>
		<?php endforeach; ?>
	</select>
	<select name="type_day" id="type_day">
		<option value="" selected>Tipo de jornada</option>
		<?php 
		$list = [
			'data' => [
				[ 'name' => 'Jornada Nocturna', 'title' => 'Jornada Nocturna' ],
				[ 'name' => 'Jornada por Suplencia', 'title' => 'Jornada por Suplencia' ],
				[ 'name' => 'Jornada por Horas', 'title' => 'Jornada por Horas' ],
				[ 'name' => 'Jornada Tiempo Parcial', 'title' => 'Jornada Tiempo Parcial' ],
				[ 'name' => 'Jornada Completa', 'title' => 'Jornada Completa' ],
			]
		];
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
						<?php if(is_array($grade_list)) foreach($grade_list as $item): ?>
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
					<?php if(is_array($specialty_list)) foreach($specialty_list as $item): ?>
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
				<?php if(is_array($institution_list)) foreach($institution_list as $item): ?>
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
					<option value="" selected="" disabled="">Curso/Diplomado</option>
					<option value="Taller">Taller</option>
					<option value="Seminario" selected="">Seminario</option>
					<option value="Curso">Curso</option>
					<option value="Diplomado">Diplomado</option>
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
							<?php if(is_array($type_position_list)) foreach($type_position_list as $item): ?>
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
						<?php if(is_array($sector_list)) foreach($sector_list as $item): ?>
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

			<?php if(is_array($language_list)) foreach($language_list as $item): ?>
			<option value="<?=$item['name']?>"><?=$item['title']?></option>
		<?php endforeach; ?>
	</select>
	<select name="language[{{key}}][level]" id="language_{{key}}_level">

		<?php if(is_array($level_list)) foreach($level_list as $item): ?>
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

<?php include('footer-simple.php'); ?>