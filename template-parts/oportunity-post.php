<?php
    $item = $args['item'];
    $blank = isset($args['blank']) ? $args['blank'] : false;
    $item['created_at'] = \Turimet\Control\API::format_datetime($item['creation']);
   
?>

<div class="oplist__item" data-id="<?=$item['name']?>">
    <figure class="fig-contain oplist__image">
        <a href="<?=home_url('/oportunidades/empresa/company/'.$item['name'].'/')?>"<?=$blank?' target="_blank"':''?>>
            <img src="#" alt="" />
        </a>
    </figure>
    <div class="oplist__main">
        <div class="oplist__main--company"><a href="<?=home_url('/oportunidades/empresa/company')?>" target="_blank">company</a></div>
        <h3 class="oplist__main--job"><a href="<?=home_url('/oportunidades/empresa/company'.$item['name'].'/')?>"<?=$blank?' target="_blank"':''?>><?=$item['job']?></a></h3>
        <div class="oplist__main--country"><div class="iti__flag iti__<?=$item['code']?>"></div> <?=$item['country']?></div>
    </div>
    <div class="oplist__side">
        <ul>
            <li class="oplist__side--creation"><?=$item['created_at']?></li>
            <li class="oplist__side--modality"><?=$item['modality']?></li>
        </ul>
    </div>
</div>