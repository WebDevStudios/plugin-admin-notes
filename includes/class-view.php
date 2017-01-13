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
			<div id="<?php echo 'police_' . $plugin_data['slug']; ?>" style="width:160px"></div>

			<script type="text/javascript">
				jQuery( document ).ready( function ( $ ) {

					var data = {
						'action':  'pp_dynamic_form',
						'slug':    '<?php echo $plugin_data['slug'] ?>',
						'plugin':  '<?php echo $plugin_data['plugin'] ?>',
						'version': '<?php echo $plugin_data['Version'] ?>',
						'update':  '<?php echo $plugin_data['update'] ?>'
					};

					jQuery.post( ajaxurl, data, function ( response ) {
						console.log( 'Got this from the server: ' + response );
						jQuery( "#<?php echo 'police_' . $plugin_data['slug']; ?>" ).html( response );
					} );

					jQuery(document).on("click", "#<?php echo 'police_comment_submit_' . $plugin_data['slug']; ?>", function() {

						console.log( 'stuff' );

						var policeComment = {
							'action':  'pp_receive_comment',
							'comment' : jQuery( '#<?php echo 'police_comment_' . $plugin_data['slug']; ?>' ).val(),
						    'slug':    '<?php echo $plugin_data['slug'] ?>',
							'plugin':  '<?php echo $plugin_data['plugin'] ?>',
							'version': '<?php echo $plugin_data['Version'] ?>',
							'update':  '<?php echo $plugin_data['update'] ?>',
							'who': '<?php echo get_current_user_id() ?>'
						};

						jQuery.post( ajaxurl, policeComment, function ( response ) {
							jQuery( "#<?php echo 'police_' . $plugin_data['slug']; ?>" ).html( response );
						} );

						return false;
					});

					jQuery(document).on("click", "#<?php echo 'police_comment_link_' . $plugin_data['slug']; ?>", function() {
						jQuery('#<?php echo 'police_comment_div_' . $plugin_data['slug']; ?>').show();
						jQuery('#<?php echo 'police_comment_link_' . $plugin_data['slug']; ?>').hide();
					} );

				} );

			</script><?php
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

}
