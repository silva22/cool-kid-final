<?php
/**
 * Plugin Name:       WPForms User Registration
 * Plugin URI:        https://wpforms.com
 * Description:       User Registration, Login and Reset Password forms with WPForms.
 * Requires at least: 5.5
 * Requires PHP:      7.0
 * Author:            WPForms
 * Author URI:        https://wpforms.com
 * Version:           2.5.0
 * Text Domain:       wpforms-user-registration
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

use WPFormsUserRegistration\Plugin;

// Exit if accessed directly.
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}

/**
 * Plugin version.
 *
 * @since 1.0.0
 */
const WPFORMS_USER_REGISTRATION_VERSION = '2.5.0';

/**
 * Plugin file.
 *
 * @since 1.0.0
 */
const WPFORMS_USER_REGISTRATION_FILE = __FILE__;

/**
 * Plugin path.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_USER_REGISTRATION_PATH', plugin_dir_path( WPFORMS_USER_REGISTRATION_FILE ) );

/**
 * Plugin URL.
 *
 * @since 1.0.0
 */
define( 'WPFORMS_USER_REGISTRATION_URL', plugin_dir_url( WPFORMS_USER_REGISTRATION_FILE ) );

/**
 * Check addon requirements.
 *
 * @since 2.0.0
 * @since 2.2.0 Uses requirements feature.
 */
function wpforms_user_registration_load() {

	$requirements = [
		'file'    => WPFORMS_USER_REGISTRATION_FILE,
		'wpforms' => '1.8.6',
	];

	if ( ! function_exists( 'wpforms_requirements' ) || ! wpforms_requirements( $requirements ) ) {
		return;
	}

	wpforms_user_registration();
}

add_action( 'wpforms_loaded', 'wpforms_user_registration_load' );

/**
 * Get the instance of the addon main class.
 *
 * @since 2.0.0
 *
 * @return Plugin
 */
function wpforms_user_registration() {

	require_once WPFORMS_USER_REGISTRATION_PATH . 'vendor/autoload.php';

	return Plugin::get_instance();
}
