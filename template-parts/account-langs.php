<?php
extract($args);

$key = uniqid('s');
?>
<div class="ff__field-wrap--lang" data-input="repeater-item">
    <select name="language[<?=$key?>][language]" id="language_<?=$key?>_language" class="language_language_">
    <?php if(is_array(\Turimet\Control\API::language_list())) foreach(\Turimet\Control\API::language_list() as $item): ?>
        <option value="<?=$item['name']?>"<?=($lang['language']==$item['name'])?' selected':''?>><?=$item['title']?></option>
    <?php endforeach; ?>
    </select>
    <select name="language[<?=$key?>][level]" id="language_<?=$key?>_level">
    <?php if(is_array(\Turimet\Control\API::level_list())) foreach(\Turimet\Control\API::level_list() as $item): ?>
        <option value="<?=$item['name']?>"<?=($lang['level']==$item['name'])?' selected':''?>><?=$item['title']?></option>
    <?php endforeach; ?>
    </select>
    <a href="javascript:void(0);" data-action="remove"><span>Eliminar</span></a>
</div>