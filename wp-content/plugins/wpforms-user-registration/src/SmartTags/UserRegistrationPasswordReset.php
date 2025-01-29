<?php

namespace WPFormsUserRegistration\SmartTags;

use WPForms\SmartTags\SmartTag\SmartTag;
use WPFormsUserRegistration\Admin\Builder\Reset;
use WPFormsUserRegistration\SmartTags\Helpers\Helper;

/**
 * Class UserRegistrationPasswordReset.
 *
 * @since 2.0.0
 *
 * @noinspection PhpUnused
 */
class UserRegistrationPasswordReset extends SmartTag {

	/**
	 * Get smart tag value.
	 *
	 * @since 2.0.0
	 *
	 * @param array  $form_data Form data.
	 * @param array  $fields    List of fields.
	 * @param string $entry_id  Entry ID.
	 *
	 * @return string
	 */
	public function get_value( $form_data, $fields = [], $entry_id = '' ) {

		if ( empty( $form_data['meta']['template'] ) || $form_data['meta']['template'] !== Reset::TEMPLATE_SLUG ) {
			return '';
		}

		global $wp;

		$user = Helper::get_user();

		$page_url = wp_doing_ajax() && ! empty( $_POST['action'] ) && ! empty( $_POST['page_url'] ) && $_POST['action'] === 'wpforms_submit' ? esc_url_raw( wp_unslash( $_POST['page_url'] ) ) : home_url( $wp->request ); // phpcs:ignore WordPress.Security.NonceVerification.Missing

		if ( ! $user ) {
			return '';
		}

		return esc_url(
			add_query_arg(
				[
					'action' => 'wpforms_rp',
					'key'    => get_password_reset_key( $user ),
					'login'  => rawurlencode( $user->user_login ),
				],
				$page_url
			)
		);
	}
}
