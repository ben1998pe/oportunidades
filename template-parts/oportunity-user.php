<?php
    $item = $args['item'];
    $item['created_at'] = \Turimet\Control\API::format_datetime($item['creation']);
    $company = \Turimet\Control\API::get_company($item['company']);
    
?>
<div class="oplist__item" data-id="<?=$item['name']?>">
    <figure class="fig-contain oplist__image">
        <a href="<?=home_url('/oportunidades/empresa/' . $item['company'] . '/'.$item['name'].'/')?>" target="_blank">
            <img src="<?=$item['company_image']?>" alt="" />
        </a>
    </figure>
    <div class="oplist__main">
        <div class="oplist__main--company"><?=@$company['company']?></div>
        <h3 class="oplist__main--job"><?=$item['job']?></h3>
        <div class="oplist__main--country"><div class="iti__flag iti__<?=$item['code']?>"></div> <?=$item['country']?></div>
    </div>
    <div class="oplist__side">
        <ul>
            <li class="oplist__side--modality"><?=$item['status']?></li>    
            <li class="oplist__side--creation"><?=$item['created_at']?></li>
        </ul>
    </div>
</div>