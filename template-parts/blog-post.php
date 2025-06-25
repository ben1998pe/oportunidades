<article id="post-<?php the_ID(); ?>" class="blog-post">
	<header class="entry-header">
    <?php the_title( '<h1 class="entry-title"><a href="' . esc_url( get_permalink() ) . '" rel="bookmark">', '</a></h1>' ); ?>
    <?php if ( has_post_thumbnail() ) { ?>
    <figure class="blog-post-image">
            <a href="<?php the_permalink(); ?>">
            <img src="<?php echo get_the_post_thumbnail_url(); ?>" loading="lazy" alt="<?php echo esc_attr( get_the_title() ); ?>" />
        </a>
    </figure>
    <?php } ?>
	</header><!-- .entry-header -->

	<div class="entry-content">
        <?php the_excerpt(); ?>
        <a href="<?php the_permalink(); ?>" class="button button-main mt20">Ver más</a>
	</div><!-- .entry-content -->
</article><!-- #post-## -->
