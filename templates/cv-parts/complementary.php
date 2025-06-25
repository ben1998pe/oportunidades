<?php 

$complementary_studies = maybe_unserialize($current_member['complementary_studies'] ?? []);

if(!empty($complementary_studies)):  ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">FORMACIÓN COMPLEMENTARIA
</h3>
<ul>
<?php 
foreach($complementary_studies as $item): 
?>
    <li style="margin-bottom:10px;">
        <b><?=$item['type_course']?><?php if(!empty($item['course'])): ?> de <?=$item['course']?><?php endif; ?></b><br/>
        <span style="color:#000;"><?=$item['institution']?> | <?=$item['year']?></span><br/>
    </li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if(is_array($current_member['skills']) && !empty($current_member['skills'])): ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">HERRAMIENTAS - SOFTWARE / IDIOMAS / HABILIDADES</h3>
<ul>
<?php 
foreach($current_member['skills'] as $item): 
?>
    <li><?=$item['skill']?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>

<?php if(is_array($current_member['language']) && !empty($current_member['language'])): ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">Idiomas:</h3>
<ul>
<?php 
foreach($current_member['language'] as $item): 
?>
    <li><?=$item['language']?>: <?=$item['level']?></li>
<?php endforeach; ?>
</ul>
</div>
<?php endif; ?>
<?php if( !empty($current_member['linkedin']) || !empty($current_member['facebook'])  || !empty($current_member['instagram']) || !empty($current_member['twitter']) || !empty($current_member['youtube']) || !empty($current_member['tiktok'])): ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px; text-transform: uppercase;">Redes sociales:</h3>
<ul>
   <?php if (!empty($current_member['linkedin'])): ?>
        <li>Linkedin: <a target="_blank" href="https://linkedin.com/<?=$current_member['linkedin']?>">https://linkedin.com/<?=$current_member['linkedin']?></a></li>
   <?php endif ?>
   <?php if (!empty($current_member['facebook'])): ?>
        <li>Facebook: <a target="_blank" href="https://facebook.com/<?=$current_member['facebook']?>">https://facebook.com/<?=$current_member['facebook']?></a></li>
   <?php endif ?>
   <?php if (!empty($current_member['instagram'])): ?>
        <li>Instagram: <a target="_blank" href="https://instagram.com/<?=$current_member['instagram']?>">https://instagram.com/<?=$current_member['instagram']?></a></li>
   <?php endif ?>
   <?php if (!empty($current_member['twitter'])): ?>
        <li>Twitter: <a target="_blank" href="https://twitter.com/<?=$current_member['twitter']?>">https://twitter.com/<?=$current_member['twitter']?></a></li>
   <?php endif ?>
    <?php if (!empty($current_member['youtube'])): ?>
        <li>Youtube: <a target="_blank" href="https://youtube.com/<?=$current_member['youtube']?>">https://youtube.com/<?=$current_member['youtube']?></a></li>
   <?php endif ?>
    <?php if (!empty($current_member['tiktok'])): ?>
        <li>Tiktok: <a target="_blank" href="https://tiktok.com/<?=$current_member['tiktok']?>">https://tiktok.com/<?=$current_member['tiktok']?></a></li>
   <?php endif ?>
</ul>
</div>
<?php endif; ?>

