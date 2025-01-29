<?php
/**
 * Plugin Name:       WPForms GetResponse
 * Plugin URI:        https://wpforms.com
 * Description:       GetResponse integration with WPForms.
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           1.6.0
 * Requires at least: 5.2
 * Requires PHP:      5.6
 * Text Domain:       wpforms-getresponse
 * Domain Path:       languages
 *
 * WPForms is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 2 of the License, or
 * any later version.
 *
 * WPForms is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with WPForms. If not, see <https://www.gnu.org/licenses/>.
 */

use WPFormsGetResponse\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
const WPFORMS_GETRESPONSE_VERSION = '1.6.0';

/**
 * Plugin file.
 *
 * @since 1.0.0
 */
const WPFORMS_GETRESPONSE_FILE = __FILE__;

/**
 * Plugin path.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_GETRESPONSE_PATH', plugin_dir_path( WPFORMS_GETRESPONSE_FILE ) );

/**
 * Plugin URL.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_GETRESPONSE_URL', plugin_dir_url( WPFORMS_GETRESPONSE_FILE ) );

/**
 * Check addon requirements.
 *
 * @since 1.0.0
 * @since 1.3.0 Update API version.
 * @since 1.6.0 Uses requirements feature.
 */
function wpforms_getresponse_load() {

	$requirements = [
		'file'    => WPFORMS_GETRESPONSE_FILE,
		'wpforms' => '1.8.3',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return;
	}

	wpforms_getresponse();
}

add_action( 'wpforms_loaded', 'wpforms_getresponse_load' );

/**
 * Get the instance of the addon main class.
 *
 * @since 1.3.0
 *
 * @return Plugin
 */
function wpforms_getresponse() {

	// Load the GetResponse addon.
	require_once WPFORMS_GETRESPONSE_PATH . 'vendor/autoload.php';

	// Get all active integrations.
	$providers = wpforms_get_providers_options();

	// Load v2 API integration if the user currently has it setup.
	if ( ! empty( $providers['getresponse'] ) ) {
		require_once WPFORMS_GETRESPONSE_PATH . 'deprecated/class-getresponse.php';
	}

	return Plugin::get_instance();
}
