<?php  
/**
 * Home page feature
 */
$feature_type = get_field_object('feature_type');
$feature_type = $feature_type['choices'][get_field('feature_type')];

$content_link = get_field('content_link');
$content_link = $content_link[0];

$feature_image = wp_get_attachment_image_src( get_field('feature_image'), 'medium' );
$feature_caption = get_post( get_field('feature_image') );

$unit = get_field('unit');
$unit_color = coenv_unit_color($unit);

$feature = array(
	'type' => $feature_type,
	'label' => get_field('feature_label'),
	'content_link' => array(
		'title' => $content_link['title'],
		'url' => get_permalink($content_link['content_link']->ID)
	),
	'image' => array(
		'url' => $feature_image[0],
		'caption' => $feature_caption->post_excerpt
	),
	'color' => $unit_color != '' ? $unit_color : get_field('color_picker')
);

?>
<article class="feature loading">

	<div class="feature-image" style="background-image: url(<?php echo $feature['image']['url']  ?>);">
		<p class="feature-image-caption"><?php echo $feature['image']['caption'] ?></p>
	</div>

	<div class="feature-info-container">

		<div class="feature-info" style="background-color: <?php echo $feature['color'] ?>">

			<div class="feature-type">

				<?php if ( !empty( $feature['label'] ) ) : ?>

					<?php echo $feature['label'] ?>

				<?php else : ?>

					<?php echo $feature['type'] ?>

				<?php endif ?>

			</div><!-- .feature-type -->

			<div class="feature-content">

				<?php if ( get_field('feature_type') == 'college-news' || get_field('feature_type') == 'basic' ) : ?>

					<h1><?php the_field('headline') ?></h1>

					<?php if ( get_field('teaser') ) : ?>
						<p><?php the_field('teaser') ?></p>
					<?php endif ?>

					<?php if ( !empty( $feature['content_link'] ) ) : ?>
						<a class="button feature-button" href="<?php echo $feature['content_link']['url'] ?>"><?php echo $feature['content_link']['title'] ?></a>
					<?php endif ?>

					<?php if ( !empty( $feature['image']['caption'] ) ) : ?>
						<p class="feature-caption"><?php echo $feature['image']['caption'] ?></p>
					<?php endif ?>

				<?php  endif ?>

			</div><!-- .feature-content -->

		</div><!-- .feature-info -->

	</div><!-- .feature-info-container -->

</article><!-- .feature -->