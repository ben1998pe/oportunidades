<?php
//extract($args);

$excerpt = get_post_meta( get_the_ID(), '_excerpt', true );
$host = get_post_meta( get_the_ID(), '_host', true );
$url = get_post_meta( get_the_ID(), '_url', true );
$date = get_post_meta( get_the_ID(), '_date_start', true );

$timestamp = strtotime($date);
// $formatted_date = date_i18n('d \d\e F \d\e Y', $timestamp);
$formatted_date = !empty($date) ? date_i18n('d \d\e F \d\e Y', strtotime($date)) : '';

?>
<article class="training-entry">
    <?php if( has_post_thumbnail() ): ?>
    <figure class="fig-cover training-entry__thumb">
        <img src="<?=get_the_post_thumbnail_url()?>" alt="<?=esc_attr(get_the_title())?>"/>
    </figure>
    <?php endif; ?>
    <div class="training-entry__body">
        <h4 class="training-entry__title"><a href="<?php the_permalink(); ?>"><?php the_title(); ?></a></h4>
        <?php if( !empty($host) ): ?><div class="training-entry__author">Docente: <span><?=$host?></span></div><?php endif; ?>
        <div class="training-entry__datetime"><strong>Fecha de inicio:</strong> <span>
            <?= !empty($formatted_date) ? $formatted_date : 'A elección' ?>
        </span></div>
        <div class="training-entry__content"><?=$excerpt?></div>
        <div class="event-entry__buttons">
            <a href="<?php the_permalink(); ?>" target="_blank" class="btn btn-secondary pink">Ver más</a>
        </div>
    </div>
</article>