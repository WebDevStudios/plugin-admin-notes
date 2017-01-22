<?php
/**
 * WDS Plugin Police Dynamic_form
 *
 * @since   0.1.0
 * @package WDS Plugin Police
 */

/**
 * WDS Plugin Police Dynamic_form.
 *
 * @since 0.1.0
 */
class WDSPP_Dynamic_form {
	/**
	 * Parent plugin class
	 *
	 * @var   WDS_Plugin_Police
	 * @since 0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 *
	 * @since  0.1.0
	 *
	 * @param  WDS_Plugin_Police $plugin Main plugin object.
	 *
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {
	}

	/**
	 * Create the dynamic stuff to be returned from the admin-ajax request.
	 *
	 * @since 0.1.0
	 */
	public function dynamic_form() {
		$this->get_form( $_POST['slug'] );
		$this->get_comments( $_POST['slug'] );
		$this->lock( $_POST['slug'] );
		die();
	}

	public function lock($slug) {
		echo '<BR /><a id=plugin_lock_update_' . $slug . '>';
		if ( $this->lock_status( $slug ) ) {
			echo 'un-lock';
		} else {
			echo 'Lock this plugin (do not find updates)';
		}
		echo '</a>';
	}

	private function lock_status($slug) {
		$lock_plugins = get_option( 'wds_plugin_lock_updates' );
		if ( is_array( $lock_plugins ) && in_array( $slug, $lock_plugins ) ) {
			return true;
		}

		return false;
	}

	public function toggle_lock() {
		$lock_plugins = get_option( 'wds_plugin_lock_updates' );
		// If this plugin is in the list, remove it.
		if ( in_array( $_POST['slug'], $lock_plugins ) ) {
			$new_lock_plugins = array();
			foreach ( $lock_plugins as $plugin ) {
				if ( $_POST['slug'] != $plugin ) {
					$new_lock_plugins[] = $plugin;
				}
			}

			if ( isset( $new_lock_plugins ) ) {
				update_option( 'wds_plugin_lock_updates', $new_lock_plugins );
			}

			// If this plugin isn't in the array add it.
		} else {
			$lock_plugins[] = $_POST['slug'];
			update_option( 'wds_plugin_lock_updates', $lock_plugins );
		}
	}

	/**
	 * @param $slug
	 *
	 * Create the form for the slug.
	 */
	public function get_form( $slug ) {
		// @TODO: This is kinda ugly, refactor.
		echo '<a id=police_comment_link_' . $slug . '>';
		echo 'Add a Note';
		echo '</a>';

		echo '<div style="display: none;" id=police_comment_div_' . $slug . '>';
		echo '<input type=hidden name=slug value=' . $slug . '>';
		echo '<input type=text class="plugin_notes_' . $slug . '" name=note id=police_comment_' . $slug . '>';
		echo '<input type=button value="Add a Note" id=police_comment_submit_' . $slug . '>';
		echo '</div>';
	}

	/**
	 * Get the existing comments.
	 *
	 * @since 0.1.0
	 *
	 * @param $slug
	 */
	public function get_comments( $slug ) {
		$args    = array(
			'post_type'  => 'wdspp-plugin-police',
			'meta_query' => array(
				array(
					'key'     => 'pp_slug',
					'value'   => $slug,
					'compare' => 'IN',
				),
			),
		);
		$results = new WP_Query( $args );
		foreach ( $results->posts as $post ) {
			// @TODO: Clean this up.
			$user_info = get_userdata( $post->post_author );
			echo '<div style="font-size:smaller;">';
			echo '<div>' . $post->post_content . '</div>';
			echo '<i>' . $user_info->user_login . ' </i>';
			echo 'on ' . $post->post_date;
			echo '</div>';
			echo '<HR>';
		}
	}

	/**
	 * Save incoming comment.
	 *
	 * @since 0.1.0
	 */
	public function save_comment() {
		$args = array(
			'post_content' => $_POST['comment'],
			'post_status'  => 'publish',
			'post_type'    => 'wdspp-plugin-police',
		);
		$id   = wp_insert_post( $args );
		update_post_meta( $id, 'pp_slug', $_POST['slug'] );
	}
}
