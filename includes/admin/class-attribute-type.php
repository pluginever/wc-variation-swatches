<?php

namespace Pluginever\WcVariationSwatches\Admin;

class Attribute_type {

	/**
     * AttributeTypes constructor.
     */
    public function __construct() {
        add_filter("product_attributes_type_selector" , array( $this, 'WPWVS_register_attribute_type' ) );
    }

    public function WPWVS_register_attribute_type($attribute_types){   
    	$attribute_types = array();

        $attribute_types[ 'image' ] = array(
            'title'   => esc_html__( 'Image', 'wc-variation-swatches' )    
        );

        $attribute_types[ 'color' ] = array(
            'title'   => esc_html__( 'Color', 'wc-variation-swatches' )
        );    
            
        $attribute_types[ 'button' ] = array(
            'title'   => esc_html__( 'Button', 'wc-variation-swatches' )
        );

        foreach ( $attribute_types as $key => $value ) {
            $attribute_types[ $key ] = $value[ 'title' ];
        }   
        return $attribute_types;
    }

}
