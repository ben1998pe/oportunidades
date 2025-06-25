<?php
$member_keys = [];
if( isset($current_member['key1']) && !empty($current_member['key1']) ) $member_keys[] = $current_member['key1'];
if( isset($current_member['key2']) && !empty($current_member['key2']) ) $member_keys[] = $current_member['key2'];
if( isset($current_member['key3']) && !empty($current_member['key3']) ) $member_keys[] = $current_member['key3'];
$fechaNacimiento = '2007-04-03';
$fechaNacimientoObj = new DateTime($fechaNacimiento);
$hoy = new DateTime();

$edad = $hoy->diff($fechaNacimientoObj)->y;


?>

<table style="border-collapse:collapse;border:0;margin-bottom:0;width:100%;">
    <tbody>
        <!-- <td with="100%" style="padding-bottom:20px; text-align: center;"><?php if( filter_var($avatar, FILTER_VALIDATE_URL) ): ?><img src="<?=$avatar?>" alt="" style="border:0;width:100%; max-width: 220px;"><?php endif; ?></td> -->
    
    </tbody>
</table>
<!-- <table style="border-collapse:collapse;border:0;border-bottom:1px solid #ccc;margin-bottom:20px;width:100%;"> -->
<table style="border-collapse:collapse;border:0;;margin-bottom:0;width:100%;">
    <tbody>
       
        <td width="100%" style="padding-left:20px;padding-bottom:0;">
            <table style="border-collapse:collapse;border:0;width:100%;">
                <tbody>
                    <td width="40%" valign="top" style="text-transform: capitalize;"><?=$current_member['full_name']?></td>
                    <td width="40%" valign="top" style=""><?=$current_member['document_type']?>: <?=$current_member['document_number']?></td>
                    <td width="40%" valign="top" style="text-align: center;"><?=$edad?> años</td>

                </tbody>
            </table>
        </td>
    </tbody>
    <tbody>
       
        <td width="100%" style="padding-left:20px;padding-bottom:20px;">
            <table style="border-collapse:collapse;border:0;width:100%;">
                <tbody>
                
                    <td width="40%" valign="top">Cel. <?=$current_member['mobile']?></td>
                    <td width="80%" valign="top">Email: <a href="mailto:<?=$current_member['email']?>" target="_blank"><?=$current_member['email']?></a><br/></td>
                    <!-- <td width="40%" valign="top"></td> -->
                </tbody>
            </table>
    
            <?php if(!empty($member_keys)): ?><div style="margin-top:20px;color:#000; text-align: center;"><?php echo implode(' | ', $member_keys); ?></div><?php endif; ?>
        </td>
    </tbody>
</table>

<?php if(!empty($current_member['profile'])): ?>
<div>
<h3 style="color:#000; border-bottom: 2px solid #000; margin-bottom: 10px;text-transform: uppercase;">Sobre mi</h3>
<!-- <div><?=wpautop($current_member['profile'])?></div> -->
<div><?=$current_member['profile']?></div>
<br>
</div>
<?php endif; ?>