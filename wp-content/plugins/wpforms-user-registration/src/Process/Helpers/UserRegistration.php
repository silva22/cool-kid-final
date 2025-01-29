<?php

namespace WPFormsUserRegistration\Process\Helpers;

use WPFormsUserRegistration\Admin\Builder\Registration as BuilderRegistration;

/**
 * Helper class for registration processing.
 *
 * @since 2.0.0
 */
class UserRegistration {

	/**
	 * Temporary storage for password.
	 *
	 * @since 2.0.0
	 *
	 * @var string
	 */
	private static $password = '';

	/**
	 * Set user password.
	 *
	 * @since 2.0.0
	 *
	 * @param string $password Password.
	 */
	public static function set_password( $password ) {

		self::$password = $password;
	}

	/**
	 * Get user password.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_password() {

		if ( ! empty( self::$password ) ) {
			return self::$password;
		}

		$password = wp_generate_password( 18 );

		self::set_password( $password );

		return $password;
	}

	/**
	 * Decide if this user requires activation and if so what type.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_settings The form settings.
	 *
	 * @return string
	 */
	public static function get_activation_type( $form_settings ) {

		return ! empty( $form_settings['registration_activation'] ) && ! empty( $form_settings['registration_activation_method'] )
			? $form_settings['registration_activation_method'] : '';
	}

	/**
	 * Check if registration enabled.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data The information for the form.
	 *
	 * @return bool
	 */
	public static function is_registration_enabled( $form_data ) {

		return ( ! empty( $form_data['meta']['template'] ) && $form_data['meta']['template'] === BuilderRegistration::TEMPLATE_SLUG ) || ! empty( $form_data['settings']['registration_enable'] );
	}
}
