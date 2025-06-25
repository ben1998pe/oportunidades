<?php
$description = get_post_meta( get_the_ID(), '_description', true );
$type = get_post_meta( get_the_ID(), '_service_type', true );
$file_id = get_post_meta( get_the_ID(), '_file', true );
$file = false;


if( filter_var($file_id, FILTER_VALIDATE_INT) ) $file = wp_get_attachment_url($file_id);
?>
<article class="training-entry">
    <?php if( has_post_thumbnail() ): ?>
    <figure class="fig-cover training-entry__thumb">
        <img src="<?=get_the_post_thumbnail_url()?>" alt="<?=esc_attr(get_the_title())?>"/>
    </figure>
    <?php endif; ?>
    <div class="training-entry__body">
        <h4 class="training-entry__title c-blue"><?php the_title(); ?></h4>
        <div class="training-entry__datetime"><strong>Tipo de servicio:</strong> <span><?=$type?></span></div>
        <div class="training-entry__content"><?=$description?></div>
    <?php if( $file ): ?>
        <div class="event-entry__buttons">
            <?php $dowl = wp_get_attachment_url( $file_id ); ?>
            <!-- <a href="javascript:void(0);" data-action="brochure" data-id="<?=get_the_ID()?>" data-media="<?=base64_encode($file)?>" class="btn btn-secondary pink">Descargar brochure</a> -->
             <a href="<?php echo $dowl; ?>" download target="_blank" class="btn btn-secondary pink">Descargar brochure</a>
        </div>
    <?php endif; ?>
    </div>
</article>