<?php
/**
 * search.php
 *
 * The search results template.
 */
get_header();

if ( get_query_var('post_type') == 'post' ) {

}

?>

	<section id="search" role="main" class="page-area full-width">

		<div class="layout-container">

			<nav id="secondary-nav" class="side-col">
				<ul>
					<li class="pagenav"><a href="#">Search results</a></li>
				</ul>
			</nav><!-- #secondary-nav.side-col -->

			<div class="main-col">

				<section class="article">
					<header class="article-header">
						<h1>Search results for "<?php the_search_query() ?>"</h1>
					</header>
				</section>

				<div class="search-results">

					<?php if ( have_posts() ) : ?>

						<?php while ( have_posts() ) : the_post() ?>

							<?php// if ( get_post_type() === 'post' ) continue; // filter for pages only ?>

							<section class="article">

								<header class="article-header">

									<?php echo coenv_breadcrumbs() ?>

									<h1><a href="<?php the_permalink() ?>"><?php the_title() ?></a></h1>

								</header><!-- .article-header -->

								<section class="article-content article-excerpt">

									<?php the_excerpt() ?>

								</section><!-- .article-content -->

								<footer class="article-footer">

								</footer><!-- .article-footer -->

							</section><!-- .article -->

						<?php endwhile ?>

					<?php endif ?>

				</div><!-- .search-results -->

				<footer class="pagination">
					<?php coenv_paginate() ?>
				</footer>

			</div><!-- .main-col -->

			<div class="side-col">

				<?php //if (!function_exists('dynamic_sidebar') || !dynamic_sidebar('sidebar-search')): endif; ?>
				<?php get_sidebar() ?>

			</div><!-- .side-col -->

		</div><!-- .layout-container -->

	</section><!-- #page -->

<?php get_footer(); ?>