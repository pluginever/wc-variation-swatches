<?php

defined( 'ABSPATH' ) || exit;

class WC_Variation_Swatches_Attribute_Handler {

	function __construct() {
		add_action( 'admin_init', array( $this, 'setup_attribute_hooks' ) );
		add_action( 'woocommerce_product_option_terms', array( $this, 'product_option_terms' ), 10, 2 );
	}

	/**
	 * Set all the hooks for adding fields to attribute screen
	 *
	 * Save new term meta
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function setup_attribute_hooks() {

		if ( empty( wc_get_attribute_taxonomies() ) ) {
			return;
		}

		foreach ( wc_get_attribute_taxonomies() as $attribute_taxonomy ) {

			$attribute_taxonomy_name = wc_attribute_taxonomy_name( $attribute_taxonomy->attribute_name );

			add_action( $attribute_taxonomy_name . '_add_form_fields', array( $this, 'add_attribute_fields' ) );
			add_action( $attribute_taxonomy_name . '_edit_form_fields', array(
				$this,
				'edit_attribute_fields'
			), 10, 2 );

			add_filter( 'manage_edit-' . $attribute_taxonomy_name . '_columns', array(
				$this,
				'add_attribute_columns'
			) );
			add_filter( 'manage_' . $attribute_taxonomy_name . '_custom_column', array(
				$this,
				'add_attribute_column_content'
			), 10, 3 );

		}

		add_action( 'created_term', array( $this, 'save_term_meta' ) );
		add_action( 'edit_term', array( $this, 'save_term_meta' ) );

	}

	/**
	 * add attribute fields to attribute term screen
	 *
	 * @since 1.0.0
	 *
	 * @param string $taxonomy
	 */

	public function add_attribute_fields( $taxonomy ) {
		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name( $taxonomy );

		?>

		<div class="form-field term-slug-wrap">

			<label for="tag-slug" style="margin: 10px 0;">
				<?php wc_variation_swatches_field_label( $attribute_tax->attribute_type ) ?>
			</label>

			<?php
			wc_variation_swatches_field( $attribute_tax->attribute_type, null );
			do_action( 'wc_variation_swatches_add_edit_hover_image', $attribute_tax->attribute_type, false );
			?>

		</div>

		<script>

			jQuery(document).ajaxComplete(function (event, request, options) {

				if (request && 4 === request.readyState && 200 === request.status
					&& options.data && 0 <= options.data.indexOf('action=add-tag')) {

					var res = wpAjax.parseAjaxResponse(request.responseXML, 'ajax-response');
					if (!res || res.errors) {
						return;
					}

					// Clear Thumbnail fields on submit
					jQuery('.wc-variation-swatches-preview').find('img').attr('src', '<?php echo esc_js( wc_placeholder_img_src() ); ?>');
					jQuery('.wc-variation-swatches-term-image').val('');
					jQuery('.wc-variation-swatches-remove-image').hide();
				}

			});

		</script>

		<?php
	}

	/**
	 * Create hook to fields to edit attribute term screen
	 *
	 * @param object $term
	 * @param string $taxonomy
	 */

	function edit_attribute_fields( $term, $taxonomy ) {

		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name( $taxonomy );

		// Return if this is a default attribute type
		if ( in_array( $attribute_tax->attribute_type, array( 'select', 'text' ) ) ) {
			return;
		}

		$value = get_term_meta( $term->term_id, $attribute_tax->attribute_type, true );

		?>

		<tr class="form-field term-slug-wrap">
			<th scope="row">
				<label for="term-slug"><?php wc_variation_swatches_field_label( $attribute_tax->attribute_type ); ?></label>
			</th>
			<td>
				<?php wc_variation_swatches_field( $attribute_tax->attribute_type, $value ); ?>
			</td>
		</tr>

		<?php
		do_action( 'wc_variation_swatches_add_edit_hover_image', $attribute_tax->attribute_type, $term );
		?>

	<?php }

	/**
	 * Save attribute term meta
	 *
	 * @param integer $term_id
	 */


	function save_term_meta( $term_id ) {

		foreach ( wc_variation_swatches_types() as $swatches_type => $label ) {

			if ( ! empty( $_REQUEST[ $swatches_type ] ) ) {
				update_term_meta( $term_id, $swatches_type, sanitize_text_field( $_REQUEST[ $swatches_type ] ) );
			}

		}

		//save image type attribute terms images
		if ( ! empty( $_REQUEST['image'] ) ) {
			update_term_meta( $term_id, 'image', intval( $_REQUEST['image'] ) );
		}
		//save hover-image type attribute terms images
		if ( ! empty( $_REQUEST['hover_image'] ) ) {
			update_term_meta( $term_id, 'hover_image', intval( $_REQUEST['hover_image'] ) );
		}

	}

	/**
	 * Add extra custom column on attribute term screen list
	 *
	 * @since 1.0.0
	 *
	 * @param array $columns
	 *
	 * @return array columns
	 */

	function add_attribute_columns( $columns ) {

		$new_columns = array();

		$new_columns['cb'] = ! empty( $columns['cb'] ) ? $columns['cb'] : '';

		$new_columns['thumb'] = __('Image', 'wc-variation-swatches');
		$new_columns['hover'] = __('Hover Image', 'wc-variation-swatches');

		unset( $columns['cb'] );

		return array_merge( $new_columns, $columns );
	}

	/**
	 * Render thumbnail HTML for attributes terms depend on attribute type
	 *
	 * @since 1.0.0
	 *
	 * @param $columns
	 * @param $column
	 * @param $term_id
	 */

	function add_attribute_column_content( $columns, $column, $term_id ) {

		$taxonomy = '';

		if ( ! empty( $_REQUEST['taxonomy'] ) ) {
			$taxonomy = esc_attr( $_REQUEST['taxonomy'] );
		}

		if ( empty( $taxonomy ) ) {
			return;
		}

		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name( $taxonomy );

		$value = get_term_meta( $term_id, $attribute_tax->attribute_type, true );
		$hover = get_term_meta( $term_id, 'hover_image', true );

		if('hover' == $column){
			$image = ! empty( $hover ) ? wp_get_attachment_image_src( $hover ) : '';
			$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

			printf( '<img class="wc-variation-swatches-preview swatches-type-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
		}else {

			switch ( $attribute_tax->attribute_type ) {

				case 'color':
					printf( '<div class="wc-variation-swatches-preview swatches-type-color" style="background-color:%s;"></div>', esc_attr( $value ) );
					break;

				case 'image':
					$image = ! empty( $value ) ? wp_get_attachment_image_src( $value ) : '';
					$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

					printf( '<img class="wc-variation-swatches-preview swatches-type-image" src="%s" width="44px" height="44px">', esc_url( $image ) );
					break;

				case 'label':
					printf( '<div class="wc-variation-swatches-preview swatches-type-label">%s</div>', esc_html( $value ) );
					break;
			}
		}

	}


	/**
	 * Add selector for extra attribute types
	 *
	 * @param $taxonomy
	 * @param $index
	 */
	function product_option_terms( $taxonomy, $index ) {

		if ( ! array_key_exists( $taxonomy->attribute_type, wc_variation_swatches_types() ) ) {
			return;
		}

		$taxonomy_name = wc_attribute_taxonomy_name( $taxonomy->attribute_name );

		global $id;

		$id = isset( $_POST['post_id'] ) ? absint( $_POST['post_id'] ) : $id;

		?>

		<select multiple="multiple" data-placeholder="<?php esc_attr_e( 'Select terms', 'wc-variation-swatches' ); ?>" class="multiselect attribute_values wc-enhanced-select" name="attribute_values[<?php echo $index; ?>][]">

			<?php

			$all_terms = get_terms( $taxonomy_name, apply_filters( 'woocommerce_product_attribute_terms', array(
				'orderby'    => 'name',
				'hide_empty' => false
			) ) );

			if ( $all_terms ) {
				foreach ( $all_terms as $term ) {

					printf( '<option value="%1$s" %2$s>%3$s</option>',
						esc_attr( $term->term_id ),
						selected( has_term( absint( $term->term_id ), $taxonomy_name, $id ), true, false ),
						esc_attr( apply_filters( 'woocommerce_product_attribute_term_name', $term->name, $term ) )
					);
				}
			}

			?>

		</select>

		<button class="button plus select_all_attributes"><?php esc_html_e( 'Select all', 'wc-variation-swatches' ); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e( 'Select none', 'wc-variation-swatches' ); ?></button>
		<button class="button fr plus tawcvs_add_new_attribute" data-type="<?php echo $taxonomy->attribute_type ?>"><?php esc_html_e( 'Add new', 'wc-variation-swatches' ); ?></button>

		<?php
	}


}

new WC_Variation_Swatches_Attribute_Handler();
