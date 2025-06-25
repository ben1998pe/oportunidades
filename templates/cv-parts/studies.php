<?php 
$studies = maybe_unserialize($current_member['studies'] ?? []);


if(!empty($studies)):  ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">FORMACIÓN ACADÉMICA</h3>
<ul>
<?php foreach($studies as $item): ?>
    <li style="margin-bottom:10px;">
        <b><?=$item['grade']?> en <?=$item['specialty']?></b><br/>
        <?=$item['institution']?><br/>
        <?=$item['year_start']?> - <?=($item['study_now']=='1')?'Actualmente':$item['year_end']?>
</li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>