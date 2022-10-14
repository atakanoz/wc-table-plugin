<?php
/**
 * The file that defines the core plugin class
 *
 * A class definition that includes attributes and functions used across both the
 * public-facing side of the site and the admin area.
 *
 * @link       pangolia.com
 * @since      1.0.0
 *
 * @package    ComparisonTable
 * @subpackage ComparisonTable/includes
 */

/**
 * The core plugin class.
 *
 * This is used to define internationalization, admin-specific hooks, and
 * public-facing site hooks.
 *
 * Also maintains the unique identifier of this plugin as well as the current
 * version of the plugin.
 *
 * @since      1.0.0
 * @package    ComparisonTable
 * @subpackage ComparisonTable/includes
 * @author     Atakan Oz <authoeremail.com>
 */

namespace ComparisonTable\Core;

/**
 * Core
 */
class Init {

	/**
	 * The loader that's responsible for maintaining and registering all hooks that power
	 * the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      ComparisonTable_Loader    $loader    Maintains and registers all hooks for the plugin.
	 */
	protected $loader;

	/**
	 * The unique identifier of this plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $plugin_name    The string used to uniquely identify this plugin.
	 */
	protected $plugin_name;

	/**
	 * The current version of the plugin.
	 *
	 * @since    1.0.0
	 * @access   protected
	 * @var      string    $version    The current version of the plugin.
	 */
	protected $version;

	/**
	 * Define the core functionality of the plugin.
	 *
	 * Set the plugin name and the plugin version that can be used throughout the plugin.
	 * Load the dependencies, define the locale, and set the hooks for the admin area and
	 * the public-facing side of the site.
	 *
	 * @since    1.0.0
	 */
	public function __construct() {

		if ( defined( 'COMPARISON_TABLE_VERSION' ) ) {
			$this->version = COMPARISON_TABLE_VERSION;
		} else {
			$this->version = '1.0.0';
		}
		$this->plugin_name = 'comparison_table';

		$this->load_dependencies();
		$this->define_admin_hooks();
		$this->define_public_hooks();

	}

	/**
	 * Load the required dependencies for this plugin.
	 *
	 * Include the following files that make up the plugin:
	 *
	 * - ComparisonTable_Loader. Orchestrates the hooks of the plugin.
	 * - ComparisonTable_i18n. Defines internationalization functionality.
	 * - ComparisonTable_Admin. Defines all hooks for the admin area.
	 * - ComparisonTable_Public. Defines all hooks for the public side of the site.
	 *
	 * Create an instance of the loader which will be used to register the hooks
	 * with WordPress.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function load_dependencies() {

		$includes_paths = COMPARISON_TABLE_PLUGIN_DIR . 'inc/*.php';

		foreach ( glob( '{' . $includes_paths . '}', GLOB_BRACE ) as $filename ) {
			require_once $filename;
		}

		$this->loader = new Loader();

	}

	/**
	 * Register all of the hooks related to the admin area functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_admin_hooks() {

		$plugin_admin = new Backend( $this->get_plugin_name(), $this->get_version() );

		// Enqueue Styles and Scripts.
		$this->loader->add_action( 'admin_enqueue_scripts', $plugin_admin, 'enqueue_styles' );

		// Init Post Type.
		$this->loader->add_action( 'init', $plugin_admin, 'comparison_table_post_type' );

		// Init Carbon Fields.
		$this->loader->add_action( 'plugins_loaded', $plugin_admin, 'add_carbon_fields' );

		// Register Fields.
		$this->loader->add_action( 'carbon_fields_register_fields', $plugin_admin, 'comparison_custom_fields' );

		// Shortcode Informer.
		$this->loader->add_action( 'edit_form_after_title', $plugin_admin, 'add_content_before_editor' );

		// Add the Shortcode.
		$this->loader->add_action( 'init', $plugin_admin, 'winners_shortcode' );

		$this->loader->add_filter( 'post_row_actions', $plugin_admin, 'remove_view_button', 10, 1 );

	}

	/**
	 * Register all of the hooks related to the public-facing functionality
	 * of the plugin.
	 *
	 * @since    1.0.0
	 * @access   private
	 */
	private function define_public_hooks() {

		$plugin_public = new Frontend( $this->get_plugin_name(), $this->get_version() );

		$this->loader->add_action( 'wp_enqueue_scripts', $plugin_public, 'enqueue_styles' );

	}

	/**
	 * Run the loader to execute all of the hooks with WordPress.
	 *
	 * @since    1.0.0
	 */
	public function run() {
		$this->loader->run();
	}

	/**
	 * The name of the plugin used to uniquely identify it within the context of
	 * WordPress and to define internationalization functionality.
	 *
	 * @since     1.0.0
	 * @return    string    The name of the plugin.
	 */
	public function get_plugin_name() {
		return $this->plugin_name;
	}

	/**
	 * The reference to the class that orchestrates the hooks with the plugin.
	 *
	 * @since     1.0.0
	 * @return    ComparisonTable_Loader    Orchestrates the hooks of the plugin.
	 */
	public function get_loader() {
		return $this->loader;
	}

	/**
	 * Retrieve the version number of the plugin.
	 *
	 * @since     1.0.0
	 * @return    string    The version number of the plugin.
	 */
	public function get_version() {
		return $this->version;
	}

}