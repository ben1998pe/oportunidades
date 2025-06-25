<?php
extract($args);

$key = uniqid('s');
$title_parts = [];

if(!empty($exp['position'])) $title_parts[] = $exp['position'];
if(!empty($exp['company'])) $title_parts[] = $exp['company'];

$title = implode(' - ',$title_parts);

?>
<div class="ff__field-group" data-key="<?=$key?>">
    <h3 class="ff__field-group--title" data-input="group-title"><span><?=$title?></span> <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a></h3>
    <div class="ff__field-group--content">
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="experience_<?=$key?>_grade">Cargo, Rol y Tiempo</label>
                <small>Ingresa el nombre de tu cargo, fecha de inicio y de cierre, si corresponde.</small>
            </div>
            <div class="ff__subfield--field">
                <div class="ff__subfield--field-exp">
                    <select name="experience[<?=$key?>][type_position]" id="experience_<?=$key?>_type_position" class="experiencepp">
                        <option value="" selected disabled>Tipo de cargo</option>
                    <?php if(is_array(\Turimet\Control\API::type_position_list())) foreach(\Turimet\Control\API::type_position_list() as $item): ?>
                        <option value="<?=$item['name']?>"<?=($exp['type_position']==$item['name'])?' selected':''?>><?=$item['title']?></option>
                    <?php endforeach; ?>
                    </select>
<?php
    $currentDate = current_time('mysql');
    if( '1'==$exp['currently_work'] && !empty($exp['date_final']) ){
        $currentDate = $exp['date_final'];
    }

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


    $currentDate = current_time('mysql');
    $dateTime = new DateTime($currentDate);
    $maxDate = $dateTime->format('Y-m-d');
	//$toDate = $dateTime->format('Y-m-d');

    
?>
                    <input type="text" value="<?=esc_attr($exp['position'])?>" data-validate="text" maxlength="50" name="experience[<?=$key?>][position]" id="experience_<?=$key?>_position" placeholder="Ingresa el nombre del cargo" data-input="position">
                    <input type="date" max="<?=$maxDate?>" value="<?=esc_attr($exp['date_initial'])?>" name="experience[<?=$key?>][date_initial]" id="experience_<?=$key?>_date_initial" placeholder="Fecha de inicio"/>
                    <input type="date" min="<?=$dateInitial->format('Y-m-d')?>" max="<?=$maxDate?>" value="<?=esc_attr($exp['date_final'])?>" name="experience[<?=$key?>][date_final]" id="experience_<?=$key?>_date_final" placeholder="Fecha de cierre" <?=($exp['currently_work']=='1')?' disabled':''?>/>
                    <label class="checkbox-label"><input type="checkbox"<?=($exp['currently_work']=='1')?' checked':''?> name="experience[<?=$key?>][currently_work]" id="experience_<?=$key?>_currently_work"> <span>Trabajo actualmente</span></label>
                    <div class="result-div"><span>Tiempo en el cargo:</span> <input type="text" value="<?=$resultado?>" data-date="<?=$toDate?>" readonly id="experience_<?=$key?>_time"></div>
                </div>
            </div>
        </div>
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_specialty">Organización y sector</label>
                <small>Ingresa nombre de la empresa u organizacion.</small>
            </div>
            <div class="ff__subfield--field">
                <div class="ff__subfield--field-exp2">
                    <input type="text" value="<?=esc_attr($exp['company'])?>" data-validate="text" maxlength="60" name="experience[<?=$key?>][company]" id="experience_<?=$key?>_company" placeholder="Ingresa el nombre de la empresa u organización" data-input="company">
                    <select name="experience[<?=$key?>][sector]" id="experience_<?=$key?>_sector">
                        <option value="" selected disabled>Selecciona el sector</option>
                    <?php if(is_array(\Turimet\Control\API::sector_list(true))) foreach(\Turimet\Control\API::sector_list(true) as $item): ?>
                        <option value="<?=$item['name']?>"<?=($exp['sector']==$item['name'])?' selected':''?>><?=$item['title']?></option>
                    <?php endforeach; ?>
                    </select>
                    <input type="text" value="<?=esc_attr('0'==$exp['salary'] ? '' : $exp['salary'])?>" maxlength="12" name="experience[<?=$key?>][salary]" id="experience_<?=$key?>_salary" placeholder="Ingresa el salario que percibiste en este puesto" <?=($exp['not_share_salary']=='1')?' disabled':''?>>
                    <label class="checkbox-label"><input type="checkbox"<?=($exp['not_share_salary']=='1')?' checked':''?> name="experience[<?=$key?>][not_share_salary]" id="experience_<?=$key?>_not_share_salary"> <span>No deseo compartir esta información</span></label>
                </div>
            </div>
        </div>
        <div class="ff__subfield">
            <div class="ff__subfield--caption">
                <label for="studies_<?=$key?>_institution">Descripción de experiencia laboral</label>
                <small>Describe brevemente las funciones y logros más importantes desarrollados.</small>
            </div>
            <div class="ff__subfield--field">
                <textarea name="experience[<?=$key?>][description]" maxlength="1200" id="experience_<?=$key?>_description" rows="4" data-validate="text" placeholder="Ingresar descripción"><?=esc_textarea($exp['description'])?></textarea>
            </div>
        </div>
    </div>
</div>