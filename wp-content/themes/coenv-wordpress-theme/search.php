<?php
/**
 * search.php
 *
 * The search results template.
 */
get_header();

$classes = array( 'template-search' );

if ( get_query_var('post_type') == 'post' ) {
	$classes[] = 'template-blog';
} else {
	$classes[] = 'template-page';
}

?>

	<section id="search" role="main" class="<?php echo implode( ' ', $classes ) ?>">

		<div class="layout-container">

			<nav id="secondary-nav" class="side-col">
				<ul>
					<li class="pagenav"><a href="#">Search results</a></li>
				</ul>
			</nav><!-- #secondary-nav.side-col -->

			<div class="main-col">

				<section class="article search-header">
					<header class="article__header">
						<h1 class="article__title">Search results for "<?php the_search_query() ?>"</h1>
					</header>
				</section>

				<div class="search-results">

					<?php if ( have_posts() ) : ?>

						<?php while ( have_posts() ) : the_post() ?>

							<?php if ( in_array( 'template-blog', $classes ) ) : ?>

								<?php get_template_part( 'partials/partial', 'article' ) ?>

							<?php else : ?>

								<?php get_template_part( 'partials/partial', 'article-search' ) ?>

							<?php endif ?>

						<?php endwhile ?>

					<?php endif ?>

				</div><!-- .search-results -->

				<footer class="pagination">
					<?php coenv_paginate() ?>
				</footer>

			</div><!-- .main-col -->

		</div><!-- .layout-container -->

	</section><!-- #page -->

<?php get_footer(); ?>