<?php
/**
 * WDS Plugin Police View
 *
 * @since   0.1.0
 * @package WDS Plugin Police
 */

/**
 * WDS Plugin Police View.
 *
 * @since 0.1.0
 */
class WDSPP_View {
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
		add_filter( 'manage_plugins_columns', array( $this, 'add_column' ) );
		add_action( 'manage_plugins_custom_column', array( $this, 'render_column' ), 10, 3 );
		add_action( 'wp_ajax_pp_dynamic_form', array( $this, 'display_form' ) );
		add_action( 'wp_ajax_pp_receive_comment', array( $this, 'receive_comment' ) );
		add_action( 'wp_ajax_pp_toggle_updates', array( $this, 'toggle_updates' ) );
		add_action( 'wp_ajax_pp_lock_updates', array( $this, 'toggle_lock' ) );
		add_filter( 'plugin_action_links', array( $this, 'remove_updates_plugin_page' ), 10, 4 );
		add_action( 'plugins_loaded', array( $this, 'remove_updates_globally' ), 2 );
	}

	/**
	 * Add a column to the plugins view.
	 *
	 * @since 0.1.0
	 *
	 * @param $columns
	 *
	 * @return mixed
	 */
	public function add_column( $columns ) {
		$columns['plugin_admin_notes'] = esc_html__( 'Notes', 'plugin-admin-notes' );

		return $columns;
	}

	/**
	 * Render the data in each TD.
	 *
	 * @since 0.1.0
	 *
	 * @param $column_name
	 * @param $plugin_file
	 * @param $plugin_data
	 */
	public function render_column( $column_name, $plugin_file, $plugin_data ) {
		if ( 'plugin_admin_notes' == $column_name ) {

			if ( ! isset( $plugin_data['slug'] ) ) {
				$slug = sanitize_title( $plugin_data['Name'] );
			} else {
				$slug = $plugin_data['slug'];
			}

			global $wp_filter;
			if ( key_exists( 'plugin', $plugin_data ) && isset( $wp_filter[ 'after_plugin_row_' . $plugin_data['plugin'] ] ) && $this->plugin->dynamic_form->lock_status( $slug ) ) {
				unset( $wp_filter[ 'after_plugin_row_' . $plugin_data['plugin'] ] );
			}

			if ( file_exists( $this->plugin->path . 'pluginnotes.log' ) ) {
				file_put_contents( $this->plugin->path . 'pluginnotes.log', print_r( $plugin_data, 1 ), FILE_APPEND );
			}

			?>
			<div class="pluginnote" id="<?php echo $slug; ?>" style="width:160px">
				<?php
				$this->plugin->dynamic_form->update( $slug );
				$this->plugin->dynamic_form->lock( $slug );
				$this->plugin->dynamic_form->get_form( $slug );
				$this->plugin->dynamic_form->get_comments( $slug );
				?>
			</div>
			<?php
		}
	}

	/**
	 * Display the form.
	 *
	 * @since 0.1.0
	 */
	public function display_form() {
		$this->plugin->dynamic_form->dynamic_form();
	}

	/**
	 * Handle an incoming comment.
	 *
	 * @since 0.1.0
	 */
	public function receive_comment() {
		$this->plugin->dynamic_form->save_comment();
		$this->display_form();
	}


	public function toggle_updates() {
		$this->plugin->dynamic_form->toggle_updates();
		$this->display_form();
	}


	public function toggle_lock() {
		$this->plugin->dynamic_form->toggle_lock();
		$this->display_form();
	}

	public function remove_updates_plugin_page( $actions, $plugin_file, $plugin_data, $context ) {
		$plugin_update = get_option( '_site_transient_update_plugins' );

		if ( isset( $plugin_data['slug'] ) && $this->plugin->dynamic_form->lock_status( $plugin_data['slug'] ) ) {
			if ( key_exists( $plugin_data['plugin'], $plugin_update->response ) ) {

				// Set the no_update to the same data as the update.
				$plugin_update->no_update[ $plugin_data['plugin'] ] = $plugin_update->response[ $plugin_data['plugin'] ];

				// Unset the update data.
				unset( $plugin_update->response[ $plugin_data['plugin'] ] );

				// Rewrite the options.
				update_option( '_site_transient_update_plugins', $plugin_update );

			}
		}

		return $actions;

	}

	public function remove_updates_globally() {

		$plugin_update  = get_transient( 'update_plugins' );
		$locked_updates = get_option( 'wds_plugin_lock_updates' );

		if ( is_array( $plugin_update ) ) {
			
			foreach ( $plugin_update->response as $plugin_key => $plugin_array ) {

				$plugin_slug = $plugin_array->slug;
				if ( in_array( $plugin_slug, $locked_updates ) ) {
					$plugin_update->no_update[ $plugin_key ]              = $plugin_update->response[ $plugin_key ];
					$plugin_update->no_update[ $plugin_key ]->new_version = $plugin_update->checked[ $plugin_key ];

					// Unset the update data.
					unset( $plugin_update->response[ $plugin_key ] );

					// Rewrite the options.
					set_transient( 'update_plugins', $plugin_update, WEEK_IN_SECONDS );

				}
			}
		}
	}

	public function remove_plugin( $slug ) {
		$args  = array(
			'post_type'      => 'wdspp-plugin-police',
			'meta_query'     => array(
				array(
					'key'     => 'pp_slug',
					'value'   => $slug,
					'compare' => '=',
				),
			),
			'fields'         => 'ids',
			'posts_per_page' => - 1,
		);
		$posts = new WP_Query( $args );

		foreach ( $posts->posts as $post ) {
			$result = wp_delete_post( $post, true );
		}

		$update_plugins = get_option( 'wds_plugin_updates_auto_updates' );
		foreach ( $update_plugins as $index => &$plugin_name ) {
			if ( $slug === $plugin_name ) {
				unset( $update_plugins[ $index ] );
			}
		}
		update_option( 'wds_plugin_updates_auto_updates', $update_plugins );

		$lock_plugins = get_option( 'wds_plugin_lock_updates' );
		foreach ( $lock_plugins as $index => &$plugin_name ) {
			if ( $slug === $plugin_name ) {
				unset( $lock_plugins[ $index ] );
			}
		}
		update_option( 'wds_plugin_lock_updates', $lock_plugins );
	}

}
