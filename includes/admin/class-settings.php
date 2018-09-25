<?php

namespace Pluginever\WcVariationSwatches\Admin;
class Settings {
    private $settings_api;

    function __construct() {
        $this->settings_api = new \Ever_WC_Variable_Swatches_API();
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
            __( 'Swatches Seetings Title', 'wc-variation-swatches' ),
            'Swatches Seetings',
            'manage_options',
            'wc-variation-swatches-settings',
            array($this, 'settings_page'),
            'dashicons-sos',
            6
        );
    
    }

    function get_settings_sections() {
        $sections = array(
            array(
                'id'    => 'wc-variation-swatches_settings_article',
                'title' => __( 'Simple Settings', 'wc-variation-swatches' )
            ),
            array(
                'id'    => 'wc-variation-swatches_settings_youtube',
                'title' => __( 'Youtube Settings', 'wc-variation-swatches' )
            ),
            array(
                'id'    => 'wc-variation-swatches_settings_flickr',
                'title' => __( 'Flickr Settings', 'wc-variation-swatches' )
            ),
            array(
                'id'    => 'wc-variation-swatches_settings_envato',
                'title' => __( 'Envato Settings', 'wc-variation-swatches' )
            )
        );

        return apply_filters( 'wc-variation-swatches_settings_sections', $sections );
    }

    /**
     * Returns all the settings fields
     *
     * @return array settings fields
     */
    function get_settings_fields() {
        $settings_fields = array(
            'wc-variation-swatches_settings_article' => array(
                array(
                    'name'        => 'banned_hosts',
                    'label'       => __( 'Banned Hosts', 'wc-variation-swatches' ),
                    'desc'        => __( 'Articles from the above hosts will be rejected. put single url/host per line.', 'wc-variation-swatches' ),
                    'placeholder' => __( "example.com \n example1.com", 'wc-variation-swatches' ),
                    'type'        => 'text',
                ),
            ),
            'wc-variation-swatches_settings_youtube' => array(
                array(
                    'name'    => 'api_key',
                    'label'   => __( 'Youtube API Key', 'wc-variation-swatches' ),
                    'desc'    => __( 'Youtube campaigns wont run without settings this.', 'wc-variation-swatches' ),
                    'type'    => 'text',
                    'default' => ''
                ),
            ),
            'wc-variation-swatches_settings_flickr'  => array(
                array(
                    'name'    => 'api_key',
                    'label'   => __( 'Flickr API Key', 'wc-variation-swatches' ),
                    'desc'    => __( 'Flickr campaigns wont run without settings this.', 'wc-variation-swatches' ),
                    'type'    => 'text',
                    'default' => ''
                ),
            ),
            'wc-variation-swatches_settings_envato'  => array(
                array(
                    'name'    => 'token',
                    'label'   => __( 'Envato Token', 'wc-variation-swatches' ),
                    'desc'    => __( 'Check this tutorial <a href="https://www.pluginever.com/docs/wp-content-pilot/how-to-create-envato-token/" target="_blank">Here</a> to get your token.', 'wc-variation-swatches' ),
                    'type'    => 'text',
                    'default' => ''
                ),
                array(
                    'name'    => 'user_name',
                    'label'   => __( 'Envato Username', 'wc-variation-swatches' ),
                    'desc'    => __( 'Your username (Affiliate ID) for affiliate integration. e.g. "pluginever"' ),
                    'type'    => 'text',
                    'default' => ''
                ),
            )
        );

        return apply_filters( 'wc-variation-swatches_settings_fields', $settings_fields );
    }

    function settings_page() {
        ?>
        <?php
        echo '<div class="wrap">';
        echo sprintf( "<h2>%s</h2>", __( 'WP Content Pilot Settings', 'wc-variation-swatches' ) );
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



	
