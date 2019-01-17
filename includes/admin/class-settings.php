<?php

namespace Pluginever\WCVariationSwatches\Admin;

use Pluginever\WCVariationSwatches\Admin\WCVS_Settings_API;

class Settings {

	private $settings_api;

	function __construct() {
		$this->settings_api = new WCVS_Settings_API();
		add_action('admin_init', array($this, 'admin_init'));
		add_action('admin_menu', array($this, 'admin_menu'));
	}


	function admin_init() {

		//set the settings
		$this->settings_api->set_sections($this->get_settings_sections());
		$this->settings_api->set_fields($this->get_settings_fields());

		//initialize settings
		$this->settings_api->admin_init();
	}

	function get_settings_sections() {
		$sections = array(
			array(
				'id'    => 'wc_variation_swatches_settings',
				'title' => __('WC Variation Swatches Settings', 'wc-variation-swatches')
			),
		);

		return apply_filters('wc_variation_swatches_settings_sections', $sections);
	}

	/**
	 * Returns all the settings fields
	 *
	 * @return array settings fields
	 */
	function get_settings_fields() {

		$settings_fields = array(

			'wc_variation_swatches_settings' => array(

				array(
					'name'    => 'enable_stylesheet',
					'label'   => __('Enable Stylesheet', 'wc-variation-swatches'),
					'desc'    => sprintf('<span class="howto">%s</span>', __('Enable / Disable plugin default stylesheet.', 'wc-variation-swatches')),
					'type'    => 'checkbox',
					'default' => 'checked',
					'class'   => 'enable_stylesheet_check',
				),

				/**
				 * ====================
				 * Shape
				 * ====================
				 */

				array(
					'name'  => 'shape_style_heading',
					'label' => __('Shape Style', 'wc-variation-swatches'),
					'type'  => 'heading',
				),

				array(
					'name'    => 'shape_style',
					'label'   => __('Shape Style', 'wc-variation-swatches'),
					'desc'    => __('Attribute Shape Style.', 'wc-variation-swatches'),
					'type'    => 'radio',
					'options' => array(
						'round'  => 'Round',
						'square' => 'Square'
					),
					'default' => 'round',

				),

				//				 array(
				//				     'name'        => 'attribute_behaviour',
				//				     'label'       => __( 'Attribute Behaviour', 'wc-variation-swatches' ),
				//				     'desc'        => __( 'Disabled attribute will be hide / blur.', 'wc-variation-swatches' ),
				//				     'type'        => 'radio',
				//				     'options'     => array(
				//				             'with_cross'    => 'Blur With Cross',
				//				             'without_cross'        => 'Blur Without Cross',
				//				             'hide'          => 'Hide'
				//				          ),
				//				 ),

				array(
					'name'    => 'width',
					'label'   => __('Width', 'wc-variation-swatches'),
					'desc'    => __('Variation Item Width.', 'wc-variation-swatches'),
					'type'    => 'text',
					'default' => '30px',
				),

				array(
					'name'    => 'height',
					'label'   => __('Height', 'wc-variation-swatches'),
					'desc'    => __('Variation Item Height.', 'wc-variation-swatches'),
					'type'    => 'text',
					'default' => '30px',
				),

				/**
				 * =====================
				 * Tooltip
				 * =====================
				 */


				array(
					'name'  => 'tooltip_heading',
					'label' => __('Tooltip', 'wc-variation-swatches'),
					'type'  => 'heading',
				),

				array(
					'name'    => 'enable_tooltip',
					'label'   => __('Enable Tooltip', 'wc-variation-swatches'),
					'desc'    => sprintf('<span class="howto">%s</span>', __('Enable / Disable plugin default tooltip on each product attribute.', 'wc-variation-swatches')),
					'type'    => 'checkbox',
					'default' => 'checked',
				),

				array(
					'name'    => 'font_size',
					'label'   => __('Tooltip Font Size', 'wc-variation-swatches'),
					'desc'    => __('Tooltip Font Size.', 'wc-variation-swatches'),
					'type'    => 'text',
					'default' => '15px',
				),

				array(
					'name'    => 'tooltip_bg_color',
					'label'   => __('Tooltip Background Color', 'wc-variation-swatches'),
					'type'    => 'color',
					'default' => '#555',
				),

				array(
					'name'    => 'tooltip_text_color',
					'label'   => __('Tooltip Text Color', 'wc-variation-swatches'),
					'type'    => 'color',
					'default' => '#fff',
				),

				/**
				 * =====================
				 * Border
				 * =====================
				 */

				array(
					'name'  => 'border_heading',
					'label' => __('Border', 'wc-variation-swatches'),
					'type'  => 'heading',
				),

				array(
					'name'    => 'border',
					'label'   => __('Border Style', 'wc-variation-swatches'),
					'desc'    => __('Enable/Disable Border.', 'wc-variation-swatches'),
					'type'    => 'radio',
					'options' => array(
						'enable'  => 'Enable',
						'disable' => 'Disable'
					),
					'default' => 'enable',
				),

				array(
					'name'    => 'border_color',
					'label'   => __('Border Color', 'wc-variation-swatches'),
					'desc'    => __('Default border color.', 'wc-variation-swatches'),
					'type'    => 'color',
					'default' => '#555',
				),

			),
		);

		return apply_filters('wc_variation_swatches_settings_fields', $settings_fields);
	}

	/**
	 * Add Variation Swatches sub menu to Product admin menu
	 *
	 * @since 1.0.0
	 */

	function admin_menu() {

		add_submenu_page(
			'edit.php?post_type=product',
			__('WC Variation Swatches', 'wc-variation-swatches'),
			__('WC Variation Swatches', 'wc-variation-swatches'),
			'manage_options',
			'wc-variation-swatches',
			array($this, 'settings_page')
		);

	}

	/**
	 * Menu page for Variation Swatches sub menu
	 *
	 * @since 1.0.0
	 */

	function settings_page() {

		echo '<div class="wrap">';
		echo sprintf("<h2>%s</h2>", __('WC variation Swatches', 'wc-variation-swatches'));
		$this->settings_api->show_settings();
		echo '</div>';

	}

}




