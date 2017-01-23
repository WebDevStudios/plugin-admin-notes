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
		add_action( 'wp_ajax_pp_lock_updates', array( $this, 'toggle_lock' ) );

		add_filter( 'plugin_action_links', array( $this, 'remove_update' ), 10, 4 );
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
		$columns['plugin_police'] = 'Plugin Police';

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
		if ( 'plugin_police' == $column_name ) {

			if ( ! isset( $plugin_data['slug'] ) ) {
				$slug = sanitize_title( $plugin_data['Name'] );
			} else {
				$slug = $plugin_data['slug'];
			}

			?>
			<div class="pluginnote" id="<?php echo $slug; ?>" style="width:160px"></div>
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

	public function toggle_lock() {
		$this->plugin->dynamic_form->toggle_lock();
		$this->display_form();
	}

	public function remove_update( $actions, $plugin_file, $plugin_data, $context ) {
		$plugin_update = get_option( '_site_transient_update_plugins' );

		if ( $this->plugin->dynamic_form->lock_status( $plugin_data['slug'] ) ) {
			error_log( $plugin_data['plugin'] . ' is locked' );
			if ( key_exists( $plugin_data['plugin'], $plugin_update->response ) ) {

				// Set the no_update to the same data as the update.
				$plugin_update->no_update[$plugin_data['plugin']] = $plugin_update->response[ $plugin_data['plugin']];

				// Unset the update data.
				unset( $plugin_update->response[ $plugin_data['plugin'] ] );

				// Rewrite the options.
				update_option( '_site_transient_update_plugins', $plugin_update );
			}
		}

	}

}
