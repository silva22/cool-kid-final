<?php

namespace WPFormsUserRegistration\Process;

/**
 * Base processing class.
 *
 * @since 2.0.0
 */
abstract class Base {

	/**
	 * Hooks.
	 *
	 * @since 2.0.0
	 */
	public function hooks() {

		add_action( 'wpforms_process', [ $this, 'process' ], 9, 3 );
		add_filter( 'wpforms_process_after_filter', [ $this, 'process_after_filter' ], 10, 3 );
	}

	/**
	 * Hide password value.
	 *
	 * @since 2.0.0
	 *
	 * @param array $fields Fields.
	 *
	 * @return array
	 */
	protected function hide_password_value( $fields ) {

		foreach ( $fields as $id => $field ) {
			if ( $field['type'] !== 'password' ) {
				continue;
			}

			$fields[ $id ]['value'] = '**********';
		}

		return $fields;
	}
}
