<?php  
/**
 * An individual article
 */
?>
<article id="post-<?php the_ID() ?>" <?php post_class( 'article' ) ?>>

	<header class="article__header">

		<?php if ( !is_page() ) : ?>
			<div class="sharing align-right">
				<ul>
					<li><a href="http://twitter.com/home?status=<?php echo wp_get_shortlink() ?> - <?php the_title() ?>"><i class="icon-twitter"></i><span>Twitter</span></a></li>
					<li><a href="http://www.facebook.com/sharer/sharer.php?s=100&p[url]=<?php echo wp_get_shortlink() ?>&p[images][0]=&p[title]=<?php the_title() ?>"><i class="icon-facebook"></i><span>Facebook</span></a></li>
				</ul>
			</div>
		<?php endif ?>

		<?php if ( is_page() || is_single() ) : ?>
			<h1 class="article__title"><?php the_title() ?></h1>
		<?php else : ?>
			<h1 class="article__title"><a href="<?php the_permalink() ?>" rel="bookmark"><?php the_title() ?></a></h1>
		<?php endif ?>

	</header>

	<section class="article__content">

		<?php the_content() ?>

	</section>

	<footer class="article__footer">

		<?php if ( !is_page() ) : ?>
			<div class="article__meta">
				<div>
				Posted <time class="article__time" datetime="<?php get_the_date( 'c' ) ?>"><?php echo get_the_date() ?></time>
				<?php $categories = get_the_category_list() ?>
				<?php if ( $categories ) : ?>
					<div class="article__categories">
					in: <?php echo $categories ?>
					</div>
				<?php endif ?> 
				</div>
			</div>
		<?php endif ?>

	</footer>

</article><!-- .article -->