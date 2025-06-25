<?php
extract($args);

$key = uniqid('s');
$title_parts = [];

if(!empty($study['grade'])) $title_parts[] = $study['grade'];
if(!empty($study['specialty'])) $title_parts[] = $study['specialty'];

$title = implode(' - ',$title_parts);

?>
<div class="ff__field-group" data-key="<?=$key?>">
    <h3 class="ff__field-group--title" data-input="group-title"><span><?=$title?></span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
    <div class="ff__field-group--content">
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_grade">Grado de instrucción</label>
                <small>Selecciona tu grado académico.</small>
            </div>
            <div class="ff__subfield--field">
                <select name="studies[<?=$key?>][grade]" id="studies_<?=$key?>_grade" data-input="grade" class="studies_grade_">
                    <option value="" selected disabled>Selecciona tu grado de instrucción</option>
                <?php if(is_array(\Turimet\Control\API::grade_list())) foreach(\Turimet\Control\API::grade_list() as $item): ?>
                    <option value="<?=$item['name']?>"<?=($study['grade']==$item['name'])?' selected':''?>><?=$item['title']?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_specialty">Especialidad</label>
                <small>Escribe el nombre de tu especialidad/profesión.</small>
            </div>
            <div class="ff__subfield--field">
                <select name="studies[<?=$key?>][specialty]" id="studies_<?=$key?>_specialty" data-input="specialty" class="studies_specialty_">
                    <option value="" selected disabled>Selecciona tu especialidad</option>
                <?php if(is_array(\Turimet\Control\API::specialty_list())) foreach(\Turimet\Control\API::specialty_list() as $item): ?>
                    <option value="<?=$item['name']?>"<?=($study['specialty']==$item['name'])?' selected':''?>><?=$item['title']?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_institution">Centro de estudios</label>
                <small>Escribe el nombre de tu centro de estudios.</small>
            </div>
            <div class="ff__subfield--field">
                <select name="studies[<?=$key?>][institution]" id="studies_<?=$key?>_institution" class="studies__institution_">
                    <option value="" selected disabled>Selecciona el centro de estudios</option>
                <?php if(is_array(\Turimet\Control\API::institution_list())) foreach(\Turimet\Control\API::institution_list() as $item): ?>
                    <option value="<?=$item['name']?>"<?=($study['institution']==$item['name'])?' selected':''?>><?=$item['title']?></option>
                <?php endforeach; ?>
                </select>
            </div>
        </div>
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_year_start">Año de ingreso y egreso</label>
                <small>Selecciona los años de ingreso y egreso de estudios.</small>
            </div>
            <div class="ff__subfield--field">
                <div class="ff__subfield--field-25-25-50">
                    <select name="studies[<?=$key?>][year_start]" id="studies_<?=$key?>_year_start" class="studies_year_start_">
                        <option value="" selected disabled>Ingreso</option>
                    <?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
                        <option value="<?=$item?>"<?=($study['year_start']==$item)?' selected':''?>><?=$item?></option>
                    <?php endforeach; ?>
                    </select>
                    <select name="studies[<?=$key?>][year_end]" id="studies_<?=$key?>_year_end"<?=($study['study_now']=='1')?' disabled':''?> class="studies_year_end_">
                        <option value="" selected disabled>Egreso</option>
                    <?php $years = array_reverse(range(1970, date_i18n('Y'))); foreach($years as $item): ?>
                        <option value="<?=$item?>"<?=($study['year_end']==$item)?' selected':''?><?=(intval($study['year_start'])>$item)?' disabled class="hidden"':''?>><?=$item?></option>
                    <?php endforeach; ?>
                    </select>
                    <label class="checkbox-label"><input type="checkbox"<?=($study['study_now']=='1')?' checked':''?> name="studies[<?=$key?>][study_now]" id="experience_<?=$key?>_study_now"> <span>Estudiando actualmente</span></label>
                </div>
            </div>
        </div>
    </div>
</div>