<?php
/*
Plugin Name: CoEnv Member API
Description: Registers post types and taxonomies for faculty, provides an endpoint API to access faculty attributes. Depends on <a href="http://www.advancedcustomfields.com/">Advanced Custom Fields</a> plugin.
Version: 0.1
Author: <a href="http://elcontraption.com/">Darin Reid</a>
*/

/*
 * TODO/ISSUES:
 * 
 * The structure of member data is dependent on Advanced Custom Fields–we'll probably need to require ACF?
 */

global $coenv_member_api;
$coenv_member_api = new CoEnvMemberAPI();

require_once( $coenv_member_api->directory . 'member-importer.php' );

class CoEnvMemberAPI {

	function __construct() {

		$this->directory = dirname( __FILE__ ) . '/';

		// Plugin directory
		if ( !defined('CMAPI_DIRNAME') ) define( 'CMAPI_DIRNAME', dirname(__FILE__) );

		// Plugin activate/deactivation
		register_activation_hook( __FILE__, array( $this, 'activate_plugin' ) );
		register_deactivation_hook( __FILE__, array( $this, 'deactivate_plugin' ) );

		// Initialize plugin
		$this->init();
	}

	/**
	 * Runs on activation of plugin
	 *
	 * @return void
	 */
	function activate_plugin() {

		// flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Runs on deactivation of plugin
	 *
	 * @return void
	 */
	function deactivate_plugin() {
		// always preserve plugin data on deactivation

		// flush rewrite rules
		flush_rewrite_rules();
	}

	/**
	 * Plugin initilization
	 *
	 * @return void
	 */
	function init() {

		// register faculty taxonomy
		add_action( 'init', array( $this, 'register_faculty_taxonomy' ) );

		// register faculty post type
		add_action( 'init', array( $this, 'register_faculty_post_type' ) );

		// handle rewrite rules for faculty
		add_action( 'init', array( $this, 'faculty_rewrites' ) );

		add_filter( 'query_vars', array( $this, 'add_query_vars' ) );

		// allow requests ending in /json
		add_filter( 'request', array( $this, 'faculty_json_request' ) );

		// handle output for json endpoints
		add_action( 'template_redirect', array( $this, 'faculty_json_endpoint' ) );

		// handle faculty member menu highlighting
		add_filter( 'nav_menu_css_class', array( $this, 'menu_highlighting' ), 10, 2 );

		// remove admin meta boxes
		add_action( 'add_meta_boxes', array( $this, 'remove_meta_boxes' ) );

		// ajax get/save units actions
		add_action( 'wp_ajax_coenv_member_api_search', array( $this, 'ajax_search' ) );
		add_action( 'wp_ajax_nopriv_coenv_member_api_search', array( $this, 'ajax_search' ) );


	}

	/**
	 * Register faculty taxonomy categories and tags
	 *
	 * Must be registered before faculty post type
	 * to support 'faculty/units' slug
	 * https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
	 *
	 * @return void
	 */
	function register_faculty_taxonomy() {

		// Move this to functions.php? Is this more general than faculty?
		register_taxonomy('member_unit', array('faculty'), 
			array(
				'hierarchical' => true,
				'labels' => array(
					'name' => _x( 'Units', 'taxonomy general name' ),
					'singular_name' => _x( 'Unit', 'taxonomy singular name' ),
					'search_items' =>  __( 'Search Units' ),
					'popular_items' => __( 'Popular Units' ),
					'all_items' => __( 'All Units' ),
					'edit_item' => __( 'Edit Unit' ),
					'update_item' => __( 'Update Unit' ),
					'add_new_item' => __( 'Add New Unit' ),
					'new_item_name' => __( 'New Unit Name' ),
				),
				'show_ui' => true,
				'query_var' => true,
				'rewrite' => array( 
					'slug' => 'faculty/units', 
					'with_front' => false
				),
			)
		);

		register_taxonomy('member_theme', array('faculty'), array(
			'labels' => array(
				'name' => _x( 'Research Themes', 'taxonomy general name' ),
				'singular_name' => _x( 'Research Theme', 'taxonomy singular name' ),
				'menu_name' => __( 'Research Themes' ),
				'all_items' => __( 'All Themes' ),
				'edit_item' => __( 'Edit Theme' ),
				'view_item' => __( 'View Theme' ),
				'update_item' => __( 'Update Theme' ),
				'add_new_item' => __( 'Add New Theme' ),
				'new_item_name' => __( 'New Theme Name' ),
				'parent_item' => __( 'Parent Theme' ),
				'parent_item_colon' => __( 'Parent Theme:' ),
				'search_items' => __( 'Search Themes' ),
				'popular_items' => __( 'Popular Themes' ),
				'separate_items_with_commas' => ( 'Separate themes with commas' ),
				'choose_from_most_used' => ( 'Choose from the most used themes' ),
				'not_found' => ( 'No themes found' )
			),
			'rewrite' => array(
				'slug' => 'faculty/themes',
				'with_front' => false
			),
			'hierarchical' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true
		));

		register_taxonomy('member_tag', array('faculty'), array(
			'labels' => array(
				'name' => _x( 'Tags', 'taxonomy general name' ),
				'singular_name' => _x( 'Tags', 'taxonomy singular name' ),
				'menu_name' => __( 'Tags' ),
				'all_items' => __( 'All Tags' ),
				'edit_item' => __( 'Edit Tag' ),
				'view_item' => __( 'View Tag' ),
				'update_item' => __( 'Update Tag' ),
				'add_new_item' => __( 'Add New Tag' ),
				'new_item_name' => __( 'New Tag Name' ),
				'parent_item' => __( 'Parent Tag' ),
				'parent_item_colon' => __( 'Parent Tag:' ),
				'search_items' => __( 'Search Tags' ),
				'popular_items' => __( 'Popular Tags' ),
				'separate_items_with_commas' => ( 'Separate tags with commas' ),
				'choose_from_most_used' => ( 'Choose from the most used tags' ),
				'not_found' => ( 'No tags found' )
			),
			'rewrite' => array(
				'slug' => 'faculty/tags',
				'with_front' => false
			),
			'hierarchical' => true,
			'show_ui' => true,
			'show_admin_column' => true,
			'query_var' => true
		));
	}

	/**
	 * Register faculty post type
	 *
	 * Must be registered after custom taxonomies
	 * to enable desired rewrites
	 * https://mondaybynoon.com/revisiting-custom-post-types-taxonomies-permalinks-slugs/
	 *
	 * @return void
	 */
	function register_faculty_post_type() {

		$labels = array(
			'name' => __( 'Faculty' ),
			'singular_name' => __( 'Faculty' ),
			'add_new' => __( 'Add New Faculty Member' ),
			'edit_item' => __( 'Edit Faculty Member' ),
			'add_new_item' => __( 'New Faculty Member' ),
			'view_item' => __( 'View Faculty Member' ),
			'search_items' => __( 'Search Faculty' ),
			'not_found' => __( 'No Faculty members found' ),
			'not_found_in_trash' => __( 'No Faculty members found in Trash' )
		);

		// Restrict to admins (for development only?)
		$capabilities = array(
			'publish_posts' => 'manage_options',
      'edit_posts' => 'manage_options',
      'edit_others_posts' => 'manage_options',
      'delete_posts' => 'manage_options',
      'delete_others_posts' => 'manage_options',
      'read_private_posts' => 'manage_options',
      'edit_post' => 'manage_options',
      'delete_post' => 'manage_options',
      'read_post' => 'manage_options',
		);

		$args = array(
			'labels' => $labels,
			'capabilities' => $capabilities, // restrict to admins
			'menu_position' => null,
			'supports' => array('title','editor','custom-fields','revisions', 'thumbnail'),
			'public' => true,
			'has_archive' => true,
			'hierarchical' => false,
			'capability_type' => 'page',
			'rewrite' => array(
				'slug' => 'faculty',
				'with_front' => false
			)
		);

		register_post_type( 'faculty', $args );
	}

	/**
	 * Faculty rewrites
	 *
	 * @return void
	 */
	function faculty_rewrites() {
		add_rewrite_endpoint( 'json', EP_PERMALINK );

		// all faculty
		// endpoint: /faculty/json/
		add_rewrite_rule(
			'faculty/json/',
			'index.php?'
		);

		// combine multiple themes and units
		// endpoint: /faculty/themes/theme_1&theme_2/units/unit_1&unit_2/json/
		// to get all faculty: /faculty/themes/all/units/all/json/
		add_rewrite_rule(
			'faculty/themes/([^/]+)/units/([^/]+)/json?',
			'index.php?faculty_themes=$matches[1]&faculty_units=$matches[2]&json=true',
			'top'
		);

		// get list of faculty themes (no members)
		add_rewrite_rule(
			'faculty/themes/json',
			'index.php?faculty_themes=all&json=true',
			'top'
		);

		// get list of faculty units (no members)
		add_rewrite_rule(
			'faculty/units/json',
			'index.php?faculty_units=all&json=true',
			'top'
		);
	}

	function add_query_vars( $query_vars ) {

		$query_vars[] = 'faculty_units';
		$query_vars[] = 'faculty_themes';

		return $query_vars;
	}

	/**
	 * Faculty JSON request
	 * Enables endpoint URL to end in JSON instead of JSON/...
	 *
	 * @param array $vars
	 * @return array $vars
	 */
	function faculty_json_request( $vars ) {

		// endpoint: faculty/member-name/json
		if ( isset( $vars['json'] ) ) {
			$vars['json'] = true;
		}

		// endpoint: faculty/json
		if ( isset( $vars['faculty'] ) && $vars['faculty'] == 'json' ) {
			unset( $vars['faculty'] );
			unset( $vars['name'] );
			$vars['json'] = true;
		}

		return $vars;
	}

	/**
	 * Handle output for JSON(P) endpoints
	 *
	 * @return void
	 */
	function faculty_json_endpoint() {

		global $wp_query;

		// test query vars
		if (
			// must include json query var
			!get_query_var('json') && (

				// may include faculty_units query var
				!get_query_var( 'faculty_units' ) ||

				// or faculty_themes query var
				!get_query_var( 'faculty_themes' ) ||

				// or be faculty post_type
				get_post_type() !== 'faculty'
			)
		) {
			// otherwise, exit
			return;
		}

		$results = array();

		// test for faculty archive
		if ( is_archive() ) {

			$results = $this->get_faculty(array(
				
				// set to 'false' for production
				'test_data' => false
			));

		// test for single faculty member
		} else if ( is_single() ) {

			// get single faculty member
			$post = get_queried_object();

			$results = $this->get_faculty(array(
				'p' => $post->ID,
				
				// set to 'false' for production
				'test_data' => false
			));

		// test for themes && units query vars
		} else if ( get_query_var('faculty_themes') && get_query_var('faculty_units') ) {

			$args = array(
				'themes' => explode( '&', get_query_var('faculty_themes') ),
				'units' => explode( '&', get_query_var('faculty_units') ),
				'test_data' => true
			);

			// return faculty who are in passed themes and units
			$results = $this->get_faculty( $args );

		// test for theme query var but no unit query var
		} else if ( get_query_var('faculty_themes') && !get_query_var('faculty_units') ) {

			// return faculty theme objects (not members)
			$results = $this->get_themes();

		} else if ( get_query_var('faculty_units') && !get_query_var('faculty_themes') ) {

			// return faculty unit objects (not members)
			$results = $this->get_units();

		}

		if ( empty( $results ) ) {
			return false;
		}

		// get jsonp callback
		$callback = strip_tags( html_entity_decode( urldecode( $_GET['callback'] ) ) );

		// output as jsonp
		header('Content-Type: application/javascript; charset=utf-8');
		print sprintf('%s(%s);', $callback, json_encode( $results ) );
		exit();
	}

	/**
	 * Assign demo images and units to each faculty member for testing
	 */
	function setup_test_data( $faculty ) {

		$results = array();

		$count = 1;
		foreach ( $faculty as $f ) {

			$f['images']['thumbnail'] = array(
				'url' => get_template_directory_uri() . '/assets/img/faculty-test-images/' . $count . '-sm.jpg'
			);

			$results[] = $f;
			$count = $count < 12 ? $count + 1 : 1;
		}

		return $results;
	}

	/**
	 * Querying all faculty
	 * gets all faculty post objects using get_posts()
	 */
	function faculty_query( $args = array() ) {

		// Required query parameters
		// Supercede anything passed in $args
		$required_params = array(
			'post_type' => 'faculty',	// must be faculty post type
			'post_status' => 'publish', // must be published
		);

		// Default query parameters
		$default_params = array(
			'test_data' => false,
			'numberposts' => -1,
			'no_found_rows' => true, // disable SQL_CALC_FOUND_ROWS (speeds up query but disables pagination)
			'orderby' => 'rand' // default order is random
		);

		// check for themes and units in args
		if ( isset( $args['themes'] ) && isset( $args['units'] ) ) {

			// don't pass theme query if it contains 'all'
			if ( !in_array( 'all', $args['themes'] ) ) {
				$default_params['tax_query'][] = array(
					'taxonomy' => 'member_theme',
					'field' => 'slug',
					'terms' => $args['themes']
				);
			}

			// don't pass unit query if it contains 'all'
			if ( !in_array( 'all', $args['units'] ) ) {
				$default_params['tax_query'][] = array(
					'taxonomy' => 'member_unit',
					'field' => 'slug',
					'terms' => $args['units']
				);
			}
		}

		$args = array_merge( $default_params, $args, $required_params );

		$results = get_posts( $args );

		return $results;
	}

	/**
	 * Get all faculty
	 */
	function get_faculty( $args = array() ) {
		extract( $args );

		$results = array();

		$query = $this->faculty_query( $args );

		if ( empty($query) ) {
			return false;
		}

		foreach ( $query as $f ) {
			$results[] = $this->setup_faculty_attributes( $f );
		}

		if ( $test_data === true ) {
			//$results = $this->setup_test_data( $results );
		}

		return $results;
	}

	/**
	 * Return clean array of faculty attributes
	 *
	 * @param object $f The faculty post object
	 * @return array Faculty attributes
	 */
	function setup_faculty_attributes( $f, $heavy = false ) {

		// should amount to a lightweight query by default
		// TODO: look to storing complex queries in a transient
		// http://codex.wordpress.org/Transients_API

		// TODO: make response even lighter
//		$member = array(
//			'full_name' => '',
//			'permalink' => '',
//			'unit' => '',
//			'thumbnail' => '',
//			'themes' => array('theme-1-slug', 'theme-2-slug') // match these up on the other side
//		);

		// this is too much to POST from ajax -> php
		// response needs to be much lighter
		// don't include multiple images
		// don't include multiple units
		// minimize themes query
		$member = array(
			'full_name' => get_the_title( $f->ID ),
			'first_name' => get_field( 'first_name', $f->ID ),
			'last_name' => get_field( 'last_name', $f->ID ),
			'permalink' => get_permalink( $f->ID ),
			'units' => $this->get_member_terms( $f->ID, 'member_unit' ),
			'themes' => $this->get_member_terms( $f->ID, 'member_theme' ),
			'images' => $this->get_member_images( $f->ID )
		);

		// heavier query (currently used on single faculty profile)
		if ( $heavy ) {
			$member['academic_title'] = get_field( 'academic_title', $f->ID );
			$member['contact_links'] = get_field( 'contact_links', $f->ID );
			$member['endowments_chairs'] = get_field( 'endowments_chairs', $f->ID );
			$member['degree_institutions'] = get_field( 'degree_institutions', $f->ID );
			$member['biography'] = get_field( 'biography', $f->ID );
			$member['pullquote'] = get_field( 'pullquote', $f->ID );
			$member['publications'] = get_field( 'publications', $f->ID );
			$member['stories'] = get_field( 'related_stories', $f->ID );
			$member['tags'] = $this->get_member_terms( $f->ID, 'member_tag' );
		}

		return $member;
	}

	/**
	 * Returns member terms
	 */
	function get_member_terms( $ID, $taxonomy ) {
		$results = array();

		$member_terms = get_the_terms( $ID, $taxonomy );
		if ( !empty( $member_terms ) ) {
			foreach ( $member_terms as $term ) {

				switch ( $taxonomy ) {
					case 'member_theme':
						$term_prefix = 'theme';
						break;
					case 'member_unit':
						$term_prefix = 'unit';
						break;
				}

				$atts = array(
					'term_id' => $term->term_id,
					'url' => get_bloginfo('url') . '/faculty/#' . $term_prefix . '-' . $term->slug,
					'name' => $term->name,
					'slug' => $term->slug,
					'count' => $term->count
				);

				// add 'color' attribute for units
				if ( $taxonomy == 'member_unit' ) {
					$atts['color'] = $this->unit_color( $term->term_id );
				}

				$results[] = $atts;
			}
		}

		return $results;
	}

	/**
	 * Return a member's images
	 */
	function get_member_images( $ID ) {
		$image_id = get_field( 'image', $ID );
		$image_thumbnail = wp_get_attachment_image_src( $image_id, 'thumbnail' );
		$image_small = wp_get_attachment_image_src( $image_id, 'small' );
		$image_medium = wp_get_attachment_image_src( $image_id, 'medium' );
		$image_large = wp_get_attachment_image_src( $image_id, 'large' );
		$image_full = wp_get_attachment_image_src( $image_id, 'full' );
		$images = array(
			'thumbnail' => array(
				'url' => $image_thumbnail[0],
				'width' => $image_thumbnail[1],
				'height' => $image_thumbnail[2]
			),
			'small' => array(
				'url' => $image_small[0],
				'width' => $image_small[1],
				'height' => $image_small[2]
			),
			'medium' => array(
				'url' => $image_medium[0],
				'width' => $image_medium[1],
				'height' => $image_medium[2]
			),
			'large' => array(
				'url' => $image_large[0],
				'width' => $image_large[1],
				'height' => $image_large[2]
			),
			'full' => array(
				'url' => $image_full[0],
				'width' => $image_full[1],
				'height' => $image_full[2]
			)
		);
		return $images;
	}

	/**
	 * Handle menu highlighting for faculty members
	 *
	 * @param array $classes
	 * @param object $item
	 * @return array $classes
	 */
	function menu_highlighting( $classes, $item ) {

		if ( !$this->is_faculty_section() ) {
			return $classes;
		}

		if ( $item->post_name != 'faculty' ) {
			return $classes;
		}

		array_push( $classes, 'current-menu-item' );

		return $classes;
	}

	/**
	 * Check if we're in the faculty section
	 */
	function is_faculty_section () {

		$obj = get_queried_object();

		if (
			get_post_type( $obj ) == 'faculty' ||
			$obj->taxonomy == 'member_unit' ||
			$obj->taxonomy == 'member_theme' ||
			$obj->taxonomy == 'member_tag'
		) {
			return true;
		}

		return false;
	}

	/**
	 * Remove admin metaboxes
	 * These are replaced by ACF fields
	 */
	function remove_meta_boxes() {

		// remove original unit checklist
		remove_meta_box( 'member_unitdiv', 'faculty', 'side' );
	}

	/**
	 * Get unit color
	 */
	function unit_color( $unit_id ) {
		return get_field( 'color', 'member_unit_' . $unit_id );
	}

	/**
	 * Get all units
	 * Including unit color
	 */
	function get_units( $args = array() ) {

		$units = array();

		$terms = get_terms( array('member_unit'), $args );

		foreach ( $terms as $term ) {
			$unit = array(
				'term_id' => $term->term_id,
				//'url' => get_term_link( $term, $taxonomy ),
				'url' => get_bloginfo('url') . '/faculty/#unit-' . $term->slug,
				'name' => $term->name,
				'slug' => $term->slug,
				'count' => $term->count,
				'color' => $this->unit_color( $term->term_id )
			);

			// check if array of unit slugs has been passed
			if ( isset( $args['units'] ) && !empty( $args['units'] ) ) {
				if ( in_array( $term->slug, $args['units'] ) ) {
					$units[] = $unit;
				}
			} else {
				$units[] = $unit;
			}
		}

		return $units;
	}

	/**
	 * Get all themes
	 */
	function get_themes( $args = array() ) {

		$themes = array();

		$terms = get_terms( array('member_theme'), $args );

		foreach ( $terms as $term ) {
			$theme = array(
				'term_id' => $term->term_id,
				//'url' => get_term_link( $term, $taxonomy ),
				'url' => get_bloginfo('url') . '/faculty/#theme-' . $term->slug,
				'name' => $term->name,
				'slug' => $term->slug,
				'count' => $term->count
			);

			// check if array of theme slugs has been passed
			if ( isset( $args['themes'] ) && !empty( $args['themes'] ) ) {
				if ( in_array( $term->slug, $args['themes'] ) ) {
					$themes[] = $theme;
				}
			} else {
				$themes[] = $theme;
			}
		}

		return $themes;
	}

/**
 * Ajax faculty search
 * Search: full_name, biography
 */
function ajax_search () {
	global $wpdb;

	$querystring = $_POST['data'];

	// querystring should be more than two characters
	if ( strlen($querystring) <= 2 ) {
		echo json_encode(array(
			'results' => '',
			'number' => 0,
			'message' => 'Search keywords should be longer than two characters'
		));
		die();
	}

	// run normal search first
	$query_search = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'faculty',
		'post_status' => 'publish',
		's' => $querystring
	));

	$query_meta = get_posts(array(
		'posts_per_page' => -1,
		'post_type' => 'faculty',
		'post_status' => 'publish',
		'meta_query' => array(
			'relation' => 'OR',
			array(
				'key' => 'biography',
				'value' => $querystring,
				'compare' => 'LIKE'
			),
			array(
				'key' => 'academic_title',
				'value' => $querystring,
				'compare' => 'LIKE'
			)
		)
	));

	$results = array_merge( $query_search, $query_meta );

	// this produces duplicate results,
	// which doesn't really matter since isotope filtering
	// handles duplicates just fine.

	echo json_encode(array(
		'results' => $results,
		'number' => count($results),
		'message' => sanitize_text_field( $querystring )
	));

	die();
}
















} // class CoEnvMemberAPI