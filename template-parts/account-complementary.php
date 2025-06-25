<?php
extract($args);

$key = uniqid('s');
$title_parts = [];

if(!empty($study['type_course'])) $title_parts[] = $study['type_course'];
if(!empty($study['course'])) $title_parts[] = $study['course'];

$title = implode(' - ',$title_parts);

?>
<div class="ff__field-group" data-key="<?=$key?>">
    <h3 class="ff__field-group--title" data-input="group-title"><span><?=$title?></span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
    <div class="ff__field-group--content">
        <div class="ff__subfield">
            <select name="complementary_studies[<?=$key?>][type_course]" id="complementary_<?=$key?>_type" data-input="type_course">
                <option value="" selected disabled>Curso/Diplomado</option>
            <?php if(is_array(\Turimet\Control\API::type_course_list())) foreach(\Turimet\Control\API::type_course_list() as $item): ?>
                <option value="<?=$item['name']?>"<?=($study['type_course']==$item['name'])?' selected':''?>><?=$item['title']?></option>
            <?php endforeach; ?>
            </select>
            <input type="text" value="<?=esc_attr($study['course'])?>" name="complementary_studies[<?=$key?>][course]" id="complementary_<?=$key?>_course" data-validate="text" placeholder="Nombre de la especialidad" data-input="complementary_course">
            <input type="text" value="<?=esc_attr($study['hours'])?>" name="complementary_studies[<?=$key?>][hours]" maxlength="3" data-validate="int" id="complementary_<?=$key?>_hours" placeholder="Nro de horas">
            <input type="text" value="<?=esc_attr($study['institution'])?>" name="complementary_studies[<?=$key?>][institution]" id="complementary_<?=$key?>_institution" data-validate="text" placeholder="Institución u Organización">
            <select name="complementary_studies[<?=$key?>][year]" id="complementary_<?=$key?>_year">
                <option value="" selected disabled>Año</option>
            <?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
                <option value="<?=$item?>"<?=($study['year']==$item)?' selected':''?>><?=$item?></option>
            <?php endforeach; ?>
            </select>
        </div>
    </div>
</div>