<?php

namespace Pluginever\WCVariationSwatches\Admin;

class Attribute_Handler
{

	/**
	 * Terms_Handler constructor.
	 */
	public function __construct()
	{
		add_action('admin_init', array($this, 'setup_attribute_hooks'));
		add_action('woocommerce_product_option_terms', array($this, 'setup_attribute_term_in_product'), 10, 2);
	}

	/**
	 * Set all the hooks to hook wc terms
	 *
	 * @since 1.0.0
	 */
	public function setup_attribute_hooks()
	{
		if (!function_exists('wc_get_attribute_taxonomies')
			|| !function_exists('wc_attribute_taxonomy_name')) {
			return;
		}

		$attribute_taxonomies = wc_get_attribute_taxonomies();

		if (empty($attribute_taxonomies)) {
			return;
		}

		foreach ($attribute_taxonomies as $attribute_taxonomy) {
			$attribute_taxonomy_name = wc_attribute_taxonomy_name($attribute_taxonomy->attribute_name);
			add_action($attribute_taxonomy_name . '_add_form_fields', array($this, 'add_attribute_fields'));
			add_action($attribute_taxonomy_name . '_edit_form_fields', array($this, 'edit_attribute_fields'), 10, 2);

			add_filter('manage_edit-' . $attribute_taxonomy_name . '_columns', array($this, 'add_attribute_columns'));
			add_filter('manage_' . $attribute_taxonomy_name . '_custom_column', array($this, 'add_attribute_column_content'), 10, 3);
		}

		add_action('created_term', array($this, 'save_term_meta'), 10, 2);
		add_action('edit_term', array($this, 'save_term_meta'), 10, 2);

	}

	public function add_attribute_fields($taxonomy)
	{
		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name($taxonomy);
		$swatches_types = wc_variation_swatches_types();
		?>
		<div class="form-field term-wrap">
		<label for="term_value"><?php echo ucfirst($attribute_tax->attribute_type); ?></label>
		<?php
		switch ($attribute_tax->attribute_type) {
			case 'image':
				?>
				<input type="text" name="term_value" id="term_value" value="<?php //if ($term_image) echo $term_image?>" data-type="<?php echo $attribute_tax->attribute_type ?>"/>
					<input type="button" name="" id="term-value_button" class="button term-value_button" value="Upload">
				<?php
				break;
			case 'color':
				?>
				<input type="text" name="term_value" id="term_value" size="40" value="<?php //if ($term_image) echo $term_image ?>" data-type="<?php echo $attribute_tax->attribute_type ?>"/>
				<?php
				break;
			case 'label':
				?>
				<input type="text" name="term_value" id="term_value" value="<?php //if ($term_image) echo $term_image?>" data-type="<?php echo $attribute_tax->attribute_type ?>"/>
				<?php
				break;
		}
		echo '</div>';
	}

	public function edit_attribute_fields($term, $taxonomy)
	{
//		var_dump( $term );
//		var_dump( $taxonomy );
		$term_id = $term->term_id;
		$term_image = get_term_meta($term->term_id, 'meta_value', true);
		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name($taxonomy);
		$value = get_term_meta($term_id, $attribute_tax->attribute_type, true);


		echo '<tr class="form-field term-description-wrap">';
						printf('<th scope="row"><label for="term_value">'.ucfirst($attribute_tax->attribute_type).'</label></th>');

		switch ($attribute_tax->attribute_type) {
			case 'color':
				printf('<td><input name="term_value" id="term_value" type="text" value="%s" ></td>', esc_attr($term_image));
				break;

			case 'image':
				$value = get_term_meta($term_id, $attribute_tax->attribute_type, true);
				$image = $value ? wp_get_attachment_image_src($value) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';
				printf('<td><input type="text" name="term_value" id="term_value" value="%s" data-type="'.$attribute_tax->attribute_type.'"/> <input type="button" name="" id="term-value_button" class="button term-value_button" value="Upload"> </td>', esc_url($term_image));

				break;

			case 'label':
					printf('<td><input name="term_value" id="term_value" type="text" value="%s" ></td>', esc_attr($term_image));
				break;
		}
		echo '</tr>';

	}

	public function save_term_meta($term_id, $term_taxonomy_id)
	{
		if (!isset($_POST['term_value'])) {
			return;
		}
		update_term_meta($term_id, 'meta_value', $_POST['term_value']);

		$swatches_types = wc_variation_swatches_types();
		foreach ($swatches_types as $swatches_type => $label) {
			if (isset($_POST[$swatches_type])) {
				update_term_meta($term_id, $swatches_type, $_POST[$swatches_type]);
			}
		}
	}

	public function get_attribute_fields($taxonomy, $term)
	{

	}

	public function add_attribute_columns($columns)
	{
//		unset($columns['cb']);
//		array_unshift($columns, 'thumb');
//		array_unshift($columns, 'cb');
		$new_columns = array();
		$new_columns['cb'] = $columns['cb'];
		$new_columns['thumb'] = '';
		unset($columns['cb']);

		return array_merge($new_columns, $columns);
	}

	public function add_attribute_column_content($columns, $column, $term_id)
	{
		$taxonomy = '';
		if (!empty($_POST['taxonomy'])) {
			$taxonomy = esc_attr($_POST['taxonomy']);
		}
		if (!empty($_GET['taxonomy'])) {
			$taxonomy = esc_attr($_GET['taxonomy']);
		}
		if (empty($taxonomy)) {
			return;
		}
		$attribute_tax = wc_variation_swatches_get_attr_tax_by_name($taxonomy);
		$value = get_term_meta($term_id, $attribute_tax->attribute_type, true);

		$term_image = get_term_meta($term_id, 'meta_value', true);

		switch ($attribute_tax->attribute_type) {

			case 'color':
				printf('<div class="wc-variation-swatches-preview swatches-type-color" style="background-color:%s;"></div>', esc_attr($term_image));
				break;

			case 'image':


		$term_image = get_term_meta($term_id, 'meta_value', true);

				printf('<img id="term_value" class="wc-variation-swatches-preview swatches-type-image" src="%s" width="44px" height="44px">', esc_url($term_image) );

				break;

			case 'label':
				$term_image = get_term_meta($term_id, 'meta_value', true);
				printf('<div class="wc-variation-swatches-preview swatches-type-label">%s</div>', esc_html($term_image));
				break;
		}

	}


	public function setup_attribute_term_in_product($taxonomy, $index)
	{
		$taxonomy_name = wc_attribute_taxonomy_name($taxonomy->attribute_name);
		global $id;
		?>
		<select multiple="multiple" data-placeholder="<?php esc_attr_e('Select terms', 'wc-variation-swatches'); ?>"
				class="multiselect attribute_values wc-enhanced-select"
				name="attribute_values[<?php echo esc_attr($index); ?>][]">
			<?php
			$args = array(
				'orderby' => 'name',
				'hide_empty' => 0,
			);
			$all_terms = get_terms($taxonomy_name, apply_filters('woocommerce_product_attribute_terms', $args));
			if ($all_terms) {
				foreach ($all_terms as $term) {
					$options = $taxonomy->attribute_type;
					$options = !empty($options) ? $options : array();
					echo '<option class="button" value="' . esc_attr($term->term_id) . '" ' . wc_selected(has_term(absint($term->term_id), $taxonomy_name, $id), true, false) . '>' . esc_attr(apply_filters('woocommerce_product_attribute_term_name', $term->name, $term)) . '</option>';
				}
			}
			?>
		</select>
		<button class="button plus select_all_attributes"><?php esc_html_e('Select all', 'wc-variation-swatches'); ?></button>
		<button class="button minus select_no_attributes"><?php esc_html_e('Select none', 'wc-variation-swatches'); ?></button>
		<button class="button fr plus add_new_attribute"><?php esc_html_e('Add new', 'wc-variation-swatches'); ?></button>
		<?php

	}

}
