<?php
/**
 * Template Name: Top level non-menu page
 *
 *
 * Other top-level pages that don't appear in the main menu (like "intranet") should use wp_list_pages in their own custom page template
 * wp_list_pages(array(
 *	'child_of' => $ancestor['id'],
 *	'depth' => 3,
 *	'title_li' => '<a href="' . $ancestor['permalink'] . '">' . $ancestor['title'] . '</a>',
 *	'link_after' => '<i class="icon-arrow-right"></i>'
 * )); ]
 */
get_header();

$ancestor_id = coenv_get_ancestor();

$ancestor = array(
	'id' => $ancestor_id,
	'permalink' => get_permalink( $ancestor_id ),
	'title' => get_the_title( $ancestor_id )
);

$banner = coenv_banner();
?>

	<section id="page" role="main" class="page-area">

		<div class="container">

			<nav id="secondary-nav" class="side-col">
				<?php 
					wp_list_pages(array(
						'child_of' => $ancestor['id'],
						'depth' => 3,
						'title_li' => '<a href="' . $ancestor['permalink'] . '">' . $ancestor['title'] . '</a>',
						'link_after' => '<i class="icon-arrow-right"></i>'
					)); 
				?>
			</nav><!-- #secondary-nav.side-col -->

			<div class="main-col">

				<?php if ( isset( $banner ) && isset( $banner['caption'] ) && !empty( $banner['caption'] ) ) : ?>
					<p class="banner-caption">Banner image: <?php echo $banner['caption'] ?></p>
				<?php endif ?>

				<?php if ( have_posts() ) : ?>

					<?php while ( have_posts() ) : the_post() ?>

						<section class="entry">

							<header class="entry-header">

								<h1><?php the_title() ?></h1>

							</header><!-- .entry-header -->

							<section class="entry-content">

								<?php the_content() ?>

							</section><!-- .entry-content -->

							<footer class="entry-footer">

							</footer><!-- .entry-footer -->

						</section><!-- .entry -->

					<?php endwhile ?>

				<?php endif ?>

			</div><!-- .main-col -->

			<div class="side-col">
				<?php get_sidebar() ?>
			</div><!-- .side-col -->

		</div><!-- .container -->

	</section><!-- #page -->

<?php get_footer() ?>