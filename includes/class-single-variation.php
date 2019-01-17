<?php

namespace Pluginever\WCVariationSwatches;

class Single_Variation {

	public $color = '';
	public $shape_width = '';
	public $shape_height = '';
	public $tooltip_bg_color = '';
	public $font_size = '';
	public $tooltip_text_color = '';
	public $border_color = '';

	public function __construct() {
		add_action('init', array($this, 'initialize_settings'), 10, 1);
	}

	public function initialize_settings() {

		$stylesheet = wc_variation_swatches_get_settings('enable_stylesheet', 'on', 'wc_variation_swatches_settings');

		if ($stylesheet === 'on') {
			add_filter('woocommerce_dropdown_variation_attribute_options_html', array($this, 'render_variable_swatch_style'), 100, 2);
			add_filter('wc_variation_swatch_attribute_html', array($this, 'wc_variation_swatch_attribute_html'), 5, 4);
			add_action('wp_footer', array($this, 'render_variable_swatch_css'));
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

		$shape_style    = wc_variation_swatches_get_settings('shape_style', 'round', 'wc_variation_swatches_settings');
		$enable_tooltip = wc_variation_swatches_get_settings('enable_tooltip', 'on', 'wc_variation_swatches_settings');
		$border         = wc_variation_swatches_get_settings('border', 'enable', 'wc_variation_swatches_settings');

		$this->shape_width        = wc_variation_swatches_get_settings('width', '15px', 'wc_variation_swatches_settings');
		$this->shape_height       = wc_variation_swatches_get_settings('height', '15px', 'wc_variation_swatches_settings');
		$this->tooltip_bg_color   = wc_variation_swatches_get_settings('tooltip_bg_color', '', 'wc_variation_swatches_settings');
		$this->font_size          = wc_variation_swatches_get_settings('font_size', '15px', 'wc_variation_swatches_settings');
		$this->tooltip_text_color = wc_variation_swatches_get_settings('tooltip_text_color', '', 'wc_variation_swatches_settings');
		$this->border_color       = wc_variation_swatches_get_settings('border_color', '', 'wc_variation_swatches_settings');


		$border_style = ($border == 'enable') ? 'wcvs-border-style' : 'wcvs-border-style-none';

		$tooltip_class = ($enable_tooltip == 'off') ? 'hidden' : 'wcvs-color-tooltip';
		$tooltip_html  = '<span class="' . $tooltip_class . '">' . $name . '</span>';

		$class_shape       = $shape_style . '-box';
		$class_shape_image = $shape_style . '-box-image';
		$class             = join(' ', ['swatch', $class_shape, $border_style, 'swatch-' . $term->slug, $selected]);

		switch ($attr->attribute_type) {

			case 'color':
				$this->color = get_term_meta($term->term_id, 'color', true);

				$html        = sprintf('<div class="wcvs-swatch-color %s" title="%s" data-value="%s"><div class="variation_check"  style="background: '.$this->color.';"></div>' . $tooltip_html . '</div>', $class, $name, $term->slug);
				break;

			case 'image':
				$image = get_term_meta($term->term_id, 'image', true);
				$image = $image ? wp_get_attachment_image_src($image) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

				$html = sprintf('<div class="wcvs-swatch-image %s %s" title="%s" data-value="%s"><div class="variation_check" style="background: url(\''.$image.'\');"></div>' . $tooltip_html . '</div>', $class, $class_shape_image, $name, $term->slug, $image);
				break;

			case 'label':
				$label = get_term_meta($term->term_id, 'label', true);
				$label = $label ? $label : $name;
				$html  = sprintf('<div class="wcvs-swatch-label %s" title="%s" data-value="%s">%s' . $tooltip_html . '</div><div class="variation_check"></div>', $class, $name, $term->slug, $label);
				break;
		}

		return $html;

	}

	/**
	 * Styles for frontend
	 *
	 * @param $term
	 *
	 * @return string style
	 */

	function render_variable_swatch_css() {

		list($r, $g, $b) = sscanf($this->color, "#%02x%02x%02x");
		$rgb = join(', ', [$r, $g, $b]);

		list($br, $bg, $bb) = sscanf($this->border_color, "#%02x%02x%02x");
		$brgb = join(', ', [$br, $bg, $bb]);


		?>

		<style type="text/css">

			.wcvs-color-tooltip {
				background-color: <?php echo $this->tooltip_bg_color ?>;
				color: <?php echo $this->tooltip_text_color ?>;
				font-size: <?php echo $this->font_size ?>;
			}

			.wcvs-swatch-image>.variation_check, .wcvs-swatch-label>.variation_check, .wcvs-swatch-color>.variation_check {
				width: <?php echo $this->shape_width ?>;
				height: <?php echo $this->shape_height ?>;
			}

			.round-box.wcvs-border-style, .square-box.wcvs-border-style {
				border: 2px solid rgba(<?php echo $brgb ?>, 0.5);
			}

			.round-box.selected.wcvs-border-style, .square-box.selected.wcvs-border-style {
				border: 2px solid rgba(<?php echo $brgb ?>, 1);
			}

		</style>

		<?php

		return;

	}

}
