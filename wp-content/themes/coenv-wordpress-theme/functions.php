<?php

/**
 * Incorporate CoEnv Member API into the theme
 * this used to be a separate plugin, but it makes more sense to include it in the theme
 */
require 'member-api.php';

/**
 * Print styles and scripts in header and footer
 */
add_action( 'wp_enqueue_scripts', 'coenv_styles_and_scripts' );
function coenv_styles_and_scripts() {

	// for public side only
	if ( is_admin() ) {
		return false;
	}

//	// main theme stylesheet (now in header to prevent serving to < ie8)
//	wp_register_style( 'screen', get_template_directory_uri() . '/assets/styles/build/screen.css' );
//	wp_enqueue_style( 'screen' );

	// include jQuery, using CDN version first
	wp_deregister_script( 'jquery' );
	wp_register_script( 'jquery', 'http://code.jquery.com/jquery-1.9.1.min.js', array(), '1.9.1', false );
	wp_enqueue_script( 'jquery' );

	// should CDN jQuery be unavailable, include local copy
	wp_register_script( 'jquery-fallback', get_template_directory_uri() . '/assets/scripts/build/jquery-fallback.min.js' );
	wp_enqueue_script( 'jquery-fallback' );

	// include jQuery migrate plugin
	//wp_register_script( 'jquery-migrate', get_template_directory_uri() . '/components/jquery/jquery-migrate.min.js', array( 'jquery' ), '1.1.0', false );
	//wp_enqueue_script( 'jquery-migrate' );

	// include theme scripts in footer
	wp_register_script( 'coenv-main', get_template_directory_uri() . '/assets/scripts/build/main.min.js', array( 'jquery' ), null, true );
	wp_enqueue_script( 'coenv-main' );

	// register faculty scripts, enqueued within template files
	wp_register_script( 'coenv-faculty', get_template_directory_uri() . '/assets/scripts/build/faculty.min.js', array( 'jquery', 'coenv-main' ), null, true );

	// make variables available to theme scripts
	wp_localize_script( 'coenv-main', 'themeVars', array( 'ajaxurl' => admin_url( 'admin-ajax.php' ), 'themeurl' => get_template_directory_uri() ) );
}

/**
 * Admin only scripts
 */
add_action( 'admin_enqueue_scripts', 'coenv_admin_scripts' );
function coenv_admin_scripts() {
	wp_register_script( 'coenv_admin', get_template_directory_uri() . '/assets/scripts/build/admin.min.js', array( 'jquery' ) );
	wp_enqueue_script( 'coenv_admin' );
}

/**
 * Print the <title> tag based on what is being viewed
 * @return 
 * - echo string
 */
function coenv_title() {
	global $page, $paged;

	wp_title( '|', true, 'right' );

	// Add the blog name
	bloginfo( 'name' );

	// Add a page number if necessary:
	if ( $paged >= 2 || $page >= 2 ) {
		echo ' | ' . sprintf( __( 'Page %s', 'vpc' ), max( $paged, $page ) );
	}
}

/**
 * Open graph doctype
 */
add_filter( 'language_attributes', 'coenv_language_attributes' );
function coenv_language_attributes( $output ) {
	return $output . ' xmlns="http://www.w3.org/1999/xhtml" xmlns:og="http://ogp.me/ns#" xmlns:fb="https://www.facebook.com/2008/fbml"';
}

/**
 * Add meta tags to head
 */
add_action( 'wp_head', 'coenv_meta_tags' );
function coenv_meta_tags() {
	$post = get_queried_object();

	if ( has_post_thumbnail( $post->ID ) ) {
		$thumb_src = wp_get_attachment_image_src( get_post_thumbnail_id( $post->ID ), 'medium' );
		$thumbnail = $thumb_src[0];
	} else {
		$thumbnail = get_template_directory_uri() . '/assets/img/apple-touch-icon-114x114-precomposed.png';
	}

	?>
	<meta property="og:title" content="<?php echo coenv_title() ?>" />
	<meta property="og:type" content="article" />
	<meta property="og:url" content="<?php echo get_permalink() ?>" />
	<meta property="og:image" content="<?php echo $thumbnail ?>" />
	<meta property="og:site_name" content="<?php	bloginfo('name') ?>" />
	<?php
}

/**
 * Admin settings
 */
add_action( 'admin_init', 'coenv_admin_settings' );
function coenv_admin_settings() {

	add_option( 'meta_description' );
	add_settings_field( 'meta_description', 'Site description', 'coenv_setting_meta_description', 'general' );
	register_setting( 'general', 'meta_description' );

	add_option( 'facebook' );
	add_settings_field( 'facebook', 'Facebook', 'coenv_setting_facebook', 'general' );
	register_setting( 'general', 'facebook' );

	add_option( 'twitter' );
	add_settings_field( 'twitter', 'Twitter', 'coenv_setting_twitter', 'general' );
	register_setting( 'general', 'twitter' );

	add_option( 'google_plus' );
	add_settings_field( 'google_plus', 'Google+', 'coenv_setting_google_plus', 'general' );
	register_setting( 'general', 'google_plus' );

	add_option( 'youtube' );
	add_settings_field( 'youtube', 'YouTube', 'coenv_setting_youtube', 'general' );
	register_setting( 'general', 'youtube' );

//	add_option( 'feeds' );
//	add_settings_field( 'feeds', 'Feeds', 'coenv_setting_feeds', 'general' );
//	register_setting( 'general', 'feeds' );

	add_option( 'uw_social' );
	add_settings_field( 'UW Social', 'UW Social', 'coenv_setting_uw_social', 'general' );
	register_setting( 'general', 'uw_social' );
}

/**
 * Meta description setting
 */
function coenv_setting_meta_description() {
	$value = get_option('meta_description');

	?>	
		<p>In one or two short sentences, describe your site. This description is used in the search results of search engines like Google.</p>
		<textarea name="meta_description" id="meta_description" cols="30" rows="10" style="width: 100%"><?php echo $value ?></textarea>
	<?php
}

/**
 * Facebook setting
 */
function coenv_setting_facebook() {
	$value = get_option('facebook');

	?>	
		<input name="facebook" type="text" id="facebook" value="<?php echo $value; ?>" class="regular-text">
		<p class="description">Full URL to your Facebook page (e.g. https://facebook.com&hellip;).</p>
	<?php
}

/**
 * Twitter setting
 */
function coenv_setting_twitter() {
	$value = get_option('twitter');

	?>	
		<input name="twitter" type="text" id="twitter" value="<?php echo $value; ?>" class="regular-text">
		<p class="description">Just the handle ONLY (e.g. @handle).</p>
	<?php
}

/**
 * Google Plus setting
 */
function coenv_setting_google_plus() {
	$value = get_option('google_plus');

	?>	
		<input name="google_plus" type="text" id="google_plus" value="<?php echo $value; ?>" class="regular-text">
		<p class="description">Full URL to your Google+ profile.</p>
	<?php
}

/**
 * YouTube setting
 */
function coenv_setting_youtube() {
	$value = get_option('youtube');

	?>	
		<input name="youtube" type="text" id="youtube" value="<?php echo $value; ?>" class="regular-text">
		<p class="description">Full URL to your YouTube Channel.</p>
	<?php
}

/**
 * Feeds setting
 */
function coenv_setting_feeds() {
	$value = get_option('feeds');

	?>	
		<input name="feeds" type="text" id="feeds" value="<?php echo $value; ?>" class="regular-text">
		<p class="description">Full URL to your Feeds aggregation page (e.g. http://www.feedburner&hellip;).</p>
	<?php
}

/**
 * UW Social setting
 */
function coenv_setting_uw_social() {
	$value = get_option('uw_social');

	?>	
		<input name="uw_social" type="text" id="uw_social" value="<?php echo $value; ?>" class="regular-text">
	<?php
}

/**
 * Theme setup
 */
add_action( 'after_setup_theme', 'coenv_theme_setup' );
function coenv_theme_setup() {

	// Add RSS feed links to <head> for posts and comments.
	add_theme_support('automatic-feed-links');

	// Register nav menus
	register_nav_menus(array(
		'uw-links' => 'UW links',
		'top-links' => 'Top links',
		'top-buttons' => 'Top buttons',
		'footer-links' => 'Footer links',
		'footer-units' => 'Footer academic units'
	));

	// Featured images
	if ( function_exists( 'add_theme_support' ) ) {
		add_theme_support('post-thumbnails');
	}

	// Set media sizes
	// thumbnail: 200x200 square crop
  update_option( 'thumbnail_size_w', 200 );
  update_option( 'thumbnail_size_h', 200 );
  update_option( 'thumbnail_crop', 1 );

  // small: 262x262
	if ( function_exists( 'add_image_size' ) ) { 
		add_image_size( 'small', 262, 262 );
		add_image_size( 'banner', 1680 );
	}

  // medium: 528x528
  update_option( 'medium_size_w', 528 );
  update_option( 'medium_size_h', 528 );

  // large: 750x750
  update_option( 'large_size_w', 794 );
  update_option( 'large_size_h', 794 );
}

/**
 * Main menu walker class
 * To be included, pages must be set to appear in the main menu
 */
class CoEnv_Main_Menu_Walker extends Walker_Page {

	function __construct() {

		$this->top_level_counter = 0;
		$this->top_level_pages = $this->get_top_level_pages();
		$this->top_level_page_ids = $this->get_top_level_page_ids();

		$this->menu_pages = $this->get_menu_pages();
		$this->menu_page_ids = $this->get_menu_page_ids();

		$this->subheader_page_ids = $this->get_subheader_page_ids();
	}

	/**
	 * Get top-level pages that are set to appear as top-level 
	 * in the main menu as post objects
	 */
	function get_top_level_pages() {
		$top_level_pages = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'page',
			'post_parent' => 0,
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'show_as_top-level_page',
					'value' => '1',
					'compare' => '=='
				)
			)
		) );
		return $top_level_pages;
	}

	/**
	 * Build array of ids for top-level menu items
	 */
	function get_top_level_page_ids() {
		$top_level_page_ids = array();

		if ( !empty( $this->top_level_pages ) ) {
			foreach ( $this->top_level_pages as $top_level_page ) {
				$top_level_page_ids[] = $top_level_page->ID;
			}
		}
		return $top_level_page_ids;
	}

	/**
	 * Get pages that are set to appear in the main menu system
	 * as wp objects
	 */
	function get_menu_pages() {
		$menu_pages = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'page',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'show_in_main_menu',
					'value' => '1',
					'compare' => '=='
				)
			)
		) );
		return $menu_pages;
	}

	/**
	 * Build array of menu page ids
	 */
	function get_menu_page_ids() {
		$menu_page_ids = array();

		if ( !empty( $this->menu_pages ) ) {
			foreach ( $this->menu_pages as $mpage ) {
				$menu_page_ids[] = $mpage->ID;
			}
		}
		return $menu_page_ids;
	}

	/**
	 * Get page ids that are set to appear in the main menu as subheaders
	 */
	function get_subheader_page_ids() {
		$subheader_page_ids = array();
		$subheader_pages = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'page',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'show_as_sub-header',
					'value' => '1',
					'compare' => '=='
				)
			)
		) );

		if ( !empty( $subheader_pages ) ) {
			foreach ( $subheader_pages as $subheader_page ) {
				$subheader_page_ids[] = $subheader_page->ID;
			}
		}
		return $subheader_page_ids;
	}

	function walk ( $elements, $max_depth ) {

		$args = array_slice(func_get_args(), 2);
		$output = '';

		if ($max_depth < -1) //invalid parameter
			return $output;

		if (empty($elements)) //nothing to walk
			return $output;

		$id_field = $this->db_fields['id'];
		$parent_field = $this->db_fields['parent'];

		/*
		 * need to display in hierarchical order
		 * separate elements into two buckets: top level and children elements
		 * children_elements is two dimensional array, eg.
		 * children_elements[10][] contains all sub-elements whose parent is 10.
		 */
		$top_level_elements = array();
		$children_elements  = array();
		foreach ( $elements as $e) {
			if ( 0 == $e->$parent_field ) {
				$top_level_elements[] = $e;
			} else {

				// child elements must be set to appear in the main menu
				if ( in_array( $e->ID, $this->menu_page_ids ) ) {
					$children_elements[ $e->$parent_field ][] = $e;
				}

			}
		}

		foreach ( $top_level_elements as $e ) {

			// check that element id is in top_level_page_ids
			if ( in_array( $e->ID, $this->top_level_page_ids ) ) {
				$this->display_element( $e, $children_elements, $max_depth, 0, $args, $output );
			}
		}

		return $output;
	}

	function start_lvl( &$output, $depth = 0, $args = array() ) {
		parent::start_lvl( $output, $depth, $args );
	}

	function end_lvl( &$output, $depth = 0, $args = array() ) {
		parent::end_lvl( $output, $depth, $args );
	}

	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {

		extract($args, EXTR_SKIP);
		$css_class = array('page-depth-' . $depth, 'page_item', 'page-item-'.$page->ID);

		// check if this item is a "subheader item"
		// meaning we need to show it in subheader style and show its children
		// at launch, we're using this for the students section, where the dropdown
		// should show items underneath 'undergraduate' and 'graduate'
		if ( $depth == 1 && in_array( $page->ID, $this->subheader_page_ids ) ) {
			array_push( $css_class, 'menu-item-subheader' );
		}

		if ( !empty($current_page) ) {
			$_current_page = get_post( $current_page );
			if ( in_array( $page->ID, $_current_page->ancestors ) )
				$css_class[] = 'current_page_ancestor';
			if ( $page->ID == $current_page )
				$css_class[] = 'current_page_item';
			elseif ( $_current_page && $page->ID == $_current_page->post_parent )
				$css_class[] = 'current_page_parent';
		} elseif ( $page->ID == get_option('page_for_posts') ) {
			$css_class[] = 'current_page_parent';
		}

		$css_class = implode( ' ', apply_filters( 'page_css_class', $css_class, $page, $depth, $args, $current_page ) );

		if ( $depth == 0 ) {

			if ( $this->top_level_counter % 3 == 0 ) {
				$output .= "\n" . '<li class="column">' . "\n";
				$output .= '<ul>' . "\n";
			}

			$this->top_level_counter++;
		}

		$output .= '<li class="' . $css_class . '">';

		$alt_title = '';

		// wrap top-level items in <span>
		if ( $depth == 0 ) {

			// check for alternate drop-down title
			// like "Meet our faculty" for "Faculty"
			$alt_title = get_field( 'drop-down_title', $page->ID );
			$alt_title = $alt_title ? ' title="' . $alt_title . '"' : '';

			$output .= '<span>';
		}

		$output .= '<a' . $alt_title . ' href="' . get_permalink($page->ID) . '">' . $link_before . apply_filters( 'the_title', $page->post_title, $page->ID ) . $link_after . '</a>';

		// wrap top-level items in <span>
		if ( $depth == 0 ) {
			$output .= '</span>';
		}
	}

	function end_el( &$output, $page, $depth = 0, $args = array() ) {

		// continue with original end_el()
		parent::end_el( $output, $page, $depth, $args );

		if ( $depth == 0 ) {

			if ( ( $this->top_level_counter % 3 == 0 ) || ( $this->top_level_counter == count( $this->top_level_pages ) ) ) {
				$output .= '</ul>' . "\n";
				$output .= '</li><!-- .column -->' . "\n";
			}

		}
	}

}


// update all pages
// BEWARE
//add_action('admin_init', 'coenv_update_pages');
//function coenv_update_pages() {
//
//	$pages = get_posts( array(
//		'posts_per_page' => -1,
//		'post_type' => 'page',
//		'post_status' => 'publish'
//	) );
//
//	foreach ( $pages as $page ) {
//		//update_field( 'field_51fa8ff231d73', '1', $page->ID );
//	}
//
//}

/**
 * Used for secondary nav on pages that don't appear within the main menu
 * Use "sub-menu" class on child menus instead of "children", to mirror
 * display of nav menus.
 */
class CoEnv_Secondary_Menu_Walker extends Walker_Page {

	function __construct() {
		$this->menu_pages = array();

		// get all pages that are set to appear in the main menu
		$menu_pages = get_posts( array(
			'posts_per_page' => -1,
			'post_type' => 'page',
			'post_status' => 'publish',
			'meta_query' => array(
				array(
					'key' => 'show_in_main_menu',
					'value' => 1,
					'compare' => 'LIKE'
				)
			)
		) );

		if ( !empty( $menu_pages ) ) {
			foreach ( $menu_pages as $page ) {
				$this->menu_pages[] = $page->ID;

				if ( empty( $page->ancestors ) ) {
					$this->top_level_pages[] = $page->ID;
				}
			}
		}
	}

	function in_main_menu( $item, $depth ) {

		// item must be in menu pages
		if ( !in_array( $item->ID, $this->menu_pages ) ) {
			return false;
		}

		return true;
	}

	/**
	 * Starts a branch of the tree (<ul>)
	 */
	function start_lvl ( &$output, $depth = 0, $args = array() ) {

		$indent = str_repeat("\t", $depth);
		$output .= "\n$indent<ul class='children'>\n";
	}

	/**
	 * Ends a branch of the tree (</ul>)
	 */
	function end_lvl ( &$output, $depth = 0, $args = array() ) {

		// continue with original end_lvl()
		parent::end_lvl( $output, $depth, $args );
	}

	/**
	 * Starts an element of a branch (<li>)
	 */
	function start_el( &$output, $page, $depth, $args, $current_page = 0 ) {

		// check that this page is set to appear in the main menu
		if ( !$this->in_main_menu( $page, $depth ) ) {
			return false;
		}

		// continue with original start_el()
		parent::start_el( $output, $page, $args, $depth, $current_page );

	}

	/**
	 * Ends an element of a branch (</li>)
	 */
	function end_el ( &$output, $page, $depth = 0, $args = array() ) {

		// check that this element is set to appear in main menu
		if ( !$this->in_main_menu( $page, $depth ) ) {
			return false;
		}

		// continue with original end_el()
		parent::end_el( $output, $page, $depth, $args );

	}

}

class CoEnv_Top_Menu_Walker extends Walker_Nav_Menu {

//	function display_element( $element, &$children_elements, $max_depth, $depth = 0, $args, &$output ) {

	//}

	function start_el( &$output, $item, $depth = 0, $args = array() ) {
		parent::start_el( $output, $item, $depth, $args );
	}

}

/**
 * Register features
 */
add_action( 'init', 'coenv_register_features' );

function coenv_register_features() {

	$labels = array(
		'name' => __( 'Features' ),
		'singular_name' => __( 'Featiure' ),
		'add_new' => __( 'Add New Feature' ),
		'edit_item' => __( 'Edit Feature' ),
		'add_new_item' => __( 'New Feature' ),
		'view_item' => __( 'View Feature' ),
		'search_items' => __( 'Search Features' ),
		'not_found' => __( 'No Features found' ),
		'not_found_in_trash' => __( 'No Features found in Trash' )
	);

	$rewrite = array(
		'slug' => 'feature',
		'with_front' => false
	);

	$args = array(
		'labels' => $labels,
		'menu_position' => null,
		'supports' => array('title','editor','page-attributes'),
		'public' => true,
		'has_archive' => false,
		'hierarchical' => false,
		'capability_type' => 'page',
		'rewrite' => $rewrite,
		'exclude_from_search' => true
	);

	register_post_type( 'feature', $args );
}

/**
 * Returns a unit color (hex value)
 *
 * @param int Unit post ID
 * @return string Color hex value
 */
function coenv_unit_color( $unit_id ) {
	global $coenv_member_api;
	return $coenv_member_api->unit_color( $unit_id );
}

/**
 * Excerpt "more" link
 */
add_filter( 'excerpt_more', 'coenv_excerpt_more' );
function coenv_excerpt_more( $more ) {
	return ' <a class="read-more" href="' . get_permalink() . '">[continue]</a>';
}

/**
 * Register sidebars for all pages, format widget HTML,
 * include widgets.php
 */
add_action( 'widgets_init', 'coenv_widgets_init' );

function coenv_widgets_init() {

	// include custom widgets
	$file = dirname(__FILE__) . '/widgets.php';

	if ( file_exists( $file ) ) {
		require( $file );
	}

	$before_widget	= '<section id="%1$s" class="widget %2$s">';
	$before_title 	= '<header class="section-header"><h1>';
	$after_title	= '</h1></header> <!-- end .section-header -->';
	$after_widget	= '</section> <!-- end #%1$s -->';

	// this will return only top-level pages
	$pages = get_pages('parent=0&sort_column=menu_order&sort_order=ASC');

	// remove specific pages by page name
	$pages_to_remove = array( );

	if ( empty( $pages ) ) {
		return false;
	}

	foreach( $pages as $page ) {
		// remove specific pages
		if( !in_array( $page->post_name, $pages_to_remove ) ) {
			register_sidebar( array(
				'name' 			=> $page->post_title,
				'id'			=> 'sidebar-' . $page->ID,
				'before_widget' => $before_widget,
				'after_widget'	=> $after_widget,
				'before_title' 	=> $before_title,
				'after_title'	=> $after_title
			) );
		}
	}

	$additional_sidebars = array('Search');

	if ( !empty( $additional_sidebars ) ) {
		foreach ( $additional_sidebars as $sidebar ) {
			register_sidebar( array(
				'name' => $sidebar,
				'id' => 'sidebar-' . str_replace(' ', '-', strtolower( $sidebar ) ),
				'before_widget' => $before_widget,
				'after_widget'	=> $after_widget,
				'before_title' 	=> $before_title,
				'after_title'	=> $after_title
			) );
		}
	}

}

/**
 * Add class 'page-top-level' class to top level page body_class
 */
add_filter( 'body_class', 'coenv_page_top_ancestor_class' );
function coenv_page_top_ancestor_class( $classes ) {
	$post = get_queried_object();
	$ancestor_id = coenv_get_ancestor('ID');

	if ( isset( $post->ID ) && $post->ID == $ancestor_id ) {
		$classes[] = 'page-top-level';
	}
	return $classes;
}

/**
 * Gets the top-level ancestor for pages, posts and custom post types
 * Credit: https://github.com/elcontraption/wp-tools 
 * @param
 * - string
 * @return 
 * - array
 */
function coenv_get_ancestor($attr = 'ID') {
	
	$post = get_queried_object();

	// test for search
	if ( is_search() ) {
		return false;
	}

	if ( ($post->post_type == 'post' || is_archive() || is_search()) && !is_post_type_archive( array( 'faculty' ) ) ) {

		$page_for_posts = get_option( 'page_for_posts' );

		if ( $page_for_posts == 0 ) {
			return false;
		}

		$ancestor = get_post( $page_for_posts );
		return $ancestor->$attr;
	}

	// test for pages
	if ( $post->post_type == 'page' ) {

		// test for top-level pages
		if ( $post->post_parent == 0 ) {
			return $post->$attr;
		}

		// must be a child page
		$ancestors = get_post_ancestors( $post->ID );
		$ancestor = get_post( array_pop( $ancestors ) );
		return $ancestor->$attr;
	}

	// test for custom post types
	$custom_post_types = get_post_types( array( '_builtin' => false ), 'object' );
	if ( !empty( $custom_post_types ) && array_key_exists( $post->post_type, $custom_post_types ) ) {

		// is parent_page slug defined?
		if ( isset( $custom_post_types[ $post->post_type ]->parent_page ) ) {

			// parent_page slug is defined.
			$parent = get_page_by_path( $custom_post_types[ $post->post_type ]->parent_page );

		} else {

			// parent_page slug is not defined
			// find custom slug
			$slug = $custom_post_types[ $post->post_type ]->rewrite[ 'slug' ];

			// if a page exists with the same slug, assume that's the parent page
			$parent = get_page_by_path( $slug );
		}

		// get ancestors of $parent
		$ancestors = get_post_ancestors( $parent->ID );

		// if ancestors is empty, just return $parent;
		if ( empty( $ancestors ) ) {
			return $parent->$attr;
		}

		$ancestor = get_post( array_pop( $ancestors ) );
		return $ancestor->$attr;
	}
}

/**
 * Customize TinyMCE editor formats
 */
add_filter('tiny_mce_before_init', 'coenv_editor_formats');
function coenv_editor_formats( $init )
{
	$init['theme_advanced_blockformats'] = 'p, h1, h2, h3, h4';
	
	return $init;
}

/**
 * Page banners
 *
 * 2013.07.31 | Darin | disabled check for post thumbnail, will always fall back to ancestor thumbnail.
 */
function coenv_banner() {
	$obj = get_queried_object();

	$page_id = false;
	$banner = false;

	$ancestor_id = coenv_get_ancestor('ID');
	if ( has_post_thumbnail( $ancestor_id ) ) {
		$page_id = $ancestor_id;
	}

	if ( $page_id == false ) {
		return false;
	}

	$thumb_id = get_post_thumbnail_id( $page_id );
	$image_src = wp_get_attachment_image_src( $thumb_id, 'banner' );
	$attachment_post_obj = get_post( $thumb_id );

	$banner = array(
		'url' => $image_src[0],
		'permalink' => get_permalink( $attachment_post_obj->ID ),
		'title' => $attachment_post_obj->post_title,
		'caption' => $attachment_post_obj->post_excerpt
	);

	return $banner;
}

/**
 * Prevent default gallery style
 */
add_filter( 'use_default_gallery_style', '__return_false' );

/**
 * Display breadcrumb links for a page, post or faculty
 */
function coenv_breadcrumbs() {
	global $post;

	$post_type_obj = get_post_type_object( get_post_type( $post ) );

	switch ( $post_type_obj->labels->singular_name ) {
		case 'Post':
			$post_type = 'News';
			break;
		default:
			$post_type = $post_type_obj->labels->singular_name;
			break;
	}

	$output = '<div class="breadcrumbs">';
	$output .= $post_type . ': ';

	// for news, output date
	if ( $post_type == 'News' ) {
		$output .= '<ul class="breadcrumbs__list">';
		$output .= '<li>' . coenv_post_date() . '</li>';
		$output .= '</ul>';
	}

	if ( isset( $post->ancestors ) && !empty( $post->ancestors ) ) {
		$output .= '<ul class="breadcrumbs__list">';
		
		foreach ( $post->ancestors as $ancestor ) {
			$output .= '<li><a href="' . get_permalink( $ancestor ) . '">' . get_the_title( $ancestor ) . '</a></li>';
		}

		$output .= '</ul>';
	}

	$output .= '</div>';

	echo $output;
}

/**
 * Add submenu checkbox to wp custom nav menus
 */
add_action( 'wp_update_nav_menu', 'coenv_save_nav_menu_custom_fields' );
function coenv_save_nav_menu_custom_fields() {

	$ids_to_save = array();

	// collect ids submitted in $_POST['menu-item-subheader']
	$checked_ids = isset( $_POST['menu-item-subheader'] ) ? $_POST['menu-item-subheader'] : array();

	foreach ( $checked_ids as $key => $value ) {
		$ids_to_save[] = "$key";
	}

	update_option( 'coenv_main_menu_subheaders', $ids_to_save );

	// this gets called twice for some reason
	remove_action( 'wp_update_nav_menu', __FUNCTION__ );
}

/**
 * Add ajax action to check if checkbox is checked for subhead nav item
 */
add_action( 'wp_ajax_coenv_ajax_get_menu_status', 'coenv_ajax_get_menu_status' );
function coenv_ajax_get_menu_status() {

	// get items saved as subheaders
	$subheaders = get_option( 'coenv_main_menu_subheaders' );

	exit( json_encode( $subheaders ) );
}

/**
 * Checks if a page's ancestor is assigned in a custom menu
 * Returns true/false
 */
function coenv_has_menu( $menu_slug ) {

	global $post;

	$top_level_nav_item_permalinks = array();

	$ancestor_permalink = get_permalink( coenv_get_ancestor( 'ID' ) );

	$nav_menu_items = wp_get_nav_menu_items( $menu_slug );

	foreach ( $nav_menu_items as $item ) {

		// filter for top level items
		// add permalink to array
		if ( empty( $item->post_parent ) ) {
			$top_level_nav_item_permalinks[] = $item->url;
		}
	}

	// check if the ancestor permalink is in permalinks array
	if ( in_array( $ancestor_permalink, $top_level_nav_item_permalinks ) ) {
		return true;
	}

	return false;
}

/*
|---------------------------------------------------------------------------
| Pagination
|---------------------------------------------------------------------------
*/
function coenv_paginate() {

	if( is_singular() )
		return;

	global $wp_query;

	$prev_label = '&laquo;';
	$next_label = '&raquo;';

	/** Stop execution if there's only 1 page */
	if( $wp_query->max_num_pages <= 1 )
		return;

	$paged = get_query_var( 'paged' ) ? absint( get_query_var( 'paged' ) ) : 1;
	$max   = intval( $wp_query->max_num_pages );

	/**	Add current page to the array */
	if ( $paged >= 1 )
		$links[] = $paged;

	/**	Add the pages around the current page to the array */
	if ( $paged >= 3 ) {
		$links[] = $paged - 1;
		$links[] = $paged - 2;
	}

	if ( ( $paged + 2 ) <= $max ) {
		$links[] = $paged + 2;
		$links[] = $paged + 1;
	}

	echo '<div class="navigation"><ul>' . "\n";

	/**	Previous Post Link */
	if ( get_previous_posts_link() )
		printf( '<li>%s</li>' . "\n", get_previous_posts_link( $prev_label ) );

	/**	Link to first page, plus ellipses if necessary */
	if ( ! in_array( 1, $links ) ) {
		$class = 1 == $paged ? ' class="active"' : '';

		printf( '<li%s><a href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( 1 ) ), '1' );

		if ( ! in_array( 2, $links ) )
			echo '<li>…</li>';
	}

	/**	Link to current page, plus 2 pages in either direction if necessary */
	sort( $links );
	foreach ( (array) $links as $link ) {
		$class = $paged == $link ? ' class="current"' : '';
		printf( '<li><a%s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $link ) ), $link );
	}

	/**	Link to last page, plus ellipses if necessary */
	if ( ! in_array( $max, $links ) ) {
		if ( ! in_array( $max - 1, $links ) )
			echo '<li>…</li>' . "\n";

		$class = $paged == $max ? ' class="current"' : '';
		printf( '<li><a%s href="%s">%s</a></li>' . "\n", $class, esc_url( get_pagenum_link( $max ) ), $max );
	}

	/**	Next Post Link */
	if ( get_next_posts_link() )
		printf( '<li>%s</li>' . "\n", get_next_posts_link( $next_label ) );

	echo '</ul></div>' . "\n";
}

/**
 * Format post dates
 */
function coenv_post_date() {
	global $post;
	return '<time class="updated" datetime="' . esc_attr( get_the_date( 'c' ) ) . '" pubdate>' . esc_html( get_the_date() ) . '</time>';
}

/*
|---------------------------------------------------------------------------
| Archive titles
|---------------------------------------------------------------------------
*/
if ( !function_exists( 'coenv_archive_title' ) ) {

	function coenv_archive_title()
	{
		if ( is_category() ) {
			printf( __( 'Category Archives: %s', 'coenv' ), '<span>' . single_cat_title( '', false ) . '</span>' );

		} elseif ( is_tag() ) {
			printf( __( 'Tag Archives: %s', 'coenv' ), '<span>' . single_tag_title( '', false ) . '</span>' );

		} elseif ( is_author() ) {
			/* Queue the first post, that way we know
			 * what author we're dealing with (if that is the case).
			*/
			the_post();
			printf( __( 'Author Archives: %s', 'coenv' ), '<span class="vcard"><a class="url fn n" href="' . get_author_posts_url( get_the_author_meta( "ID" ) ) . '" title="' . esc_attr( get_the_author() ) . '" rel="me">' . get_the_author() . '</a></span>' );
			/* Since we called the_post() above, we need to
			 * rewind the loop back to the beginning that way
			 * we can run the loop properly, in full.
			 */
			rewind_posts();

		} elseif ( is_day() ) {
			printf( __( 'Daily Archives: %s', 'coenv' ), '<span>' . get_the_date() . '</span>' );

		} elseif ( is_month() ) {
			printf( __( 'Monthly Archives: %s', 'coenv' ), '<span>' . get_the_date( 'F Y' ) . '</span>' );

		} elseif ( is_year() ) {
			printf( __( 'Yearly Archives: %s', 'coenv' ), '<span>' . get_the_date( 'Y' ) . '</span>' );

		} else {
			_e( 'Archives', 'coenv' );

		}
	}

}

/**
 * Print breadcrumbs
 * For print stylesheet only
 */
function coenv_print_breadcrumbs() {
	global $post;

	print '<pre>';
	print_r($post->ancestors);
	print '</pre>';

	$output = get_bloginfo('url');
	echo $output;
}






