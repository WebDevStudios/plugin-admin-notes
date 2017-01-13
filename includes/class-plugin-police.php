<?php
/**
 * WDS Plugin Police Plugin_police
 *
 * @since 0.1.0
 * @package WDS Plugin Police
 */

require_once dirname( __FILE__ ) . '/../vendor/cpt-core/CPT_Core.php';

/**
 * WDS Plugin Police Plugin_police post type class.
 *
 * @see https://github.com/WebDevStudios/CPT_Core
 * @since 0.1.0
 */
class WDSPP_Plugin_police extends CPT_Core {
	/**
	 * Parent plugin class
	 *
	 * @var WDS_Plugin_Police
	 * @since  0.1.0
	 */
	protected $plugin = null;

	/**
	 * Constructor
	 * Register Custom Post Types. See documentation in CPT_Core, and in wp-includes/post.php
	 *
	 * @since  0.1.0
	 * @param  WDS_Plugin_Police $plugin Main plugin object.
	 * @return void
	 */
	public function __construct( $plugin ) {
		$this->plugin = $plugin;
		$this->hooks();

		// Register this cpt
		// First parameter should be an array with Singular, Plural, and Registered name.
		parent::__construct(
			array( __( 'WDS Plugin Police Plugin_police', 'wds-plugin-police' ), __( 'WDS Plugin Police Plugin_polices', 'wds-plugin-police' ), 'wdspp-plugin-police' ),
			array(
				'supports' => array( 'title', 'editor', 'excerpt', 'thumbnail' ),
				'public' => 'false',
				'show_in_menu' => 'false',
				)
		);
	}

	/**
	 * Initiate our hooks
	 *
	 * @since  0.1.0
	 * @return void
	 */
	public function hooks() {

	}
}
