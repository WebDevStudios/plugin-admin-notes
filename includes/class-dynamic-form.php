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
		add_filter( 'auto_update_plugin', array( $this, 'auto_update_stored_plugins' ), 10, 2 );
	}

	/**
	 * Create the dynamic stuff to be returned from the admin-ajax request.
	 *
	 * @since 0.1.0
	 */
	public function dynamic_form() {
		$this->update( $_POST['slug'] );
		$this->lock( $_POST['slug'] );
		$this->get_form( $_POST['slug'] );
		$this->get_comments( $_POST['slug'] );
		die();
	}

	public function lock( $slug ) {
		if ( $this->lock_status( $slug ) ) {
			echo '<a href="javascript:void(0)" id=plugin_lock_update_' . $slug . ' aria-label="Unlock the ' . $slug . '  plugin"';
			echo '<i class="fa fa-lock fa-lg green" aria-hidden="true"></i>';
		} else {
			echo '<a href="javascript:void(0)" id=plugin_lock_update_' . $slug . ' aria-label="Lock the ' . $slug . '  plugin"';
			echo '<i class="fa fa-lock fa-lg grey" aria-hidden="true"></i>';
		}
		echo '</a>';
	}

	public function lock_status( $slug ) {
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
	 * Set status for auto-updating.
	 */
	public function update( $slug ) {
		if ( $this->update_status( $slug ) ) {
			echo '<a href="javascript:void(0)" id=plugin_auto_update_' . $slug . ' aria-label="Turn off auto updates for the ' . $slug . '  plugin"';
			echo '<i class="fa fa-refresh fa-lg green"></i>';
		} else {
			echo '<a href="javascript:void(0)" id=plugin_auto_update_' . $slug . ' aria-label="Turn on auto updates for the ' . $slug . '  plugin"';
			echo '<i class="fa fa-refresh fa-lg grey"></i>';
		}
		echo '</a>';
	}

	private function update_status( $slug ) {
		$update_plugins = get_option( 'wds_plugin_updates_auto_updates' );
		if ( is_array( $update_plugins ) && in_array( $slug, $update_plugins ) ) {
			return true;
		}

		return false;
	}

	/**
	 * @param $slug
	 *
	 * Create the form for the slug.
	 */
	public function get_form( $slug ) {
		// @TODO: This is kinda ugly, refactor.
		echo '<BR><a href="javascript:void(0);" id=police_comment_link_' . $slug . '>';
		echo 'Add a Note';
		echo '</a>';

		echo '<div style="display: none;" id=police_comment_div_' . $slug . '>';
		echo '<input type=hidden name=slug value=' . $slug . '>';
		echo '<input type=text class="plugin_notes_' . $slug . ' plugin-admin-notes-note" name=note id=police_comment_' . $slug . '>';
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

	/**
	 * Toggle the udpate status.
	 *
	 *
	 */
	public function toggle_updates() {
		$update_plugins = get_option( 'wds_plugin_updates_auto_updates' );

		// If this plugin is in the list, remove it.
		if ( in_array( $_POST['slug'], $update_plugins ) ) {
			$new_update_plugins = array();

			foreach ( $update_plugins as $plugin ) {
				if ( $_POST['slug'] != $plugin ) {
					$new_update_plugins[] = $plugin;
				}
			}

			if ( isset( $new_update_plugins ) ) {
				update_option( 'wds_plugin_updates_auto_updates', $new_update_plugins );
			}

			// If this plugin isn't in the array add it.
		} else {
			$update_plugins[] = $_POST['slug'];
			update_option( 'wds_plugin_updates_auto_updates', $update_plugins );
		}
	}

	/**
	 * Sets the auto-update for the plugins that are in the updates array.
	 *
	 *
	 */
	public function auto_update_stored_plugins( $update, $item ) {
		$plugins = get_option( 'wds_plugin_updates_auto_updates' );
		if ( in_array( $item->slug, $plugins ) ) {
			return true; // Always update plugins in this array
		} else {
			return $update; // Else, use the normal API response to decide whether to update or not
		}
	}
}
