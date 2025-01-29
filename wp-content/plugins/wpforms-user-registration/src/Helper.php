<?php

namespace WPFormsUserRegistration;

use WP_User;

/**
 * Main Helper class.
 *
 * @since 2.0.0
 */
class Helper {

	/**
	 * Maybe set user role.
	 *
	 * @since 2.0.0
	 *
	 * @param int $user_id User Id.
	 */
	public static function set_user_role( $user_id ) {

		$user = new WP_User( $user_id );

		// Check if we need to assign new role.
		$role = $user->get( 'wpforms-role' );

		if ( $role && empty( $user->roles ) ) {
			$user->set_role( $role );
		}

		delete_user_meta( $user_id, 'wpforms-role' );
	}
}
