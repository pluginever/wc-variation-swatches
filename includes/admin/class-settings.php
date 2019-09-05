<?php

class WC_Variation_Swatches_Settings {

	private $settings_api;

	function __construct() {
		$this->settings_api = new WC_Variation_Swatches_Settings_API();
		add_action( 'admin_init', array( $this, 'admin_init' ) );
		add_action( 'admin_menu', array( $this, 'admin_menu' ) );
	}


	function admin_init() {

		//set the settings
		$this->settings_api->set_sections( $this->get_settings_sections() );
		$this->settings_api->set_fields( $this->get_settings_fields() );

		//initialize settings
		$this->settings_api->admin_init();
	}

	function get_settings_sections() {
		$sections = array(
			array(
				'id'    => 'general_settings',
				'title' => __( 'General Settings', 'wc-variation-swatches' )
			),
			array(
				'id'    => 'shape_settings',
				'title' => __( 'Shape Settings', 'wc-variation-swatches' )
			),
			array(
				'id'    => 'tooltip_settings',
				'title' => __( 'Tooltip Settings', 'wc-variation-swatches' )
			),
			array(
				'id'    => 'border_settings',
				'title' => __( 'Border Settings', 'wc-variation-swatches' )
			),
		);

		return apply_filters( 'wc_variation_swatches_settings_sections', $sections );
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {

		$settings_fields = array(

			'general_settings' => array(

				array(
					'name'    => 'enable_stylesheet',
					'label'   => __( 'Enable Stylesheet', 'wc-variation-swatches' ),
					'desc'    => sprintf( '<span class="howto">%s</span>', __( 'Enable / Disable plugin default stylesheet.', 'wc-variation-swatches' ) ),
					'type'    => 'checkbox',
					'default' => 'on',
				),

				array(
					'name'    => 'attribute_behaviour',
					'label'   => __( 'Attribute Behaviour', 'wc-variation-swatches' ),
					'desc'    => __( 'Options to highlight disabled attributes.', 'wc-variation-swatches' ),
					'type'    => 'radio',
					'options' => array(
						'with_cross'    => 'With Cross',
						'without_cross' => 'Blur Without Cross',
						'hide'          => 'Hide'
					),
					'default' => 'with_cross',
				),

				apply_filters( 'wc_variation_swatches_show_attr',
					array(
						'name'    => 'show_attr',
						'label'   => __( 'Shown Attribute Name', 'wc-variation-swatches' ),
						'desc'    => sprintf( '<span class="howto">%s</span>', __( 'Show attribute name on single product page. <strong>This feature is only available in PRO version.</strong>', 'wc-variation-swatches' ) ),
						'type'    => 'checkbox',
						'default' => 'off',
						'attr'    => [
							'disabled' => 'disabled'
						]
					)
				),

				apply_filters( 'wc_variation_swatches_catalog_pos',
					array(
						'name'    => 'catalog_pos',
						'label'   => __( 'Catalog Swatches Position', 'wc-variation-swatches' ),
						'desc'    => __( 'Select the position where you want to show the swatches in the shop/catalog pages. <strong>This feature is only available in PRO version.</strong>', 'wc-variation-swatches' ),
						'type'    => 'select',
						'options' => array(
							'1' => __( 'After item title and price', 'wc-variation-swatches' ),
							'2' => __( 'Before item title and price', 'wc-variation-swatches' ),
							'3' => __( 'After select options button', 'wc-variation-swatches' )
						),
						'attr'    => [
							'disabled' => 'disabled'
						]
					)
				),


			),

			'shape_settings' => array(
				array(
					'name'    => 'shape_style',
					'label'   => __( 'Shape Style', 'wc-variation-swatches' ),
					'desc'    => __( 'Attribute Shape Style.', 'wc-variation-swatches' ),
					'type'    => 'radio',
					'options' => array(
						'round'  => 'Round',
						'square' => 'Square'
					),
					'default' => 'round',

				),

				array(
					'name'    => 'width',
					'label'   => __( 'Width', 'wc-variation-swatches' ),
					'desc'    => __( 'Variation Item Width.', 'wc-variation-swatches' ),
					'type'    => 'number',
					'default' => '30',
				),

				array(
					'name'    => 'height',
					'label'   => __( 'Height', 'wc-variation-swatches' ),
					'desc'    => __( 'Variation Item Height.', 'wc-variation-swatches' ),
					'type'    => 'number',
					'default' => '30',
				),
			),

			'tooltip_settings' => array(

				array(
					'name'    => 'enable_tooltip',
					'label'   => __( 'Enable Tooltip', 'wc-variation-swatches' ),
					'desc'    => sprintf( '<span class="howto">%s</span>', __( 'Enable / Disable plugin default tooltip on each product attribute.', 'wc-variation-swatches' ) ),
					'type'    => 'checkbox',
					'default' => 'checked',
				),

				array(
					'name'    => 'font_size',
					'label'   => __( 'Tooltip Font Size', 'wc-variation-swatches' ),
					'desc'    => __( 'Tooltip Font Size in PX.', 'wc-variation-swatches' ),
					'type'    => 'number',
					'default' => '15',
				),

				array(
					'name'    => 'tooltip_bg_color',
					'label'   => __( 'Tooltip Background Color', 'wc-variation-swatches' ),
					'type'    => 'color',
					'default' => '#555555',
				),

				array(
					'name'    => 'tooltip_text_color',
					'label'   => __( 'Tooltip Text Color', 'wc-variation-swatches' ),
					'type'    => 'color',
					'default' => '#ffffff',
				),
			),

			'border_settings' => array(

				array(
					'name'    => 'border',
					'label'   => __( 'Border Style', 'wc-variation-swatches' ),
					'desc'    => __( 'Enable/Disable Border.', 'wc-variation-swatches' ),
					'type'    => 'radio',
					'options' => array(
						'enable'  => 'Enable',
						'disable' => 'Disable'
					),
					'default' => 'enable',
				),

				array(
					'name'    => 'border_color',
					'label'   => __( 'Border Color', 'wc-variation-swatches' ),
					'desc'    => __( 'Default border color.', 'wc-variation-swatches' ),
					'type'    => 'color',
					'default' => '#81d742',
				),

			),

		);

		return apply_filters( 'wc_variation_swatches_settings_fields', $settings_fields );
	}

	/**
	 * Add Variation Swatches sub menu to Product admin menu
	 *
	 * @since 1.0.0
	 */

	function admin_menu() {

		add_submenu_page(
			'edit.php?post_type=product',
			__( 'WC Variation Swatches', 'wc-variation-swatches' ),
			__( 'WC Variation Swatches', 'wc-variation-swatches' ),
			'manage_options',
			'wc-variation-swatches',
			array( $this, 'settings_page' )
		);

	}

	/**
	 * Menu page for Variation Swatches sub menu
	 *
	 * @since 1.0.0
	 */

	function settings_page() {

		echo '<div class="wrap">';
		echo sprintf( "<h2>%s</h2>", __( 'WC variation Swatches', 'wc-variation-swatches' ) );
		$this->settings_api->show_settings();
		echo '</div>';

	}

}


new WC_Variation_Swatches_Settings();
