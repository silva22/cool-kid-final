<?php

namespace WPFormsUserRegistration;

/**
 * SmartTags class.
 *
 * @since 2.0.0
 */
class SmartTags {

	/**
	 * Initialize.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_smart_tags', [ $this, 'tags' ] );
		add_filter( 'wpforms_smarttags_get_smart_tag_class_name', [ $this, 'get_smart_tag_class_name' ], 10, 2 );
	}

	/**
	 * Add new tags.
	 *
	 * @since 2.0.0
	 *
	 * @param array $tags Available tags.
	 *
	 * @return array
	 */
	public function tags( $tags ) {

		$tags['user_registration_login']          = esc_html__( 'User Registration Login', 'wpforms-user-registration' );
		$tags['user_registration_email']          = esc_html__( 'User Registration Email', 'wpforms-user-registration' );
		$tags['user_registration_password']       = esc_html__( 'User Registration Password', 'wpforms-user-registration' );
		$tags['url_user_activation']              = esc_html__( 'User Registration user activation URL', 'wpforms-user-registration' );
		$tags['url_manage_activations']           = esc_html__( 'Admin manage users activations URL', 'wpforms-user-registration' );
		$tags['user_registration_password_reset'] = esc_html__( 'User Registration user password reset URL', 'wpforms-user-registration' );

		return $tags;
	}

	/**
	 * Maybe adjust smart tag class name.
	 *
	 * @since 2.0.0
	 *
	 * @param string $class_name     Smart tag class.
	 * @param string $smart_tag_name Smart tag name.
	 *
	 * @return string
	 */
	public function get_smart_tag_class_name( $class_name, $smart_tag_name ) {

		$full_class_name = '\\WPFormsUserRegistration\\SmartTags\\' . str_replace( ' ', '', ucwords( str_replace( '_', ' ', $smart_tag_name ) ) );

		if ( class_exists( $full_class_name ) ) {
			return $full_class_name;
		}

		return $class_name;
	}
}
