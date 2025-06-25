<?php
get_header();
global $wp_query;

$backlink =  home_url('/');

$btn_url = '#';

global $countries;
global $fecha;
global $categoria_list;
global $modality_list;
global $type_day_list;
global $type_contract_list;
global $range_salary;

    // O si quieres usar el arreglo global directamente:
$countries              = $GLOBALS['countries'];
$fecha                  = $GLOBALS['fecha'];
$category_list          = $GLOBALS['categoria_list'];
$modality_list          = $GLOBALS['modality_list'];
$type_contract_list     = $GLOBALS['type_contract_list'];
$range_salary           = $GLOBALS['range_salary'];

// Recoger filtros como antes
// $filters = !empty($wp_query->query_vars['filters_processed']) ? $wp_query->query_vars['filters_processed'] : false;


// $page = !isset($wp_query->query_vars['paged']) ? 1 : absint($wp_query->query_vars['paged']);
$page = isset($_GET['page']) ? max(1, absint($_GET['page'])) : 1;

$page = $page < 1 ? 1 : $page;

$items_per_page = 6;

// Construir args para WP_Query
$query_args = [
    'post_type'      => 'oportunidad',
    'post_status'    => 'publish',
    'posts_per_page' => $items_per_page,
    'paged'          => $page,
    'orderby'        => 'date',
    'order'          => 'DESC',
    'meta_query'     => [
        'relation' => 'AND',
        [
            'key'     => '_fecha_fin',
            'compare' => 'EXISTS',
        ],
        [
            'key'     => '_fecha_fin',
            'value'   => date('Y-m-d'),
            'compare' => '>=',
            'type'    => 'DATE'
        ]
    ],

];

$raw_filters = !empty($_GET) ? $_GET : [];

$filters = [];

if (!empty($raw_filters['empresa'])) {
    $query_args['author_name'] = sanitize_text_field($raw_filters['empresa']);
}

$campos_permitidos = [
    'category'       => '_categoria',
    'sector'         => '_sector',
    'country'        => '_pais',
    'departamento'   => '_oportunida_departamento',
    'provincia'      => '_oportunida_provincia',
    'distrito'       => '_oportunida_distrito',
    'modality'       => '_modalidad',
    'type_day'       => '_jornada',
    'type_contract'  => '_contrato',
    'salary'         => 'salary',
    'disability'     => '_discapacidad',
    'company'        => 'company'

];

foreach ($campos_permitidos as $campo => $meta_key) {
    if (!empty($raw_filters[$campo])) {
        $filters[$campo] = sanitize_text_field($raw_filters[$campo]);
    }
}


// // Agrega los filtros meta para los campos permitidos (excepto salary que ya tienes)
foreach ($campos_permitidos as $campo => $meta_key) {
    if (!empty($filters[$campo])) {
        // Ignora salary porque ya está manejado aparte
        if ($campo !== 'salary') {
            $query_args['meta_query'][] = [
                'key' => $meta_key,
                'value' => $filters[$campo],
                'compare' => '=',
                'type' => 'CHAR', // Cambia a NUMERIC si el campo es numérico
            ];
        }
    }
}


if (!empty($filters['empresa'])) {
    $query_args['author_name'] = $filters['empresa'];
}
// Capturar manualmente filtros adicionales que no están en $campos_permitidos
if (!empty($raw_filters['range_date'])) {
    $filters['range_date'] = sanitize_text_field($raw_filters['range_date']);
}

$date_query = [];

if (!empty($filters['range_date'])) {
    $today = current_time('Y-m-d');

    switch ($filters['range_date']) {
    case 'today':
        $date_query[] = [
            'year'  => date('Y'),
            'month' => date('m'),
            'day'   => date('d'),
        ];
        break;

    case 'yesterday':
        $date_query[] = [
            'year'  => date('Y', strtotime('-1 day')),
            'month' => date('m', strtotime('-1 day')),
            'day'   => date('d', strtotime('-1 day')),
        ];
        break;

    case '3d':
        $date_query[] = [
            'after' => date('Y-m-d', strtotime('-3 days')),
            'inclusive' => true,
        ];
        break;

    case '7d':
        $date_query[] = [
            'after' => date('Y-m-d', strtotime('-7 days')),
            'inclusive' => true,
        ];
        break;

    case '1m':
        $date_query[] = [
            'after' => date('Y-m-d', strtotime('-1 month')),
            'inclusive' => true,
        ];
        break;
}

}


if (!empty($date_query)) {
    $query_args['date_query'] = $date_query;
}


$salary_ranges = [
    "l750"   => ['max' => 750],
    "m750"   => ['min' => 750, 'max' => 1500],
    "m1500"  => ['min' => 1500, 'max' => 3000],
    "m3000"  => ['min' => 3000, 'max' => 6000],
    "m6000"  => ['min' => 6000, 'max' => 10000],
    "m10000" => ['min' => 10000],
];

if (!empty($filters['salary']) && isset($salary_ranges[$filters['salary']])) {
    $range = $salary_ranges[$filters['salary']];
    
    $salary_meta_query = ['relation' => 'AND'];
    
    if (isset($range['max'])) {
        $salary_meta_query[] = [
            'key' => '_salario_min',  // mínimo del post
            'value' => $range['max'], // máximo del filtro
            'compare' => '<=',
            'type' => 'NUMERIC',
        ];
    }
    
    if (isset($range['min'])) {
        $salary_meta_query[] = [
            'key' => '_salario_max',  // máximo del post
            'value' => $range['min'], // mínimo del filtro
            'compare' => '>=',
            'type' => 'NUMERIC',
        ];
    }
    
    $query_args['meta_query'][] = $salary_meta_query;
}

$range_dates = [
    'today' => 'Hoy',
    'yesterday' => 'Ayer',
    '3d' => 'Últimos 3 días',
    '7d' => 'Última semana',
    '1m' => 'Último mes'
];

// Ejecutar consulta
$query = new WP_Query($query_args);

// Datos para la paginación
$total_items = $query->found_posts;
$total_pages = $query->max_num_pages;

// Obtener posts
$items = $query->posts;

?>
<style>
    .choices[data-type*=select-one] .choices__button{
        display: none;
        pointer-events: none;
    }
    .pagination-bar{
       text-align: center;
       width: 100% !important;
    }
    .page-numbers:not(.next):not(.prev){
        width: 25px;
        height: 25px;
        display: inline-block;
        text-align: center;
        line-height: 25px;
        border-radius: 999px;
    }
    .page-numbers.current{
        background: #02006C !important;
        color: #fff !important;
    }
    @media (max-width: 764px){
        .pagination-bar{
        padding-left: 0;
        text-align: center;
    }
    }
</style>
<link rel="stylesheet" href="/wp-content/themes/turimet/assets/css/oportunity.css">
<!-- <script src="/wp-content/plugins/opt-custom/assets/js/oportunity.js"></script> -->

<section class="section-1 section-p-s no-desktop no-tablet">
    <div class="row">
        <ul class="breadcrumb">

            <li><a href="<?=home_url('oportunidades/')?>">Todas las oportunidades</a></li>
            <?php if(isset($wp_query->query_vars['event-category']) && !empty($wp_query->query_vars['event-category'])): $term = get_queried_object(); ?><li><a href="<?=get_term_link($term)?>"><?=$term->name?></a></li><?php endif; ?>
            <li>Resultado encontrado</li>
        </ul>
    </div>
</section>
<section class="section-2 oportunity-section-2">
    <div class="row ta-c">
        <a href="<?=$backlink?>" class="link-back abs no-mobile">regresar</a>
        <strong><?= sprintf('%02d', $total_items) ?></strong>  Oportunidades de trabajo encontradas
    </div>
    <div class="row rfilters-form-row">
        <form class="rfilters-form" method="get" action="<?=home_url('oportunidades')?>">
            <select name="sort" id="sort">
                <option value="" selected>Ordenar por</option>
                <option value="creation_desc"<?php echo @$filters['sort']=='creation_desc' ? ' selected="selected"' : ''; ?>>Más recientes</option>
                <option value="creation_asc"<?php echo @$filters['sort']=='creation_asc' ? ' selected="selected"' : ''; ?>>Más antiguos</option>
            </select>
            <select name="sector" id="sector" class="no-mobile">
                <option value="" selected>Sector</option>
                <option value="Privado" <?php echo @$filters['sector']== 'Privado' ? ' selected="selected"' : ''; ?>>
                    Privado
                </option>
                <option value="Público" <?php echo @$filters['sector']== 'Público' ? ' selected="selected"' : ''; ?>>
                    Público
                </option>
            </select>

            <select name="country" id="countrys" class="no-mobile">
                <option value="" selected>País</option>
                <?php foreach( $countries as $item ): ?>
                    <option value="<?=$item['name']?>"<?php echo @$filters['country']==$item['name'] ? ' selected="selected"' : ''; ?>><?=$item['name']?></option>
                <?php endforeach; ?>
            </select>

            <style>
                .box__pdot .choices{
                   pointer-events: none; opacity: 0.5;
                }

                .box__pdot.active .choices{
                    pointer-events: auto; opacity: 1;
                }
                @media (max-width: 764px){
                     .box__pdot{
                        display: none;
                     }
                }
            </style>

            <div class="box__pdot">
                <select id="departamento" class="pdot no-mobile" name="departamento" >
                    <option value="">Departamento</option>
                </select>
            </div>
            <div class="box__pdot">
                <select id="provincia" class="pdot no-mobile" name="provincia">
                    <option value="">Provincia</option>
                </select>
            </div>

            <div class="box__pdot">
                <select id="distrito"  class="pdot no-mobile" name="distrito">
                    <option value="">Distrito</option>
                </select>
            </div>

            <script>
                document.addEventListener('DOMContentLoaded', function () {
                   const basePath = '/wp-content/plugins/opt-custom/assets/';
    let departamentos = [], provincias = [], distritos = [];

    // Declarar primero los elementos y Choices
    const countrySel = document.getElementById('countrys');
    const depSel = document.getElementById('departamento');
    const provSel = document.getElementById('provincia');
    const distSel = document.getElementById('distrito');

    const choicesDep = new Choices(depSel, { removeItemButton: true });
    const choicesProv = new Choices(provSel, { removeItemButton: true });
    const choicesDist = new Choices(distSel, { removeItemButton: true });

    const depBox = depSel.closest('.box__pdot');
    const provBox = provSel.closest('.box__pdot');
    const distBox = distSel.closest('.box__pdot');

    // Ahora sí puedes usar estas variables
    const selectedDep = "<?= isset($filters['departamento']) ? esc_js($filters['departamento']) : '' ?>";
    const selectedProv = "<?= isset($filters['provincia']) ? esc_js($filters['provincia']) : '' ?>";
    const selectedDist = "<?= isset($filters['distrito']) ? esc_js($filters['distrito']) : '' ?>";
    const selectedCountry = "<?= isset($filters['country']) ? esc_js($filters['country']) : '' ?>";

    // Cargar los archivos JSON
    Promise.all([
        fetch(basePath + 'ubigeo_peru_2016_departamentos.json').then(r => r.json()),
        fetch(basePath + 'ubigeo_peru_2016_provincias.json').then(r => r.json()),
        fetch(basePath + 'ubigeo_peru_2016_distritos.json').then(r => r.json())
    ]).then(([deps, provs, dists]) => {
        departamentos = deps;
        provincias = provs;
        distritos = dists;

        // Forzar activación y selección si el país es Perú y hay filtros
        if (selectedCountry === 'Peru') {
            countrySel.value = 'Peru';
            depBox.classList.add('active');
            provBox.classList.add('active');
            distBox.classList.add('active');

            // choicesDep.clearStore();
            // choicesDep.setChoices(
            //     [{ value: '', label: 'Departamento', disabled: true }]
            //     .concat(departamentos.map(dep => ({
            //         value: dep.name.trim(),
            //         label: dep.name.trim(),
            //         selected: dep.name.trim() === selectedDep
            //     }))),
            //     'value', 'label', true
            // );
            const depChoices = [{ value: '', label: 'Departamento', disabled: true, selected: !selectedDep }]
    .concat(departamentos.map(dep => ({
        value: dep.name.trim(),
        label: dep.name.trim(),
        selected: dep.name.trim() === selectedDep
    })));

choicesDep.clearStore();
choicesDep.setChoices(depChoices, 'value', 'label', true);


            const depSelectedObj = departamentos.find(dep => dep.name.trim() === selectedDep);
            if (depSelectedObj) {
                const provOptions = provincias
                    .filter(prov => prov.department_id === depSelectedObj.id)
                    .map(prov => ({
                        value: prov.name.trim(),
                        label: prov.name.trim(),
                        selected: prov.name.trim() === selectedProv
                    }));

                choicesProv.clearStore();
                choicesProv.setChoices(
                    [{ value: '', label: 'Provincia', disabled: true }].concat(provOptions),
                    'value', 'label', true
                );

                const provSelectedObj = provincias.find(prov => prov.name.trim() === selectedProv);
                if (provSelectedObj) {
                    const distOptions = distritos
                        .filter(dist => dist.province_id === provSelectedObj.id)
                        .map(dist => ({
                            value: dist.name.trim(),
                            label: dist.name.trim(),
                            selected: dist.name.trim() === selectedDist
                        }));

                    choicesDist.clearStore();
                    choicesDist.setChoices(
                        [{ value: '', label: 'Distrito', disabled: true }].concat(distOptions),
                        'value', 'label', true
                    );
                }
            }
        }
    });

                    countrySel.addEventListener('change', () => {
                        if (countrySel.value === 'Peru') {
                            depBox.classList.add('active');
                            provBox.classList.add('active');
                            distBox.classList.add('active');

                            choicesDep.clearStore(); // Limpia las opciones actuales
                            choicesDep.setChoices(
                                [{ value: '', label: 'Departamento', selected: true, disabled: true }]
                                .concat(
                                    departamentos.map(dep => ({
                                        value: dep.name.trim(),
                                        label: dep.name.trim()
                                    }))
                                    ),
                                'value',
                                'label',
                                true
                                );
                        } else {
                            depBox.classList.remove('active');
                            provBox.classList.remove('active');
                            distBox.classList.remove('active');

                            // choicesDep.clearStore();
                            // choicesProv.clearStore();
                            // choicesDist.clearStore();
                        }
                    });

                    depSel.addEventListener('change', () => {
                        const nombreDep = depSel.value.trim();
                        const depSeleccionado = departamentos.find(dep => dep.name.trim() === nombreDep);
                        if (!depSeleccionado) return;

                        choicesProv.clearStore();
                        choicesDist.clearStore();

                        const provOptions = provincias
                        .filter(prov => prov.department_id === depSeleccionado.id)
                        .map(prov => ({
                            value: prov.name.trim(),
                            label: prov.name.trim()
                        }));

                        choicesProv.setChoices(
                            [{ value: '', label: 'Provincia', selected: true, disabled: true }].concat(provOptions),
                            'value',
                            'label',
                            true
                            );
                        choicesDist.setChoices(
                            [{ value: '', label: 'Distrito', selected: true, disabled: true }],
                            'value',
                            'label',
                            true
                            );
                    });

                    provSel.addEventListener('change', () => {
                        const nombreProv = provSel.value.trim();
                        const provSeleccionado = provincias.find(prov => prov.name.trim() === nombreProv);
                        if (!provSeleccionado) return;

                        choicesDist.clearStore();

                        const distOptions = distritos
                        .filter(dist => dist.province_id === provSeleccionado.id)
                        .map(dist => ({
                            value: dist.name.trim(),
                            label: dist.name.trim()
                        }));

                        choicesDist.setChoices(
                            [{ value: '', label: 'Distrito', selected: true, disabled: true }].concat(distOptions),
                            'value',
                            'label',
                            true
                            );
                    });
                });


            </script>

            <select name="range_date" id="range_date" class="no-mobile">
                <option value="" selected>Fecha</option>
                <?php foreach( $range_dates as $key => $item ): ?>
                    <option value="<?=$key?>"<?php echo @$filters['range_date']==$key ? ' selected="selected"' : ''; ?>><?=$item?></option>
                <?php endforeach; ?>
            </select>
            <select name="category" id="category" class="no-mobile">
                <option value="" selected>Categoría</option>

                <?php foreach( $category_list as $item ): ?>
                    <option value="<?=$item['name']?>"<?php echo @$filters['category']==$item['name'] ? ' selected="selected"' : ''; ?>><?=$item['title']?></option>
                <?php endforeach; ?>
            </select>
            <select name="modality" id="modality" class="no-mobile">
                <option value="" selected>Modalidad</option>
                <?php foreach( $modality_list as $item ): ?>
                    <option value="<?=$item['name']?>"<?php echo @$filters['modality']==$item['name'] ? ' selected="selected"' : ''; ?>><?=$item['title']?></option>
                <?php endforeach; ?>
            </select>
            <select name="type_day" id="type_day" class="no-tablet no-mobile">
                <option value="" selected>Tipo de jornada</option>
                <?php foreach( $type_day_list as $item ): ?>
                    <option value="<?=$item['name']?>"<?php echo @$filters['type_day']==$item['name'] ? ' selected="selected"' : ''; ?>><?=$item['title']?></option>
                <?php endforeach; ?>
            </select>
            <select name="type_contract" id="type_contract" class="no-tablet no-mobile">
                <option value="" selected>Tipo de contrato</option>
                <?php foreach( $type_contract_list as $item ): ?>
                    <option value="<?=$item['name']?>"<?php echo @$filters['type_contract']==$item['name'] ? ' selected="selected"' : ''; ?>><?=$item['title']?></option>
                <?php endforeach; ?>
            </select>

            <select name="salary" id="salary" class="no-tablet no-mobile">
                <option value="" <?php echo (!isset($filters['salary']) || $filters['salary'] == '') ? 'selected' : ''; ?>>Salario</option>
                <option value="l750" <?php echo (isset($filters['salary']) && $filters['salary'] == 'l750') ? 'selected' : ''; ?>>Menos de S/750</option>
                <option value="m750" <?php echo (isset($filters['salary']) && $filters['salary'] == 'm750') ? 'selected' : ''; ?>>Más de S/750</option>
                <option value="m1500" <?php echo (isset($filters['salary']) && $filters['salary'] == 'm1500') ? 'selected' : ''; ?>>Más de S/1,500</option>
                <option value="m3000" <?php echo (isset($filters['salary']) && $filters['salary'] == 'm3000') ? 'selected' : ''; ?>>Más de S/3,000</option>
                <option value="m6000" <?php echo (isset($filters['salary']) && $filters['salary'] == 'm6000') ? 'selected' : ''; ?>>Más de S/6,000</option>
                <option value="m10000" <?php echo (isset($filters['salary']) && $filters['salary'] == 'm10000') ? 'selected' : ''; ?>>Más de S/10,000</option>
            </select>

         <select name="disability" id="disability" class="no-tablet no-mobile">
    <option value="" selected>Discapacidad</option>
    <option value="Sí"<?php echo @$filters['disability'] == 'Sí' ? ' selected="selected"' : ''; ?>>Sí</option>
    <option value="No"<?php echo @$filters['disability'] == 'No' ? ' selected="selected"' : ''; ?>>No</option>
</select>

            <?php if( isset($filters['company']) && !empty($filters['company'])): ?>
                <input type="hidden" name="company" value="<?=$filters['company']?>" />
            <?php endif; ?>
            <input type="submit" name="Filtrar" value="Filtrar" />
    </form>
</div>
</section>
<section class="section-1">
    <div class="turimet-account__alerts"></div>
    <div class="row mb40">
        <ul class="breadcrumb no-mobile">
            <li><a href="<?=home_url('oportunidades/')?>">Todas las oportunidades</a></li>
            <?php if(isset($wp_query->query_vars['event-category']) && !empty($wp_query->query_vars['event-category'])): $term = get_queried_object(); ?><li><a href="<?=get_term_link($term)?>"><?=$term->name?></a></li><?php endif; ?>
            <li>Resultado encontrado</li>
        </ul>
        <h3 class="c-blue">Oportunidades encontradas</h3>
    </div>
    <div class="row cols-45-55">
        <div class="oplist">
            <?php
            if ($items) {
                foreach ($items as $post) {
                    setup_postdata($post);
                    ?>

                    <?php
                  
                    // $created_at = get_the_date('Y-m-d H:i:s', $post);
                    $blank = isset($args['blank']) ? $args['blank'] : false;
                    $item['created_at'] = ucfirst(date_i18n('F j, Y', strtotime(get_the_date('Y-m-d', $post))));
                    $post_author_id = (int) $post->post_author;
                    $user_info = get_userdata($post_author_id);


                    $author_slug = $user_info ? $user_info->user_nicename : '';
                    $empresa_nombre = get_user_meta($post_author_id, 'empresa_nombre', true);
                    $empresa_pais   = get_user_meta($post_author_id, 'empresa_pais', true);
                    $empresa_icono  = get_user_meta($post_author_id, 'empresa_icono', true);

                    $modalidad = get_post_meta($post->ID, '_modalidad', true);
                    $_salario_max = get_post_meta($post->ID, '_salario_min', true);
                    $_salario_min =  get_post_meta($post->ID, '_salario_max', true);
                    $_pais =  get_post_meta($post->ID, '_pais', true);
                    $code = ''; // Inicializamos

                    foreach ($countries as $country) {
                        if (strcasecmp($country['name'], $empresa_pais) === 0) {
                            $code = $country['code'];
                            break;
                        }
                    }


                    $url = get_permalink($post);

                    $urlautor = get_author_posts_url($post_author_id);

                    ?>

                    <div class="oplist__item" data-id="<?=$post->ID?>">
                        <figure class="fig-contain oplist__image">
                            <a href="<?= $urlautor; ?>"<?=$blank?' target="_blank"':''?>>
                                <img src="<?=$empresa_icono?>" alt="" />
                            </a>
                        </figure>
                        <div class="oplist__main">
                            <div class="oplist__main--company"><a href="<?= $urlautor; ?>" target="_blank"><?=@$empresa_nombre?></a></div>
                            <h3 class="oplist__main--job"><a href="<?= $url ?>"<?=$blank?' target="_blank"':''?>><?=$post->post_title?></a></h3>
                            <div class="oplist__main--country">
                                <div class="iti__flag iti__<?=$code?>"></div>
                                <?=$empresa_pais?></div>
                            </div>
                            <div class="oplist__side">
                                <ul>
                                    <li class="oplist__side--creation"><?=$item['created_at']?></li>
                                    <li class="oplist__side--modality"><?=$modalidad?></li>
                                </ul>
                            </div>
                        </div>
                    <?php  }
                    wp_reset_postdata();
                } else {
                    echo '<p>No se encontraron oportunidades.</p>';
                }
                ?>


                <div class="row pagination-bar">
                    <?php
                    global $wp_rewrite;
                    $path = '';

                    if (isset($wp_query->query_vars['filters']) && !empty($wp_query->query_vars['filters'])) {
                        $path = 'filtrar/' . $wp_query->query_vars['filters'] . '/';
                    }

                // Generar links de paginación
                    
                    $query_args_paginate = $_GET;
                    $query_args_paginate['page'] = '%#%';

                    $base_url = add_query_arg($query_args_paginate, home_url('/oportunidades/'));

                    echo paginate_links([
                        'base'      => $base_url,
                        'format'    => '',
                        'current'   => $page,
                        'total'     => $total_pages,
                        'prev_text' => __('« Anterior'),
                        'next_text' => __('Siguiente »'),
                    ]);

                    ?>
                </div>

                 
            </div>

            <section id="oportunity-section" class="oportunity-section no-mobile">
                <div class="oportunity-placeholder">
                    <img src="<?php echo OPT_CUSTOM_PLUGIN_URL . '/assets/images/oportunity-placeholder.svg'; ?>" alt="Imagen"/>
                    <p>¡Encontramos <?= sprintf('%02d', $total_items) ?> oportunidades para ti!</p>
                </div>
            </section>
        </div>
       
    </section>

    <script type="text/template" id="tmpl-oportunity">
        <article class="oportunity-entry">
            <div class="oportunity-entry__header">
                <div class="oportunity-entry__header-title">
                    <h2>{{job}}</h2>
                    <ul>
                        <li><a href="{{company_url}}" target="_blank">{{company_name}}</a></li>
                        <li><div class="iti__flag iti__{{code}}"></div> {{country}}</li>
                        <li><figure class="fig-contain"><img src="{{company_image}}" /></figure></li>
                    </ul>
                </div>
                <div class="oportunity-entry__header-salary">Salario: <strong>{{salary}}</strong></div>
                <ul class="oportunity-entry__meta">
                    <li class="creation">{{created_at}}</li>
                    <li class="category">{{category}}</li>
                    <li class="day">{{type_day}}</li>
                    <li class="contract">{{type_contract}}</li>
                    <li class="modality">{{modality}}</li>
                </ul>
                <div class="oportunity-entry__buttons">
                    <div>
                       <?php
                       if ( is_user_logged_in() ) {
                        $url = $btn_url;
                        $extra_attrs = 'data-action="postulate" data-id="{{id}}"';
                    } else {
                        $url = home_url('/iniciar-sesion');  // O la URL que uses para el login
                        $extra_attrs = 'target="_blank"';
                    }
                    ?>
                    <a href="<?= esc_url($url) ?>" <?= $extra_attrs ?> class="btn btn-primary pink">Postular</a>

                    <div class="share-select" data-url="{{url}}">
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
                <a href="{{url}}" target="_blank" class="btn-more">Más información</a>
            </div>
        </div>
        <div class="oportunity-entry__body">
            <div class="mb20"><strong>Descripción del puesto</strong></div>
            {{description}}
        </div>
    </article>
</script>


<script>
    document.addEventListener('DOMContentLoaded', function () {
        const form = document.querySelector('.rfilters-form');

        if (form) {
            form.addEventListener('submit', function (e) {
                const fields = form.querySelectorAll('select, input[name]:not([type="submit"]):not([type="hidden"])');
                fields.forEach(field => {
                    if (field.value === '') {
                        field.disabled = true;
                    }
                });
            });
        }
    });
</script>

<script>
//   document.addEventListener('DOMContentLoaded', function () {
//     const selects = document.querySelectorAll('select');
//     selects.forEach((select) => {
//       new Choices(select, {
//         removeItemButton: true,   // si quieres botón para eliminar opción seleccionada
//         searchEnabled: true       // para que tengan buscador
//     });
//   });
// });
</script>

<?php get_footer('simple');  

?>