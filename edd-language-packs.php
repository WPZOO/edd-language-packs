<?php
/**
 *
 * @package   EDD Language Packs
 * @author    Ulrich Pogson <ulrich@pogson.ch>
 * @license   GPL-2.0+
 * @link      http://wpzoo.ch/en/plugins/edd-language-packs/
 * @copyright 2015 WPZOO
 *
 * @wordpress-plugin
 * Plugin Name: EDD Language Packs
 * Plugin URI:  http://wpzoo.ch/en/plugins/edd-language-packs/
 * Description: Support languages pack for the themes and plugins. Checks for a valid license before sharing the download link. Requires EDD & EDD Software Licenses.
 * Version:     0.1.0
 * Author:      WPZOO, Ulrich Pogson
 * Author URI:  http://wpzoo.ch/
 * Text Domain: edd-language-packs
 * Domain Path: /languages
 * License:     GPL-2.0+
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

require_once( plugin_dir_path( __FILE__ ) . 'edd-sl-api.php' );

if ( ! is_admin() ) {
	return;
}

if ( class_exists( 'EDD_License' ) ) {
	$edd_language_packs_license = new EDD_License(
		__FILE__,
		'EDD Language Packs',
		'0.1.0',
		'WPZOO',
		'edd_language_packs_license_key',
		'http://wpzoo.ch'
	);
}

/**
 * Load textdomain
 *
 * @since 0.1.0
 */
function edd_lp_load_plugin_textdomain() {
	load_plugin_textdomain( 'edd-language-packs', FALSE, basename( dirname( __FILE__ ) ) . '/languages/' );
}
add_action( 'plugins_loaded', 'edd_lp_load_plugin_textdomain' );

require_once( plugin_dir_path( __FILE__ ) . 'metabox.php' );
