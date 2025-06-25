
<div class="oportunity-search">
    <form method="post" class="oportunity-search__form">
        <ul class="inline-list">
            <li>
                <select name="sector" id="sector">
                    <option value="" selected>Sector</option>
                    <?php foreach( $sector_list['data'] as $item ): ?>
                        <option value="<?=$item['name']?>"><?=$item['title']?></option>
                    <?php endforeach; ?>
                </select>
            </li>
            <li>
                <select name="category" id="category">
                    <option value="" selected>Categoría</option>
                    <?php foreach( $category_list['data'] as $item ): ?>
                        <option value="<?=$item['name']?>"><?=$item['title']?></option>
                    <?php endforeach; ?>
                </select>
            </li>

            <li>
                <select name="country" id="country" data-input="country">
                    <option value="" selected>Lugar</option>
                    <?php foreach( $country_list['data'] as $item ): ?>
                       <option value="<?=$item['name']?>" data-id="<?=$item['id']?>"<?=(isset($current_member['country']) && $item['name']==$current_member['country'])?' selected':''?>><?=$item['name']?></option>
                   <?php endforeach; ?>
               </select>
           </li>
         <!--   <li>

            <select <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden no-choices"':''?> name="region_born" id="region_born" data-input="region">
                <option value="" selected disabled>Departamento</option>
                <?php 
                if('Peru'==$current_member['country'] && !empty($current_member['state'])){
                    $options = \Turimet\Control\API::ubigeo_region($current_member['country']);
                    foreach($options as $id => $option){
                        printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['state']==$option?' selected':'' );
                    }
                }
                ?>
            </select>
        </li>
        <li>
            <select <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden"':''?> name="province_born" 
                id="province_born" data-input="province">
                <option value="" selected disabled>Provincia</option>
                <?php 
                if('Peru'==$current_member['country'] && !empty($current_member['state']) && !empty($current_member['county'])){
                    $options = \Turimet\Control\API::ubigeo_province($current_member['country'], $current_member['ubigeo']);
                    foreach($options as $id => $option){
                        printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['county']==$option?' selected':'' );
                    }
                }
                ?>
            </select>
        </li>
        <li>
            <select <?=(isset($current_member['country']) && 'Peru'!=$current_member['country'])?'class="hidden"':''?> name="city_born" id="city_born" data-input="city">
                <option value="" selected disabled>Distrito</option>
                <?php 
                if('Peru'==$current_member['country'] && !empty($current_member['state']) && !empty($current_member['county']) && !empty($current_member['city'])){
                    $options = \Turimet\Control\API::ubigeo_city($current_member['country'], $current_member['ubigeo']);
                    foreach($options as $id => $option){
                        printf('<option value="%1$s" data-id="%2$s"%3$s>%1$s</option>', $option, $id, $current_member['city']==$option?' selected':'' );
                    }
                }
                ?>
            </select>
        </li> -->

        <!-- <input type="hidden" name="ubigeo_born" id="ubigeo" data-input="ubigeo" value="<?=isset($current_member['ubigeo'])?$current_member['ubigeo']:''?>" /> -->
        <li>
            <?php wp_nonce_field('oportunity_filter', 'oportunity_filter_nonce'); ?>
            <input type="submit" value="Buscar" class="btn btn-primary" />
        </li>
    </ul>
</form>
<div class="oportunity-search__tags no-mobile">
    <?php $category_list = \Turimet\Control\API::category_list(); foreach( $category_list['data'] as $item ): ?>
    <a href="<?=home_url('oportunidades/filtrar/categoria:' . $item['name'] . '/')?>"><?=$item['title']?></a>
<?php endforeach; ?>
</div>
</div>
<!-- 
<script>
    var tuProfile = {
        ajax_url: '<?= admin_url("admin-ajax.php") ?>',
        nonce: '<?= wp_create_nonce("oportunity_filter") ?>'
    };
</script>

<script type="text/javascript">
    document.addEventListener('DOMContentLoaded', () => {


        const ubigeoModule = {
            init: function () {
                this.bindEvents();
            },

            bindEvents: function () {
                document.querySelectorAll('[data-input="country"], [data-input="region"], [data-input="province"]').forEach(select => {
                    select.addEventListener('change', this.fetchOptions.bind(this));
                });
            },
            fetchOptions: function (evt) {
                let type = evt.target.dataset.input;
                let input = '';
                try {
                    input = evt.target.options[evt.target.selectedIndex].dataset.id;
                } catch (ex) {
                    console.error("No se pudo obtener el data-id del select", ex);
                }

                const wrapper = evt.target.closest('form');
                const country = wrapper.querySelector('[data-input="country"]').value;

                if (country !== 'Peru') {
                    ['region', 'province', 'city'].forEach(id => {
                        let el = wrapper.querySelector(`[data-input="${id}"]`);
                        if (el) el.classList.add('hidden');
                        if (el) el.innerHTML = `<option value="" selected disabled>Seleccionar ${id.charAt(0).toUpperCase() + id.slice(1)}</option>`;
                    });
                    return;
                } else {
                    ['region', 'province', 'city'].forEach(id => {
                        let el = wrapper.querySelector(`[data-input="${id}"]`);
                        if (el) el.classList.remove('hidden');
                    });
                }

                let data = new FormData();
                data.append('nonce', tuProfile.nonce);
                data.append('action', 'turimet-ubigeo');
                data.append('type', type);
                data.append('input', input);
                data.append('country', country);

                fetch(tuProfile.ajax_url, {
                    method: 'POST',
                    body: data
                })
                .then(response => response.json())
                .then(result => {
                    console.log(result);
                    let output_id = (type === 'country') ? 'region' :
                    (type === 'region') ? 'province' :
                    (type === 'province') ? 'city' : false;
                    if (!output_id) return;

                    let elm = wrapper.querySelector(`[data-input="${output_id}"]`);
                    let label = (output_id === 'region') ? 'Departamento' :
                    (output_id === 'province') ? 'Provincia' :
                    (output_id === 'city') ? 'Distrito' : '';
                    


                    if (result.success && elm) {
                        const choicesInstances = new Map();

                      if (choicesInstances.has(elm)) {
    try {
        choicesInstances.get(elm).destroy();
    } catch (ex) {
        console.warn('Error al destruir Choices', ex);
    }
    choicesInstances.delete(elm);
}


                        let label = (output_id === 'region') ? 'Departamento' :
                        (output_id === 'province') ? 'Provincia' :
                        (output_id === 'city') ? 'Distrito' : '';

                        elm.innerHTML = `<option value="" selected disabled>${label}</option>`;
                        Object.keys(result.data.options).forEach(index => {
                            elm.innerHTML += `<option value="${result.data.options[index]}" data-id="${index}">${result.data.options[index]}</option>`;
                        });
                    }
                    else if (elm) {
                        elm.innerHTML = `<option value="" selected disabled>${label}</option>`;
                    }
                })
                .catch(error => {
                    console.error("Error en la petición AJAX:", error);
                });
            }

        };

        ubigeoModule.init();
    });

</script>
 -->