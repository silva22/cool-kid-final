<?php

namespace WPFormsUserRegistration\Migrations;

use WPForms\Migrations\Base;

/**
 * Class Migrations handles addon upgrade routines.
 *
 * @since 2.1.0
 */
class Migrations extends Base {

	/**
	 * WP option name to store the migration versions.
	 *
	 * @since 2.1.0
	 */
	const MIGRATED_OPTION_NAME = 'wpforms_user_registration_versions';

	/**
	 * Current plugin version.
	 *
	 * @since 2.1.0
	 */
	const CURRENT_VERSION = WPFORMS_USER_REGISTRATION_VERSION;

	/**
	 * Name of plugin used in log messages.
	 *
	 * @since 2.1.0
	 */
	const PLUGIN_NAME = 'WPForms User Registration';

	/**
	 * Upgrade classes.
	 *
	 * @since 2.1.0
	 */
	const UPGRADE_CLASSES = [
		'Upgrade200',
	];
}
