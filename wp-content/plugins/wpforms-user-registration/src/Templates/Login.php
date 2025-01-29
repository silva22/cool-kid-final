<?php

namespace WPFormsUserRegistration\Templates;

use WPForms_Template;

/**
 * User login form template.
 *
 * @since 2.0.0
 */
class Login extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'User Login Form', 'wpforms-user-registration' );
		$this->slug        = 'user_login';
		$this->description = esc_html__( 'Allow your users to easily log in to your site with their username and password.', 'wpforms-user-registration' );
		$this->includes    = '';
		$this->icon        = '';
		$this->modal       = '';
		$this->core        = true;
		$this->data        = [
			'field_id' => '3',
			'fields'   => [
				'0' => [
					'id'       => '0',
					'type'     => 'text',
					'label'    => esc_html__( 'Username or Email', 'wpforms-user-registration' ),
					'required' => '1',
					'size'     => 'medium',
					'meta'     => [
						'nickname' => 'login',
						'delete'   => false,
					],
				],
				'1' => [
					'id'       => '1',
					'type'     => 'password',
					'label'    => esc_html__( 'Password', 'wpforms-user-registration' ),
					'required' => '1',
					'size'     => 'medium',
					'meta'     => [
						'nickname' => 'password',
						'delete'   => false,
					],
				],
				'2' => [
					'id'         => '2',
					'type'       => 'checkbox',
					'label'      => esc_html__( 'Remember me', 'wpforms-user-registration' ),
					'required'   => '0',
					'size'       => 'medium',
					'choices'    => [
						[
							'label' => esc_html__( 'Remember me', 'wpforms-user-registration' ),
						],
					],
					'label_hide' => '1',
					'meta'       => [
						'nickname' => 'remember_me',
						'delete'   => false,
					],
				],
			],
			'settings' => [
				'confirmation_type'     => 'redirect',
				'confirmation_redirect' => home_url(),
				'notification_enable'   => '0',
				'disable_entries'       => '1',
				'user_login_hide'       => '1',
				'antispam'              => '1',
				'ajax_submit'           => '0',
			],
			'meta'     => [
				'template' => $this->slug,
			],
		];
	}
}
