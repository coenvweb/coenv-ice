<?php
/**
 * Template to show single faculty member
 */
// Redirect to /coenv-faculty/ if not logged in
if ( !is_user_logged_in() ) {
	wp_redirect( get_bloginfo('url') . '/coenv-faculty/' );
}


get_header();

$obj = get_queried_object();

$member = $coenv_member_api->setup_faculty_attributes( $obj->ID, 'heavy' );

// set up member tab nav
$member_tabs = array();
if ( !empty( $member['pullquote'] ) || !empty( $member['biography'] ) ) {
	$member_tabs[] = array(
		'data-tab' => 'member-tab-bio',
		'title' => 'About ' . $member['full_name'],
		'active' => true
	);
}

if ( !empty( $member['publications'] ) ) {
	$member_tabs[] = array(
		'data-tab' => 'member-tab-publications',
		'title' => 'Publications'
	);
}

if ( !empty( $member['stories'] ) ) {
	$member_tabs[] = array(
		'data-tab' => 'member-tab-stories',
		'title' => 'Stories from CoEnv Currents'
	);
}

?>

	<section id="faculty" role="main" class="single-faculty">

		<div class="container">

			<nav class="faculty-header">

				<div class="upper">
					<a class="back-to-faculty" href="<?php bloginfo('url') ?>/faculty/"><i class="icon-faculty-grid-alt-2"></i> Search and filter faculty</a>
				</div>

				<div class="lower">
					<!--<h1><a href="<?php bloginfo('url') ?>/faculty/">Faculty</a></h1>-->
					<h1>Faculty</h1>
				</div>

			</nav><!-- .faculty-header -->

			<header class="member-header">

				<div class="member-image">

					<?php if ( !empty( $member['images']['large'] ) ) : ?>

						<img src="<?php echo $member['images']['medium']['url'] ?>" alt="Portrait of <?php echo $member['full_name'] ?>" />

					<?php endif ?>

				</div><!-- .member-image -->

				<div class="member-vcard" style="background-color: <?php echo $member['units'][0]['color'] ?>;">

					<div class="member-vcard-inner">
					
						<h1 class="member-name"><?php echo $member['full_name'] ?></h1>

						<?php if ( !empty( $member['academic_title'] ) ) : ?>
							<h2 class="member-academic-title"><?php echo $member['academic_title'] ?></h2>
						<?php endif ?>

						<?php if ( !empty( $member['units'] ) ) : ?>
							<h3 class="member-unit"><a href="<?php echo $member['units'][0]['url'] ?>"><?php echo $member['units'][0]['name'] ?></a></h3>
						<?php endif ?>

						<?php if ( !empty( $member['themes'] ) ) : ?>

							<div class="member-themes">

								Working on: 

								<ul>

									<?php foreach ( $member['themes'] as $theme ) : ?>

										<li><a href="<?php echo $theme['url'] ?>"><?php echo $theme['name'] ?></a></li>

									<?php endforeach ?>

								</ul>

							</div><!-- .member-themes -->

						<?php endif ?>

					</div><!-- .inner -->

				</div><!-- .member-vcard -->

			</header><!-- .member-header -->

		</div><!-- .container -->

		<div class="profile-bottom template-page">

			<div class="container">

				<div class="side-col">

					<?php if ( count( $member_tabs ) > 1 ) : ?>

						<nav id="secondary-nav">
							<ul id="member-tab-nav">
								<li class="pagenav">
									<ul>
										<?php foreach ( $member_tabs as $tab ) : ?>
											<li<?php if ( $tab['active'] ) echo ' class="active-tab"' ?>><a data-tab="<?php echo $tab['data-tab'] ?>" href="#"><?php echo $tab['title'] ?></a></li>
										<?php endforeach ?>
									</ul>
								</li>
										
							</ul>
						</nav><!-- #secondary-nav -->

					<?php endif ?>

					<?php if ( !empty( $member['contact_links'] ) ) : ?>

						<ul class="member-contact-links">

							<?php foreach ( $member['contact_links'] as $link ) : ?>

								<li>

									<?php 
									if ( $link['type'] === 'email' ) {
										$link['url'] = 'mailto:' . $link['url'];
									}
									?>

									<?php if ( !empty( $link['url'] ) ) : ?><a href="<?php echo $link['url'] ?>"><?php endif ?>

										<i class="icon-contact-link-<?php echo $link['type'] ?>"></i><?php echo $link['title'] ?>

									<?php if ( !empty( $link['url'] ) ) : ?></a><?php endif ?>

								</li>

							<?php endforeach ?>

						</ul>

					<?php endif ?>

				</div><!-- .side-col -->

				<div class="main-col">

					<section class="member-tabs">

						<article class="member-tab member-tab-bio active-tab">

							<div class="entry-content">

								<?php if ( !empty( $member['pullquote'] ) ) : ?>

									<blockquote class="member-pullquote">

										<p><?php echo $member['pullquote'] ?></p>

									</blockquote>

								<?php endif ?>

								<?php if ( !empty( $member['biography'] ) ) : ?>

									<div class="member-bio">
										<?php echo $member['biography'] ?>
									</div>

								<?php endif ?>

							</div><!-- .entry-content -->
						</article>

						<?php if ( !empty( $member['publications'] ) ) : ?>

							<article class="member-tab member-tab-publications">

								<div class="entry-content">

									<h2>Publications</h2>

									<table class="publications-table">

										<tr>
											<th class="title">Title</th>
											<th class="source">Source</th>
										</tr>

										<?php foreach ( $member['publications'] as $pub ) : ?>

											<tr>
												<td class="title">
													<?php if ( isset( $pub['link'] ) ) : ?>
														<a href="<?php echo $pub['link'] ?>"><?php echo $pub['title'] ?></a>
													<?php else : ?>
														<?php echo $pub['title'] ?>
													<?php endif ?>
												</td>
												<td class="source">
													<?php echo $pub['source'] ?>
												</td>
											</tr>

										<?php endforeach ?>

									</table>

								</div><!-- .entry-content -->

							</article>

						<?php endif ?>

						<?php if ( !empty( $member['stories'] ) ) : ?>

							<article class="member-tab member-tab-stories">

								<header class="entry-header">

									<h1>Stories</h1>

								</header><!-- .entry-header -->

								<div class="entry-content">

								</div><!-- .entry-content -->

							</article>

						<?php endif ?>

					</ul><!-- .member-tabs -->

				</div><!-- .main-col -->

				<div class="side-col">

					<?php 
						the_widget( 'CoEnv_Widget_Faculty', array(
							//'location' => 'external', 
							'id' => 'coenv_faculty_widget-' . str_replace( ' ', '-', preg_replace("/[^A-Za-z0-9 ]/", '', $member['full_name'] ) ),
							'theme' => $member['themes'][0]['slug'],
							'unit' => 'all'
						) ) 
					?>

				</div><!-- .side-col -->

			</div><!-- .container -->

		</div><!-- .profile-bottom -->

	</section><!-- #faculty.single-faculty -->










<?php get_footer() ?>