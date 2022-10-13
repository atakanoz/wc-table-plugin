<?php
/**
 * The plugin bootstrap file
 *
 * This file is read by WordPress to generate the plugin information in the plugin
 * admin area. This file also includes all of the dependencies used by the plugin,
 * registers the activation and deactivation functions, and defines a function
 * that starts the plugin.
 *
 * @link              pangolia.com
 * @since             1.0.0
 * @package           ComparisonTable
 *
 * @wordpress-plugin
 * Plugin Name:       Comparison Table
 * Plugin URI:        pangolia.com
 * Description:       This is a short description of what the plugin does. It's displayed in the WordPress admin area.
 * Version:           1.0.0
 * Author:            Atakan Oz
 * Author URI:        pangolia.com
 * License:           GPL-2.0+
 * License URI:       http://www.gnu.org/licenses/gpl-2.0.txt
 * Text Domain:       comparison_table
 * Domain Path:       /languages
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Global Definitions.
 * Start at version 1.0.0 and use SemVer - https://semver.org
 * Rename this for your plugin and update it as you release new versions.
 */
define( 'COMPARISON_TABLE', 'comparison_table' );
define( 'COMPARISON_TABLE_NAME', 'ComparisonTable' );
define( 'COMPARISON_TABLE_VERSION', '1.0.0' );
define( 'COMPARISON_TABLE_FILE', __FILE__ );
define( 'COMPARISON_TABLE_PLUGIN_DIR', trailingslashit( plugin_dir_path( COMPARISON_TABLE_FILE ) ) );
define( 'COMPARISON_TABLE_PLUGIN_URL', trailingslashit( plugin_dir_url( COMPARISON_TABLE_FILE ) ) );

// require_once COMPARISON_TABLE_PLUGIN_DIR . 'resources/admin/config.php';

/**
 * The core plugin class that is used to define internationalization,
 * admin-specific hooks, and public-facing site hooks.
 */
require COMPARISON_TABLE_PLUGIN_DIR . 'app/core/class-wc-table.php';

/**
 * Begins execution of the plugin.
 *
 * Since everything within the plugin is registered via hooks,
 * then kicking off the plugin from this point in the file does
 * not affect the page life cycle.
 *
 * @since    1.0.0
 */
function run_comparison_table() {

	$plugin = new ComparisonTable\Core\Init();
	$plugin->run();

}
run_comparison_table();