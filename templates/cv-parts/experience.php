<?php 
$experience_raw = $current_member['experience'] ?? '';
$experience = maybe_unserialize($experience_raw);

$experience = maybe_unserialize($current_member['experience'] ?? []);
if(!empty($experience)): ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">Experiencia</h3>
<div>
<?php 


foreach($experience as $item): 
    $dates[] = ucfirst(date_i18n('F Y', strtotime($item['date_initial'])));

    $date_final = '';
    if(1 == $item['currently_work']){
        $date_final = 'Actualmente';
    } elseif( !empty($item['date_final']) ) {
        $date_final = ucfirst(date_i18n('F Y', strtotime($item['date_final'])));
    }

    if( !empty($date_final) ){
        $dates[] = $date_final;
    }
?>
    <div style="margin-bottom:10px;">
        <b><?=$item['type_position']?><?php if(!empty($item['position'])): ?> en <?=$item['position']?><?php endif; ?></b><br/>
        <span style="color:#000;"><?=$item['company']?> | <?=implode(' - ', $dates)?></span><br/>
        <?php if(!empty($item['description'])): ?><ul>
            <li><?=$item['description']?></li>
        </ul><?php endif; ?>
    </div>
<?php endforeach; ?>
</div>
</div>
<?php endif; ?>