<?php
/**
 * 404.php
 *
 * The 404 not found template
 */
get_header();

?>

	<section id="404" role="main" class="page-area">

		<div class="container">

				<section class="entry">

					<header class="entry-header">
						<h1>Page not found</h1>
					</header>

					<div class="entry-content">
						
						<p>The page you requested could not be found.</p>

						<?php get_search_form() ?>

					</div><!-- .entry-content -->

				</section><!-- .entry -->

		</div><!-- .container -->

	</section><!-- #404 -->

<?php get_footer(); ?>