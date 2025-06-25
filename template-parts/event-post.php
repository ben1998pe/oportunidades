<?php
$excerpt = get_post_meta( get_the_ID(), '_excerpt', true );
$host = get_post_meta( get_the_ID(), '_host', true );
$url = get_post_meta( get_the_ID(), '_url', true );
$date = get_post_meta( get_the_ID(), '_date', true );
$date_end = get_post_meta( get_the_ID(), '_date_end', true );

$timestamp = strtotime($date);
$date_output = date_i18n('d \d\e F \d\e Y', $timestamp);

if( !empty($date) && !empty($date_end) && preg_match('/^([0-9]{4})-([0-9]{2})-([0-9]{2})$/m', $date_end) ){
    $date = date('d/m/Y', strtotime($date));
    $date_end = date('d/m/Y', strtotime($date_end));
    $date_output = sprintf('Del %s al %s', $date, $date_end);
}
?>
<article class="event-entry">
    <?php if( has_post_thumbnail() ): ?>
    <figure class="fig-cover event-entry__thumb">
        <img src="<?=get_the_post_thumbnail_url()?>" alt="<?=esc_attr(get_the_title())?>"/>
    </figure>
    <?php endif; ?>
    <div class="event-entry__body">
        <h4 class="event-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <?php if( !empty($host) ): ?><div class="event-entry__host">Por: <span><?=$host?></span></div><?php endif; ?>
        <div class="event-entry__datetime"><strong>Fecha:</strong> <span><?=$date_output?></span></div>
        <div class="event-entry__content"><?=$excerpt?></div>
        <ul class="event-entry__meta">
            <li><strong>Tipo de evento:</strong> <span><?php echo get_the_term_list(get_the_ID(), 'event-type'); ?></span></li>
            <li><strong>Categoría:</strong> <span><?php echo get_the_term_list(get_the_ID(), 'event-category'); ?></span></li>
        </ul>
        <div class="event-entry__buttons">
            <?php if( filter_var($url, FILTER_VALIDATE_URL) ): ?><a href="<?=$url?>" target="blank" rel="nofollow" class="btn btn-primary">Sitio del evento</a><?php endif; ?>
            <a href="<?php the_permalink(); ?>" target="_blank" class="btn btn-secondary pink">Ver detalles</a>
        </div>
    </div>
</article>