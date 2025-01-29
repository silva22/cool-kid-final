<?php

namespace WPFormsUserRegistration\Process\Helpers;

// phpcs:ignore WPForms.PHP.UseStatement.UnusedUseStatement
use WP_User;
use WPFormsUserRegistration\Admin\Builder\Reset as BuilderReset;

/**
 * Helper class for reset password processing.
 *
 * @since 2.0.0
 */
class UserReset {

	/**
	 * Get cookie name.
	 *
	 * @since 2.0.0
	 *
	 * @return string
	 */
	public static function get_cookie_name() {

		return 'wpforms-resetpass-' . COOKIEHASH;
	}

	/**
	 * Set cookie.
	 *
	 * @since 2.0.0
	 *
	 * @param string $value   Cookie value.
	 * @param int    $expires Expires time.
	 */
	public static function set_cookie( $value, $expires ) {

		if ( headers_sent() ) {
			return;
		}

		setcookie( self::get_cookie_name(), $value, $expires, COOKIEPATH ? COOKIEPATH : '/', COOKIE_DOMAIN, is_ssl(), true );
	}

	/**
	 * Checking if reset in progress.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	public static function is_reset_in_progress() {

		return isset( $_COOKIE[ self::get_cookie_name() ] ) && 0 < strpos( wp_unslash( $_COOKIE[ self::get_cookie_name() ] ), ':' ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized
	}

	/**
	 * Depending on stage display login or new password field.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return array
	 */
	public static function filter_form_fields( $form_data ) {

		if (
			empty( $form_data['meta']['template'] ) ||
			$form_data['meta']['template'] !== BuilderReset::TEMPLATE_SLUG ||
			empty( $form_data['id'] )
		) {
			return $form_data;
		}

		$is_reset = self::is_reset_in_progress();
		$is_user  = self::get_user();

		foreach ( $form_data['fields'] as $id => $field ) {
			// if reset in progress and user exists display password field.
			if ( $is_reset && $is_user && $field['type'] === 'password' ) {
				continue;
			}

			// if reset not in progress or user is invalid display username/email field.
			if ( $field['type'] !== 'password' && ( ! $is_reset || ! $is_user ) ) {
				continue;
			}

			unset( $form_data['fields'][ $id ] );
		}

		return $form_data;
	}

	/**
	 * Get User from the cookie.
	 *
	 * @since 2.0.0
	 *
	 * @return bool|WP_User
	 */
	public static function get_user() {

		if ( empty( $_COOKIE[ self::get_cookie_name() ] ) ) {
			return false;
		}

		list( $rp_login, $rp_key ) = explode( ':', wp_unslash( $_COOKIE[ self::get_cookie_name() ] ), 2 ); // phpcs:disable WordPress.Security.ValidatedSanitizedInput.InputNotSanitized

		$user = check_password_reset_key( $rp_key, $rp_login );

		if ( is_wp_error( $user ) ) {
			return false;
		}

		return $user;
	}
}
