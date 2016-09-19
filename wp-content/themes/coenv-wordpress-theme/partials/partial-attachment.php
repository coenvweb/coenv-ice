<?php $metadata = wp_get_attachment_metadata() ?>

<?php print '<pre>';
print_r($metadata);
print '</pre>'; ?>

<div class="entry-content">
	<?php echo wp_get_attachment_image( $post->ID, $attachment_size ) ?>
</div>