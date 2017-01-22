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
		add_action( 'wp_ajax_pp_toggle_updates', array($this, 'toggle_updates') );
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

			// @TODO: Refactor this .js into a file.
			?>
			<div class="pluginnote" id="<?php echo $plugin_data['slug']; ?>" style="width:160px"></div>
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

}
