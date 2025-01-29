<?php
/**
 * HTML for the all snippets and codevault list table
 *
 * @package    Code_Snippets
 * @subpackage Views
 */

namespace Code_Snippets;

/**
 * Loaded from the manage menu.
 *
 * @var Manage_Menu $this
 */

if ( 'cloud' === $this->get_current_type() ) {
	printf(
		'<div class="refresh-button-container"><a id="refresh-button" class="button dashicons dashicons-update-alt" href="%s"></a><span>%s</span></div>',
		esc_url( add_query_arg( 'refresh', 'true', code_snippets()->get_menu_url( 'cloud' ) ) ),
		esc_html__( 'Synchronise snippets and refresh your codevault.', 'code-snippets' ),
	);
}
?>
<form method="get" action="">
	<?php
	List_Table::required_form_fields( 'search_box' );

	if ( 'cloud' === $this->get_current_type() ) {
		$this->cloud_list_table->search_box( __( 'Search Snippets', 'code-snippets' ), 'cloud_search_id' );
	} else {
		$this->list_table->search_box( __( 'Search Snippets', 'code-snippets' ), 'search_id' );
	}

	?>
</form>

<form method="post" action="">
	<input type="hidden" id="code_snippets_ajax_nonce"
	       value="<?php echo esc_attr( wp_create_nonce( 'code_snippets_manage_ajax' ) ); ?>">
	<?php
	List_Table::required_form_fields();

	if ( 'cloud' === $this->get_current_type() ) {
		$this->cloud_list_table->display();
	} else {
		$this->list_table->display();
	}

	?>
</form>
