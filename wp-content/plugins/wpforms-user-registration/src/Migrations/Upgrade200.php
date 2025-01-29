<?php

namespace WPFormsUserRegistration\Migrations;

use WPForms\Migrations\UpgradeBase;
use WPFormsUserRegistration\EmailNotifications\Helper;

/**
 * Class User Registration addon v2.0.0 upgrade.
 *
 * @since 2.1.0
 *
 * @noinspection PhpUnused
 */
class Upgrade200 extends UpgradeBase {

	/**
	 * Run upgrade.
	 *
	 * @since 2.1.0
	 *
	 * @return bool|null Upgrade result:
	 *                   true  - the upgrade completed successfully,
	 *                   false - in the case of failure,
	 *                   null  - upgrade started but not yet finished (background task).
	 */
	public function run() {

		if ( get_option( Helper::LEGACY_EMAILS ) === '1' ) {
			return true;
		}

		if ( ! $this->is_used_before() ) {
			return true;
		}

		add_option( Helper::LEGACY_EMAILS, 1 );

		return true;
	}

	/**
	 * Check if addon was used before.
	 *
	 * @since 2.0.0
	 *
	 * @return bool
	 */
	private function is_used_before() {

		$form_obj = wpforms()->get( 'form' );

		if ( $form_obj === null ) {
			return false;
		}

		$forms = $form_obj->get(
			'',
			[
				'content_only' => true,
				'cap'          => false,
			]
		);

		if ( ! is_array( $forms ) || empty( $forms ) ) {
			return false;
		}

		$templates = [ 'user_registration', 'user_login' ];

		foreach ( $forms as $form ) {

			$form_data = wpforms_decode( $form->post_content );

			if ( ! empty( $form_data['meta']['template'] ) && in_array( $form_data['meta']['template'], $templates, true ) ) {
				return true;
			}
		}

		return false;
	}
}
