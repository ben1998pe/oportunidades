<?php 


    define('TR_PATH1', plugin_dir_path(__FILE__));



function opt_custom_enqueue_scripts() {
    // Obtiene la URL correcta del archivo JS dentro del plugin
    $js_url = plugins_url('assets/js/oportunity.js', __FILE__);

    wp_enqueue_script(
        'oportunity-js',  // handle único para el script
        $js_url,          // URL al archivo JS
        array('jquery'),  // dependencias si las hay
        filemtime( plugin_dir_path(__FILE__) . 'assets/js/oportunity.js' ),
        true              // cargar en footer
    );

    // Pasar variables JS al script (opcional)
    wp_localize_script('oportunity-js', 'oportunity', array(
        'ajax_url' => admin_url('admin-ajax.php'),
         'nonce'    => wp_create_nonce('apply_opt')
    ));
}
add_action('wp_enqueue_scripts', 'opt_custom_enqueue_scripts');


function mi_plugin_enqueue_multiple_styles() {
    $css_path = plugin_dir_path(__FILE__) . 'assets/css/';  // ruta física al folder
    $css_url = plugin_dir_url(__FILE__) . 'assets/css/';    // url base para los css

    $css_files = [
        'choices.css',
        'cf7.css',
        'choices.min.css',
        'company.css',
        'defaults.css',
        'events.css',
        // 'gutenberg-editor.css',
        'header.css',
        'intlTelInput.min.css',
        'login.css',
        'oportunity.css',
        'reset.css',
        // 'tiny-slider.css',
        // 'vanilla-calendar.min.css',
        'variables.css',
        'style.css',
        'style.min.css',
        'account.css',

    ];

    foreach ($css_files as $css_file) {
        $file_path = $css_path . $css_file;

        // Obtiene la fecha de modificación del archivo, si existe, para usarla como versión
        $version = file_exists($file_path) ? filemtime($file_path) : false;

        wp_enqueue_style(
            'mi-plugin-' . sanitize_title($css_file),
            $css_url . $css_file,
            array(),
            $version
        );
    }
}
add_action('wp_enqueue_scripts', 'mi_plugin_enqueue_multiple_styles');


add_action('wp_enqueue_scripts', 'base_scripts');

function base_scripts()
    {

        $plugin_url = plugin_dir_url(__FILE__);

        wp_dequeue_script('wpcf7-recaptcha');

        // $css = [
        //     'turimet-intl'      => 'intlTelInput.min.css',
        //     'turimet-choices'   => 'choices.min.css',
        //     'turimet-variables' => 'variables.css',
        //     'turimet-reset'     => 'reset.css',
        //     'turimet-header'    => 'header.css',
        //     'turimet-footer'    => 'footer.css',
        //     'turimet-main'      => [
        //         'file' => 'style.css', 
        //         'deps' => ['google-fonts', 'turimet-choices', 'turimet-slider', 'turimet-variables', 'turimet-reset', 'turimet-header', 'turimet-footer']
        //     ]
        // ];
        

        wp_enqueue_style('google-fonts', 'https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&amp;family=Poppins:ital,wght@0,400;0,600;0,700;1,400;1,600;1,700&display=swap', [], '1.0');
        
        wp_register_style('turimet-intl', $plugin_url . '/assets/css/intlTelInput.min.css', [], rand());
        
        // wp_register_style('turimet-slider', $plugin_url . '/assets/css/tiny-slider.css', [], rand(), true);
        // wp_register_style('turimet-choices', $plugin_url . '/assets/css/choices.min.css', [], rand());
        // wp_register_style('turimet-variables', $plugin_url . '/assets/css/variables.css', [], rand());
        // wp_register_style('turimet-reset', $plugin_url . '/assets/css/reset.css', [], rand());
        // wp_register_style('turimet-header', $plugin_url . '/assets/css/header.css', [], rand());
        // wp_register_style('turimet-footer', $plugin_url . '/assets/css/footer.css', [], rand());
        // wp_register_style('turimet-main', $plugin_url . '/style.css', ['turimet-choices', 'turimet-slider', 'turimet-variables', 'turimet-reset', 'turimet-header', 'turimet-footer'], rand());

        wp_register_script( 'turimet-intl-utils', $plugin_url . '/assets/js/intlUtils.js', [], rand(), true);
        wp_register_script( 'turimet-intl', $plugin_url . '/assets/js/intlTelInput.min.js', ['turimet-intl-utils'], rand(), true);

        wp_register_script( 'turimet-slider', $plugin_url . '/assets/js/tiny-slider.js', [], false, true);
        wp_register_script( 'turimet-choices', $plugin_url . '/assets/js/choices.min.js', [], rand(), true);
        wp_register_script( 'turimet-main', $plugin_url . '/assets/js/main.js', ['turimet-choices', 'turimet-slider'], rand(), true);

        wp_register_style('turimet-oportunity', $plugin_url . '/assets/css/oportunity.css', [], rand());
        // wp_register_script('turimet-oportunity', $plugin_url . '/assets/js/oportunity.js', [], rand(), true);
        // wp_localize_script('turimet-oportunity', 'oportunity', [
        //     'ajax_url' => admin_url('admin-ajax.php'),
        //     'nonce' => wp_create_nonce('oportunity')
        // ]);

        

        wp_enqueue_style( 'turimet-cf7', $plugin_url . '/assets/css/cf7.css', [], filemtime( TR_PATH1 . 'assets/css/cf7.css' ) );
        // wpcf7_enqueue_scripts();
        // wpcf7_recaptcha_enqueue_scripts();

        if('oportunity'==get_query_var('section') || 'company' ==get_query_var('section')){
            wp_enqueue_style('turimet-intl');
            wp_enqueue_style('turimet-oportunity');
            wp_enqueue_script('turimet-oportunity');
            wp_localize_script('turimet-oportunity', 'turivar', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('turimet-oportunity')
            ]);
        }

        if( is_singular() && has_block('turimet/oportunity') ){
            wp_enqueue_style('turimet-intl');
        }

        wp_enqueue_style('turimet-intl');
        wp_enqueue_script('turimet-intl');

        wp_enqueue_style('turimet-main');
        wp_enqueue_script('turimet-main');

        if( 'oportunity'==get_query_var('section') || is_singular('event') ){
            $args = [
                'current_url' => 'https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']
            ];
            wp_localize_script('turimet-main', 'turishare', $args);
        }
        if( is_page() && 'template-recovery.php'===basename(get_page_template()) ){
            wp_enqueue_script('popper', 'https://unpkg.com/popper.js@1', [], false, true);
            wp_enqueue_script('tippy', 'https://unpkg.com/tippy.js@4', [], false, true);
        }
        

       if (
            is_page() &&
            in_array(get_post_field('post_name', get_post()), ['login','iniciar-sesion', 'registro', 'registro-opt',  'login-opt'])
        ) {
            $version_base = '6.0.0';

            $login_css_path = TR_PATH1 . 'assets/css/login.css';
            $login_js_path  = TR_PATH1 . 'assets/js/login.js';

            $login_css_version = file_exists($login_css_path) ? $version_base . '-' . filemtime($login_css_path) : $version_base;
            $login_js_version  = file_exists($login_js_path) ? $version_base . '-' . filemtime($login_js_path) : $version_base;

            wp_enqueue_style(
                'login_opt',
                $plugin_url . '/assets/css/login.css',
                [],
                $login_css_version
            );

            wp_enqueue_script(
                'login_opt',
                $plugin_url . '/assets/js/login.js',
                [],
                $login_js_version,
                true // recomendable: cargar al final del body
            );

            wp_localize_script('login_opt', 'login_opt_all', [
                'ajax_url'      => admin_url('admin-ajax.php'),
                'nonce_login_opt'   => wp_create_nonce('turimet-login-opt'),
                'nonce_register_opt'=> wp_create_nonce('turimet-register-opt')
            ]);

            // Desactiva estilos/scripts principales si es necesario
            wp_dequeue_style('turimet-main');
            wp_dequeue_script('turimet-main');
        }


         if (
            is_page() &&
            in_array(get_post_field('post_name', get_post()), ['mi-cuenta/m'])
        ){
            wp_enqueue_style('turimet-account', $plugin_url . '/assets/css/account.css', [], filemtime( TR_PATH1 . 'assets/css/account.css' ) );
            wp_enqueue_script('turimet-account', $plugin_url . '/assets/js/account.js', [], filemtime( TR_PATH1 . 'assets/js/account.js' ) );
        }

         if (
            is_page() &&
            in_array(get_post_field('post_name', get_post()), ['mi-cuenta'])
        ){
            wp_enqueue_script('turimet-profile', $plugin_url . '/assets/js/profile.js', [], filemtime( TR_PATH1 . 'assets/js/profile.js' ) );
            wp_enqueue_script('popper', 'https://unpkg.com/popper.js@1', [], false, true);
            wp_enqueue_script('tippy', 'https://unpkg.com/tippy.js@4', [], false, true);
            wp_localize_script('turimet-profile', 'tuProfile', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('turimet-profile')
            ]);
        }

         if (
            is_page() &&
            in_array(get_post_field('post_name', get_post()), ['configuracion'])
        ){
            wp_enqueue_script('turimet-profile', $plugin_url . '/assets/js/change-password.js', [], filemtime( TR_PATH1 . 'assets/js/change-password.js' ) );
            wp_enqueue_script('popper', 'https://unpkg.com/popper.js@1', [], false, true);
            wp_enqueue_script('tippy', 'https://unpkg.com/tippy.js@4', [], false, true);
            wp_localize_script('turimet-profile', 'tuProfile', [
                'ajax_url' => admin_url('admin-ajax.php'),
                'nonce' => wp_create_nonce('turimet-cpwd')
            ]);
        }
    }



//     add_action('admin_init', function() {
//     // Si el usuario NO puede editar posts (es decir, no es editor o admin)
//     if ( current_user_can('subscriber') && !current_user_can('edit_posts') ) {
//         // Redirigir fuera del admin al inicio (o a cualquier URL)
//         wp_redirect( home_url() );
//         exit;
//     }
// });

// add_action('admin_bar_menu', function($wp_admin_bar) {
//     // Ocultar la barra de administración para suscriptores
//     if ( current_user_can('subscriber') && !current_user_can('edit_posts') ) {
//         remove_action( 'admin_bar_menu', 'wp_admin_bar_my_account_menu', 0 );
//         show_admin_bar(false);
//     }
// }, 999);
add_action('after_setup_theme', function() {
    if ( is_user_logged_in() && current_user_can('subscriber') ) {
        show_admin_bar(false);
    }
});

add_filter('show_admin_bar', function($show) {
    if ( is_user_logged_in() && current_user_can('subscriber') ) {
        return false;
    }
    return $show;
});

