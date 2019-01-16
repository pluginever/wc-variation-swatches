<?php

namespace Pluginever\WCVariationSwatches;

class Single_Variation {

	public function __construct() {
		add_action('init', array($this, 'initialize_settings'), 10, 1);
	}

	public function initialize_settings() {

		$stylesheet = wc_variation_swatches_get_settings('enable_stylesheet', 'on', 'wc_variation_swatches_settings');

		if ($stylesheet === 'on') {
			add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'render_variable_swatch_style'), 100, 2);
			add_filter('wc_variation_swatch_attribute_html', array($this, 'wc_variation_swatch_attribute_html'), 5, 4);
		}
	}

	/**
	 * Chnage the default list of variation attributes html.
	 *
	 * @since 1.0.0
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string html
	 */


	public function render_variable_swatch_style($html, $args) {

		$types = wc_variation_swatches_types();
		$attr  = wc_variation_swatches_get_tax_attribute($args['attribute']);

		if (empty($attr)) {
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$class     = "variation-selector variation-select-{$attr->attribute_type}";
		$swatches  = '';

		if (empty($options) && !empty($product) && !empty($attribute)) {

			$attributes = $product->get_variation_attributes();
			$options    = $attributes[$attribute];

		}

		if (array_key_exists($attr->attribute_type, $types)) {

			if (!empty($options) && $product && taxonomy_exists($attribute)) {

				$all_terms = wc_get_product_terms($product->get_id(), $attribute, array('fields' => 'all'));

				foreach ($all_terms as $term) {

					if (in_array($term->slug, $options)) {
						$swatches .= apply_filters('wc_variation_swatch_attribute_html', '', $term, $attr, $args);
					}

				}

			}
		}

		if (!empty($swatches)) {
			$class    = "hidden";
			$swatches = '<div class="wc-ever-swatches" data-attribute_name="attribute_' . esc_attr($attribute) . '">' . $swatches . '</div>';
			$html     = '<div class="' . esc_attr($class) . '">' . $html . '</div>' . $swatches;
		}

		return $html;
	}

	/**
	 * @param $html
	 * @param $term
	 * @param $attr
	 * @param $args
	 * @return string html
	 */

	function wc_variation_swatch_attribute_html($html, $term, $attr, $args) {

		$selected = sanitize_title($args['selected']) == $term->slug ? 'selected' : '';
		$name     = esc_html($term->name);

		$shape_style         = wc_variation_swatches_get_settings('shape_style', 'round', 'wc_variation_swatches_settings');
		$enable_tooltip      = wc_variation_swatches_get_settings('enable_tooltip', 'on', 'wc_variation_swatches_settings');
		$border              = wc_variation_swatches_get_settings('border', 'enable', 'wc_variation_swatches_settings');
		$shape_width         = wc_variation_swatches_get_settings('width', '15px', 'wc_variation_swatches_settings');
		$shape_height        = wc_variation_swatches_get_settings('height', '15px', 'wc_variation_swatches_settings');
		$tooltip_bg_color    = wc_variation_swatches_get_settings('tooltip_bg_color', '', 'wc_variation_swatches_settings');
		$font_size           = wc_variation_swatches_get_settings('font_size', '15px', 'wc_variation_swatches_settings');
		$tooltip_text_color  = wc_variation_swatches_get_settings('tooltip_text_color', '', 'wc_variation_swatches_settings');
		$border_color        = wc_variation_swatches_get_settings('border_color', '', 'wc_variation_swatches_settings');
		$border_active_color = wc_variation_swatches_get_settings('border_active_color', '', 'wc_variation_swatches_settings');


		$border_style = ($border == 'enable') ? 'wcvs-border-style' : 'wcvs-border-style-none';

		$tooltip_class = ($enable_tooltip == 'off') ? 'hidden' : 'wcvs-color-tooltip';
		$tooltip_html  = '<span class="' . $tooltip_class . '">' . $name . '</span>';

		$class_shape       = $shape_style . '-box';
		$class_shape_image = $shape_style . '-box-image';
		$class = join(' ', ['swatch', $class_shape, $border_style, 'swatch-' . $term->slug, $selected]);

		$color = get_term_meta($term->term_id, 'color', true);

		switch ($attr->attribute_type) {
			case 'color':
				$html = sprintf('<div class="wcvs-swatch-color %s" title="%s" data-value="%s">'.$tooltip_html.'</div>', $class, $name, $term->slug);
				break;

			case 'image':
				$image = get_term_meta($term->term_id, 'image', true);
				$image = $image ? wp_get_attachment_image_src($image) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

				$html = sprintf('<div class="wcvs-swatch-image %s %s" title="%s" data-value="%s"><img src="%s" alt="%2$s">'.$tooltip_html.'</div>', $class, $class_shape_image, $name, $term->slug, $image);
				break;

			case 'label':
				$label = get_term_meta($term->term_id, 'label', true);
				$label = $label ? $label : $name;
				$html  = sprintf('<div class="wcvs-swatch-label %s" title="%s" data-value="%s">%s'.$tooltip_html.'</div>', $class, $name, $term->slug);
				break;
		}

		echo $this->style($color, $shape_width, $shape_height, $tooltip_bg_color, $font_size, $tooltip_text_color);

		return $html;

	}

	/**
	 * Styles for frontend
	 *
	 * @param $term
	 *
	 * @return string style
	 */

	function style($color, $shape_width, $shape_height, $tooltip_bg_color, $font_size, $tooltip_text_color) {

		list($r, $g, $b) = sscanf($color, "#%02x%02x%02x");
		$rgb = join(', ', [$r, $g, $b]);

		?>

		<style type="text/css">

			.wcvs-color-tooltip {
				background-color: <?php echo $tooltip_bg_color ?>;
				color: <?php echo $tooltip_text_color ?>;
				font-size: <?php echo $font_size ?>;
			}

			.wcvs-swatch-image, .wcvs-swatch-label, .wcvs-swatch-color{
				width: <?php echo $shape_width ?>;
				height: <?php echo $shape_height ?>;
			}

			.wcvs-swatch-color {
				background-color: <?php echo $color ?>;
				color: rgba(<?php echo $rgb ?>, 0.5);
			}

		</style>

		<?php

		return;

	}

}
