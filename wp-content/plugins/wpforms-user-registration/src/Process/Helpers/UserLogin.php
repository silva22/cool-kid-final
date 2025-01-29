<?php

namespace WPFormsUserRegistration\Process\Helpers;

/**
 * Helper class for login processing.
 *
 * @since 2.0.0
 */
class UserLogin {

	/**
	 * If the user wants SSL but the session is not SSL, force a secure cookie.
	 *
	 * @since 2.0.0
	 *
	 * @param array $credentials Credentials.
	 *
	 * @return bool|string ( Empty string is default value for wp_signon )
	 */
	public static function maybe_force_secure_cookie( $credentials ) {

		if ( empty( $credentials['user_login'] ) || force_ssl_admin() ) {
			return '';
		}

		$user_name = sanitize_user( wp_unslash( $credentials['user_login'] ) );
		$user      = get_user_by( 'login', $user_name );

		if ( ! $user && strpos( $user_name, '@' ) ) {
			$user = get_user_by( 'email', $user_name );
		}

		if ( ! $user || ! get_user_option( 'use_ssl', $user->ID ) ) {
			return '';
		}

		force_ssl_admin( true );

		return true;
	}
}
