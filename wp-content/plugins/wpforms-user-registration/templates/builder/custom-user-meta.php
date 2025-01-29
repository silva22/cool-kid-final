<?php
/**
 * Custom user meta block.
 *
 * @since 2.0.0
 *
 * @var array         $meta   Form data registration_meta settings.
 * @var boolean|array $fields Fields from a form.
 */

if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
?>

<div class="wpforms-field-map-table">
	<table>
		<tbody>
		<?php foreach ( $meta as $meta_key => $meta_field ) : ?>
			<?php
				// Allow characters (lowercase and uppercase), numbers, underscore and dash.
				$key  = $meta_field !== false ? preg_replace( '/[^a-zA-Z0-9_\-]/', '', $meta_key ) : '';
				$name = ! empty( $key ) ? 'settings[registration_meta][' . $key . ']' : '';
			?>
			<tr>
				<td class="key">
					<input type="text" value="<?php echo esc_attr( $key ); ?>"
						   placeholder="<?php esc_attr_e( 'Enter meta key...', 'wpforms-user-registration' ); ?>"
						   class="key-source">
				</td>
				<td class="field">
					<select data-name="settings[registration_meta][{source}]" name="<?php echo esc_attr( $name ); ?>"
							class="key-destination wpforms-field-map-select" data-field-map-allowed="all-fields">';
						<option value=""><?php esc_html_e( '--- Select Field ---', 'wpforms-user-registration' ); ?></option>

						<?php if ( ! empty( $fields ) ) : ?>
							<?php foreach ( $fields as $field_id => $field ) : ?>
								<?php
								$default_label = sprintf( /* translators: %d - field ID. */
									__( 'Field #%d', 'wpforms-user-registration' ),
									absint( $field_id )
								);
								$label         = ! empty( $field['label'] ) ? $field['label'] : $default_label;
								?>

								<option value="<?php echo esc_attr( $field_id ); ?>" <?php echo selected( $meta_field, $field_id, false ); ?> ><?php echo esc_html( $label ); ?></option>
							<?php endforeach; ?>
						<?php endif; ?>
					</select>
				</td>
				<td class="actions">
					<a class="add" href="#"><i class="fa fa-plus-circle"></i></a>
					<a class="remove" href="#"><i class="fa fa-minus-circle"></i></a>
				</td>
			</tr>
		<?php endforeach; ?>
		</tbody>
	</table>
</div>
