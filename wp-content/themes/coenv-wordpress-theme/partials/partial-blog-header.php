<div id="blog-header" class="blog-header">

	<form role="search" method="get" class="search-form" action="<?php echo home_url( '/' ); ?>">
	  <div class="field-wrap">
	  	<input type="hidden" name="post_type" value="post" />
	    <input type="text" value="<?php echo get_search_query() ?>" name="s" id="s" placeholder="Search news" />
	    <button type="submit"><i class="icon-search"></i><span>Search</span></button>
	  </div>
	</form>

	<div class="input-item select-category" data-url="<?php echo get_bloginfo('url') . '/news/category/' ?>">
		<?php
			$cats = get_categories(array(
				'type' => 'post',
				'taxonomy' => 'category'
			));

			if ( !empty( $cats ) ) {
				$output .= '<select name="category-dropdown">';
				$output .= '<option value="">Select category</option>';
				foreach ( $cats as $cat ) {
					$selected = get_query_var('cat') == $cat->term_id ? ' selected="selected"' : '';
					$output .= '<option value="' . $cat->slug . '" ' . $selected . '>' . $cat->name . '</option>';
				}
				$output .= '</select>';
				echo $output;
			}
		?>
	</div>

	<div class="input-item select-month">
			<select name="archive-dropdown">
				<option value="">Select month</option>
				<?php wp_get_archives(array(
					'type' => 'monthly',
					'format' => 'option'
				)) ?>
			</select>
	</div>
	
</div><!-- #blog-header -->