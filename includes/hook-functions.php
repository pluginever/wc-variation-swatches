<?php

defined( 'ABSPATH' ) || exit;

add_filter( 'product_attributes_type_selector', 'wc_variation_swatches_add_attribute_types' );

function wc_variation_swatches_add_attribute_types( $types ) {

	$swatches_types = wc_variation_swatches_types();
	$types          = array_merge( $types, $swatches_types );

	return $types;
}
