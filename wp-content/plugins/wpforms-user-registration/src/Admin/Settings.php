<?php

namespace WPFormsUserRegistration\Admin;

use WPFormsUserRegistration\EmailNotifications\Helper;

/**
 * Addon settings.
 *
 * @since 2.0.0
 */
class Settings {

	/**
	 * Add hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_filter( 'wpforms_settings_defaults', [ $this, 'email_settings' ] );
	}

	/**
	 * Maybe add legacy email settings.
	 *
	 * @since 2.0.0
	 *
	 * @param array $settings Settings.
	 *
	 * @return array
	 */
	public function email_settings( $settings ) {

		if ( get_option( Helper::LEGACY_EMAILS ) !== '1' ) {
			return $settings;
		}

		$email_setting = [
			'user-registration-template' => [
				'id'        => 'user-registration-template',
				'name'      => esc_html__( 'User Registration Template', 'wpforms-user-registration' ),
				'desc'      => esc_html__( 'Choose the HTML Template used with User Registration. Our Modern template is recommended if you have not customized the emails using code (WordPress hooks).', 'wpforms-user-registration' ),
				'type'      => 'select',
				'choicesjs' => true,
				'default'   => 'legacy',
				'options'   => [
					'legacy' => esc_html__( 'Legacy', 'wpforms-user-registration' ),
					'modern' => esc_html__( 'Modern', 'wpforms-user-registration' ),
				],
			],
		];

		$settings['email'] = wpforms_list_insert_after( $settings['email'], 'email-template', $email_setting );

		return $settings;
	}
}
