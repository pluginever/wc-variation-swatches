<?php

namespace Pluginever\WCVariationSwatches\Admin;

use Pluginever\WCVariationSwatches\Admin\WCVS_Settings_API;

class Settings {
	private $settings_api;

    function __construct() {
        $this->settings_api = new WCVS_Settings_API();
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

    function admin_menu() {
        add_menu_page(
	        __( 'Variation Swatches', 'wc-variation-swatches' ),
	        'Variation Swatches',
	        'manage_options',
	        'myplugin/myplugin-admin.php',
	        array($this,'settings_page'),
	        'dashicons-sos',
	        59
	    );
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wc_variation_swatches_simple',
                'title' => __( 'Simple Settings', 'wc-variation-swatches' )
            ),
            array(
                'id'    => 'wc_variation_swatches_advance',
                'title' => __( 'Advance Settings', 'wc-variation-swatches' )
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
            'wc_variation_swatches_simple' => array(
                array(
                    'name'        => 'enable_tooltip',
                    'label'       => __( 'Enable Tooltip', 'wc-variation-swatches' ),
                    'desc'        => __( 'Enable / Disable plugin default tooltip on each product attribute.', 'wc-variation-swatches' ),
                    'type'        => 'checkbox',
                    'default'     => 'checked',
                ),
                array(
                    'name'        => 'enable_stylesheet',
                    'label'       => __( 'Enable Stylesheet', 'wc-variation-swatches' ),
                    'desc'        => __( 'Enable / Disable plugin default stylesheet.', 'wc-variation-swatches' ),
                    'type'        => 'checkbox',
                    'default'     => 'checked',
                ),
                array(
                    'name'        => 'shape_style',
                    'label'       => __( 'Shape Style', 'wc-variation-swatches' ),
                    'desc'        => __( 'Attribute Shape Style.', 'wc-variation-swatches' ),
                    'type'        => 'radio',
                    'options'     => array(
				                        'round'         => 'Round Shape',
				                        'square'        => 'Square Shape'
				                     ),
                    'default'     => 'round',

                ),
            ),
            'wc_variation_swatches_advance' => array(
                // array(
                //     'name'        => 'attribute_behaviour',
                //     'label'       => __( 'Attribute Behaviour', 'wc-variation-swatches' ),
                //     'desc'        => __( 'Disabled attribute will be hide / blur.', 'wc-variation-swatches' ),
                //     'type'        => 'radio',
                //     'options'     => array(
				            //             'with_cross'    => 'Blur With Cross',
				            //             'without_cross'        => 'Blur Without Cross',
				            //             'hide'          => 'Hide'
				            //          ),
                // ),
                array(
                    'name'        => 'width',
                    'label'       => __( 'Width', 'wc-variation-swatches' ),
                    'desc'        => __( 'Variation Item Width.', 'wc-variation-swatches' ),
                    'type'        => 'text',
                    'default'     => '30px',
                ),
                array(
                    'name'        => 'height',
                    'label'       => __( 'Height', 'wc-variation-swatches' ),
                    'desc'        => __( 'Variation Item Height.', 'wc-variation-swatches' ),
                    'type'        => 'text',
                    'default'     => '30px',
                ),
                array(
                    'name'        => 'font_size',
                    'label'       => __( 'Tooltip Font Size', 'wc-variation-swatches' ),
                    'desc'        => __( 'Tooltip Font Size.', 'wc-variation-swatches' ),
                    'type'        => 'text',
                    'default'     => '15px',
                ),
                array(
                    'name'        => 'tooltip_bg_color',
                    'label'       => __( 'Tooltip Background Color', 'wc-variation-swatches' ),
                    'type'        => 'color',
                    'default'     => '#555',
                ),
                array(
                    'name'        => 'tooltip_text_color',
                    'label'       => __( 'Tooltip Text Color', 'wc-variation-swatches' ),
                    'type'        => 'color',
                    'default'     => '#fff',
                ),
                array(
                    'name'        => 'border_style',
                    'label'       => __( 'Border Style', 'wc-variation-swatches' ),
                    'desc'        => __( 'Enable/Disable Border.', 'wc-variation-swatches' ),
                    'type'        => 'radio',
                    'options'     => array(
                                        'enable'         => 'Enable Border',
                                        'disable'        => 'Disable Border'
                                     ),
                    'default'     => 'enable',

                ),
            ),
        );

        return apply_filters( 'wc_variation_swatches_settings_fields', $settings_fields );
    }

    function settings_page() {
        ?>
        <?php
        echo '<div class="wrap">';
        echo sprintf( "<h2>%s</h2>", __( 'WC variation Swatches', 'wc-variation-swatches' ) );
        $this->settings_api->show_settings();
        echo '</div>';
    }

    /**
     * Get all the pages
     *
     * @return array page names with key value pairs
     */
    function get_pages() {
        $pages         = get_pages();
        $pages_options = array();
        if ( $pages ) {
            foreach ( $pages as $page ) {
                $pages_options[ $page->ID ] = $page->post_title;
            }
        }

        return $pages_options;
    }
}




