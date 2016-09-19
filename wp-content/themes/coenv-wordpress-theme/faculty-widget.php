<?php
global $coenv_member_api;

// Set up related faculty demo
$related_faculty = array();
$all_faculty = $coenv_member_api->get_faculty();
shuffle($related_faculty);

$units = array();
$raw_units = get_terms(
	array('member_unit'),
	array(
		'hide_empty' => false
	)
);

if ( !empty( $raw_units ) ) {

	foreach ( $raw_units as $unit ) {

		$units[] = array(
			'term_id' => $unit->term_id,
			'name' => $unit->name,
			'slug' => $unit->slug,
			'color' => coenv_unit_color( $unit->term_id )
		);

	}
}

$count = 1;
foreach( $all_faculty as $af ) {

	$af['units'][0] = $units[array_rand( $units, 1 )];
	//$af['units'][0] = $units[3];
	$af['images'] = array( 
		'thumbnail' => array(
			'url' => get_template_directory_uri() . '/assets/img/faculty-test-images/' . $count . '.jpg'
		) 
	);

	$related_faculty[] = $af;

	if ( $count < 12 ) {
		$count++;
	} else {
		$count = 1;
	}
}

$themes = get_terms(
    array('member_theme'),
    array(
        'hide_empty' => false
    )
);
?>

<section class="coenv-fw">

	<?php $faculty = array_slice( $related_faculty, 0, 5 ); ?>
	<?php  

		$num = count( $faculty );
		$over = $num / 5;

		if ( $num > 5 ) {
			$uprounded = ceil( $over );
			$total = $uprounded * 5;
			$remain = $total % $num;
		} else {
			$remain = 5 - $num;
		}
	?>

	<header class="coenv-fw-section coenv-fw-header">

		<h1>
			<a href="<?php get_bloginfo('url') ?>/faculty/">
				Faculty
				<small>UW College of the Environment</small>
			</a>
		</h1>

	</header><!-- .coenv-fw-section.coenv-fw-header -->

	<div class="coenv-fw-section coenv-fw-feedback">

		<p><span class="coenv-fw-feedback-number"><?php echo count( $faculty ) ?></span> faculty working on <a href="#">Climate</a> in <a href="#">Earth &amp; Space Sciences</a></p>

	</div><!-- .coenv-fw-section.coenv-fw-feedback -->

	<ul class="coenv-fw-section coenv-fw-results">
		<?php foreach ( $faculty as $rf ) : ?>
			<li class="coenv-fw-member" style="background-color: <?php echo $rf['units'][0]['color'] ?>;">
				<a href="#" class="coenv-fw-member-inner">
					<img class="coenv-fw-member-image" src="<?php echo $rf['images']['thumbnail']['url'] ?>" />
					<p class="coenv-fw-member-name"><?php echo $rf['full_name'] ?></p>
				</a><!-- .coenv-fw-member-inner -->
			</li><!-- .coenv-fw-member -->
		<?php endforeach ?>

		<?php if ( $remain > 0 ) : ?>
			<li class="coenv-fw-member coenv-fw-view-all coenv-fw-span-<?php echo $remain ?>">
				<a href="#" class="inner">
					<p><i class="icon-grid"></i>View all</p>
				</a>
			</li>
		<?php else : ?>
			<li class="coenv-fw-member coenv-fw-view-all coenv-fw-span-5">
				<a href="#" class="inner">
					<p href="#"><i class="icon-grid"></i>View all</p>
				</a>
			</li>
		<?php endif ?>

	</ul>

</section><!-- .coenv-fw -->


