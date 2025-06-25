<?php
/**
 * Plugin Name: Opt Custom (Oportunidades)
 * Description: Plugin personalizado para gestionar oportunidades y páginas especiales.
 * Version: 1.0
 * Author: MM
 */

require_once __DIR__ . '/vendor/autoload.php';


require_once(ABSPATH . 'wp-admin/includes/file.php');
require_once(ABSPATH . 'wp-admin/includes/media.php');
require_once(ABSPATH . 'wp-admin/includes/image.php');

require_once plugin_dir_path(__FILE__) . 'user.php';
require_once plugin_dir_path(__FILE__) . 'ajax.php';
require_once plugin_dir_path(__FILE__) . 'globales.php';
require_once plugin_dir_path(__FILE__) . 'upload-assets.php';

define('OPT_CUSTOM_PLUGIN_URL', plugin_dir_url(__FILE__));


register_activation_hook(__FILE__, 'crear_tabla_opt_inscripciones');

function crear_tabla_opt_inscripciones() {
    global $wpdb;

    // Obtener el prefijo de tabla de WordPress
    $tabla = $wpdb->prefix . 'opt_inscripciones';

    // Establecer el charset y collation
    $charset_collate = $wpdb->get_charset_collate();

    // Incluir la librería necesaria para dbDelta
    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');

    // Definir el SQL de creación de la tabla
    $sql = "
        CREATE TABLE IF NOT EXISTS $tabla (
            id INT NOT NULL AUTO_INCREMENT,
            id_oportunidades INT NOT NULL,
            id_usuario INT NOT NULL,
            fecha DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            PRIMARY KEY (id)
        ) $charset_collate;
    ";

    // Ejecutar la creación de la tabla
    dbDelta($sql);
}


function crear_custom_post_oportunidades() {
	$labels = array(
		'name' => 'Oportunidades',
		'singular_name' => 'Oportunidad',
		'menu_name' => 'Oportunidades',
		'name_admin_bar' => 'Oportunidad',
		'add_new' => 'Añadir Nueva',
		'add_new_item' => 'Añadir Nueva Oportunidad',
		'new_item' => 'Nueva Oportunidad',
		'edit_item' => 'Editar Oportunidad',
		'view_item' => 'Ver Oportunidad',
		'all_items' => 'Todas las Oportunidades',
		'search_items' => 'Buscar Oportunidades',
		'not_found' => 'No se encontraron oportunidades.',
	);

	$args = array(
		'labels' => $labels,
		'public' => true,
		'has_archive' => true,
		'rewrite' => array('slug' => 'oportunidades'),
		'supports' => array('title', 'editor', 'thumbnail'),
		'show_in_rest' => true,
	);

	register_post_type('oportunidad', $args);
}

function crear_taxonomia_categoria_oportunidad() {
	$labels = array(
		'name'              => 'Categorías de Oportunidad',
		'singular_name'     => 'Categoría de Oportunidad',
		'search_items'      => 'Buscar Categorías',
		'all_items'         => 'Todas las Categorías',
		'parent_item'       => 'Categoría Padre',
		'parent_item_colon' => 'Categoría Padre:',
		'edit_item'         => 'Editar Categoría',
		'update_item'       => 'Actualizar Categoría',
		'add_new_item'      => 'Añadir Nueva Categoría',
		'new_item_name'     => 'Nombre de Nueva Categoría',
		'menu_name'         => 'Categorías',
	);

	$args = array(
		'hierarchical'      => true,
		'labels'            => $labels,
		'show_ui'           => true,
		'show_admin_column' => true,
		'rewrite'           => array('slug' => 'categoria-oportunidad'),
		'show_in_rest'      => true,
	);

	register_taxonomy('categoria_oportunidad', array('oportunidad'), $args);
}

add_action('init', 'crear_custom_post_oportunidades');
add_action('init', 'crear_taxonomia_categoria_oportunidad');


add_filter('template_include', 'mi_plugin_archive_oportunidad_template');

function mi_plugin_archive_oportunidad_template($template) {
	if (is_post_type_archive('oportunidad')) {
        // Ruta al archivo archive dentro del plugin
		$plugin_template = plugin_dir_path(__FILE__) . 'templates/archive-oportunidades.php';

		if (file_exists($plugin_template)) {
			return $plugin_template;
		}
	}
	return $template;
}


add_filter('template_include', 'mi_plugin_template_para_pagina', 99);
function mi_plugin_template_para_pagina($template) {
	if (is_page('mis-postulaciones')) {
		$plugin_template = plugin_dir_path(__FILE__) . 'templates/cuenta-oportunidad.php';
		if (file_exists($plugin_template)) {
			return $plugin_template;
		}
	}
	return $template;
}



add_filter('template_include', 'mi_plugin_single_oportunidad_template');

function mi_plugin_single_oportunidad_template($template) {
	if (is_singular('oportunidad')) {
        // Ruta al template single dentro del plugin
		$plugin_template = plugin_dir_path(__FILE__) . 'templates/single-oportunidades.php';

		if (file_exists($plugin_template)) {
			return $plugin_template;
		}
	}
	return $template;
}


add_filter('template_include', 'mi_plugin_single_company_template');

function mi_plugin_single_company_template($template) {
	if (is_author()) {
		$plugin_template = plugin_dir_path(__FILE__) . 'templates/single-company.php';

		if (file_exists($plugin_template)) {
			return $plugin_template;
		}
	}
	return $template;
}





// Añadir metaboxes personalizados
function agregar_campos_personalizados_oportunidades() {
	add_meta_box('datos_oportunidad', 'Datos de la Oportunidad', 'campos_oportunidad_callback', 'oportunidad', 'normal', 'high');
}
add_action('add_meta_boxes', 'agregar_campos_personalizados_oportunidades');




// Mostrar los campos en el panel
function campos_oportunidad_callback($post) {
	wp_nonce_field('guardar_datos_oportunidad', 'datos_oportunidad_nonce');
	global $countries;
	$salario = get_post_meta($post->ID, '_salario', true);
	$fecha = get_post_meta($post->ID, '_fecha', true);
	$fecha_fin = get_post_meta($post->ID, '_fecha_fin', true);
	$currency = get_post_meta($post->ID, '_currency', true);
	$categoria = get_post_meta($post->ID, '_categoria', true);
	$jornada = get_post_meta($post->ID, '_jornada', true);
	$contrato = get_post_meta($post->ID, '_contrato', true);
	$modalidad = get_post_meta($post->ID, '_modalidad', true);
	// $tipo_moneda = get_post_meta($post->ID, '_tipo_moneda', true);
	$salario_min = get_post_meta($post->ID, '_salario_min', true);
	$salario_max = get_post_meta($post->ID, '_salario_max', true);
	$pais = get_post_meta($post->ID, '_pais', true); // Nuevo campo para país
	$enlace_reportar = get_post_meta($post->ID, '_enlace_reportar', true);
	$countries = $GLOBALS['countries']; // Obtener el array global de países
	$departamento = get_post_meta($post->ID, '_oportunida_departamento', true);
	$provincia = get_post_meta($post->ID, '_oportunida_provincia', true);
	$distrito = get_post_meta($post->ID, '_oportunida_distrito', true);



	?>
	<div style="display: flex; gap: 8px; flex-direction: row;">
		<p>
			<label>País:
				<select name="pais">
					<option value="" selected>País</option>
					<?php foreach( $countries as $item ): ?>
						<option value="<?php echo esc_attr($item['name']); ?>" <?php selected($pais, $item['name']); ?>>
							<?php echo esc_html($item['name']); ?>
						</option>
					<?php endforeach; ?>
				</select>
			</label>
		</p>

		<div id="ubigeo-selects" style="display:none; flex-direction: row; gap: 8px;">

			<p>
				<label>Departamento:
					<select name="oportunida_departamento" id="departamento">
						<option value="">Selecciona departamento</option>
					</select>
				</label>
			</p>
			<p>
				<label>Provincia:
					<select name="oportunida_provincia" id="provincia">
						<option value="">Selecciona provincia</option>
					</select>
				</label>
			</p>
			<p>
				<label>Distrito:
					<select name="oportunida_distrito" id="distrito">
						<option value="">Selecciona distrito</option>
					</select>
				</label>
			</p>
		</div>

	</div>

	<!-- <p>
		<label>Moneda:
			<select name="tipo_moneda">
				<option value="">Seleccione</option>
				<option value="S/" <?php selected($tipo_moneda, 'S/'); ?>>S/</option>
				<option value="$" <?php selected($tipo_moneda, '$'); ?>>$</option>
			</select>
		</label>
	</p>
 -->
	<p><label>Salario Mínimo: <input type="number" step="0.01" name="salario_min" value="<?php echo esc_attr($salario_min); ?>"></label></p>
	<p><label>Salario Máximo: <input type="number" step="0.01" name="salario_max" value="<?php echo esc_attr($salario_max); ?>"></label></p>
	<p><label>Fecha: <input type="date" name="fecha" value="<?php echo esc_attr($fecha); ?>"></label></p>
	<p><label>Fecha final: <input type="date" name="fecha_fin" value="<?php echo esc_attr($fecha_fin); ?>"></label></p>
	<p>
		<label>Currency:
			<select name="currency">
				<option value="" disabled>Seleccione</option>
				<option value="USD" <?php selected($currency, 'USD'); ?>>USD</option>
				<option value="EUR" <?php selected($currency, 'EUR'); ?>>EUR</option>
				<option value="SOL" <?php selected($currency, 'SOL'); ?>>SOL</option>
			</select>
		</label>
	</p>

	<p><label>Categoría:
		<select name="categoria">
			<option value="">Selecciona una categoría</option>
			<option value="Gestión Nacional" <?php selected($categoria, 'Gestión Nacional'); ?>>Gestión Nacional</option>
			<option value="Gestión Regional" <?php selected($categoria, 'Gestión Regional'); ?>>Gestión Regional</option>
			<option value="Gestión Municipal" <?php selected($categoria, 'Gestión Municipal'); ?>>Gestión Municipal</option>
			<option value="Voluntariado" <?php selected($categoria, 'Voluntariado'); ?>>Voluntariado</option>
			<option value="Prácticas Profesionales" <?php selected($categoria, 'Prácticas Profesionales'); ?>>Prácticas Profesionales</option>
			<option value="Prácticas Pre Profesionales" <?php selected($categoria, 'Prácticas Pre Profesionales'); ?>>Prácticas Pre Profesionales</option>
			<option value="Educación" <?php selected($categoria, 'Educación'); ?>>Educación</option>
			<option value="Consultoría" <?php selected($categoria, 'Consultoría'); ?>>Consultoría</option>
			<option value="Agencias de Viajes" <?php selected($categoria, 'Agencias de Viajes'); ?>>Agencias de Viajes</option>
			<option value="Restaurantes" <?php selected($categoria, 'Restaurantes'); ?>>Restaurantes</option>
			<option value="Hotelería" <?php selected($categoria, 'Hotelería'); ?>>Hotelería</option>
		</select>
	</label></p>


	<p><label>Jornada:
		<select name="jornada">
			<option value="">Tipo de jornada</option>
			<option value="Jornada Nocturna" <?php selected($jornada, 'Jornada Nocturna'); ?>>Jornada Nocturna</option>
			<option value="Jornada por Suplencia" <?php selected($jornada, 'Jornada por Suplencia'); ?>>Jornada por Suplencia</option>
			<option value="Jornada por Horas" <?php selected($jornada, 'Jornada por Horas'); ?>>Jornada por Horas</option>
			<option value="Jornada Tiempo Parcial" <?php selected($jornada, 'Jornada Tiempo Parcial'); ?>>Jornada Tiempo Parcial</option>
			<option value="Jornada Completa" <?php selected($jornada, 'Jornada Completa'); ?>>Jornada Completa</option>
		</select>
	</label></p>

	<p><label>Contrato:
		<select name="contrato">
			<option value="">Tipo de contrato</option>
			<option value="Teletrabajo" <?php selected($contrato, 'Teletrabajo'); ?>>Teletrabajo</option>
			<option value="Parcial - Medio Tiempo" <?php selected($contrato, 'Parcial - Medio Tiempo'); ?>>Parcial - Medio Tiempo</option>
			<option value="Para obra o servicio" <?php selected($contrato, 'Para obra o servicio'); ?>>Para obra o servicio</option>
			<option value="Temporal" <?php selected($contrato, 'Temporal'); ?>>Temporal</option>
			<option value="Determinado a plazo fijo" <?php selected($contrato, 'Determinado a plazo fijo'); ?>>Determinado a plazo fijo</option>
			<option value="Indefinido" <?php selected($contrato, 'Indefinido'); ?>>Indefinido</option>
		</select>
	</label></p>


	<p><label>Modalidad:
		<select name="modalidad">
			<option value="">Tipo de modalidad</option>
			<option value="Mixta" <?php selected($modalidad, 'Mixta'); ?>>Mixta</option>
			<option value="Remoto" <?php selected($modalidad, 'Remoto'); ?>>Remoto</option>
			<option value="Presencial" <?php selected($modalidad, 'Presencial'); ?>>Presencial</option>
		</select>
	</label></p>

	<p>
	<label>Discapacidad
		<select name="discapacidad">
			<option value="">Selecciona una opción</option>
			<option value="Sí" <?php selected(get_post_meta($post->ID, '_discapacidad', true), 'Sí'); ?>>Sí</option>
			<option value="No" <?php selected(get_post_meta($post->ID, '_discapacidad', true), 'No'); ?>>No</option>
		</select>
	</label>
</p>


	<p>
		<label for="enlace_reportar">Enlace reportar oportunidad:</label><br>
		<input type="url" name="enlace_reportar" id="enlace_reportar" value="<?php echo esc_attr($enlace_reportar); ?>" style="width:100%;" placeholder="https://">
	</p>	


<script>
const basePath = '/wp-content/plugins/opt-custom/assets/';
let departamentos = [], provincias = [], distritos = [];

const paisSelect = document.querySelector('[name="pais"]');
const depSel = document.getElementById('departamento');
const provSel = document.getElementById('provincia');
const distSel = document.getElementById('distrito');
const ubigeoDiv = document.getElementById('ubigeo-selects');

// Valores guardados desde PHP (ya sin espacios)
const valDep = "<?php echo esc_js(trim($departamento)); ?>";
const valProv = "<?php echo esc_js(trim($provincia)); ?>";
const valDist = "<?php echo esc_js(trim($distrito)); ?>";

Promise.all([
    fetch(basePath + 'ubigeo_peru_2016_departamentos.json').then(r => r.json()),
    fetch(basePath + 'ubigeo_peru_2016_provincias.json').then(r => r.json()),
    fetch(basePath + 'ubigeo_peru_2016_distritos.json').then(r => r.json())
]).then(([deps, provs, dists]) => {
    departamentos = deps;
    provincias = provs;
    distritos = dists;

    if (paisSelect.value.trim() === 'Peru') {
        ubigeoDiv.style.display = 'flex';
        cargarDepartamentos(valDep);
    }
});

paisSelect.addEventListener('change', () => {
    if (paisSelect.value.trim() === 'Peru') {
        ubigeoDiv.style.display = 'flex';
        cargarDepartamentos();
    } else {
        ubigeoDiv.style.display = 'none';
    }
});

function cargarDepartamentos(selectedDep = '') {
    depSel.innerHTML = '<option value="">Selecciona departamento</option>';
    departamentos.forEach(dep => {
        const depName = dep.name.trim();
        const selected = depName === selectedDep ? 'selected' : '';
        depSel.innerHTML += `<option value="${depName}" ${selected}>${depName}</option>`;
    });

    if (selectedDep) {
        cargarProvincias(selectedDep, valProv);
    }
}

function cargarProvincias(nombreDep, selectedProv = '') {
    const dep = departamentos.find(d => d.name.trim() === nombreDep.trim());
    if (!dep) return;

    provSel.innerHTML = '<option value="">Selecciona provincia</option>';
    distSel.innerHTML = '<option value="">Selecciona distrito</option>';

    provincias
        .filter(p => p.department_id === dep.id)
        .forEach(prov => {
            const provName = prov.name.trim();
            const selected = provName === selectedProv ? 'selected' : '';
            provSel.innerHTML += `<option value="${provName}" ${selected}>${provName}</option>`;
        });

    if (selectedProv) {
        cargarDistritos(selectedProv, valDist);
    }
}

function cargarDistritos(nombreProv, selectedDist = '') {
    const prov = provincias.find(p => p.name.trim() === nombreProv.trim());
    if (!prov) return;

    distSel.innerHTML = '<option value="">Selecciona distrito</option>';

    distritos
        .filter(d => d.province_id === prov.id)
        .forEach(dist => {
            const distName = dist.name.trim();
            const selected = distName === selectedDist ? 'selected' : '';
            distSel.innerHTML += `<option value="${distName}" ${selected}>${distName}</option>`;
        });
}

// Eventos del usuario
depSel.addEventListener('change', () => {
    cargarProvincias(depSel.value);
});

provSel.addEventListener('change', () => {
    cargarDistritos(provSel.value);
});
</script>



	<?php
}


// Guardar los valores al guardar el post
function guardar_datos_oportunidad($post_id) {
	if (!isset($_POST['datos_oportunidad_nonce']) || !wp_verify_nonce($_POST['datos_oportunidad_nonce'], 'guardar_datos_oportunidad')) {
		return;
	}
	if (defined('DOING_AUTOSAVE') && DOING_AUTOSAVE) return;

	if (isset($_POST['oportunida_departamento'])) {
		update_post_meta($post_id, '_oportunida_departamento', sanitize_text_field($_POST['oportunida_departamento']));
	}
	if (isset($_POST['oportunida_provincia'])) {
		update_post_meta($post_id, '_oportunida_provincia', sanitize_text_field($_POST['oportunida_provincia']));
	}
	if (isset($_POST['oportunida_distrito'])) {
		update_post_meta($post_id, '_oportunida_distrito', sanitize_text_field($_POST['oportunida_distrito']));
	}


	if (isset($_POST['salario'])) update_post_meta($post_id, '_salario', sanitize_text_field($_POST['salario']));
	if (isset($_POST['fecha'])) update_post_meta($post_id, '_fecha', sanitize_text_field($_POST['fecha']));
	if (isset($_POST['fecha_fin'])) update_post_meta($post_id, '_fecha_fin', sanitize_text_field($_POST['fecha_fin']));
	if (isset($_POST['currency'])) update_post_meta($post_id, '_currency', sanitize_text_field($_POST['currency']));
	if (isset($_POST['categoria'])) update_post_meta($post_id, '_categoria', sanitize_text_field($_POST['categoria']));
	if (isset($_POST['jornada'])) update_post_meta($post_id, '_jornada', sanitize_text_field($_POST['jornada']));
	if (isset($_POST['contrato'])) update_post_meta($post_id, '_contrato', sanitize_text_field($_POST['contrato']));
	if (isset($_POST['modalidad'])) update_post_meta($post_id, '_modalidad', sanitize_text_field($_POST['modalidad']));
	// if (isset($_POST['tipo_moneda'])) update_post_meta($post_id, '_tipo_moneda', sanitize_text_field($_POST['tipo_moneda']));
	if (isset($_POST['salario_min'])) update_post_meta($post_id, '_salario_min', sanitize_text_field($_POST['salario_min']));
	if (isset($_POST['salario_max'])) update_post_meta($post_id, '_salario_max', sanitize_text_field($_POST['salario_max']));
	if (isset($_POST['enlace_reportar'])) {
		update_post_meta($post_id, '_enlace_reportar', esc_url_raw($_POST['enlace_reportar']));
	}
	if (isset($_POST['pais'])) {
		update_post_meta($post_id, '_pais', sanitize_text_field($_POST['pais']));
	}
	if (isset($_POST['discapacidad'])) {
		update_post_meta($post_id, '_discapacidad', sanitize_text_field($_POST['discapacidad']));
	}


}
add_action('save_post', 'guardar_datos_oportunidad');


// CREAR VISTAS

// Crear las páginas necesarias al activar el plugin
register_activation_hook(__FILE__, 'opt_crear_paginas_usuario');

function opt_crear_paginas_usuario() {
    // Verificar si existe "Mi Cuenta"
    $mi_cuenta = get_page_by_path('mi-cuenta');
    if (!$mi_cuenta) {
        $mi_cuenta_id = wp_insert_post([
            'post_title'    => 'Mi Cuenta',
            'post_name'     => 'mi-cuenta',
            'post_status'   => 'publish',
            'post_type'     => 'page',
            'post_content'  => '',
            'page_template' => 'usuario-perfil.php',
        ]);
    } else {
        $mi_cuenta_id = $mi_cuenta->ID;
    }

    // Crear páginas hijas
    $paginas_hijas = [
        [
            'titulo'   => 'Mis Postulaciones',
            'slug'     => 'mis-postulaciones',
            'template' => 'usuario-postulaciones.php',
        ],
        [
            'titulo'   => 'Configuración',
            'slug'     => 'configuracion',
            'template' => 'usuario-password.php',
        ]
    ];

    foreach ($paginas_hijas as $pagina) {
    	$path_hijo = 'mi-cuenta/' . $pagina['slug'];
    	if (!get_page_by_path($path_hijo)) {
    		wp_insert_post([
    			'post_title'    => $pagina['titulo'],
    			'post_name'     => $pagina['slug'],
    			'post_status'   => 'publish',
    			'post_type'     => 'page',
    			'post_content'  => '',
    			'page_template' => $pagina['template'],
    			'post_parent'   => $mi_cuenta_id,
    		]);
    	}
    }

    // Crear otras páginas (login, registro, descargar)
    $otras_paginas = [
        [
            'titulo'   => 'Iniciar sesión',
            'slug'     => 'iniciar-sesion',
            'template' => 'template-login.php',
        ],
        [
            'titulo'   => 'Registro',
            'slug'     => 'registro',
            'template' => 'template-register.php',
        ],
        [
            'titulo'   => 'Descargar',
            'slug'     => 'descargar',
            'template' => 'usuario-descargar.php',
        ],
    ];

    foreach ($otras_paginas as $pagina) {
        if (!get_page_by_path($pagina['slug'])) {
            wp_insert_post([
                'post_title'    => $pagina['titulo'],
                'post_name'     => $pagina['slug'],
                'post_status'   => 'publish',
                'post_type'     => 'page',
                'post_content'  => '',
                'page_template' => $pagina['template'],
            ]);
        }
    }
}


// Registrar templates para que aparezcan en el editor de páginas
add_filter('theme_page_templates', 'opt_registrar_templates_plugin');
function opt_registrar_templates_plugin($templates) {
	$templates['usuario-postulaciones.php'] = 'Usuario - Postulaciones OPT';
	$templates['usuario-perfil.php']        = 'Usuario - Perfil OPT';
	$templates['usuario-password.php']      = 'Usuario - Cambio de Contraseña OPT';
	$templates['template-login.php']        = 'Login Page OPT';
	$templates['template-register.php']     = 'Register Page OPT';
	$templates['usuario-descargar.php'] = 'Usuario - Descargar CV OPT';

	return $templates;
}

// Cargar templates desde el plugin si están asignados a una página
add_filter('template_include', 'opt_cargar_templates_plugin');
function opt_cargar_templates_plugin($template) {
	if (is_page()) {
		$template_slug = get_page_template_slug(get_queried_object_id());

		$templates = [
			'usuario-postulaciones.php',
			'usuario-perfil.php',
			'usuario-password.php',
			'template-login.php',
			'template-register.php',
			'usuario-descargar.php',
		];

		if (in_array($template_slug, $templates)) {
			$ruta = plugin_dir_path(__FILE__) . 'templates/' . $template_slug;
			if (file_exists($ruta)) {
				return $ruta;
			}
		}
	}

	return $template;
}



function oportunidades_shortcode($atts) {
    // Atributos del shortcode
	$atts = shortcode_atts(array(
        'categoria' => '', // slug de la categoría
        'max'       => 5,  // número máximo de entradas
        'blank'     => true, // abrir enlaces en nueva pestaña
      ), $atts, 'oportunidades');

    // Query
	$args = array(
		'post_type'      => 'oportunidad',
		'posts_per_page' => intval($atts['max']),
		'post_status'    => 'publish',
		'orderby'        => 'date',
		'order'          => 'DESC',
		'meta_query'     => array(
			'relation' => 'AND',
			array(
				'key'     => '_fecha_fin',
				'compare' => 'EXISTS',
			),
			array(
				'key'     => '_fecha_fin',
				'value'   => date('Y-m-d'),
				'compare' => '>=',
				'type'    => 'DATE'
			)
		),
	);

	if (!empty($atts['categoria'])) {
		$args['tax_query'] = array(
			array(
                'taxonomy' => 'categoria_oportunidad', // Asegúrate que el CPT usa esta taxonomía
                'field'    => 'slug',
                'terms'    => sanitize_text_field($atts['categoria']),
              )
		);
	}

	$items = get_posts($args);

    ob_start(); // Captura el output
    if ($items) {
    	foreach ($items as $post) {
    		setup_postdata($post);

    		$blank = $atts['blank'] ? ' target="_blank"' : '';
    		$created_at = ucfirst(date_i18n('F j, Y', strtotime(get_the_date('Y-m-d', $post))));
    		$post_author_id = (int)$post->post_author;
    		$user_info = get_userdata($post_author_id);

    		$author_slug = $user_info ? $user_info->user_nicename : '';
    		$empresa_nombre = get_user_meta($post_author_id, 'empresa_nombre', true);
    		$empresa_pais = get_user_meta($post_author_id, 'empresa_pais', true);
    		$empresa_icono = get_user_meta($post_author_id, 'empresa_icono', true);

    		$modalidad = get_post_meta($post->ID, '_modalidad', true);
    		$_salario_max = get_post_meta($post->ID, '_salario_min', true);
    		$_salario_min = get_post_meta($post->ID, '_salario_max', true);
    		$_pais = get_post_meta($post->ID, '_pais', true);

            $code = "pe"; // Puedes personalizar esto según el país
            $url = get_permalink($post);
            $urlautor = get_author_posts_url($post_author_id);
            ?>

            <style>
            	.oplist__main a{
            		text-decoration: none;
            	}
            </style>

            <div class="oplist__item oplist__item"  data-url="<?= esc_url($url) ?>">
            	<figure class="fig-contain oplist__image">
            		<a href="<?= $urlautor; ?>"<?= $blank ?>>
            			<img src="<?= esc_url($empresa_icono) ?>" alt="" />
            		</a>
            	</figure>
            	<div class="oplist__main">
            		<div class="oplist__main--company">
            			<a href="<?= $urlautor; ?>" target="_blank"><?= esc_html($empresa_nombre) ?></a>
            		</div>
            		<h3 class="oplist__main--job">
            			<a href="<?= $url ?>" target="_blank"><?= esc_html($post->post_title) ?></a>
            		</h3>
            		<div class="oplist__main--country">
            			<div class="iti__flag iti__<?= esc_attr($code) ?>"></div>
            			<?= esc_html($empresa_pais) ?>
            		</div>
            	</div>
            	<div class="oplist__side">
            		<ul>
            			<li class="oplist__side--creation"><?= esc_html($created_at) ?></li>
            			<li class="oplist__side--modality"><?= esc_html($modalidad) ?></li>
            		</ul>
            	</div>
            </div>

            <script type="text/javascript">
            	document.addEventListener('DOMContentLoaded', function () {
            		document.querySelectorAll('.oplist__item').forEach(function (item) {
            			item.addEventListener('click', function (e) {

            				if (!e.target.closest('a')) {
            					const url = item.getAttribute('data-url');
            					if (url) {
			                    window.open(url, '_blank'); // Usa '_self' si no quieres nueva pestaña
			                  }
			                }
			              });
            		});
            	});

            </script>
          <?php }
          wp_reset_postdata();
        } else {
        	echo '<p>No se encontraron oportunidades.</p>';
        }

        return ob_get_clean();
      }
      add_shortcode('oportunidades', 'oportunidades_shortcode');

function cambiar_slug_author() {
    global $wp_rewrite;
    $wp_rewrite->author_base = 'empresa'; // Cambia 'empresa' por lo que quieras
    $wp_rewrite->flush_rules(); // Refresca las reglas de reescritura
}
add_action('init', 'cambiar_slug_author');
