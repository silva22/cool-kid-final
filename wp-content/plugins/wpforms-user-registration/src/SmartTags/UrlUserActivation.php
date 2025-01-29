<?php

namespace WPFormsUserRegistration\SmartTags;

use WPForms\SmartTags\SmartTag\SmartTag;
use WPFormsUserRegistration\SmartTags\Helpers\Helper;

/**
 * Class UrlUserActivation.
 *
 * @since 2.0.0
 *
 * @noinspection PhpUnused
 */
class UrlUserActivation extends SmartTag {

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

		$user = Helper::get_user();

		return $user ? esc_url( add_query_arg( [ 'wpforms_activate' => get_user_meta( $user->ID, 'wpforms-activate', true ) ], home_url() ) ) : '';
	}
}
