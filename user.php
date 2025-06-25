<?php

function custom_user_profile_fields($user) {

    if ( ! in_array('administrator', (array) $user->roles) ) {
        return; // Si el usuario no tiene el rol suscriptor, no mostramos nada
    }
    $icono = get_the_author_meta('empresa_icono', $user->ID);
    $fondo = get_the_author_meta('empresa_fondo', $user->ID);
    global $countries;
    $countries              = $GLOBALS['countries'];
    ?>
    <h3>Información de la Empresa</h3>

    <table class="form-table">
        <tr>
            <th><label for="empresa_nombre">Nombre de la Empresa</label></th>
            <td>
                <input type="text" name="empresa_nombre" value="<?php echo esc_attr(get_the_author_meta('empresa_nombre', $user->ID)); ?>" class="regular-text" />
            </td>
        </tr>
        <tr>
            <th><label for="empresa_pais">País de la Empresa</label></th>
            <td>
                <?php 
                $empresa_pais = esc_attr(get_the_author_meta('empresa_pais', $user->ID)); 
                ?>
                <select name="empresa_pais" id="empresa_pais" >
                    <option value="">País</option>
                    <?php foreach( $countries as $item ): ?>
                        <option value="<?= esc_attr($item['name']) ?>" <?= ($empresa_pais == $item['name']) ? 'selected="selected"' : '' ?>>
                            <?= esc_html($item['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </td>
        </tr>
        <tr>
            <th><label for="empresa_description">Descripción</label></th>
            <td>
                <?php
                $content = get_the_author_meta('empresa_description', $user->ID);
                $editor_id = 'empresa_description';
                $settings = [
                    'textarea_name' => 'empresa_description',
                    'textarea_rows' => 10,
            'teeny' => true, // editor más simple
            'media_buttons' => false, // sin botones para subir medios
            'quicktags' => true // permite HTML básico
        ];
        wp_editor($content, $editor_id, $settings);
        ?>
    </td>
</tr>

<tr>
    <th><label for="empresa_video_youtube">Video de YouTube</label></th>
    <td>
        <input type="text" name="empresa_video_youtube" value="<?php echo esc_attr(get_the_author_meta('empresa_video_youtube', $user->ID)); ?>" class="regular-text" />
    </td>
</tr>
<tr>
    <th><label for="empresa_twitter">Twitter</label></th>
    <td>
        <input type="text" name="empresa_twitter" value="<?php echo esc_attr(get_the_author_meta('empresa_twitter', $user->ID)); ?>" class="regular-text" />
    </td>
</tr>
<tr>
    <th><label for="empresa_facebook">Facebook</label></th>
    <td>
        <input type="text" name="empresa_facebook" value="<?php echo esc_attr(get_the_author_meta('empresa_facebook', $user->ID)); ?>" class="regular-text" />
    </td>
</tr>
<tr>
    <th><label for="empresa_instagram">Instagram</label></th>
    <td>
        <input type="text" name="empresa_instagram" value="<?php echo esc_attr(get_the_author_meta('empresa_instagram', $user->ID)); ?>" class="regular-text" />
    </td>
</tr>
<tr>
    <th><label for="empresa_icono">Ícono Empresa</label></th>
    <td>
        <input type="file" name="empresa_icono" /><br>
        <?php if ($icono): ?>
            <img src="<?php echo esc_url($icono); ?>" alt="Icono actual" style="max-width:100px;">
        <?php endif; ?>
    </td>
</tr>
<tr>
    <th><label for="empresa_fondo">Fondo Empresa</label></th>
    <td>
        <input type="file" name="empresa_fondo" /><br>
        <?php if ($fondo): ?>
            <img src="<?php echo esc_url($fondo); ?>" alt="Fondo actual" style="max-width:100px;">
        <?php endif; ?>
    </td>
</tr>
</table>
<?php
}

add_action('show_user_profile', 'custom_user_profile_fields');
add_action('edit_user_profile', 'custom_user_profile_fields');

add_action('user_edit_form_tag', 'formulario_usuario_con_uploads');
function formulario_usuario_con_uploads() {
    echo ' enctype="multipart/form-data"';
}

function save_custom_user_profile_fields($user_id) {
    if (!current_user_can('edit_user', $user_id)) return;

    // Guardar campos de texto
    update_user_meta($user_id, 'empresa_nombre', sanitize_text_field($_POST['empresa_nombre']));
    update_user_meta($user_id, 'empresa_pais', sanitize_text_field($_POST['empresa_pais']));
    update_user_meta($user_id, 'empresa_description', wp_kses_post($_POST['empresa_description']));

    update_user_meta($user_id, 'empresa_video_youtube', esc_url_raw($_POST['empresa_video_youtube']));
    update_user_meta($user_id, 'empresa_twitter', sanitize_text_field($_POST['empresa_twitter']));
    update_user_meta($user_id, 'empresa_facebook', sanitize_text_field($_POST['empresa_facebook']));
    update_user_meta($user_id, 'empresa_instagram', sanitize_text_field($_POST['empresa_instagram']));

    // Manejar subida de ícono
    if (!empty($_FILES['empresa_icono']['name'])) {
        $uploaded = media_handle_upload('empresa_icono', 0);
        if (!is_wp_error($uploaded)) {
            $url = wp_get_attachment_url($uploaded);
            update_user_meta($user_id, 'empresa_icono', esc_url_raw($url));
        }
    }

    // Manejar subida de fondo
    if (!empty($_FILES['empresa_fondo']['name'])) {
        $uploaded = media_handle_upload('empresa_fondo', 0);
        if (!is_wp_error($uploaded)) {
            $url = wp_get_attachment_url($uploaded);
            update_user_meta($user_id, 'empresa_fondo', esc_url_raw($url));
        }
    }
}

add_action('personal_options_update', 'save_custom_user_profile_fields');
add_action('edit_user_profile_update', 'save_custom_user_profile_fields');



// Mostrar datos personalizados en el perfil del usuario en el admin
function mostrar_datos_personalizados_usuario($user) {
    if (!current_user_can('edit_user', $user->ID)) {
        return;
    }

    if ( ! in_array('subscriber', (array) $user->roles) ) {
        return; // Si el usuario no tiene el rol suscriptor, no mostramos nada
    }

    $campos = [
        'first_name', 'last_name', 'document_type', 'document_number', 'gender', 'born_date',
        'country', 'state', 'county', 'city',
        'country_r', 'state_r', 'county_r', 'city_r',
        'address', 'mobile', 'has_colegiatura', 'colegiatura_school', 'colegiatura_number',
        'linkedin', 'twitter', 'facebook', 'youtube', 'tiktok',
        'profile', 'availability', 'sector_interest', 'type_day', 'min_salary', 'max_salary', 'agree_policy',
    ];

    $campos_array_serializados = [
       'studies', 'complementary_studies', 'language', 'experience', 'skills'
   ];

   echo '<h2>Datos de Perfil Usuario</h2>';
   echo '<h2>Avatar</h2>';
   $avatar_id = get_user_meta($user->ID, 'avatar', true);

   if ($avatar_id) {
        $avatar_url =  $avatar_id; // o 'medium', 'full', etc.
        echo '<img src="' . esc_url($avatar_url) . '" alt="Avatar del usuario" style="width: 80px; height: auto;">';
    } else {
        echo '<em>No hay avatar personalizado.</em>';
    }

    echo '<table class="form-table">';

    // Mostrar campos simples
    foreach ($campos as $campo) {
        $valor = get_user_meta($user->ID, $campo, true);
        echo '<tr>';
        echo '<th><label>' . esc_html(ucwords(str_replace('_', ' ', $campo))) . '</label></th>';
        echo '<td>' . (!empty($valor) ? esc_html($valor) : '<em>No especificado</em>') . '</td>';
        echo '</tr>';
    }

    // Mostrar las keywords como key1, key2, key3
    $keys = [];
    for ($i = 1; $i <= 3; $i++) {
        $key = get_user_meta($user->ID, 'key' . $i, true);
        if (!empty($key)) {
            $keys[] = $key;
        }
    }

    // Mostrar enlace al CV
    $cv_url = get_user_meta($user->ID, 'cv_url', true);

    echo '<tr>';
    echo '<th><label>CV</label></th>';
    echo '<td>';

    if (!empty($cv_url)) {
        echo '<a href="' . esc_url($cv_url) . '" target="_blank">Ver CV</a>';
    } else {
        echo '<em>No se ha subido ningún CV.</em>';
    }

    echo '</td>';
    echo '</tr>';


    echo '<tr>';
    echo '<th><label>Keywords</label></th>';
    echo '<td>';

    if (!empty($keys)) {
        echo '<ul style="list-style-type: disc; padding-left: 20px;">';
        foreach ($keys as $kw) {
            echo '<li>' . esc_html($kw) . '</li>';
        }
        echo '</ul>';
    } else {
        echo '<em>No especificado</em>';
    }

    echo '</td>';
    echo '</tr>';


    // Mostrar arrays serializados
    foreach ($campos_array_serializados as $campo) {
        $valores_raw = get_user_meta($user->ID, $campo, false);

        echo '<tr>';
        echo '<th><label>' . esc_html(ucwords(str_replace('_', ' ', $campo))) . '</label></th>';
        echo '<td>';

        if (!empty($valores_raw) && is_array($valores_raw)) {
            $valores = maybe_unserialize($valores_raw[0]);

            if (is_array($valores) && !empty($valores)) {
                echo '<ul style="list-style-type: disc; padding-left: 20px;">';

                foreach ($valores as $item) {
                    echo '<li style="margin-bottom: 10px;">';

                    if (is_array($item)) {
                        // Tomar el primer campo y valor para mostrar en el <li>
                        reset($item);
                        $primer_campo = key($item);
                        $primer_valor = current($item);

                        echo '<strong>' . esc_html(ucwords(str_replace('_', ' ', $primer_campo))) . ':</strong> ' . esc_html($primer_valor) . '<br>';

                        // Mostrar el resto de campos excepto el primero en lista anidada
                        echo '<ul style="list-style-type: circle; margin-left: 20px;">';

                        foreach ($item as $key => $value) {
                            if ($key === $primer_campo) continue; // omitimos el primero

                            echo '<li><strong>' . esc_html(ucwords(str_replace('_', ' ', $key))) . ':</strong> ' . esc_html($value) . '</li>';
                        }

                        echo '</ul>';
                    } else {
                        // Si no es array, solo mostrar el valor
                        echo esc_html($item);
                    }

                    echo '</li>';
                }

                echo '</ul>';
            } else {
                echo '<em>No especificado</em>';
            }
        } else {
            echo '<em>No especificado</em>';
        }

        echo '</td>';
        echo '</tr>';
    }

    echo '</table>';
}
add_action('show_user_profile', 'mostrar_datos_personalizados_usuario');
add_action('edit_user_profile', 'mostrar_datos_personalizados_usuario');


function mostrar_postulantes_admin() {
    if (!current_user_can('manage_options')) return;

    $search_term = isset($_GET['search']) ? sanitize_text_field($_GET['search']) : '';
    $fecha_inicio = isset($_GET['fecha_inicio']) ? sanitize_text_field($_GET['fecha_inicio']) : '';
    $fecha_fin = isset($_GET['fecha_fin']) ? sanitize_text_field($_GET['fecha_fin']) : '';

    echo '<div class="wrap"><h1>Postulantes</h1>';

    // Formulario de filtros
   

    echo '<a href="' . admin_url('admin-post.php?action=descargar_postulantes_xlsx') . '" class="button button-primary" style="margin-bottom: 15px;">Descargar todos los postulantes (XLSX)</a>';
    echo '<div style="display:flex; flex-direction: row; gap:15px; margin-bottom: 15px;">';
    echo '<button type="submit" form="form_seleccionados" class="button button-primary">Descargar seleccionados XLS</button>';
    echo '<button type="submit" form="form_seleccionados_pdf" class="button button-secondary">Descargar seleccionados PDF</button>';
    echo '</div>';

     echo '<form method="get" style="margin-bottom: 20px;">';
    echo '<input type="hidden" name="page" value="descargar_postulantes" />';
    echo '<input type="text" name="search" placeholder="Buscar por nombre o email" value="' . esc_attr($search_term) . '" style="margin-right: 10px;" />';
    echo '<input type="date" name="fecha_inicio" value="' . esc_attr($fecha_inicio) . '" style="margin-right: 10px;" />';
    echo '<input type="date" name="fecha_fin" value="' . esc_attr($fecha_fin) . '" style="margin-right: 10px;" />';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form>';

    echo '<form method="post" action="' . admin_url('admin-post.php') . '" id="form_seleccionados">';
    wp_nonce_field('descargar_seleccionados_action', 'descargar_seleccionados_nonce');

    echo '<table class="widefat fixed striped">';
    echo '<thead><tr>';
    echo '<th><input type="checkbox" id="select_all"></th>';
    echo '<th>Fecha de Registro</th><th>Nombre</th><th>Documento</th><th>Correo</th><th>Teléfono</th><th>Acciones</th>';

    echo '</tr></thead><tbody>';

    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $per_page = 20;
    $offset = ($paged - 1) * $per_page;

    $args = [
        'number' => $per_page,
        'offset' => $offset,
        'role__not_in' => ['Administrator'],
        'search' => "*{$search_term}*",
        'search_columns' => ['user_email', 'display_name']
    ];

    $user_query = new WP_User_Query($args);
    $total_users = $user_query->get_total();

    if (!empty($user_query->get_results())) {
        foreach ($user_query->get_results() as $user) {
            $first = get_user_meta($user->ID, 'first_name', true);
            $last = get_user_meta($user->ID, 'last_name', true);
            $doc = get_user_meta($user->ID, 'document_number', true);
            $phone = get_user_meta($user->ID, 'mobile', true);
            $tipo_documento = get_user_meta($user->ID, 'document_type', true);

            $fecha_registro = strtotime($user->user_registered);
            $fecha_mostrar = date('Y-m-d', $fecha_registro);

            if (!empty($fecha_inicio) && strtotime($fecha_inicio) > $fecha_registro) continue;
            if (!empty($fecha_fin) && strtotime($fecha_fin . ' 23:59:59') < $fecha_registro) continue;

            echo '<tr>';
            echo '<td><input type="checkbox" name="user_ids[]" value="' . esc_attr($user->ID) . '" class="select_user"></td>';
            echo '<td>' . esc_html($fecha_mostrar) . '</td>';

            echo '<td>' . esc_html($first . ' ' . $last) . '</td>';
            echo '<td>' . esc_html($tipo_documento . ' ' . $doc) . '</td>';
            echo '<td>' . esc_html($user->user_email) . '</td>';
            echo '<td>' . esc_html($phone) . '</td>';
            echo '<td>
                    <a href="' . admin_url('admin.php?page=ver_postulante&user_id=' . $user->ID) . '" class="button">Ver detalles</a>
                    <a href="' . admin_url('admin-post.php?action=descargar_usuario_xlsx&user_id=' . $user->ID) . '" class="button">Descargar XLSX</a>
                    <a href="' . admin_url('admin-post.php?action=descargar_cv_pdf&user_id=' . $user->ID) . '" class="button button-secondary">Descargar CV</a>
                </td>';
            echo '</tr>';
        }
    } else {
        echo '<tr><td colspan="6">No hay postulantes.</td></tr>';
    }

    echo '</tbody></table>';
    echo '<input type="hidden" name="action" value="descargar_seleccionados_xlsx">';
    echo '</form>';

    echo '<form method="post" action="' . admin_url('admin-post.php') . '" id="form_seleccionados_pdf">';
    wp_nonce_field('descargar_seleccionados_pdf_action', 'descargar_seleccionados_pdf_nonce');
    echo '<input type="hidden" name="action" value="descargar_seleccionados_pdf">';
    echo '</form>';

    $total_pages = ceil($total_users / $per_page);
    if ($total_pages > 1) {
        echo '<div class="tablenav"><div class="tablenav-pages">';
        for ($i = 1; $i <= $total_pages; $i++) {
            $url = add_query_arg('paged', $i);
            echo '<a class="button' . ($i == $paged ? ' button-primary' : '') . '" href="' . esc_url($url) . '">' . $i . '</a> ';
        }
        echo '</div></div>';
    }

    echo '</div>';
    ?>
    <script>
        document.getElementById('select_all').addEventListener('change', function () {
            document.querySelectorAll('.select_user').forEach(cb => cb.checked = this.checked);
        });

        document.getElementById('form_seleccionados_pdf').addEventListener('submit', function (e) {
            e.preventDefault();
            this.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());

            document.querySelectorAll('#form_seleccionados input[name="user_ids[]"]:checked').forEach(cb => {
                let input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = cb.value;
                this.appendChild(input);
            });

            if (this.querySelectorAll('input[name="user_ids[]"]').length === 0) {
                alert('Por favor selecciona al menos un usuario.');
                return;
            }

            this.submit();
        });

        document.getElementById('form_seleccionados').addEventListener('submit', function (e) {
            if (this.querySelectorAll('input[name="user_ids[]"]:checked').length === 0) {
                alert('Por favor selecciona al menos un usuario.');
                e.preventDefault();
            }
        });
    </script>
    <?php
}






use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use PhpOffice\PhpSpreadsheet\Cell\Coordinate;
use Dompdf\Dompdf;
use Dompdf\Options;

//EXPORT ID
add_action('admin_post_descargar_postulantes_por_id', 'exportar_postulantes_xlsx_porid');


function exportar_postulantes_xlsx_porid() {
    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos suficientes.');
    }

    global $wpdb;

    $post_id = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    $tabla_inscripciones = $wpdb->prefix . 'opt_inscripciones';

    // Obtener solo user_ids que hayan postulado a ese post_id
    $user_ids = [];
    if ($post_id) {
        $result = $wpdb->get_col($wpdb->prepare(
            "SELECT id_usuario FROM $tabla_inscripciones WHERE id_oportunidades = %d",
            $post_id
        ));
        $user_ids = array_map('intval', $result);
    }

    // Estructura del archivo
    $campos_simples = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'document_type' => 'Tipo de documento',
        'document_number' => 'Número de documento',
        'gender' => 'Género',
        'born_date' => 'Fecha de nacimiento',
        'country' => 'País',
        'state' => 'Departamento',
        'county' => 'Provincia',
        'city' => 'Ciudad',
        'country_r' => 'País de residencia',
        'state_r' => 'Departamento residencia',
        'county_r' => 'Provincia residencia',
        'city_r' => 'Ciudad residencia',
        'address' => 'Dirección',
        'mobile' => 'Celular',
        'has_colegiatura' => '¿Tiene colegiatura?',
        'colegiatura_school' => 'Institución colegiatura',
        'colegiatura_number' => 'Número colegiatura',
        'linkedin' => 'LinkedIn',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'key1' => 'Palabra clave 1',
        'key2' => 'Palabra clave 2',
        'key3' => 'Palabra clave 3',
        'profile' => 'Perfil',
        'studies' => 'Estudios',
        'complementary_studies' => 'Estudios complementarios',
        'language' => 'Idiomas',
        'experience' => 'Experiencia',
        'skills' => 'Habilidades',
        'sector_interest' => 'Sector de interés',
        'availability' => 'Disponibilidad',
        'type_day' => 'Tipo de jornada',
        'min_salary' => 'Salario mínimo',
        'max_salary' => 'Salario máximo',
        'cv_url' => 'CV Subida por el usuario (URL)',
    ];

    $campos_serializados_keys = ['studies', 'complementary_studies', 'language', 'experience', 'skills'];

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $col = 1;
    foreach ($campos_simples as $campo => $label) {
        $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($colLetter . '1', $label);
    }

    // Obtener usuarios
    $user_args = [
        'role__not_in' => ['Administrator'],
        'include' => $user_ids,
        'number' => -1,
    ];

    $users = get_users($user_args);
    $row = 2;

    foreach ($users as $user) {
        $col = 1;
        foreach ($campos_simples as $campo => $label) {
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($col++);

            if (in_array($campo, $campos_serializados_keys)) {
                $raw = get_user_meta($user->ID, $campo, false);
                $valor = '';

                if (!empty($raw)) {
                    $array = maybe_unserialize($raw[0]);
                    if (is_array($array)) {
                        $items = [];
                        foreach ($array as $item) {
                            if (is_array($item)) {
                                $subitems = [];
                                foreach ($item as $k => $v) {
                                    $subitems[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                                }
                                $items[] = implode(', ', $subitems);
                            } else {
                                $items[] = $item;
                            }
                        }
                        $valor = implode(" | ", $items);
                    }
                }

                $sheet->setCellValue($colLetter . $row, $valor);
            } else {
                $value = $campo === 'email' ? $user->user_email : get_user_meta($user->ID, $campo, true);
                $sheet->setCellValue($colLetter . $row, $value);
            }
        }

        $row++;
    }

    // Descargar XLSX
    $filename = 'postulantes_' . ($post_id ? 'post_' . $post_id : 'todos') . '_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header("Content-Disposition: attachment; filename=\"{$filename}\"");
    header('Cache-Control: max-age=0');

    $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}




// EXPORT TODO
add_action('admin_post_descargar_postulantes_xlsx', 'exportar_postulantes_xlsx');

function exportar_postulantes_xlsx() {
    if (!current_user_can('manage_options')) {
        wp_die('No tienes permisos suficientes.');
    }

    // Campos en orden deseado
    $campos_simples = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'document_type' => 'Tipo de documento',
        'document_number' => 'Número de documento',
        'gender' => 'Género',
        'born_date' => 'Fecha de nacimiento',
        'country' => 'País',
        // 'ubigeo' => 'Ubigeo',
        'state' => 'Departamento',
        'county' => 'Provincia',
        'city' => 'Ciudad',
        'country_r' => 'País de residencia',
        // 'ubigeo_r' => 'Ubigeo residencia',
        'state_r' => 'Departamento residencia',
        'county_r' => 'Provincia residencia',
        'city_r' => 'Ciudad residencia',
        'address' => 'Dirección',
        'mobile' => 'Celular',
        'has_colegiatura' => '¿Tiene colegiatura?',
        'colegiatura_school' => 'Institución colegiatura',
        'colegiatura_number' => 'Número colegiatura',
        'linkedin' => 'LinkedIn',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'key1' => 'Palabra clave 1',
        'key2' => 'Palabra clave 2',
        'key3' => 'Palabra clave 3',
        'profile' => 'Perfil',

        'studies' => 'Estudios',
        'complementary_studies' => 'Estudios complementarios',
        'language' => 'Idiomas',
        'experience' => 'Experiencia',
        'skills' => 'Habilidades',

        'sector_interest' => 'Sector de interés',
        'availability' => 'Disponibilidad',
        'type_day' => 'Tipo de jornada',
        'min_salary' => 'Salario mínimo',
        'max_salary' => 'Salario máximo',
        'cv_url' => 'CV Subida por el usuario (URL)',
    ];

    // Solo las claves de los serializados
    $campos_serializados_keys = [
        'studies',
        'complementary_studies',
        'language',
        'experience',
        'skills',
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $col = 1;
    foreach ($campos_simples as $campo => $label) {
        $colLetter = Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($colLetter . '1', $label);
    }

    // Obtener datos
    $users = get_users(['role__not_in' => ['Administrator']]);
    $row = 2;

    foreach ($users as $user) {
        $col = 1;
        foreach ($campos_simples as $campo => $label) {
            $colLetter = Coordinate::stringFromColumnIndex($col++);

            if (in_array($campo, $campos_serializados_keys)) {
                $raw = get_user_meta($user->ID, $campo, false);
                $valor = '';

                if (!empty($raw)) {
                    $array = maybe_unserialize($raw[0]);
                    if (is_array($array)) {
                        $items = [];
                        foreach ($array as $item) {
                            if (is_array($item)) {
                                $subitems = [];
                                foreach ($item as $k => $v) {
                                    $subitems[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                                }
                                $items[] = implode(', ', $subitems);
                            } else {
                                $items[] = $item;
                            }
                        }
                        $valor = implode(" | ", $items);
                    }
                }
                $sheet->setCellValue($colLetter . $row, $valor);
            } else {
                $value = $campo === 'email' ? $user->user_email : get_user_meta($user->ID, $campo, true);
                $sheet->setCellValue($colLetter . $row, $value);
            }
        }
        $row++;
    }

    // Descargar XLSX
    $filename = 'postulantes_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}



add_action('admin_post_descargar_usuario_xlsx', 'descargar_usuario_xlsx');

function descargar_usuario_xlsx() {
    if (!current_user_can('manage_options') || !isset($_GET['user_id'])) {
        wp_die('Acceso no permitido');
    }

    $user_id = intval($_GET['user_id']);
    $user = get_user_by('ID', $user_id);
    if (!$user) wp_die('Usuario no encontrado');

    // Mismos campos que en el global
    $campos_simples = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'document_type' => 'Tipo de documento',
        'document_number' => 'Número de documento',
        'gender' => 'Género',
        'born_date' => 'Fecha de nacimiento',
        'country' => 'País',
        'state' => 'Departamento',
        'county' => 'Provincia',
        'city' => 'Ciudad',
        'country_r' => 'País de residencia',
        'state_r' => 'Departamento residencia',
        'county_r' => 'Provincia residencia',
        'city_r' => 'Ciudad residencia',
        'address' => 'Dirección',
        'mobile' => 'Celular',
        'has_colegiatura' => '¿Tiene colegiatura?',
        'colegiatura_school' => 'Institución colegiatura',
        'colegiatura_number' => 'Número colegiatura',
        'linkedin' => 'LinkedIn',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'key1' => 'Palabra clave 1',
        'key2' => 'Palabra clave 2',
        'key3' => 'Palabra clave 3',
        'profile' => 'Perfil',
        'studies' => 'Estudios',
        'complementary_studies' => 'Estudios complementarios',
        'language' => 'Idiomas',
        'experience' => 'Experiencia',
        'skills' => 'Habilidades',
        'sector_interest' => 'Sector de interés',
        'availability' => 'Disponibilidad',
        'type_day' => 'Tipo de jornada',
        'min_salary' => 'Salario mínimo',
        'max_salary' => 'Salario máximo',
        'cv_url' => 'CV Subida por el usuario (URL)',
    ];

    $campos_serializados_keys = ['studies', 'complementary_studies', 'language', 'experience', 'skills'];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Postulante');

    // Encabezados
    $col = 1;
    foreach ($campos_simples as $campo => $label) {
        $colLetter = Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($colLetter . '1', $label);
    }


    // Datos
    $col = 1;
    foreach ($campos_simples as $campo => $label) {
        $valor = '';

        if (in_array($campo, $campos_serializados_keys)) {
            $raw = get_user_meta($user_id, $campo, false);
            if (!empty($raw)) {
                $array = maybe_unserialize($raw[0]);
                if (is_array($array)) {
                    $items = [];
                    foreach ($array as $item) {
                        $items[] = is_array($item)
                        ? implode(', ', array_map(
                            fn($k, $v) => ucwords(str_replace('_', ' ', $k)) . ': ' . $v,
                            array_keys($item), array_values($item)
                        ))
                        : $item;
                    }
                    $valor = implode(' | ', $items);
                }
            }
        } else {
            $valor = get_user_meta($user_id, $campo, true);
        }

        $colLetter = Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($colLetter . '2', $valor);
    }

    // Descargar
    $filename = 'postulante_' . $user_id . '_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}


// add_action('admin_post_descargar_cv_pdf', 'descargar_cv_usuario_pdf');

// function descargar_cv_usuario_pdf() {
//     if (!current_user_can('manage_options') || !isset($_GET['user_id'])) {
//         wp_die('Acceso no permitido');
//     }

//     $user_id = intval($_GET['user_id']);
//     $user = get_user_by('ID', $user_id);
//     if (!$user) {
//         wp_die('Usuario no encontrado');
//     }

//     // Carga datos del usuario igual que en tu plantilla
//     $current_member = [
//         'first_name'             => get_user_meta($user_id, 'first_name', true),
//         'last_name'              => get_user_meta($user_id, 'last_name', true),
//         'full_name'              => get_user_meta($user_id, 'first_name', true) . ' ' . get_user_meta($user_id, 'last_name', true),
//         'document_type'          => get_user_meta($user_id, 'document_type', true),
//         'document_number'        => get_user_meta($user_id, 'document_number', true),
//         'email'                  => get_userdata($user_id)->user_email,
//         'gender'                 => get_user_meta($user_id, 'gender', true),
//         'born_date'              => get_user_meta($user_id, 'born_date', true),
//         'country'                => get_user_meta($user_id, 'country', true),
//         'ubigeo'                 => get_user_meta($user_id, 'ubigeo', true),
//         'state'                  => get_user_meta($user_id, 'state', true),
//         'county'                 => get_user_meta($user_id, 'county', true),
//         'city'                   => get_user_meta($user_id, 'city', true),
//         'country_r'              => get_user_meta($user_id, 'country_r', true),
//         'ubigeo_r'               => get_user_meta($user_id, 'ubigeo_r', true),
//         'state_r'                => get_user_meta($user_id, 'state_r', true),
//         'county_r'               => get_user_meta($user_id, 'county_r', true),
//         'city_r'                 => get_user_meta($user_id, 'city_r', true),
//         'address'                => get_user_meta($user_id, 'address', true),
//         'mobile'                 => get_user_meta($user_id, 'mobile', true),
//         'has_colegiatura'        => get_user_meta($user_id, 'has_colegiatura', true),
//         'colegiatura_school'     => get_user_meta($user_id, 'colegiatura_school', true),
//         'colegiatura_number'     => get_user_meta($user_id, 'colegiatura_number', true),
//         'linkedin'               => get_user_meta($user_id, 'linkedin', true),
//         'twitter'                => get_user_meta($user_id, 'twitter', true),
//         'facebook'               => get_user_meta($user_id, 'facebook', true),
//         'youtube'                => get_user_meta($user_id, 'youtube', true),
//         'instagram'              => get_user_meta($user_id, 'instagram', true),
//         'tiktok'                 => get_user_meta($user_id, 'tiktok', true),
//         'profile'                => get_user_meta($user_id, 'profile', true),
//         'keywords'               => get_user_meta($user_id, 'keywords', true) ?: [],
//         'key1'                   => get_user_meta($user_id, 'key1', true),
//         'key2'                   => get_user_meta($user_id, 'key2', true),
//         'key3'                   => get_user_meta($user_id, 'key3', true),
//         'studies'                => get_user_meta($user_id, 'studies', true) ?: [],
//         'complementary_studies'  => get_user_meta($user_id, 'complementary_studies', true) ?: [],
//         'language'               => get_user_meta($user_id, 'language', true) ?: [],
//         'experience'             => get_user_meta($user_id, 'experience', true) ?: [],
//         'skills'                 => get_user_meta($user_id, 'skills', true) ?: [],
//         'availability'           => get_user_meta($user_id, 'availability', true),
//         'sector_interest'        => get_user_meta($user_id, 'sector_interest', true),
//         'type_day'               => get_user_meta($user_id, 'type_day', true),
//         'min_salary'             => get_user_meta($user_id, 'min_salary', true),
//         'max_salary'             => get_user_meta($user_id, 'max_salary', true),
//         'agree_policy'           => get_user_meta($user_id, 'agree_policy', true),
//         'avatar'                 => get_user_meta($user_id, 'avatar', true),
//     ];
//     $avatar = false;
//     if (filter_var($current_member['avatar'], FILTER_VALIDATE_URL) && @getimagesize($current_member['avatar']) !== false) {
//         $avatar = $current_member['avatar'];
//     }


//     ob_start();

//     // Incluye los archivos de la plantilla del CV
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/header.php';
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/avatar-personal.php';
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/experience.php';
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/studies.php';
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/complementary.php';
//     require plugin_dir_path(__FILE__) . 'templates/cv-parts/footer.php';

//     $output = ob_get_clean();

//     $options = new Options();
//     $options->set('isRemoteEnabled', true);
//     $dompdf = new Dompdf($options);
//     $dompdf->loadHtml($output);
//     $dompdf->setPaper('A4', 'portrait');
//     $dompdf->render();
//     $dompdf->stream('cv_usuario_' . $user_id . '.pdf', ['Attachment' => true]);
//     exit;
// }

add_action('admin_post_descargar_cv_pdf', 'descargar_cv_usuario_pdf');

function descargar_cv_usuario_pdf() {
    if (!current_user_can('manage_options') || !isset($_GET['user_id'])) {
        wp_die('Acceso no permitido');
    }

    $user_id = intval($_GET['user_id']);
    $user = get_user_by('ID', $user_id);
    if (!$user) {
        wp_die('Usuario no encontrado');
    }

    $cv_url = get_user_meta($user_id, 'cv_url', true);

    if (!$cv_url || !filter_var($cv_url, FILTER_VALIDATE_URL)) {
        wp_die('El usuario no tiene un CV válido.');
    }

    // Descarga el contenido del PDF desde la URL
    $cv_content = @file_get_contents($cv_url);
    if ($cv_content === false) {
        wp_die('No se pudo descargar el archivo CV.');
    }

    // Extrae un nombre de archivo desde la URL o crea uno
    $parsed_url = parse_url($cv_url);
    $path = isset($parsed_url['path']) ? basename($parsed_url['path']) : 'cv_usuario_' . $user_id . '.pdf';
    $filename = 'cv_usuario_' . $user_id . '_' . $path;

    // Enviar headers y archivo
    header('Content-Type: application/pdf');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Content-Length: ' . strlen($cv_content));
    header('Pragma: no-cache');
    header('Expires: 0');

    echo $cv_content;
    exit;
}



add_action('admin_menu', function () {
    add_submenu_page(
        null, // No aparece en menú
        'Ver Postulante',
        'Ver Postulante',
        'manage_options',
        'ver_postulante',
        'ver_postulante_callback'
    );
});
function ver_postulante_callback() {
    if (!current_user_can('manage_options') || !isset($_GET['user_id'])) {
        wp_die('Acceso no permitido');
    }

    $user_id = intval($_GET['user_id']);
    $user = get_user_by('ID', $user_id);
    if (!$user) wp_die('Usuario no encontrado');

    $campos_simples = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'document_type' => 'Tipo de documento',
        'document_number' => 'Número de documento',
        'gender' => 'Género',
        'born_date' => 'Fecha de nacimiento',
        'country' => 'País',
        'state' => 'Departamento',
        'county' => 'Provincia',
        'city' => 'Ciudad',
        'country_r' => 'País de residencia',
        'state_r' => 'Departamento residencia',
        'county_r' => 'Provincia residencia',
        'city_r' => 'Ciudad residencia',
        'address' => 'Dirección',
        'mobile' => 'Celular',
        'has_colegiatura' => '¿Tiene colegiatura?',
        'colegiatura_school' => 'Institución colegiatura',
        'colegiatura_number' => 'Número colegiatura',
        'linkedin' => 'LinkedIn',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'key1' => 'Palabra clave 1',
        'key2' => 'Palabra clave 2',
        'key3' => 'Palabra clave 3',
        'profile' => 'Perfil',
        'studies' => 'Estudios',
        'complementary_studies' => 'Estudios complementarios',
        'language' => 'Idiomas',
        'experience' => 'Experiencia',
        'skills' => 'Habilidades',
        'sector_interest' => 'Sector de interés',
        'availability' => 'Disponibilidad',
        'type_day' => 'Tipo de jornada',
        'min_salary' => 'Salario mínimo',
        'max_salary' => 'Salario máximo',
        'cv_url' => 'CV Subida por el usuario (URL)',
    ];

    $campos_serializados_keys = ['studies', 'complementary_studies', 'language', 'experience', 'skills'];

    echo '<div class="wrap"><h1>Detalles del Postulante</h1><table class="widefat fixed striped">';
    echo '<tbody>';

    foreach ($campos_simples as $campo => $label) {
        echo '<tr><th>' . esc_html($label) . '</th><td>';

        if ($campo === 'cv_url') {
            $url = get_user_meta($user_id, $campo, true);
            if ($url) {
                echo '<a href="' . esc_url($url) . '" target="_blank" rel="noopener noreferrer">' . esc_html($url) . '</a>';
            } else {
                echo '—';
            }
        } elseif (in_array($campo, $campos_serializados_keys)) {
            $raw = get_user_meta($user_id, $campo, false);
            if (!empty($raw)) {
                $array = maybe_unserialize($raw[0]);
                if (is_array($array)) {
                    foreach ($array as $item) {
                        if (is_array($item)) {
                            echo '<ul>';
                            foreach ($item as $k => $v) {
                                echo '<li><strong>' . esc_html(ucwords(str_replace('_', ' ', $k))) . ':</strong> ' . esc_html($v) . '</li>';
                            }
                            echo '</ul>';
                        } else {
                            echo '<p>' . esc_html($item) . '</p>';
                        }
                    }
                }
            } else {
                echo '<em>No registrado</em>';
            }
        } else {
            $valor = get_user_meta($user_id, $campo, true);
            echo esc_html($valor ? $valor : '—');
        }

        echo '</td></tr>';
    }


    echo '</tbody></table>';
    echo '<a href="' . admin_url('admin.php?page=descargar_postulantes') . '" class="button">Volver</a>';
    echo '</div>';
}



// SELECIONADOS

add_action('admin_post_descargar_seleccionados_xlsx', 'descargar_seleccionados_xlsx');
function descargar_seleccionados_xlsx() {
    if (!current_user_can('manage_options') || !isset($_POST['descargar_seleccionados_nonce']) || !wp_verify_nonce($_POST['descargar_seleccionados_nonce'], 'descargar_seleccionados_action')) {
        wp_die('No tienes permisos o nonce inválido.');
    }

    if (empty($_POST['user_ids']) || !is_array($_POST['user_ids'])) {
        wp_die('No se seleccionaron usuarios.');
    }

    $user_ids = array_map('intval', $_POST['user_ids']);

    // Usa la misma lógica que en exportar_postulantes_xlsx pero filtrando solo estos IDs
    // (Reutiliza tu función existente pero con filtro por IDs)

    // Campos como en exportar_postulantes_xlsx
    $campos_simples = [
        'first_name' => 'Nombre',
        'last_name' => 'Apellido',
        'document_type' => 'Tipo de documento',
        'document_number' => 'Número de documento',
        'gender' => 'Género',
        'born_date' => 'Fecha de nacimiento',
        'country' => 'País',
        // 'ubigeo' => 'Ubigeo',
        'state' => 'Departamento',
        'county' => 'Provincia',
        'city' => 'Ciudad',
        'country_r' => 'País de residencia',
        // 'ubigeo_r' => 'Ubigeo residencia',
        'state_r' => 'Departamento residencia',
        'county_r' => 'Provincia residencia',
        'city_r' => 'Ciudad residencia',
        'address' => 'Dirección',
        'mobile' => 'Celular',
        'has_colegiatura' => '¿Tiene colegiatura?',
        'colegiatura_school' => 'Institución colegiatura',
        'colegiatura_number' => 'Número colegiatura',
        'linkedin' => 'LinkedIn',
        'twitter' => 'Twitter',
        'facebook' => 'Facebook',
        'youtube' => 'YouTube',
        'tiktok' => 'TikTok',
        'key1' => 'Palabra clave 1',
        'key2' => 'Palabra clave 2',
        'key3' => 'Palabra clave 3',
        'profile' => 'Perfil',

        'studies' => 'Estudios',
        'complementary_studies' => 'Estudios complementarios',
        'language' => 'Idiomas',
        'experience' => 'Experiencia',
        'skills' => 'Habilidades',

        'sector_interest' => 'Sector de interés',
        'availability' => 'Disponibilidad',
        'type_day' => 'Tipo de jornada',
        'min_salary' => 'Salario mínimo',
        'max_salary' => 'Salario máximo',
        'cv_url' => 'CV Subida por el usuario (URL)',
    ];

    // Solo las claves de los serializados
    $campos_serializados_keys = [
        'studies',
        'complementary_studies',
        'language',
        'experience',
        'skills',
    ];

    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Encabezados
    $col = 1;
    foreach ($campos_simples as $campo => $label) {
        $colLetter = Coordinate::stringFromColumnIndex($col++);
        $sheet->setCellValue($colLetter . '1', $label);
    }

    $row = 2;
    foreach ($user_ids as $user_id) {
        $user = get_user_by('ID', $user_id);
        if (!$user) continue;

        $col = 1;
        foreach ($campos_simples as $campo => $label) {
            $colLetter = Coordinate::stringFromColumnIndex($col++);
            if (in_array($campo, $campos_serializados_keys)) {
                $raw = get_user_meta($user->ID, $campo, false);
                $valor = '';

                if (!empty($raw)) {
                    $array = maybe_unserialize($raw[0]);
                    if (is_array($array)) {
                        $items = [];
                        foreach ($array as $item) {
                            if (is_array($item)) {
                                $subitems = [];
                                foreach ($item as $k => $v) {
                                    $subitems[] = ucwords(str_replace('_', ' ', $k)) . ': ' . $v;
                                }
                                $items[] = implode(', ', $subitems);
                            } else {
                                $items[] = $item;
                            }
                        }
                        $valor = implode(" | ", $items);
                    }
                }
                $sheet->setCellValue($colLetter . $row, $valor);
            } else {
                $value = $campo === 'email' ? $user->user_email : get_user_meta($user->ID, $campo, true);
                $sheet->setCellValue($colLetter . $row, $value);
            }
        }
        $row++;
    }

    $filename = 'postulantes_seleccionados_' . date('Ymd_His') . '.xlsx';
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment; filename="' . $filename . '"');
    header('Cache-Control: max-age=0');

    $writer = new Xlsx($spreadsheet);
    $writer->save('php://output');
    exit;
}

// set_time_limit(300);  // 5 minutos
// ini_set('memory_limit', '512M');
// error_reporting(0);
// ini_set('display_errors', 0);





// add_action('admin_post_descargar_seleccionados_pdf', 'descargar_seleccionados_pdf');
// function descargar_seleccionados_pdf() {
//     if (!current_user_can('manage_options') 
//         || !isset($_POST['descargar_seleccionados_pdf_nonce']) 
//         || !wp_verify_nonce($_POST['descargar_seleccionados_pdf_nonce'], 'descargar_seleccionados_pdf_action')) {
//         wp_die('No tienes permisos o nonce inválido.');
//     }

//     if (empty($_POST['user_ids']) || !is_array($_POST['user_ids'])) {
//         wp_die('No se seleccionaron usuarios.');
//     }

//     $user_ids = array_map('intval', $_POST['user_ids']);

//     set_time_limit(300);
//     ini_set('memory_limit', '512M');

//     $zip = new ZipArchive();
//     $tmp_file = tempnam(sys_get_temp_dir(), 'zip');

//     if ($zip->open($tmp_file, ZipArchive::OVERWRITE) !== TRUE) {
//         wp_die('No se pudo crear el ZIP en disco.');
//     }

//     foreach ($user_ids as $user_id) {
//         $user = get_user_by('ID', $user_id);
//         if (!$user) continue;

//            $current_member = [
//             'first_name'             => get_user_meta($user, 'first_name', true),
//             'last_name'              => get_user_meta($user, 'last_name', true),
//             'full_name'              => get_user_meta($user, 'first_name', true) . ' ' . get_user_meta($user, 'last_name', true),
//             'document_type'          => get_user_meta($user, 'document_type', true),
//             'document_number'        => get_user_meta($user, 'document_number', true),
//             'email'                  => get_userdata($user)->user_email,
//             'gender'                 => get_user_meta($user, 'gender', true),
//             'born_date'              => get_user_meta($user, 'born_date', true),
//             'country'                => get_user_meta($user, 'country', true),
//             'ubigeo'                 => get_user_meta($user, 'ubigeo', true),
//             'state'                  => get_user_meta($user, 'state', true),
//             'county'                 => get_user_meta($user, 'county', true),
//             'city'                   => get_user_meta($user, 'city', true),
//             'country_r'              => get_user_meta($user, 'country_r', true),
//             'ubigeo_r'               => get_user_meta($user, 'ubigeo_r', true),
//             'state_r'                => get_user_meta($user, 'state_r', true),
//             'county_r'               => get_user_meta($user, 'county_r', true),
//             'city_r'                 => get_user_meta($user, 'city_r', true),
//             'address'                => get_user_meta($user, 'address', true),
//             'mobile'                 => get_user_meta($user, 'mobile', true),
//             'has_colegiatura'        => get_user_meta($user, 'has_colegiatura', true),
//             'colegiatura_school'     => get_user_meta($user, 'colegiatura_school', true),
//             'colegiatura_number'     => get_user_meta($user, 'colegiatura_number', true),
//             'linkedin'               => get_user_meta($user, 'linkedin', true),
//             'twitter'                => get_user_meta($user, 'twitter', true),
//             'facebook'               => get_user_meta($user, 'facebook', true),
//             'youtube'                => get_user_meta($user, 'youtube', true),
//             'instagram'              => get_user_meta($user, 'instagram', true),
//             'tiktok'                 => get_user_meta($user, 'tiktok', true),
//             'profile'                => get_user_meta($user, 'profile', true),
//             'keywords'               => get_user_meta($user, 'keywords', true) ?: [],
//             'key1'                   => get_user_meta($user, 'key1', true),
//             'key2'                   => get_user_meta($user, 'key2', true),
//             'key3'                   => get_user_meta($user, 'key3', true),
//             'studies'                => get_user_meta($user, 'studies', true) ?: [],
//             'complementary_studies'  => get_user_meta($user, 'complementary_studies', true) ?: [],
//             'language'               => get_user_meta($user, 'language', true) ?: [],
//             'experience'             => get_user_meta($user, 'experience', true) ?: [],
//             'skills'                 => get_user_meta($user, 'skills', true) ?: [],
//             'availability'           => get_user_meta($user, 'availability', true),
//             'sector_interest'        => get_user_meta($user, 'sector_interest', true),
//             'type_day'               => get_user_meta($user, 'type_day', true),
//             'min_salary'             => get_user_meta($user, 'min_salary', true),
//             'max_salary'             => get_user_meta($user, 'max_salary', true),
//             'agree_policy'           => get_user_meta($user, 'agree_policy', true),
//             'avatar'                 => get_user_meta($user, 'avatar', true),
//             'cv_url'  => get_user_meta($user, 'cv_url', true),
//         ];

//         $avatar = false;
//         if (filter_var($current_member['avatar'], FILTER_VALIDATE_URL) && @getimagesize($current_member['avatar']) !== false) {
//             $avatar = $current_member['avatar'];
//         }

//         ob_start();

//         // Puedes mantener estos require con tus templates de pdf
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/header.php';
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/avatar-personal.php';
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/experience.php';
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/studies.php';
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/complementary.php';
//         require plugin_dir_path(__FILE__) . 'templates/cv-parts/footer.php';

//         $html = ob_get_clean();

//         try {
//             $dompdf = new Dompdf();
//             $dompdf->loadHtml($html);
//             $dompdf->setPaper('A4', 'portrait');
//             $dompdf->render();

//             $pdf_content = $dompdf->output();
//             $pdf_name = 'CV_user_' . $user_id . '.pdf';

//             $zip->addFromString($pdf_name, $pdf_content);

//         } catch (Throwable $e) {
//             error_log('Error generando PDF para usuario ' . $user_id . ': ' . $e->getMessage());
//             // Opcional: continuar sin interrumpir
//             continue;
//         }
//     }

//     $zip->close();

//     header('Content-Type: application/zip');
//     header('Content-Disposition: attachment; filename="CVs_seleccionados_' . date('Ymd_His') . '.zip"');
//     header('Content-Length: ' . filesize($tmp_file));
//     header('Pragma: no-cache');
//     header('Expires: 0');

//     readfile($tmp_file);
//     unlink($tmp_file);
//     exit;
// }

add_action('admin_post_descargar_seleccionados_pdf', 'descargar_seleccionados_pdf');
function descargar_seleccionados_pdf() {
    if (!current_user_can('manage_options') 
        || !isset($_POST['descargar_seleccionados_pdf_nonce']) 
        || !wp_verify_nonce($_POST['descargar_seleccionados_pdf_nonce'], 'descargar_seleccionados_pdf_action')) {
        wp_die('No tienes permisos o nonce inválido.');
}

if (empty($_POST['user_ids']) || !is_array($_POST['user_ids'])) {
    wp_die('No se seleccionaron usuarios.');
}

$user_ids = array_map('intval', $_POST['user_ids']);

set_time_limit(300);
ini_set('memory_limit', '512M');

$zip = new ZipArchive();
$tmp_file = tempnam(sys_get_temp_dir(), 'zip');

if ($zip->open($tmp_file, ZipArchive::OVERWRITE) !== TRUE) {
    wp_die('No se pudo crear el ZIP en disco.');
}

foreach ($user_ids as $user_id) {
    $cv_url = get_user_meta($user_id, 'cv_url', true);

    if (!$cv_url || !filter_var($cv_url, FILTER_VALIDATE_URL)) {
        continue;
    }

    $cv_content = @file_get_contents($cv_url);
    if ($cv_content === false) {
        error_log("No se pudo obtener el PDF del usuario $user_id desde $cv_url");
        continue;
    }

        // Intenta obtener el nombre del archivo desde la URL
    $parsed_url = parse_url($cv_url);
    $path = isset($parsed_url['path']) ? basename($parsed_url['path']) : 'cv_user_' . $user_id . '.pdf';

        // Asegura un nombre único por usuario
    $filename = 'CV_user_' . $user_id . '_' . $path;

    $zip->addFromString($filename, $cv_content);
}

$zip->close();

header('Content-Type: application/zip');
header('Content-Disposition: attachment; filename="CVs_seleccionados_' . date('Ymd_His') . '.zip"');
header('Content-Length: ' . filesize($tmp_file));
header('Pragma: no-cache');
header('Expires: 0');

readfile($tmp_file);
unlink($tmp_file);
exit;
}


// add_action('admin_menu', 'menu_postulantes_custom');

// function menu_postulantes_custom() {
//     add_menu_page(
//         'Postulantes',
//         'Descargar Postulantes',
//         'manage_options',
//         'descargar_postulantes',
//         'mostrar_postulantes_admin',
//         'dashicons-download',
//         25
//     );
// }

// add_action('admin_menu', 'registrar_dashboard_oportunidades');

// function registrar_dashboard_oportunidades() {
//     add_menu_page(
//         'Dashboard Oportunidades',
//         'Dashboard Oportunidades',
//         'manage_options',
//         'dashboard_oportunidades',
//         'mostrar_dashboard_oportunidades',
//         'dashicons-chart-line',
//         6
//     );
// }

// // VER POSTULANTES

// add_action('admin_menu', 'agregar_menu_postulaciones');

// function agregar_menu_postulaciones() {
//     add_menu_page(
//         'Ver postulaciones',
//         'Ver postulaciones',
//         'manage_options',
//         'ver_postulaciones',
//         'mostrar_lista_postulaciones',
//         'dashicons-id',
//         6
//     );

//     add_submenu_page(
//         null, // no se muestra en el menú lateral
//         'Vista resumen postulante',
//         'Vista resumen postulante',
//         'manage_options',
//         'ver_postulacion_detalle',
//         'mostrar_detalle_postulacion'
//     );
// }
add_action('admin_menu', 'menu_dashboard_postulaciones');

function menu_dashboard_postulaciones() {
    // Menú principal
    add_menu_page(
        'Dashboard Postulaciones',          // Título de la página
        'Dashboard Postulaciones',          // Título del menú
        'manage_options',                   // Capacidad
        'dashboard_oportunidades',          // Slug principal (este carga por defecto)
        'mostrar_dashboard_oportunidades',  // Función callback
        'dashicons-chart-line',             // Icono
        6                                   // Posición en el menú
    );

    // Submenú: Dashboard (se repite porque WordPress siempre agrega automáticamente uno)
    add_submenu_page(
        'dashboard_oportunidades',
        'Dashboard de Oportunidades',
        'Dashboard de Oportunidades',
        'manage_options',
        'dashboard_oportunidades',
        'mostrar_dashboard_oportunidades'
    );

    // Submenú: Ver postulaciones
    add_submenu_page(
        // 'dashboard_oportunidades',
        null,
        'Ver postulaciones',
        'Ver postulaciones',
        'manage_options',
        'ver_postulaciones',
        'mostrar_lista_postulaciones'
    );

    
    // Submenú: Lista de Oportunidades (abre la vista para elegir un post)
    add_submenu_page(
        'dashboard_oportunidades',
        'Lista de Oportunidades',
        'Lista de Oportunidades',
        'manage_options',
        'lista_oportunidades',
        'mostrar_lista_oportunidades_admin'
    );


    // Submenú: Descargar postulantes
    add_submenu_page(
        'dashboard_oportunidades',
        'Descargar postulantes',
        'Descargar postulantes',
        'manage_options',
        'descargar_postulantes',
        'mostrar_postulantes_admin'
    );

    // Submenú oculto: Detalle de postulante (no aparece en el menú lateral)
    add_submenu_page(
        null,
        'Vista resumen postulante',
        'Vista resumen postulante',
        'manage_options',
        'ver_postulacion_detalle',
        'mostrar_detalle_postulacion'
    );

}

function mostrar_lista_oportunidades_admin() {
    $por_pagina = 30;
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;

    // Filtros
    $titulo_filtro = isset($_GET['titulo']) ? sanitize_text_field($_GET['titulo']) : '';
    $desde = isset($_GET['desde']) ? sanitize_text_field($_GET['desde']) : '';
    $hasta = isset($_GET['hasta']) ? sanitize_text_field($_GET['hasta']) : '';

    // Construir argumentos del query
    $date_query = [];

    if (!empty($desde)) {
        $date_query[] = array(
            'after' => $desde,
            'inclusive' => true,
        );
    }

    if (!empty($hasta)) {
        $date_query[] = array(
            'before' => $hasta,
            'inclusive' => true,
        );
    }

    $args = array(
        'post_type'      => 'oportunidad',
        'posts_per_page' => $por_pagina,
        'post_status'    => 'publish',
        'orderby'        => 'date',
        'order'          => 'DESC',
        'paged'          => $paged,
        's'              => $titulo_filtro,
    );

    if (!empty($date_query)) {
        $args['date_query'] = $date_query;
    }

    $query = new WP_Query($args);

    echo '<div class="wrap"><h1>Oportunidades</h1>';

    // Filtros de búsqueda
    echo '<form method="get" style="margin-bottom: 20px;">';
    echo '<input type="hidden" name="page" value="lista_oportunidades" />';
    echo '<input type="text" name="titulo" placeholder="Buscar por título" value="' . esc_attr($titulo_filtro) . '" style="margin-right: 10px;" />';
    echo '<input type="date" name="desde" value="' . esc_attr($desde) . '" style="margin-right: 10px;" />';
    echo '<input type="date" name="hasta" value="' . esc_attr($hasta) . '" style="margin-right: 10px;" />';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form>';

    if ($query->have_posts()) {
        echo '<table class="wp-list-table widefat fixed striped">';
        echo '<thead><tr><th>Título</th><th>Fecha de creación</th><th>Acción</th></tr></thead><tbody>';

        while ($query->have_posts()) {
            $query->the_post();
            $post_id = get_the_ID();
            $link = admin_url('admin.php?page=ver_postulaciones&post_id=' . $post_id);
            $fecha = get_the_date('Y-m-d', $post_id);
            $titulo = get_the_title();

            echo "<tr>
                <td><a href='" . esc_url($link) . "'>" . esc_html($titulo) . "</a></td>
                <td>{$fecha}</td>
                <td><a href='" . esc_url($link) . "' class='button'>Ver postulantes</a></td>
            </tr>";
        }

        echo '</tbody></table>';

        // Paginación
        $total_paginas = $query->max_num_pages;
        if ($total_paginas > 1) {
            echo '<div class="tablenav"><div class="tablenav-pages" style="margin-top: 20px;">';

            for ($i = 1; $i <= $total_paginas; $i++) {
                $url = add_query_arg(array(
                    'paged' => $i,
                    'titulo' => $titulo_filtro,
                    'desde' => $desde,
                    'hasta' => $hasta,
                    'page' => 'lista_oportunidades',
                ));
                $class = ($i == $paged) ? ' class="current-page"' : '';
                echo "<a{$class} href='" . esc_url($url) . "' style='margin-right: 8px; font-size: 16px;'>{$i}</a>";
            }

            echo '</div></div>';
        }

        wp_reset_postdata();
    } else {
        echo '<p>No se encontraron oportunidades.</p>';
    }

    echo '</div>';
}


function mostrar_lista_postulaciones() {
    global $wpdb;

    if (!current_user_can('manage_options')) return;

    $post_id_filtrado = isset($_GET['post_id']) ? intval($_GET['post_id']) : 0;
    $estado_filtrado = isset($_GET['estado']) ? sanitize_text_field($_GET['estado']) : '';
    $nombre_filtro = isset($_GET['nombre']) ? sanitize_text_field($_GET['nombre']) : '';
    $desde = isset($_GET['desde']) ? sanitize_text_field($_GET['desde']) : '';
    $hasta = isset($_GET['hasta']) ? sanitize_text_field($_GET['hasta']) : '';
    $paged = isset($_GET['paged']) ? max(1, intval($_GET['paged'])) : 1;
    $por_pagina = 30;
    $offset = ($paged - 1) * $por_pagina;

    $tabla = $wpdb->prefix . 'opt_inscripciones';

    // Query base
    $query = "
        SELECT i.*, u.user_email, u.ID as user_id, u.display_name, p.post_title, p.ID as post_id
        FROM {$tabla} i
        INNER JOIN {$wpdb->users} u ON u.ID = i.id_usuario
        INNER JOIN {$wpdb->posts} p ON p.ID = i.id_oportunidades
        WHERE EXISTS (
            SELECT 1 FROM {$wpdb->usermeta}
            WHERE user_id = u.ID AND meta_key = '{$wpdb->prefix}capabilities' AND meta_value LIKE '%subscriber%'
        )
    ";

    if ($post_id_filtrado) {
        $query .= $wpdb->prepare(" AND p.ID = %d", $post_id_filtrado);
    }

    if (!empty($nombre_filtro)) {
        $like = '%' . $wpdb->esc_like($nombre_filtro) . '%';
        $query .= $wpdb->prepare(" AND (u.display_name LIKE %s OR u.user_email LIKE %s)", $like, $like);
    }

    if (!empty($desde)) {
        $query .= $wpdb->prepare(" AND i.fecha >= %s", $desde . ' 00:00:00');
    }

    if (!empty($hasta)) {
        $query .= $wpdb->prepare(" AND i.fecha <= %s", $hasta . ' 23:59:59');
    }

    $query .= " ORDER BY i.fecha DESC";
    $resultados = $wpdb->get_results($query);

    // Filtrar por estado (PHP)
    $filtrados = [];
    foreach ($resultados as $fila) {
        $estado = get_user_meta($fila->user_id, "estado_postulacion_{$fila->post_id}", true);
        if (empty($estado)) $estado = 'Pendiente de revisión';

        if (!empty($estado_filtrado) && $estado !== $estado_filtrado) continue;

        $fila->estado = $estado;
        $filtrados[] = $fila;
    }

    $total = count($filtrados);
    $total_paginas = ceil($total / $por_pagina);
    $paginados = array_slice($filtrados, $offset, $por_pagina);

    // Cabecera
    echo '<div class="wrap">';
    if ($post_id_filtrado) {
        echo '<h2>Postulaciones para: <strong>' . esc_html(get_the_title($post_id_filtrado)) . '</strong></h2><Br>';
    }


    // Botones descarga masiva
    echo '<a href="' . admin_url('admin-post.php?action=descargar_postulantes_por_id&post_id=' . $post_id_filtrado) . '" class="button button-primary" style="margin-bottom: 15px;">Descargar todos los postulantes (XLSX)</a>';
    echo '<div style="display:flex; gap:15px; margin-bottom:15px;">';
    echo '<button type="submit" form="form_seleccionados" class="button button-primary">Descargar seleccionados XLS</button>';
    echo '<button type="submit" form="form_seleccionados_pdf" class="button button-secondary">Descargar seleccionados PDF</button>';
    echo '</div>';

    // Filtros
    echo '<form method="get" style="margin-bottom:20px;">';
    echo '<input type="hidden" name="page" value="ver_postulaciones">';
    if ($post_id_filtrado) echo '<input type="hidden" name="post_id" value="' . esc_attr($post_id_filtrado) . '">';
    echo '<input type="text" name="nombre" placeholder="Nombre o email" value="' . esc_attr($nombre_filtro) . '" style="margin-right:10px;">';
    echo '<input type="date" name="desde" value="' . esc_attr($desde) . '" style="margin-right:10px;">';
    echo '<input type="date" name="hasta" value="' . esc_attr($hasta) . '" style="margin-right:10px;">';
    echo '<select name="estado" style="margin-right:10px;">';
    echo '<option value="">Todos los estados</option>';
    echo '<option value="Pendiente de revisión"' . selected($estado_filtrado, 'Pendiente de revisión', false) . '>Pendiente de revisión</option>';
    echo '<option value="Enviado a la Empresa/Reclutador"' . selected($estado_filtrado, 'Enviado a la Empresa/Reclutador', false) . '>Enviado a la Empresa/Reclutador</option>';
    echo '<option value="Descartado"' . selected($estado_filtrado, 'Descartado', false) . '>Descartado</option>';
    echo '</select>';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form>';

    // Formulario para XLS seleccionados
    echo '<form method="post" action="' . admin_url('admin-post.php') . '" id="form_seleccionados">';
    wp_nonce_field('descargar_seleccionados_action', 'descargar_seleccionados_nonce');
    echo '<input type="hidden" name="action" value="descargar_seleccionados_xlsx">';
    
    echo '<table class="wp-list-table widefat fixed striped">';
    echo '<thead><tr>
        <th><input type="checkbox" id="select_all"></th>
        <th>Fecha</th><th>Correo</th><th>Nombre</th><th>Estado</th><th>Acciones</th>
    </tr></thead><tbody>';

    if ($paginados) {
        foreach ($paginados as $fila) {
            $fecha = date('Y-m-d', strtotime($fila->fecha));
            $ver = admin_url('admin.php?page=ver_postulacion_detalle&user_id=' . $fila->user_id . '&post_id=' . $fila->post_id);
            $xls = admin_url('admin-post.php?action=descargar_usuario_xlsx&user_id=' . $fila->user_id);
            $pdf = admin_url('admin-post.php?action=descargar_cv_pdf&user_id=' . $fila->user_id);

            echo "<tr>
                <td><input type='checkbox' name='user_ids[]' value='{$fila->user_id}' class='select_user'></td>
                <td>{$fecha}</td>
                <td>{$fila->user_email}</td>
                <td>{$fila->display_name}</td>
             
                <td>{$fila->estado}</td>
               <td>
                    <a href='{$ver}' class='button'>Resumen</a>
                    <a href='" . admin_url('admin.php?page=ver_postulante&user_id=' . $fila->user_id) . "' class='button'>Ver detalles</a>
                    <a href='{$xls}' class='button'>XLSX</a>
                    <a href='{$pdf}' class='button button-secondary'>CV</a>
                </td>

            </tr>";
        }
    } else {
        echo '<tr><td colspan="7">No se encontraron postulaciones.</td></tr>';
    }

    echo '</tbody></table>';
    echo '</form>';

    // Formulario PDF
    echo '<form method="post" action="' . admin_url('admin-post.php') . '" id="form_seleccionados_pdf">';
    wp_nonce_field('descargar_seleccionados_pdf_action', 'descargar_seleccionados_pdf_nonce');
    echo '<input type="hidden" name="action" value="descargar_seleccionados_pdf">';
    echo '</form>';

    // Paginación
    if ($total_paginas > 1) {
        echo '<div class="tablenav"><div class="tablenav-pages">';
        for ($i = 1; $i <= $total_paginas; $i++) {
            $url = add_query_arg(array_merge($_GET, ['paged' => $i]));
            $class = ($i == $paged) ? ' button-primary' : '';
            echo '<a class="button' . esc_attr($class) . '" href="' . esc_url($url) . '">' . $i . '</a> ';
        }
        echo '</div></div>';
    }

    echo '</div>';

    // JS para selección múltiple
    ?>
    <script>
        document.getElementById('select_all').addEventListener('change', function() {
            document.querySelectorAll('.select_user').forEach(cb => cb.checked = this.checked);
        });

        document.getElementById('form_seleccionados_pdf').addEventListener('submit', function(e) {
            e.preventDefault();
            this.querySelectorAll('input[name="user_ids[]"]').forEach(el => el.remove());
            document.querySelectorAll('#form_seleccionados input[name="user_ids[]"]:checked').forEach(cb => {
                const input = document.createElement('input');
                input.type = 'hidden';
                input.name = 'user_ids[]';
                input.value = cb.value;
                this.appendChild(input);
            });
            if (!this.querySelector('input[name="user_ids[]"]')) {
                alert('Selecciona al menos un usuario.');
                return;
            }
            this.submit();
        });

        document.getElementById('form_seleccionados').addEventListener('submit', function(e) {
            if (!this.querySelector('input[name="user_ids[]"]:checked')) {
                alert('Selecciona al menos un usuario.');
                e.preventDefault();
            }
        });
    </script>
    <?php
}


function mostrar_detalle_postulacion() {
    global $wpdb;

    $user_id = intval($_GET['user_id'] ?? 0);
    $post_id = intval($_GET['post_id'] ?? 0);

    if (!$user_id || !$post_id) {
        echo '<div class="notice notice-error"><p>Datos no válidos.</p></div>';
        return;
    }

    $user_info = get_userdata($user_id);
    $telefono = get_user_meta($user_id, 'mobile', true);
    $cv = get_user_meta($user_id, 'cv_url', true);
    $pais = get_user_meta($user_id, 'country', true);
    $ciudad = get_user_meta($user_id, 'city', true);
    $titulo_oportunidad = get_the_title($post_id);

    // Obtener fecha desde la tabla de inscripciones
    $tabla_inscripciones = $wpdb->prefix . 'opt_inscripciones';
    $fecha = $wpdb->get_var(
        $wpdb->prepare(
            "SELECT fecha FROM $tabla_inscripciones WHERE id_usuario = %d AND id_oportunidades = %d",
            $user_id,
            $post_id
        )
    );

    $fecha_formateada = $fecha ? date('d/m/Y H:i', strtotime($fecha)) : 'Fecha no disponible';

    // Simulación de actualización de estado
    if (isset($_POST['estado'])) {
        update_user_meta($user_id, "estado_postulacion_{$post_id}", sanitize_text_field($_POST['estado']));
        echo '<div class="updated notice"><p>Estado actualizado.</p></div>';
    }

    $estado_actual = get_user_meta($user_id, "estado_postulacion_{$post_id}", true) ?: 'Pendiente de revisión';

    echo '<div class="wrap"><h1>Resumen del postulante</h1>';
    echo '<table class="form-table"><tbody>';
    echo "<tr><th>Nombre:</th><td>{$user_info->first_name}</td></tr>";
    echo "<tr><th>Apellidos:</th><td>{$user_info->last_name}</td></tr>";
    echo "<tr><th>Correo:</th><td>{$user_info->user_email}</td></tr>";
    echo "<tr><th>Teléfono:</th><td>{$telefono}</td></tr>";
    echo "<tr><th>CV:</th><td>" . ($cv ? "<a href='{$cv}' target='_blank'>Ver CV</a>" : "No adjunto") . "</td></tr>";
    echo "<tr><th>País:</th><td>{$pais}</td></tr>";

    if ($ciudad) {
      echo "<tr><th>Ciudad:</th><td>{$ciudad}</td></tr>";
  }
  
  echo "<tr><th>Oportunidad:</th><td>{$titulo_oportunidad}</td></tr>";
  echo "<tr><th>Fecha de postulación:</th><td>{$fecha_formateada}</td></tr>";

  echo "<tr><th>Estado:</th><td>
  <form method='post'>
  <select name='estado'>
  <option " . selected($estado_actual, 'Pendiente de revisión', false) . ">Pendiente de revisión</option>
  <option " . selected($estado_actual, 'Enviado a la Empresa/Reclutador', false) . ">Enviado a la Empresa/Reclutador</option>
  <option " . selected($estado_actual, 'Descartado', false) . ">Descartado</option>
  </select>
  <input type='submit' class='button button-primary' value='Actualizar'>
  </form>
  </td></tr>";
  echo '</tbody></table></div>';
}





function mostrar_dashboard_oportunidades() {
    global $wpdb;

    // Parámetros de filtro
    $vista = $_GET['vista'] ?? 'mensual';
    $desde = $_GET['desde'] ?? date('Y-01-01');
    $hasta = $_GET['hasta'] ?? date('Y-m-d');

    // Formatos separados
    if ($vista === 'diaria') {
        $formato_php = 'Y-m-d';
        $formato_sql = 'DATE(%s)';
    } elseif ($vista === 'anual') {
        $formato_php = 'Y';
        $formato_sql = "DATE_FORMAT(%s, '%%Y')";
    } else { // mensual
        $formato_php = 'Y-m';
        $formato_sql = "DATE_FORMAT(%s, '%%Y-%%m')";
    }

    // Consultas
    $postulantes = $wpdb->get_results("
        SELECT " . sprintf($formato_sql, 'user_registered') . " AS periodo, COUNT(*) AS total
        FROM {$wpdb->users} u
        INNER JOIN {$wpdb->usermeta} um ON u.ID = um.user_id
        WHERE um.meta_key = '{$wpdb->prefix}capabilities'
        AND um.meta_value LIKE '%subscriber%'
        GROUP BY periodo
        ORDER BY periodo ASC
    ");

    $tabla_inscripciones = $wpdb->prefix . 'opt_inscripciones';
    $postulaciones = $wpdb->get_results($wpdb->prepare("
        SELECT " . sprintf($formato_sql, 'fecha') . " AS periodo, COUNT(*) AS total
        FROM $tabla_inscripciones
        WHERE fecha BETWEEN %s AND %s
        GROUP BY periodo ORDER BY periodo ASC", $desde . ' 00:00:00', $hasta . ' 23:59:59'));

    $historicas = $wpdb->get_results($wpdb->prepare("
        SELECT " . sprintf($formato_sql, 'post_date') . " AS periodo, COUNT(*) AS total
        FROM {$wpdb->posts}
        WHERE post_type = 'oportunidad'
        AND post_status = 'publish'
        AND post_date BETWEEN %s AND %s
        GROUP BY periodo ORDER BY periodo ASC", $desde . ' 00:00:00', $hasta . ' 23:59:59'));

    // Generar períodos esperados
    function generar_periodos($inicio, $fin, $formato) {
        $inicio = new DateTime($inicio);
        $fin = new DateTime($fin);

        if ($formato === 'Y-m') {
            $interval = new DateInterval('P1M');
        } elseif ($formato === 'Y') {
            $interval = new DateInterval('P1Y');
        } else {
            $interval = new DateInterval('P1D');
        }

        $periodos = [];
        while ($inicio <= $fin) {
            $periodos[] = $inicio->format($formato);
            $inicio->add($interval);
        }

        return $periodos;
    }

    $labels = generar_periodos($desde, $hasta, $formato_php);

    // Construcción de datasets
    $dataPostulantes = [];
    $dataPostulaciones = [];
    $dataHistoricas = [];

    foreach ($labels as $label) {
        $totalP = 0;
        foreach ($postulantes as $p) {
            if ($p->periodo === $label) {
                $totalP = (int) $p->total;
                break;
            }
        }
        $dataPostulantes[] = $totalP;

        $totalQ = 0;
        foreach ($postulaciones as $q) {
            if ($q->periodo === $label) {
                $totalQ = (int) $q->total;
                break;
            }
        }
        $dataPostulaciones[] = $totalQ;

        $totalH = 0;
        foreach ($historicas as $h) {
            if ($h->periodo === $label) {
                $totalH = (int) $h->total;
                break;
            }
        }
        $dataHistoricas[] = $totalH;
    }

    // HTML
    echo '<div class="wrap"><h1>Dashboard de Oportunidades</h1>';
    echo '<form method="get" style="margin-bottom: 20px;">';
    echo '<input type="hidden" name="page" value="dashboard_oportunidades">';
    echo 'Desde: <input type="date" name="desde" value="' . esc_attr($desde) . '"> ';
    echo 'Hasta: <input type="date" name="hasta" value="' . esc_attr($hasta) . '"> ';
    echo '<select name="vista">
        <option value="diaria"' . selected($vista, 'diaria', false) . '>Diaria</option>
        <option value="mensual"' . selected($vista, 'mensual', false) . '>Mensual</option>
        
    </select> ';
    echo '<input type="submit" class="button" value="Filtrar">';
    echo '</form>';
    echo '<canvas id="grafico_postulantes" height="120"></canvas>';
    echo '<canvas id="grafico_postulaciones" height="120" style="margin-top:40px;"></canvas>';
    echo '<canvas id="grafico_historicas" height="120" style="margin-top:40px;"></canvas>';

    ?>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        const labels = <?php echo json_encode($labels); ?>;
        const dataPostulantes = <?php echo json_encode($dataPostulantes); ?>;
        const dataPostulaciones = <?php echo json_encode($dataPostulaciones); ?>;
        const dataHistoricas = <?php echo json_encode($dataHistoricas); ?>;
        const vista = '<?php echo esc_js(ucfirst($vista)); ?>';

        function crearConfig(label, data, color, titulo) {
            return {
                type: 'line',
                data: {
                    labels: labels,
                    datasets: [{
                        label: label,
                        data: data,
                        borderColor: color,
                        backgroundColor: color.replace('rgb', 'rgba').replace(')', ', 0.1)'),
                        tension: 0.4,
                        fill: true,
                    }]
                },
                options: {
                    responsive: true,
                    plugins: {
                        title: {
                            display: true,
                            text: titulo,
                            font: { size: 18 }
                        },
                        legend: { display: false }
                    },
                    scales: {
                        x: {
                            title: {
                                display: true,
                                text: vista,
                            }
                        },
                        y: {
                            title: {
                                display: true,
                                text: 'Cantidad',
                            },
                            beginAtZero: true,
                            ticks: { precision: 0 }
                        }
                    }
                }
            };
        }

        new Chart(document.getElementById('grafico_postulantes'), crearConfig('Postulantes', dataPostulantes, 'rgb(54, 162, 235)', 'Registro de Postulantes'));
        new Chart(document.getElementById('grafico_postulaciones'), crearConfig('Postulaciones', dataPostulaciones, 'rgb(255, 99, 132)', 'Postulaciones - Usuarios'));
        new Chart(document.getElementById('grafico_historicas'), crearConfig('Históricas', dataHistoricas, 'rgb(75, 192, 192)', 'Postulaciones Históricas (Oportunidades Publicadas)'));
    </script>
    <?php
    echo '</div>';
}
