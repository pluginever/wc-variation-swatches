<?php

class WC_Variation_Swatches_Single_Variation {

	public $color = '';
	public $shape_width = '';
	public $shape_height = '';
	public $tooltip_bg_color = '';
	public $font_size = '';
	public $tooltip_text_color = '';
	public $border_color = '';

	public function __construct() {
		add_action( 'init', array( $this, 'initialize_settings' ), 10, 1 );
	}

	/**
	 * since 1.0.0
	 */
	public function initialize_settings() {

		$stylesheet = wc_variation_swatches_get_settings( 'enable_stylesheet', 'on', 'general_settings' );

		if ( $stylesheet === 'on' ) {
			add_filter( 'woocommerce_dropdown_variation_attribute_options_html', array(
				$this,
				'attribute_options_html'
			), 100, 2 );
			add_filter( 'wc_variation_swatch_attribute_html', array( $this, 'attribute_html' ), 5, 4 );
			add_action( 'wp_footer', array( $this, 'attribute_css' ) );
		}

	}

	/**
	 * Change the default list of variation attributes html.
	 *
	 * @since 1.0.0
	 *
	 * @param $html
	 * @param $args
	 *
	 * @return string html
	 */
	public function attribute_options_html( $html, $args ) {

		$types = wc_variation_swatches_types();
		$attr  = wc_variation_swatches_get_tax_attribute( $args['attribute'] );

		if ( empty( $attr ) ) {
			return $html;
		}

		$options   = $args['options'];
		$product   = $args['product'];
		$attribute = $args['attribute'];
		$swatches  = '';

		if ( empty( $options ) && ! empty( $product ) && ! empty( $attribute ) ) {
			$attributes = $product->get_variation_attributes();
			$options    = $attributes[ $attribute ];
		}

		if ( array_key_exists( $attr->attribute_type, $types ) ) {
			if ( ! empty( $options ) && $product && taxonomy_exists( $attribute ) ) {
				$all_terms = wc_get_product_terms( $product->get_id(), $attribute, array(
					'fields'  => 'all',
					'orderby' => 'term_id'
				) );

				foreach ( $all_terms as $term ) {
					if ( in_array( $term->slug, $options ) ) {
						$swatches .= apply_filters( 'wc_variation_swatch_attribute_html', '', $term, $attr, $args );
					}
				}

			}
		}

		if ( ! empty( $swatches ) ) {
			$swatches = '<div class="wc-ever-swatches" data-attribute_name="attribute_' . esc_attr( $attribute ) . '">' . $swatches . '</div>';
			$html     = sprintf( '<div class="hidden wcvs-hidden-swatches">%1$s</div>%2$s', $html, $swatches );
		}

		return $html;
	}

	/**
	 * @param $html
	 * @param $term
	 * @param $attr
	 * @param $args
	 *
	 * @return string html
	 */

	function attribute_html( $html, $term, $attr, $args ) {

		$selected = sanitize_title( $args['selected'] ) == $term->slug ? 'selected' : '';
		$name     = esc_html( $term->name );

		$attribute_behaviour = wc_variation_swatches_get_settings( 'attribute_behaviour', 'with_cross', 'general_settings' );

		$shape_width  = wc_variation_swatches_get_settings( 'width', '30', 'shape_settings' );
		$shape_height = wc_variation_swatches_get_settings( 'height', '30', 'shape_settings' );

		$this->shape_width  = apply_filters( 'wc_variation_swatches_shape_width', $shape_width, $term );
		$this->shape_height = apply_filters( 'wc_variation_swatches_shape_height', $shape_height, $term );

		$shape_style       = wc_variation_swatches_get_settings( 'shape_style', 'round', 'shape_settings' );
		$shape_style       = apply_filters( 'wc_variation_swatches_shape_style', $shape_style, $term );
		$class_shape       = $shape_style . '-box';
		$class_shape_image = $shape_style . '-box-image';

		$enable_tooltip           = wc_variation_swatches_get_settings( 'enable_tooltip', 'on', 'tooltip_settings' );
		$this->tooltip_bg_color   = wc_variation_swatches_get_settings( 'tooltip_bg_color', '', 'tooltip_settings' );
		$this->font_size          = wc_variation_swatches_get_settings( 'font_size', '15', 'tooltip_settings' );
		$this->tooltip_text_color = wc_variation_swatches_get_settings( 'tooltip_text_color', '', 'tooltip_settings' );

		$border             = wc_variation_swatches_get_settings( 'border', 'enable', 'border_settings' );
		$this->border_color = wc_variation_swatches_get_settings( 'border_color', '#81d742', 'border_settings' );
		$border_style       = ( $border == 'enable' ) ? 'wcvs-border-style' : 'wcvs-border-style-none';

		$tooltip_class = ( $enable_tooltip == 'off' ) ? 'hidden' : 'wcvs-color-tooltip';
		$tooltip_html  = sprintf( '<span class="%1$s">%2$s</span>', $tooltip_class, $name );

		$class = join( ' ', [ 'wcvs-swatch', $class_shape, $border_style, 'swatch-' . $term->slug, $selected ] );

		$attr_name = apply_filters( 'wc_variation_swatches_attr_name', '', $name, $term );
		switch ( $attr->attribute_type ) {

			case 'color':
				$this->color = get_term_meta( $term->term_id, 'color', true );

				$html = sprintf( '<div class="swatch_wrapper %1$s"><div class="wcvs-swatch-color %2$s" title="%3$s" data-value="%4$s"><div class="variation_check %5$s"  style="background: %6$s;"></div> %7$s</div>%8$s</div>', $term->taxonomy, $class, 'off' == $enable_tooltip ? $name : '', $term->slug, $attribute_behaviour, $this->color, $tooltip_html, $attr_name );
				break;

			case 'image':
				$image = get_term_meta( $term->term_id, 'image', true );
				$image = $image ? wp_get_attachment_image_src( $image ) : '';
				$image = $image ? $image[0] : WC()->plugin_url() . '/assets/images/placeholder.png';

				$html = sprintf( '<div class="swatch_wrapper %1$s"><div class="wcvs-swatch-image %2$s %3$s" title="%4$s" data-value="%5$s"><div class="variation_check %6$s" style="background: url(%7$s);"></div>%8$s</div>%9$s</div>',
					$term->taxonomy, $class, $class_shape_image, 'off' == $enable_tooltip ? $name : '', $term->slug, $attribute_behaviour, $image, $tooltip_html, $attr_name );
				break;

			case 'label':
				$label = get_term_meta( $term->term_id, 'label', true );
				$label = $label ? $label : $name;
				$html  = sprintf( '<div class="swatch_wrapper %1$s"><div class="wcvs-swatch-label %2$s" title="%3$s" data-value="%4$s"><div class="variation_check %5$s">%6$s</div>%7$s</div>%8$s</div>', $term->taxonomy, $class, 'off' == $enable_tooltip ? $name : '', $term->slug, $attribute_behaviour, $label,$tooltip_html, $attr_name );
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

	function attribute_css() {

		list( $r, $g, $b ) = sscanf( $this->color, "#%02x%02x%02x" );
		$rgb = join( ', ', [ $r, $g, $b ] );


		list( $br, $bg, $bb ) = sscanf( $this->border_color, "#%02x%02x%02x" );
		$brgb = join( ', ', [ $br, $bg, $bb ] );

		?>

		<style type="text/css">

			.wcvs-color-tooltip {
				background-color: <?php echo $this->tooltip_bg_color ?>;
				color: <?php echo $this->tooltip_text_color ?>;
				font-size: <?php echo $this->font_size . 'px'; ?>;
			}

			.wcvs-swatch-image > .variation_check, .wcvs-swatch-color > .variation_check {
				width: <?php echo $this->shape_width  . 'px';?>;
				height: <?php echo $this->shape_height . 'px'; ?>;
			}

			.wcvs-swatch-label > .variation_check {
				min-width: <?php echo intval($this->shape_width) - 3 . 'px';?>;
				min-height: <?php echo intval($this->shape_height) - 3 . 'px';?>;
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

new WC_Variation_Swatches_Single_Variation();
