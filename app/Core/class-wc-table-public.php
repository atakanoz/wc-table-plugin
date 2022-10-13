<?php
/**
 * The public-facing functionality of the plugin.
 *
 * @link       pangolia.com
 * @since      1.0.0
 *
 * @package    ComparisonTable
 * @subpackage ComparisonTable/public
 */

/**
 * The public-facing functionality of the plugin.
 *
 * Defines the plugin name, version, and two examples hooks for how to
 * enqueue the public-facing stylesheet and JavaScript.
 *
 * @package    ComparisonTable
 * @subpackage ComparisonTable/public
 * @author     Atakan Oz <authoeremail.com>
 */

namespace ComparisonTable\Core;

/**
 * Public Side
 */
class Frontend {

	/**
	 * The ID of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $plugin_name    The ID of this plugin.
	 */
	private $plugin_name;

	/**
	 * The version of this plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 * @var      string    $version    The current version of this plugin.
	 */
	private $version;

	/**
	 * Initialize the class and set its properties.
	 *
	 * @since    1.0.0
	 * @param      string $plugin_name       The name of the plugin.
	 * @param      string $version    The version of this plugin.
	 */
	public function __construct( $plugin_name, $version ) {

		$this->plugin_name = $plugin_name;
		$this->version     = $version;

	}

	/**
	 * Register the stylesheets for the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function enqueue_styles() {

		/**
		 * This function is provided for demonstration purposes only.
		 *
		 * An instance of this class should be passed to the run() function
		 * defined in ComparisonTable_Loader as all of the hooks are defined
		 * in that particular class.
		 *
		 * The ComparisonTable_Loader will then create the relationship
		 * between the defined hooks and the functions defined in this
		 * class.
		 */

		global $post;

		// Only enqueue styles if page/post has the shortcode in their content.
		if ( has_shortcode( $post->post_content, 'winner_comparison_table' ) ) {
			wp_enqueue_style( $this->plugin_name, COMPARISON_TABLE_PLUGIN_URL . 'resources/public/dist/styles.bundle.css', array(), $this->version, 'all' );
		}

	}

}