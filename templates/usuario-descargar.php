<?php
/**
 * Template name: Usuario - Descargar CV
 */

require_once plugin_dir_path(__DIR__) . 'vendor/autoload.php';


// Si el usuario NO está logueado, redirige sin importar el rol
if ( !is_user_logged_in() ) {
    $url = home_url("login");
    wp_safe_redirect($url);
    exit;
}if ( current_user_can('administrator') ) {
    wp_safe_redirect( home_url() );
    exit;
}


$current_user_id = get_current_user_id();

$current_member = [
	'first_name'             => get_user_meta($current_user_id, 'first_name', true),
	'last_name'              => get_user_meta($current_user_id, 'last_name', true),
	'full_name' => get_user_meta($current_user_id, 'first_name', true) . ' ' .  get_user_meta($current_user_id, 'last_name', true),
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

  ob_start();

  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/header.php';
  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/avatar-personal.php';
  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/experience.php';
  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/studies.php';

  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/complementary.php';

  require_once plugin_dir_path(__DIR__) . '/templates/cv-parts/footer.php';

  $output = ob_get_clean();

  

  use \Dompdf\Dompdf;
  use \Dompdf\Options;

  $options = new Options();
  $options->set('isRemoteEnabled',true); 
// instantiate and use the dompdf class
  $dompdf = new Dompdf($options);
  $dompdf->loadHtml($output);

// (Optional) Setup the paper size and orientation
  $dompdf->setPaper('A4', 'portrait');

// Render the HTML as PDF
  $dompdf->render();

// Output the generated PDF to Browser
// $dompdf->stream();
  $dompdf->stream("archivo.pdf", ["Attachment" => false]);
