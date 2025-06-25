<?php 

add_action('wp_ajax_get_oportunity_opt', 'ajax_handler_opt');
add_action('wp_ajax_nopriv_get_oportunity_opt', 'ajax_handler_opt');
function ajax_handler_opt() {
    while (ob_get_level()) {
        ob_end_clean();
    }

    $action = isset($_REQUEST['action']) ? $_REQUEST['action'] : false;

    if ( $action === 'get_oportunity_opt' ) {
      $post_id = isset($_REQUEST['id']) ? intval($_REQUEST['id']) : 0;

      if ( ! $post_id ) {
       wp_send_json_error("ID de oportunidad inválido.");
   }

   $post = get_post($post_id);

   if ( ! $post || $post->post_type !== 'oportunidad' ) {
       wp_send_json_error("Oportunidad no encontrada.");
   }

   

        // Otros campos que quieras enviar, por ejemplo salario, empresa, etc.
   $min_salary 	=  get_post_meta($post->ID, '_salario_min', true);
   $max_salary		=  get_post_meta($post->ID, '_salario_max', true);
   $currency 		=  get_post_meta($post->ID, '_currency', true);
   $categoria      =  get_post_meta($post->ID, '_categoria', true);
   $jornada 		=  get_post_meta($post->ID, '_jornada', true);
   $contrato 		=  get_post_meta($post->ID, '_contrato', true);
   $modalidad 		=  get_post_meta($post->ID, '_modalidad', true);

   $post_author_id = (int) $post->post_author;
   $user_info = get_userdata($post_author_id);


   $author_slug = $user_info ? $user_info->user_nicename : '';
   $empresa_nombre = get_user_meta($post_author_id, 'empresa_nombre', true);
   $empresa_pais =   get_user_meta($post_author_id, 'empresa_pais', true);
   $empresa_icono =  get_user_meta($post_author_id, 'empresa_icono', true);
   $user_nicename = $user_info->user_nicename;

   
        // Construir salario legible
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

        // Preparar respuesta
        $response = [
        	'id' => $post_id,
        	'job' => get_the_title($post),
        	'title' => get_the_title($post),
        	'name' => $post->slug,
        	'description' => apply_filters('the_content', $post->post_content),
        	'modalidad' => $modalidad,
        	'salary' => $salary,
        	'company_name' => $empresa_nombre,
        	'company_image' =>$empresa_icono,
        	'country' =>  $empresa_pais,
        	'code' =>  "pe",
        	'company_url' => get_author_posts_url($post_author_id),
        	'url' => get_permalink($post),
        	'created_at' => get_the_date('', $post),
        	'type_day' => $jornada,
        	'category' => $categoria,
        	'type_contract' => $contrato,
        	'modality' => $modalidad,
        ];

        wp_send_json_success($response);
    }

    wp_send_json_error("Acción inválida.");
    wp_die();
}

add_action( 'wp_ajax_nopriv_login_opt', 'login_opt' );
add_action( 'wp_ajax_login_opt',       'login_opt' );

function login_opt() {
    $errors = [];

    $username = isset($_REQUEST['username']) ? sanitize_user(wp_unslash($_REQUEST['username'])) : '';
    $password = isset($_REQUEST['password']) ? wp_unslash($_REQUEST['password']) : '';

    if (empty($username)) {
        $errors[] = ['input' => 'username', 'msg' => 'El usuario es obligatorio.'];
    }
    if (empty($password)) {
        $errors[] = ['input' => 'password', 'msg' => 'La contraseña es obligatoria.'];
    }

    if (!empty($errors)) {
        wp_send_json_error([
            'msg' => 'Hay errores en el formulario.',
            'errors' => $errors
        ]);
    }

    $creds = [
        'user_login'    => $username,
        'user_password' => $password,
        'remember'      => true,
    ];

    $user = wp_signon($creds, is_ssl());

    if (is_wp_error($user)) {
        wp_send_json_error([
            'msg' => $user->get_error_message(),
            'errors' => [['input' => 'username', 'msg' => $user->get_error_message()]]
        ]);
    }

    wp_set_current_user($user->ID);
    wp_set_auth_cookie($user->ID, true, is_ssl());

        // ✅ Crear token dinámico seguro
        $token = bin2hex(random_bytes(32)); // genera un token de 64 caracteres (256 bits)

        // ✅ Guardar el token en una cookie segura
        $expiry_time = time() + 4 * HOUR_IN_SECONDS;
        setcookie('turimet_hash', $token, $expiry_time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);

        // Opcional: guardar también el token en la usermeta si lo necesitas después
        update_user_meta($user->ID, 'turimet_token', $token);

        $redirect_url = home_url('/mi-cuenta/mis-postulaciones');

        wp_send_json_success([
            'msg' => 'Inicio de sesión exitoso',
            'redirect_to' => $redirect_url,
            'token' => $token // opcional, si quieres usarlo en JS
        ]);
    }



    add_action( 'wp_ajax_nopriv_lostpassword_opt', 'lostpassword_opt' );
    add_action( 'wp_ajax_lostpassword_opt', 'lostpassword_opt' );

    function logout_opt() {
    // Solo cerrar si el usuario está logueado
        if ( is_user_logged_in() ) {
            wp_logout();
        }

    // Redireccionar a donde quieras después del logout
    wp_redirect( home_url( '/' ) ); // Cambia la URL si es necesario
    exit;
}


add_action( 'init', 'custom_logout_url_handler' );
function custom_logout_url_handler() {
    if ( isset($_GET['logout-opt']) ) {
        logout_opt();
    }
}

function lostpassword_opt() {
    // if ( ! isset($_REQUEST['nonce']) || ! wp_verify_nonce($_REQUEST['nonce'], 'turimet-login') ) {
    //     wp_send_json_error(['msg' => 'Solicitud inválida.']);
    // }

    $email = isset($_REQUEST['email']) ? sanitize_email($_REQUEST['email']) : '';

    if (empty($email)) {
        wp_send_json_error(['msg' => 'El correo electrónico es obligatorio.']);
    }

    $user = get_user_by('email', $email);

    if ( ! $user ) {
        wp_send_json_error(['msg' => 'No existe un usuario con ese correo electrónico.']);
    }

    // Generar una nueva contraseña segura
    $new_password = wp_generate_password(12, true);

    // Actualizar la contraseña del usuario
    wp_set_password($new_password, $user->ID);

    // Enviar email al usuario
    $subject = 'Tu nueva contraseña';
    $message = "Hola {$user->display_name},\n\nTu nueva contraseña es:\n\n{$new_password}\n\nTe recomendamos cambiarla después de iniciar sesión.";

    $headers = ['Content-Type: text/plain; charset=UTF-8'];

    wp_mail($email, $subject, $message, $headers);

    wp_send_json_success(['msg' => 'Se ha enviado una nueva contraseña a tu correo electrónico.']);
}

add_action( 'wp_ajax_nopriv_cambiar_clave', 'cambiar_clave' );
add_action( 'wp_ajax_cambiar_clave', 'cambiar_clave' );

function cambiar_clave() {
    // if ( ! isset($_REQUEST['nonce']) || ! wp_verify_nonce($_REQUEST['nonce'], 'turimet-cpwd') ) {
    //     wp_send_json_error(['error' => '¡Intento de hackeo! Sesión no válida']);
    // }

    $current_password = sanitize_text_field($_REQUEST['current_password'] ?? '');
    $new_password     = sanitize_text_field($_REQUEST['password'] ?? '');
    $new_password2    = sanitize_text_field($_REQUEST['password2'] ?? '');

    if (empty($current_password) || empty($new_password) || empty($new_password2)) {
        wp_send_json_error(['error' => 'Todos los campos son obligatorios.']);
    }

    if ($new_password !== $new_password2) {
        wp_send_json_error(['error' => 'Las contraseñas no coinciden.']);
    }

    $user = wp_get_current_user();

    if ( ! $user || 0 === $user->ID ) {
        wp_send_json_error(['error' => 'No has iniciado sesión.']);
    }

    // Verificar contraseña actual
    if ( ! wp_check_password($current_password, $user->user_pass, $user->ID) ) {
        wp_send_json_error(['error' => 'La contraseña actual no es correcta.']);
    }

    // Cambiar la contraseña
    wp_set_password($new_password, $user->ID);

    // Opcional: mantener al usuario logueado tras cambiar la contraseña
    wp_set_auth_cookie($user->ID, true);
    wp_set_current_user($user->ID);

    wp_send_json_success(['msg' => 'Contraseña cambiada con éxito']);
}


add_action( 'wp_ajax_nopriv_register_opt', 'register_opt' );
add_action( 'wp_ajax_register_opt',       'register_opt' );

function register_opt() {
    $errors = [];

    $firstname  = sanitize_text_field( $_REQUEST['firstname'] ?? '' );
    $lastname   = sanitize_text_field( $_REQUEST['lastname'] ?? '' );
    $email      = sanitize_email( $_REQUEST['email'] ?? '' );
    $password   = $_REQUEST['password'] ?? '';
    $password2  = $_REQUEST['password2'] ?? '';

    // Validaciones básicas
    if ( empty( $firstname ) ) {
        $errors[] = ['input' => 'firstname', 'msg' => 'El nombre es obligatorio.'];
    }
    if ( empty( $lastname ) ) {
        $errors[] = ['input' => 'lastname', 'msg' => 'El apellido es obligatorio.'];
    }
    if ( empty( $email ) ) {
        $errors[] = ['input' => 'email', 'msg' => 'El correo es obligatorio.'];
    } elseif ( ! is_email( $email ) ) {
        $errors[] = ['input' => 'email', 'msg' => 'El correo no es válido.'];
    } elseif ( email_exists( $email ) ) {
        $errors[] = ['input' => 'email', 'msg' => 'El correo ya está registrado.'];
    }

    if ( empty( $password ) ) {
        $errors[] = ['input' => 'password', 'msg' => 'La contraseña es obligatoria.'];
    }
    if ( empty( $password2 ) ) {
        $errors[] = ['input' => 'password2', 'msg' => 'La confirmación de contraseña es obligatoria.'];
    } elseif ( $password !== $password2 ) {
        $errors[] = ['input' => 'password2', 'msg' => 'Las contraseñas no coinciden.'];
    }

    if ( ! empty( $errors ) ) {
        wp_send_json_error([
            'msg' => 'Hay errores en el formulario.',
            'errors' => $errors
        ]);
    }

    // Crear usuario
    $user_id = wp_create_user( $email, $password, $email );

    if ( is_wp_error( $user_id ) ) {
        wp_send_json_error([
            'msg' => $user_id->get_error_message(),
            'errors' => [['input' => 'email', 'msg' => $user_id->get_error_message()]]
        ]);
    }

    // Añadir datos adicionales
    wp_update_user([
        'ID'           => $user_id,
        'first_name'   => $firstname,
        'last_name'    => $lastname,
        'display_name' => $firstname . ' ' . $lastname,
        'role'         => 'subscriber'
    ]);

    // Iniciar sesión automáticamente
    wp_set_current_user( $user_id );
    wp_set_auth_cookie( $user_id, true );
    do_action( 'wp_login', $email, get_user_by( 'id', $user_id ) );

    // ✅ Crear token dinámico seguro
    $token = bin2hex(random_bytes(32));

    // ✅ Guardar el token en una cookie segura
    $expiry_time = time() + 4 * HOUR_IN_SECONDS;
    setcookie('turimet_hash', $token, $expiry_time, COOKIEPATH, COOKIE_DOMAIN, is_ssl(), true);

    // ✅ Guardar el token en la usermeta
    update_user_meta($user_id, 'turimet_token', $token);

    // Redirección final
    $url = home_url('/mi-cuenta/mis-postulaciones');

    wp_send_json_success([
        'msg' => '¡Bienvenido! Ya estás registrado.',
        'redirect_to' => $url
    ]);
}


add_action( 'wp_ajax_nopriv_apply_opt', 'apply_opt' );
add_action( 'wp_ajax_apply_opt',       'apply_opt' );

// 1. Añadir regla de reescritura para /mi-cuenta/salir/
add_action('init', function() {
    add_rewrite_rule('^mi-cuenta/salir/?$', 'index.php?custom_logout=1', 'top');
});

// 2. Registrar query var personalizada
add_filter('query_vars', function($vars) {
    $vars[] = 'custom_logout';
    return $vars;
});

// 3. Capturar la URL y ejecutar el logout
add_action('template_redirect', function() {
    if (get_query_var('custom_logout') == 1) {
        wp_logout();

        // Eliminar la cookie personalizada
        if (isset($_COOKIE['turimet_hash'])) {
            setcookie('turimet_hash', '', time() - 3600, COOKIEPATH, COOKIE_DOMAIN);
        }

        // Redirigir a donde tú quieras después del logout
        wp_redirect(home_url('/'));
        exit;
    }
});

function replace_user_avatar2( $item_output, $item, $depth, $args ) {
    if ( strpos( $item_output, '{{user_avatar}}' ) !== false || strpos( $item_output, '{{user_name}}' ) !== false ) {

        if ( ! turimet_is_user_logged_in() ) return $item_output;

        $user = turimet_get_current_user();
        $avatar = get_template_directory_uri() . '/assets/images/avatar.svg';

        if ( !empty($user['user_image']) && filter_var($user['user_image'], FILTER_VALIDATE_URL) && @getimagesize($user['user_image']) ) {
            $avatar = $user['user_image'];
        }

        if ( strpos( $item_output, '{{user_avatar}}' ) !== false ) {
            $item_output = str_replace(
                '{{user_avatar}}',
                '<figure class="fig-contain user-avatar"><img src="' . esc_url($avatar) . '" alt="' . esc_attr($user['full_name']) . '" /></figure>',
                $item_output
            );
        }

        if ( strpos( $item_output, '{{user_name}}' ) !== false ) {
            $item_output = str_replace(
                '{{user_name}}',
                esc_html($user['full_name']),
                $item_output
            );
        }
    }

    return $item_output;
}

// add_filter( 'walker_nav_menu_start_el', 'replace_user_avatar2', 999, 4 );


function apply_opt() {
    // if ( ! isset( $_REQUEST['nonce'] ) || ! wp_verify_nonce( $_REQUEST['nonce'], 'oportunity' ) ) {
    //  wp_send_json_error( [ 'code' => 'ERROR', 'msg' => '¡Intento de hackeo!' ] );
    // }

    if ( is_user_logged_in() ) {
        $user_id = get_current_user_id();

        // Evitar que el administrador se postule
        if ( user_can( $user_id, 'administrator' ) ) {
            wp_send_json_error( [ 'code' => 'ADMIN_NO_POSTULATE', 'msg' => 'Los administradores no pueden postularse.' ] );
        }

        $job_id = isset( $_REQUEST['job'] ) ? intval( $_REQUEST['job'] ) : 0;

        // Validamos que sea un post válido del CPT oportunidades
        if ( get_post_type( $job_id ) !== 'oportunidad' ) {
            wp_send_json_error( [ 'code' => 'INVALID_POST', 'msg' => 'La oportunidad no es válida.' ] );
        }

        global $wpdb;
        $table = $wpdb->prefix . 'opt_inscripciones';

        // Verificamos si ya existe la inscripción para ese usuario y esa oportunidad
        $already_applied = $wpdb->get_var( $wpdb->prepare(
            "SELECT COUNT(*) FROM $table WHERE id_oportunidades = %d AND id_usuario = %d",
            $job_id,
            $user_id
        ) );

        if ( $already_applied ) {
            wp_send_json_error( [ 'code' => 'ALREADY_APPLIED', 'msg' => 'Ya postulaste a esta oportunidad de trabajo.' ] );
        }

        // Insertamos la inscripción
        $inserted = $wpdb->insert(
            $table,
            [
                'id_oportunidades' => $job_id,
                'id_usuario'       => $user_id,
            ],
            [ '%d', '%d' ]
        );

        if ( $inserted ) {
            $post_title = get_the_title( $job_id );
            $post_url   = get_permalink( $job_id );
            $user_info  = get_userdata( $user_id );
            $user_email = $user_info->user_email;
            $user_name  = $user_info->display_name;
            $fecha      = current_time('d/m/Y H:i');

            $subject_user = 'Confirmación de postulación';
            $message_user = "<p>Hola, has aplicado correctamente a la siguiente oportunidad:</p>";
            $message_user .= "<p>Oportunidad: <a style='color:#000;' href='" . esc_url( $post_url ) . "'>" . esc_html( $post_title ) . "</a></p>";
            $message_user .= "<p>Gracias por postular.</p>";

            add_filter( 'wp_mail_content_type', function() { return 'text/html'; });

            wp_mail( $user_email, $subject_user, $message_user );


            $post_author_id = get_post_field( 'post_author', $job_id );
            $author_info = get_userdata( $post_author_id );
            $author_email = $author_info ? $author_info->user_email : get_option( 'admin_email' );

            $subject_admin = 'Nueva postulación recibida';
            $message_admin = "<p>Se ha recibido una nueva postulación:</p>";
            $message_admin .= "<p>Oportunidad: <a style='color:#000;' href='" . esc_url( $post_url ) . "'>" . esc_html( $post_title ) . "</a></p>";
            $message_admin .= "<p>Nombre del postulante: " . esc_html( $user_name ) . "<br>";
            $message_admin .= "Email del postulante: " . esc_html( $user_email ) . "<br>";
            $message_admin .= "Fecha de postulación: " . esc_html( $fecha ) . "</p>";

            wp_mail( $author_email, $subject_admin, $message_admin );

            remove_filter( 'wp_mail_content_type', 'return_html_content_type' );

            wp_send_json_success( [ 'msg' => 'Has sido registrado con éxito.' ] );
        } else {
            wp_send_json_error( [ 'code' => 'DB_ERROR', 'msg' => 'Ha ocurrido un error al registrar tu postulación.' ] );
        }

    }

    // Si NO está logueado, redirigir
    $redirect_url = home_url( '/mi-cuenta/' );
    wp_send_json_error( [
        'code'        => 'NOT_LOGGED_IN',
        'msg'         => 'Debes iniciar sesión para postular.',
        'redirect_to' => $redirect_url,
    ] );
}

function template_redirect_opt()
{
	global $wp_query;

	$section = get_query_var('section');
	$company = get_query_var('company');
	$oportunity = get_query_var('oportunity');
	$region_born = get_query_var('region_born');
	$province_born = get_query_var('province_born');
    $city_born = get_query_var('city_born');
    $departamento = get_query_var('departamento');
    $provincia = get_query_var('provincia');
    $distrito = get_query_var('distrito');

	// if( 
	// 	(isset($_POST['oportunity_search_nonce']) && wp_verify_nonce($_POST['oportunity_search_nonce'], 'oportunity_search')) 
	// 	|| (isset($_POST['oportunity_filter_nonce']) && wp_verify_nonce($_POST['oportunity_filter_nonce'], 'oportunity_filter')) 
	// )
    if( 
      (isset($_POST['oportunity_filter_opt_nonce'])) 
      || (isset($_POST['oportunity_filter_opt_nonce'])) 
  )
    {
      $path = '';

      $filters = [];

      if( isset($_POST['sort']) && !empty($_POST['sort']) ){
         $filters[] = 'ordenar:' . urlencode(sanitize_text_field($_POST['sort']));
     }

     if( isset($_POST['company']) && !empty($_POST['company']) ){
         $filters[] = 'empresa:' . urlencode(sanitize_text_field($_POST['company']));
     }

     if( isset($_POST['sector']) && !empty($_POST['sector']) ){
         $filters[] = 'sector:' . urlencode(sanitize_text_field($_POST['sector']));
     }

     if( isset($_POST['category']) && !empty($_POST['category']) ){
         $filters[] = 'categoria:' . urlencode(sanitize_text_field($_POST['category']));
     }

     if( isset($_POST['country']) && !empty($_POST['country']) ){
         $filters[] = 'lugar:' . urlencode(sanitize_text_field($_POST['country']));
     }

     if( isset($_POST['region_born']) && !empty($_POST['region_born']) ){
         $filters[] = 'region:' . urlencode(sanitize_text_field($_POST['region_born']));
     }

     if( isset($_POST['province_born']) && !empty($_POST['province_born']) ){
         $filters[] = 'province:' . urlencode(sanitize_text_field($_POST['province_born']));
     }

     if( isset($_POST['city_born']) && !empty($_POST['city_born']) ){
         $filters[] = 'city:' . urlencode(sanitize_text_field($_POST['city_born']));
     }

     if( isset($_POST['departamento']) && !empty($_POST['departamento']) ){
        $filters[] = 'departamento:' . urlencode(sanitize_text_field($_POST['departamento']));
    }

    if( isset($_POST['provincia']) && !empty($_POST['province_born']) ){
        $filters[] = 'provincia:' . urlencode(sanitize_text_field($_POST['provincia']));
    }

    if( isset($_POST['distrito']) && !empty($_POST['distrito']) ){
        $filters[] = 'distrito:' . urlencode(sanitize_text_field($_POST['distrito']));
    }

    if( isset($_POST['modality']) && !empty($_POST['modality']) ){
     $filters[] = 'modalidad:' . urlencode(sanitize_text_field($_POST['modality']));
 }

 if( isset($_POST['type_day']) && !empty($_POST['type_day']) ){
     $filters[] = 'jornada:' . urlencode(sanitize_text_field($_POST['type_day']));
 }

 if( isset($_POST['type_contract']) && !empty($_POST['type_contract']) ){
     $filters[] = 'contrato:' . urlencode(sanitize_text_field($_POST['type_contract']));
 }

 if( isset($_POST['salary']) && !empty($_POST['salary']) ){
     $filters[] = 'salario:' . urlencode(sanitize_text_field($_POST['salary']));
 }

 if( isset($_POST['disability']) && in_array($_POST['disability'], ['no', 'si']) ){
     $filters[] = 'discapacidad:' . urlencode(intval($_POST['disability']=='si'));
 }

 if( isset($_POST['range_date']) && !empty($_POST['range_date']) ){
     $filters[] = 'fecha:' . urlencode(sanitize_text_field($_POST['range_date']));
 }

 if( !empty($filters) ){
     $path = 'filtrar/' . implode(',', $filters) . '/';
 }

 wp_redirect( home_url( 'oportunidad/' . $path ) );
 die;
}

if('oportunity'==$section){
  if( empty($company) && empty($oportunity) ){
     $template = plugin_dir_path(__FILE__) .  'archive-oportunidad.php';
 } else if( !empty($company) && empty($oportunity) ){
     $template =plugin_dir_path(__FILE__) .  'archive-oportunidad.php';
 } else if( !empty($company) && !empty($oportunity) ){
     $template = plugin_dir_path(__FILE__) .  'single-oportunidad.php';
 }

 if( file_exists($template) ){
                //$single = \Turimet\Control\API::get_oportunity($oportunity);
                //if( $single ){
                    //$wp_query->oportunity = $single;
     include_once $template;
     die;
                //}
                //$wp_query->is_404 = true;
 } else {
     $wp_query->is_404 = true;
 }
} else if('company'==$section){
  if( !empty($company) ){
     $template =  plugin_dir_path(__FILE__) .  'single-company.php';
 }

 if( file_exists($template) ){
     include_once $template;
     die;
 } else {
     $wp_query->is_404 = true;
 }
}
}

add_action( 'wp_ajax_nopriv_turimet-profile', 'perfil_opt' );
add_action( 'wp_ajax_turimet-profile',       'perfil_opt' );

function perfil_opt()
{

    // if ( !is_user_logged_in() ) {
    //     wp_send_json_error([
    //         'status' => false,
    //         'message' => 'Debes iniciar sesión para actualizar tu perfil.'
    //     ]);
    // }

    $user_id = get_current_user_id();
    if ( !$user_id ) {
        wp_send_json_error([
            'status' => false,
            'message' => 'Usuario no válido.'
        ]);
    }
    $data = shortcode_atts([
        'first_name' => '',
        'last_name' => '',
        'document_type' => '',
        'document_number' => '',
        'email' => '',
        'gender' => '',
        'born_date' => '',
        'country' => '',
        'ubigeo' => '',
        'state' => '',
        'county' => '',
        'city' => '',
        'country_r' => '',
        'ubigeo_r' => '',
        'state_r' => '',
        'county_r' => '',
        'city_r' => '',
        'address' => '',
        'mobile' => '',
        'has_colegiatura' => '',
        'colegiatura_school' => '',
        'colegiatura_number' => '',
        'linkedin' => '',
        'twitter' => '',
        'facebook' => '',
        'instagram' => '',
        'youtube' => '',
        'tiktok' => '',
        'profile' => '',
        'keywords' => [],
        'studies' => [],
        'complementary_studies' => [],
        'language' => [],
        'experience' => [],
        'skills' => [],
        'availability' => '',
        'sector_interest' => '',
        'type_day' => '',
        'min_salary' => '',
        'max_salary' => '',
        'agree_policy' => '',
    ], $_POST);

    $errors = [];

        // Data Formatting
    if( isset($_POST['agree_policy']) ){
        $data['agree_policy'] = 1;
    }

    if( isset($_POST['country_born']) && !empty($_POST['country_born']) ){
        $data['country'] = sanitize_text_field($_POST['country_born']);
    }

    if( isset($_POST['region_born']) && !empty($_POST['region_born']) ){
        $data['state'] = sanitize_text_field($_POST['region_born']);
    }

    if( isset($_POST['province_born']) && !empty($_POST['province_born']) ){
        $data['county'] = sanitize_text_field($_POST['province_born']);
    }

    if( isset($_POST['city_born']) && !empty($_POST['city_born']) ){
        $data['city'] = sanitize_text_field($_POST['city_born']);
    }

    if( isset($_POST['ubigeo_born']) && !empty($_POST['ubigeo_born']) ){
        $data['ubigeo'] = sanitize_text_field($_POST['ubigeo_born']);
    }

    if( isset($_POST['country_res']) && !empty($_POST['country_res']) ){
        $data['country_r'] = sanitize_text_field($_POST['country_res']);
    }

    if( isset($_POST['region_res']) && !empty($_POST['region_res']) ){
        $data['state_r'] = sanitize_text_field($_POST['region_res']);
    }

    if( isset($_POST['province_res']) && !empty($_POST['province_res']) ){
        $data['county_r'] = sanitize_text_field($_POST['province_res']);
    }

    if( isset($_POST['city_res']) && !empty($_POST['city_res']) ){
        $data['city_r'] = sanitize_text_field($_POST['city_res']);
    }

    if( isset($_POST['ubigeo_res']) && !empty($_POST['ubigeo_res']) ){
        $data['ubigeo_r'] = sanitize_text_field($_POST['ubigeo_res']);
    }

    
    if( 
        ($data['document_type'] == 'DNI' && !preg_match('/^([0-9]{8})$/m', $data['document_number'])) ||
        ($data['document_type'] == 'RUC' && !preg_match('/^([0-9]{11})$/m', $data['document_number'])) ||
        (in_array($data['document_type'], ['Pasaporte', 'Carnet Extrangería','Carnet Extranjería','Carnet Extrangeria','Carnet Extranjeria','CE']) && !preg_match('/^([a-zA-Z0-9]{8,12})$/m', $data['document_number']))
    ){
        $errors['document_number'] = 'El número de documento no es válido.';
    }

    if( empty($data['born_date']) ) $errors['born_date'] = 'Debe ingresar su fecha de nacimiento';

    $data['mobile'] = trim(str_replace('+', '', $data['mobile']));

    if( !preg_match('/^\+?([0-9]{7,15})$/m', $data['mobile']) ){
        $errors['mobile'] = 'Por favor ingrese un número de teléfono válido.';
    }

    if( is_array($data['keywords']) && !empty($data['keywords']) ){
        $counter = 1;
        foreach($data['keywords'] as $kw){
            $data['key' . $counter] = $kw;
            $counter++;
        }
        unset($data['keywords']);
    }

    if( is_array($data['skills']) && !empty($data['skills']) ){
        $temp = [];
        foreach($data['skills'] as $skill){
            $temp[] = ['skill' => sanitize_text_field($skill)];
        }
        $data['skills'] = $temp;
    }

    if( isset($data['studies']) && is_array($data['studies']) && !empty($data['studies']) ){
        foreach($data['studies'] as $key => $item){
            $data['studies'][$key] = shortcode_atts([
                'grade' => '',
                'specialty' => '',
                'institution' => '',
                'year_start' => '',
                'year_end' => '',
                'study_now' => '0'
            ], $data['studies'][$key]);

            if( isset($_REQUEST['studies'][$key]['study_now']) ){
                $data['studies'][$key]['study_now'] = 1;
            } 

            $item = $data['studies'][$key];
            
            if( empty($item['grade']) ){
                $errors['studies_' . $key . '_grade'] = 'Debes seleccionar un grado.';
            }
            if( empty($item['specialty']) ){
                $errors['studies_' . $key . '_specialty'] = 'Debes seleccionar una especialidad.';
            }
            if( empty($item['institution']) ){
                $errors['studies_' . $key . '_institution'] = 'Debes seleccionar un centro de estudios.';
            }
            if ( empty($item['year_start']) || !ctype_digit($item['year_start']) || strlen($item['year_start']) !== 4 ) {
                $errors['studies_' . $key . '_year_start'] = 'Debes seleccionar un año de ingreso válido de 4 dígitos.';
            }
        }

        $data['studies'] = array_values($data['studies']);
    }

    if( isset($data['complementary_studies']) && is_array($data['complementary_studies']) && !empty($data['complementary_studies']) ){
        foreach($data['complementary_studies'] as $key => $item){
            $data['complementary_studies'][$key] = shortcode_atts([
                'type_course' => '',
                'course' => '',
                'hours' => '',
                'institution' => '',
                'year' => '',
            ], $data['complementary_studies'][$key]);

            $item = $data['complementary_studies'][$key];
            
            if( empty($item['type_course']) ){
                $errors['complementary_' . $key . '_type'] = 'Debes seleccionar un tipo de estudio.';
            }
            if( empty($item['course']) ){
                $errors['complementary_' . $key . '_course'] = 'Ingrese el nombre del curso.';
            } else if ( empty($item['course']) || preg_match('/\d/', $item['course']) ) {
                $errors['complementary_' . $key . '_course'] = 'Ingrese un nombre válido.';
            }

            if( empty($item['hours']) ){
                $errors['complementary_' . $key . '_hours'] = 'Ingrese el número de horas.';
            } else if ( empty($item['hours']) || !ctype_digit(strval($item['hours'])) ) {
                $errors['complementary_' . $key . '_hours'] = 'Ingrese un número válido.';
            }


            if( empty($item['institution']) ){
                $errors['complementary_' . $key . '_institution'] = 'Debes seleccionar un centro de estudios.';
            } else if ( empty($item['course']) || preg_match('/\d/', $item['course']) ) {
                $errors['complementary_' . $key . '_course'] = 'Ingrese un nombre válido.';
            }


            if( empty($item['year']) ){
                $errors['complementary_' . $key . '_year'] = 'Debes seleccionar un año de ingreso.';
            } else if (
                empty($item['year']) ||
                !ctype_digit(strval($item['year'])) ||
                (int)$item['year'] < 1900 ||
                (int)$item['year'] > date('Y')
            ) {
                $errors['complementary_' . $key . '_year'] = 'Selecciona un año válido.';
            }

        }

        $data['complementary_studies'] = array_values($data['complementary_studies']);
    }

    if( isset($data['language']) && is_array($data['language']) && !empty($data['language']) ){
        foreach($data['language'] as $key => $item){
            $data['language'][$key] = shortcode_atts([
                'language' => '',
                'level' => ''
            ], $data['language'][$key]);

            $item = $data['language'][$key];

            if ( contains_sql_injection($item['language']) ) {
                $errors['language_' . $key . '_language'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }

            if ( contains_sql_injection($item['level']) ) {
                $errors['language_' . $key . '_level'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }


            if( empty($item['language']) ) $errors['language_' . $key . '_language'] = 'Debes elegir una opción';
            if( empty($item['level']) ) $errors['language_' . $key . '_level'] = 'Debes elegir una opción';
        }

        $data['language'] = array_values($data['language']);
    }

    if( isset($data['experience']) && is_array($data['experience']) && !empty($data['experience']) ){
        foreach($data['experience'] as $key => $item){
            $data['experience'][$key] = shortcode_atts([
                'type_position' => '',
                'position' => '',
                'date_initial' => '',
                'date_final' => '',
                'currently_work' => '0',
                'company' => '',
                'sector' => '',
                'salary' => '',
                'not_share_salary' => '0',
                'description' => ''
            ], $data['experience'][$key]);

            if( isset($_POST['experience'][$key]['currently_work']) )
                $data['experience'][$key]['currently_work'] = '1';

            if( isset($_POST['experience'][$key]['not_share_salary']) )
                $data['experience'][$key]['not_share_salary'] = '1';

            $item = $data['experience'][$key];

            if ( contains_sql_injection($item['type_position']) ) {
                $errors['experience_' . $key . '_type_position'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }

            if ( contains_sql_injection($item['position']) ) {
                $errors['experience_' . $key . '_position'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }

            if ( contains_sql_injection($item['company']) ) {
                $errors['experience_' . $key . '_company'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }

            if ( contains_sql_injection($item['sector']) ) {
                $errors['experience_' . $key . '_sector'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }

            if ( contains_sql_injection($item['description']) ) {
                $errors['experience_' . $key . '_description'] = '¡Intento de Hackeo! No se permiten sentencias SQL';
            }


            if ( !is_valid_text($item['position']) ) {
                $errors['experience_' . $key . '_position'] = 'Escribe un nombre válido.';
            }

            if ( !is_valid_text($item['company']) ) {
                $errors['experience_' . $key . '_company'] = 'Escribe un nombre válido.';
            }

            if ( !is_valid_text($item['description']) ) {
                $errors['experience_' . $key . '_description'] = 'Escribe una descripción válida.';
            }


            if( empty($item['date_initial']) ) $errors['experience_' . $key . '_date_initial'] = 'Debes ingresar una fecha';

            if( empty($item['type_position']) ) $errors['experience_' . $key . '_type_position'] = 'Elige una opción';
            if( empty($item['sector']) ) $errors['experience_' . $key . '_sector'] = 'Elige una opción';

            if( '1' != $item['currently_work'] ){
                if( empty($item['date_final']) ) $errors['experience_' . $key . '_date_final'] = 'Debes ingresar una fecha';
            }

            if( '1' != $item['not_share_salary'] ){
                if ( !is_valid_number($item['salary']) ) {
                    $errors['experience_' . $key . '_salary'] = 'Ingresa un número válido';
                }

            }
        }

        $data['experience'] = array_values($data['experience']);
    }

        // Validations
    $first_name = trim($data['first_name'] ?? '');

    if ( $first_name === '' ) {
        $errors['first_name'] = 'Ingresa un nombre.';
    } elseif ( preg_match('/\d/', $first_name) ) {
        $errors['first_name'] = 'El nombre no puede contener números.';
    }

    $last_name = trim($data['last_name'] ?? '');

    if ( $last_name === '' ) {
        $errors['last_name'] = 'Ingresa un apellido.';
    } elseif ( preg_match('/\d/', $last_name) ) {
        $errors['last_name'] = 'El apellido no puede contener números.';
    }

    $email = trim($data['email'] ?? '');

    if ( $email === '' ) {
        $errors['email'] = 'Ingresa un email.';
    } elseif ( !filter_var($email, FILTER_VALIDATE_EMAIL) ) {
        $errors['email'] = 'Ingresa un email válido.';
    }

    if( empty($data['gender']) ) $errors['gender'] = 'Escoge una opción';
    if( empty($data['document_type']) ) $errors['document_type'] = 'Escoge una opción';

    if( empty($data['country']) ) $errors['country_born'] = 'Escoge una opción';
    if( empty($data['country_r']) ) $errors['country_res'] = 'Escoge una opción';

    if ( trim($data['address'] ?? '') === '' ) {
        $errors['address'] = 'Ingrese una dirección válida';
    }


    if( '1' == $data['has_colegiatura'] ){
        if( !is_valid_text($data['colegiatura_school']) ) $errors['colegiatura_school'] = 'Ingrese un nombre válido.';
        if( contains_sql_injection($data['colegiatura_number']) ) $errors['colegiatura_number'] = 'Ingrese un código válido.';
    }

    

        // if( !empty($data['linkedin']) && !is_valid_username($data['linkedin']) ) {
        //     $errors['linkedin'] = 'Ingresa un nombre de usuario válido';
        // }
        // if( !empty($data['twitter']) && !is_valid_username($data['twitter']) ) {
        //     $errors['twitter'] = 'Ingresa un nombre de usuario válido.';
        // }
        // if( !empty($data['facebook']) && !is_valid_username($data['facebook']) ) {
        //     $errors['facebook'] = 'Ingresa un nombre de usuario válido.';
        // }
        // if( !empty($data['youtube']) && !is_valid_username($data['youtube']) ) {
        //     $errors['youtube'] = 'Ingresa un nombre de usuario válido.';
        // }
        // if( !empty($data['instagram']) && !is_valid_username($data['instagram']) ) {
        //     $errors['instagram'] = 'Ingresa un nombre de usuario válido.';
        // }
        // if( !empty($data['tiktok']) && !is_valid_username($data['tiktok']) ) {
        //     $errors['tiktok'] = 'Ingresa un nombre de usuario válido.';
        // }
    
    if( empty($data['profile']) ) $errors['profile'] = 'Ingresa una descripción';

    $profile = trim($data['profile'] ?? '');

    if ( $profile === '' ) {
        $errors['profile'] = 'Ingresa una descripción válida.';
    }

    if( !empty($data['profile']) && strlen($data['profile']) < 20 ) $errors['profile'] = 'Debe ingresar como mínimo 20 caracteres';

    if ( !empty($data['min_salary']) && !filter_var($data['min_salary'], FILTER_VALIDATE_INT) ) {
        $errors['min_salary'] = 'Ingresa un número válido.';
    }

    if ( !empty($data['max_salary']) && !filter_var($data['max_salary'], FILTER_VALIDATE_INT) ) {
        $errors['max_salary'] = 'Ingresa un número válido.';
    }


    if( (!empty($data['min_salary']) && !empty($data['max_salary'])) && !isset($errors['min_salary']) && !isset($errors['max_salary']) ){
        if( intval($data['min_salary']) > intval($data['max_salary']) ){
            $errors['min_salary'] = 'El salario mínimo debe ser menor que el salario máximo';
        }
    }

    if( isset($data['studies']) && is_array($data['studies']) && empty($data['studies']) ){
        wp_send_json_error(['error' => 'Debe agregar al menos una formación a su perfil']);
    }

    if( !empty($errors) ){
        wp_send_json_error(['errors' => $errors]);
    }

    if( !isset($data['agree_policy']) || empty($data['agree_policy']) ){
        wp_send_json_error(['error' => 'Debes aceptar los términos y condiciones para completar tu perfil.']);
    }
    


    if (isset($_FILES['avatar']) && $_FILES['avatar']['size'] !== 0) {
        $response = user_update_avatar($_FILES['avatar'], 'file');

        if (!$response['success']) {
            wp_send_json_error(['error' => $response['msg']]);
        } else {
           if( isset($response['url']) ){
               $args['avatar'] = $response['url'];
               $data['avatar'] = $response['url'];
           }
       }
   }

   

        // Procesar cada campo recibido
   foreach ( $data as $key => $value ) {
    if ( $key === 'errors' ) continue;

            // Serializa si es un array (como estudios, skills, etc.)
    update_user_meta( $user_id, $key, is_array($value) ? maybe_serialize($value) : sanitize_text_field($value) );
}
$api = true;
$data['msg'] = "Tus datos han sido actualizados.";

        // Preparar estructura de respuesta esperada
$args = [
    'data'  => $data,
    'success' => true,
    'errors' => $errors,
    'api'    => $api,
    'file'   => isset($_FILES['avatar']) ? $_FILES['avatar'] : null
];

        // Enviar respuesta en formato JSON
wp_send_json($args);

}


function user_update_avatar($file, $field_name = 'file') {
        // Cargar funciones necesarias de WP para subir archivos si no están cargadas
    if ( ! function_exists('media_handle_upload') ) {
        require_once( ABSPATH . 'wp-admin/includes/file.php' );
        require_once( ABSPATH . 'wp-admin/includes/media.php' );
        require_once( ABSPATH . 'wp-admin/includes/image.php' );
    }

        // Simular estructura $_FILES para media_handle_upload
    $_FILES = [$field_name => $file];

        // Subir el archivo (0 = sin adjuntar a post)
    $attachment_id = media_handle_upload($field_name, 0);

    if (is_wp_error($attachment_id)) {
        return [
            'success' => false,
            'msg'     => $attachment_id->get_error_message()
        ];
    }

    $url = wp_get_attachment_url($attachment_id);

    if (!$url) {
        return [
            'success' => false,
            'msg'     => 'No se pudo obtener la URL del archivo subido.'
        ];
    }

    return [
        'success' => true,
        'url'     => $url,
        'attachment_id' => $attachment_id
    ];
}



function contains_sql_injection($text) {
    return preg_match('/\b(SELECT|INSERT|UPDATE|DELETE|DROP|UNION|--|#|\*|;|OR\s+\d+=\d+|AND\s+\d+=\d+)\b/i', $text);
}

function is_valid_text($text) {
        // Quita espacios en blanco y valida que no esté vacío
    if ( trim($text) === '' ) return false;

        // Verifica que no haya números
    if ( preg_match('/\d/', $text) ) return false;

    return true;
}

function is_valid_number($value) {
        // Verifica que no esté vacío y que contenga solo dígitos
    return (trim($value) !== '' && preg_match('/^\d+$/', $value));
}

function is_valid_username($username) {
            // Permite letras, números, guiones bajos y puntos, sin espacios, entre 3 y 30 caracteres
    return preg_match('/^[a-zA-Z0-9._]{3,30}$/', $username);
}




add_action( 'wp_ajax_nopriv_upload_cv', 'upload_cv' );
add_action( 'wp_ajax_upload_cv',       'upload_cv' );

function upload_cv() {

    if (isset($_REQUEST['cv'])) {
        $base64Data = $_REQUEST['cv'];
        $decodedData = base64_decode($base64Data);

        if (strlen($decodedData) <= 2 * 1024 * 1024) { // 2MB máx

            $filename = 'cv_' . time() . '.pdf'; // Asegúrate de que sea PDF, ajusta según sea necesario
            $upload_dir = wp_upload_dir();

            // Ruta completa
            $file_path = $upload_dir['path'] . '/' . $filename;

            // Guardar archivo temporalmente
            file_put_contents($file_path, $decodedData);

            // Simular archivo para subirlo con wp_handle_sideload
            $file_array = [
                'name'     => $filename,
                'type'     => 'application/pdf',
                'tmp_name' => $file_path,
                'error'    => 0,
                'size'     => strlen($decodedData),
            ];

            // Requiere funciones de manejo de medios
            require_once(ABSPATH . 'wp-admin/includes/file.php');
            require_once(ABSPATH . 'wp-admin/includes/media.php');
            require_once(ABSPATH . 'wp-admin/includes/image.php');

            $upload_overrides = ['test_form' => false];
            $movefile = wp_handle_sideload($file_array, $upload_overrides);

            if ($movefile && !isset($movefile['error'])) {
                $url = $movefile['url'];
                $user_id = get_current_user_id();

                update_user_meta($user_id, 'cv_url', $url); // Guardamos la URL en user_meta
                wp_send_json_success(['msg' => 'CV subido con éxito', 'url' => $url]);
            } else {
                wp_send_json_error(['error' => 'Error al mover el archivo: ' . $movefile['error']]);
            }

        } else {
            wp_send_json_error(['error' => 'El archivo no debe pesar más de 2MB.']);
        }
    } else {
        wp_send_json_error(['error' => 'No se ha cargado ningún archivo.']);
    }

    wp_send_json(['error' => '¡Intento de hackeo! Sesión no válida']);
}
