<?php

new CoEnvMemberApiImporter();

class CoEnvMemberApiImporter {

	function __construct() {

		$this->init();

		$this->attachments = array();
	}

	/**
	 * Initialize class
	 */
	function init () {

		// add admin import page
		add_action( 'admin_menu', array( $this, 'add_import_page' ) );
	}

	function add_import_page () {
		add_submenu_page( 'edit.php?post_type=faculty', 'Faculty Import', 'Faculty Import', 'manage_options', 'coenv-member-api-tools', array( $this, 'display_submenu_page' ) );
	}

	/**
	 * Display import admin page
	 *
	 * @return void
	 */
	function display_submenu_page () {

		$messages = array();

		// check for submitted file
		if ( isset( $_FILES['member_import'] ) ) {

			if ( $_FILES['member_import']['size'] > 0 ) {
				$messages[] = $this->import_csv( $_FILES['member_import'] );
			} else {
				$messages[] = array( 'type' => 'error', 'message' => 'Please choose a CSV file.' );
			}

		}

		?>
			<div class="wrap">
				<div id="icon-tools" class="icon32"><br /></div>
				<h2><?php echo __( 'Faculty Import' ) ?></h2>

				<?php if ( isset( $messages ) && !empty( $messages ) ) : ?>
					<?php foreach ( $message as $message ) : ?>
						<div class="<?php echo $message['type'] ?>"><p><?php echo $message['message'] ?></p></div>	
					<?php endforeach ?>
				<?php endif ?>

				<p>Import faculty members from a CSV file.</p>
				<p><strong>Important:</strong> CSV file must be formatted as follows:</p>

				<table>
					<tr>
						<th align="left">Name</th>
						<th align="left">Last</th>
						<th align="left">First</th>
						<th align="left">Department</th>
						<th align="left">Title</th>
						<th align="left">[ThemeName]</th>
						<th align="left">[ThemeName]</th>
						<th align="left">[ThemeName]</th>
					</tr>
					<tr>
						<td bgcolor="#eee">Benjamin Franklin</td>
						<td bgcolor="#eee">Franklin</td>
						<td bgcolor="#eee">Benjamin</td>
						<td bgcolor="#eee">Atmospheric Sciences</td>
						<td bgcolor="#eee">Professor</td>
						<td bgcolor="#eee">x</td>
						<td bgcolor="#eee"></td>
						<td bgcolor="#eee">x</td>
					</tr>
				</table>

				<br />

				<form id="coenv-member-api-faculty-import" enctype="multipart/form-data" method="post" action="edit.php?post_type=faculty&page=coenv-member-api-tools">
					<p>
						<label><input type="checkbox" name="delete_all_faculty"></input> Delete all faculty, themes, and units before import?</label>
					</p>
					<input type="file" name="member_import" />
					<input type="submit" class="button button-primary" value="Import CSV" />
				</form>
			</div>
		<?php
	}

	/**
	 * Save CSV file temporarily to media library
	 *
	 * @param array $file The submitted file array
	 * @return ...
	 */
	function import_csv ( $file ) {

		$allowed_file_types = array( 'text/csv' );

		$file_type_array = wp_check_filetype( basename( $file['name'] ) );
		$file_type = $file_type_array['type'];

		if ( !in_array( $file_type, $allowed_file_types ) ) {
			return array( 'type' => 'error', 'message' => 'Imported file must be CSV (text/csv).' );
		}

		// must pass 'test_form' => false through overrides option
		$upload_overrides = array( 'test_form' => false );

		// handle the upload
		$upload = wp_handle_upload( $file, $upload_overrides );

		// check wp_handle_upload returned a local path for the file
		if ( !isset( $upload['file'] ) ) {
			return array( 'type' => 'error', 'message' => 'There was a problem with your upload.' );
		}

		$file_location = $upload['file'];

		// set up options array to add this file as an attachment
		$attachment = array(
			'post_mime_type' => $file_type,
			'post_title' => 'Uploaded CSV: Faculty Import',
			'post_content' => '',
			'post_status' => 'inherit'
		);

		// add file to the media library and generate thumbnails
		$attachment_id = wp_insert_attachment( $attachment, $file_location );

		// must include image.php for wp_generate_attachment_metadata() to work
		require_once( ABSPATH . 'wp-admin/includes/image.php' );

		require_once( WP_CONTENT_DIR . '/plugins/advanced-custom-fields/acf.php' );

		$attachment_data = wp_generate_attachment_metadata( $attachment_id, $file_location );
		wp_update_attachment_metadata( $attachment_id, $attachment_data );

		// if 'delete all faculty, themes, and units' is checked
		if ( $_POST['delete_all_faculty'] ) {
			$this->delete_all_faculty();
		}

		// pass file to CSV parser
		$message = $this->parse_csv( $file_location );

		return $message;

	}

	/**
	 * Delete all faculty
	 */
	function delete_all_faculty() {

		// get all faculty
		$faculty = get_posts( array(
			'post_type' => 'faculty',
			'numberposts' => -1
		));

		if ( !empty( $faculty ) ) {
			foreach ( $faculty as $member ) {
				wp_delete_post( $member->ID, true );
			}
		}

		// get all units
		$units = get_terms( array('member_unit'), array(
			'hide_empty' => false,
		) );

		if ( !empty( $units ) ) {
			foreach ( $units as $unit ) {
				wp_delete_term( $unit->term_id, 'member_unit' );
			}
		}

		// get all themes
		$themes = get_terms( array('member_theme'), array(
			'hide_empty' => false,
		) );

		if ( !empty( $themes ) ) {
			foreach ( $themes as $theme ) {
				wp_delete_term( $theme->term_id, 'member_theme' );
			}
		}
	}

	/**
	 * Parse imported CSV
	 *
	 * @param array $file The saved file array
	 * @return ...
	 */
	function parse_csv( $file ) {

		set_time_limit(3000);

		$data = array_map( 'str_getcsv', file( $file ) );

		$header = array_shift( $data );

		// convert csv into associative array
		$rows = array();
		foreach ( $data as $row ) {
			$rows[] = array_combine( $header, $row );
		}

		// get all attachments
		$attachment_posts = get_posts( array( 'posts_per_page' => -1, 'post_type' => 'attachment' ) );

		// save attachments attributes
		// $this->attachments is searched for portrait file names by $this->attach_faculty_portrait()
		foreach ( $attachment_posts as $attachment ) {
			$this->attachments[ $attachment->post_title . '.jpg' ] = $attachment->ID;
		}

		$this->insert_units( $rows );
		$this->insert_themes( $rows );
		$this->insert_faculty( $rows );
	}

	/**
	 * Insert units
	 *
	 * @param array $rows CSV rows to process
	 * @return ...
	 */
	function insert_units( $rows ) {

		$units = array();
		$messages = array();

		foreach ( $rows as $row ) {

			foreach ( $row as $key => $value ) {

				if ( $key !== 'Department' ) {
					continue;
				}

				if ( in_array( $value, $units ) ) {
					continue;
				}

				$units[] = $value;
			}
		}

		foreach ( $units as $unit ) {

			$term = wp_insert_term( $unit, 'member_unit' );

			// add unit color
			switch ( $unit ) {
				case 'Aquatic & Fishery Sciences':
					$color = '#b82828';
					break;
				case 'Atmospheric Sciences':
					$color = '#0f4174';
					break;
				case 'Earth & Space Sciences':
					$color = '#ca5f00';
					break;
				case 'Environmental & Forest Sciences':
					$color = '#0096a4';
					break;
				case 'Marine & Environmental Affairs':
					$color = '#05774d';
					break;
				case 'Oceanography':
					$color = '#859c00';
					break;
			}

			// update unit color
			update_option( 'member_unit_' . $term['term_id'] . '_color', $color );
		}
	}

	/**
	 * Insert themes
	 *
	 * @param array $rows CSV rows to process
	 * @return ...
	 */
	function insert_themes( $rows ) {

		$non_theme_cols = array( 'Name', 'First', 'Last', 'Filename', 'Title', 'Department' );

		$themes = array();

		foreach ( $rows as $row ) {

			foreach ( $row as $key => $value ) {

				if ( in_array( $key, $non_theme_cols ) ) {
					continue;
				}

				if ( in_array( $key, $themes ) ) {
					continue;
				}

				$themes[] = $key;
			}
		}

		foreach ( $themes as $theme ) {
			wp_insert_term( $theme, 'member_theme' );
		}
	}

	/**
	 * Insert faculty posts
	 */
	function insert_faculty( $faculty ) {

		foreach ( $faculty as $f ) {

			$post_title = ucwords( strtolower( $f['First'] . ' ' . $f['Last'] ) );

			$atts = array(
				'post_title' => $post_title,
				'post_type' => 'faculty',
				'post_status' => 'publish',
				'comment_status' => 'closed',
				'ping_status' => 'closed'
			);

			// insert post if a post by the same title does not exist
			$id = get_page_by_title( $post_title, 'OBJECT', 'faculty' ) ? 0 : wp_insert_post( $atts );

			// assign units
			$unit_term_array = term_exists( $f['Department'], 'member_unit' );
			wp_set_post_terms( $id, $unit_term_array['term_id'], 'member_unit', false );

			// assign themes
			$non_theme_cols = array( 'Name', 'First', 'Last', 'Title', 'Department' );
			$themes = array();
			foreach ( $f as $key => $value ) {
				if ( in_array( $key, $non_theme_cols ) ) {
					continue;
				}
				if ( empty( $value ) ) {
					continue;
				}
				$theme_term_array = term_exists( $key, 'member_theme' );
				$themes[] = $theme_term_array['term_id'];
			}
			if ( !empty( $themes ) ) {
				wp_set_post_terms( $id, $themes, 'member_theme', false );
			}

			// attach portrait file by filename
			$this->attach_faculty_portrait( $id, $f['Filename'] );

			// super basic first name extraction (first word from string)
			$first_name = explode( ' ', trim( $f['First'] ) );
			$first_name = $first_name[0];

			// set first and last name
			update_field( 'first_name', ucwords( strtolower( $first_name ) ), $id );
			update_field( 'last_name', ucwords( strtolower( $f['Last'] ) ), $id );

		}

		return count( $faculty );
	}

	/**
	 * Attach faculty portrait
	 */
	function attach_faculty_portrait( $post_id, $filename ){

		if ( array_key_exists( $filename, $this->attachments ) ) {
			$file = $this->attachments[ $filename ];
		} else {
			$file = $this->attachments['Blank Member.jpg'];
			$this->set_post_to_draft( $post_id );
		}
		update_field( 'field_18', $file, $post_id );
	}

	/**
	 * Set post status to 'draft'
	 * We'll do this for any faculty w/out an image
	 */
	function set_post_to_draft( $post_id ) {

		wp_update_post( array(
			'ID' => $post_id,
			'post_status' => 'draft'
		) );

	}

}