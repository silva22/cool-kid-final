<?php

namespace WPFormsUserRegistration\Templates;

use WPForms_Template;

/**
 * User registration form template.
 *
 * @since 2.0.0
 */
class Registration extends WPForms_Template {

	/**
	 * Primary class constructor.
	 *
	 * @since 2.0.0
	 */
	public function init() {

		$this->name        = esc_html__( 'User Registration Form', 'wpforms-user-registration' );
		$this->slug        = 'user_registration';
		$this->description = esc_html__( 'Create customized WordPress user registration forms and add them anywhere on your website.', 'wpforms-user-registration' );
		$this->includes    = '';
		$this->icon        = '';
		$this->core        = true;
		$this->modal       = [
			'title'   => esc_html__( 'Don&#39;t Forget', 'wpforms-user-registration' ),
			'message' => esc_html__( 'Additional user registration options are available in the settings panel.', 'wpforms-user-registration' ),
		];
		$this->data        = [
			'field_id' => '6',
			'fields'   => [
				'1' => [
					'id'       => '1',
					'type'     => 'name',
					'label'    => esc_html__( 'Name', 'wpforms-user-registration' ),
					'format'   => 'first-last',
					'required' => '1',
					'size'     => 'medium',
				],
				'2' => [
					'id'       => '2',
					'type'     => 'text',
					'label'    => esc_html__( 'Username', 'wpforms-user-registration' ),
					'required' => '1',
					'size'     => 'medium',
				],
				'3' => [
					'id'       => '3',
					'type'     => 'email',
					'label'    => esc_html__( 'Email', 'wpforms-user-registration' ),
					'required' => '1',
					'size'     => 'medium',
					'meta'     => [
						'nickname' => 'email',
						'delete'   => false,
					],
				],
				'4' => [
					'id'       => '4',
					'type'     => 'password',
					'label'    => esc_html__( 'Password', 'wpforms-user-registration' ),
					'required' => '1',
					'size'     => 'medium',
				],
				'5' => [
					'id'          => '5',
					'type'        => 'textarea',
					'label'       => esc_html__( 'Short Bio', 'wpforms-user-registration' ),
					'description' => esc_html__( 'Share a little information about yourself.', 'wpforms-user-registration' ),
					'size'        => 'small',
				],
			],
			'settings' => [
				'antispam'                    => '1',
				'ajax_submit'                 => '0',
				'confirmation_message_scroll' => '1',
				'registration_username'       => '2',
				'registration_name'           => '1',
				'registration_password'       => '4',
				'registration_bio'            => '5',
				'registration_email_user'     => '1',
				'registration_email_admin'    => '1',
			],
			'meta'     => [
				'template' => $this->slug,
			],
		];
	}

	/**
	 * Conditional to determine if the template informational modal screens
	 * should display.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data Form data and settings.
	 *
	 * @return bool
	 */
	public function template_modal_conditional( $form_data ) {

		return true;
	}
}
