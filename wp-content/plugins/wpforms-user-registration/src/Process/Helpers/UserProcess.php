<?php

namespace WPFormsUserRegistration\Process\Helpers;

/**
 * Helper class for processing.
 *
 * @since 2.0.0
 */
class UserProcess {

	/**
	 * Get required user fields.
	 *
	 * @since 2.0.0
	 *
	 * @param array $form_data The information for the form.
	 * @param array $fields    The fields that have been submitted.
	 * @param array $required  The required fields which needs to get.
	 *
	 * @return array
	 */
	public static function get_required_fields( $form_data, $fields, $required ) {

		$required_fields = [];

		foreach ( $fields as $field ) {

			if ( ! isset( $field['id'], $field['value'], $form_data['fields'][ $field['id'] ]['meta'] ) ) {
				continue;
			}

			$nickname = $form_data['fields'][ $field['id'] ]['meta']['nickname'];

			if ( empty( $nickname ) || ! in_array( $nickname, $required, true ) ) {
				continue;
			}

			$required_fields[ $nickname ] = $nickname === 'password' ? $field['value_raw'] : $field['value'];
		}

		return $required_fields;
	}
}
