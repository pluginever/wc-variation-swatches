<?php

defined( 'ABSPATH' ) || exit;


/**
 * Loads all frontend scripts/styles
 *
 * @since 1.0.0
 * @return void
 */
function wc_variation_swatches_enqueue_scripts() {
	wp_register_style( 'wc-variation-swatches', WC_VARIATION_SWATCHES_ASSETS_URL . "/css/frontend.css", [], WC_VARIATION_SWATCHES_VERSION );
	wp_register_script( 'wc-variation-swatches', WC_VARIATION_SWATCHES_ASSETS_URL . "/js/frontend.min.js", [ 'jquery' ], WC_VARIATION_SWATCHES_VERSION, true );

	wp_enqueue_style( 'wc-variation-swatches' );
	wp_enqueue_script( 'wc-variation-swatches' );

	wp_localize_script( 'wc-variation-swatches', 'wpwvs', [
		'ajaxurl' => admin_url( 'admin-ajax.php' ),
		'nonce'   => 'wc-variation-swatches',
		'attribute_behaviour'   => wc_variation_swatches_get_settings('attribute_behaviour', 'with_cross', 'general_settings'),
	] );
}

add_action( 'wp_enqueue_scripts', 'wc_variation_swatches_enqueue_scripts' );

/**
 * Enqueue all requires css and js for admin backend
 *
 * @since 1.0.0
 *
 * @return void
 */

function wc_variation_swatches_admin_enqueue_scripts() {

	//Register Styles
	wp_register_style( 'wc-variation-swatches', WC_VARIATION_SWATCHES_ASSETS_URL . "/css/admin.css", [], WC_VARIATION_SWATCHES_VERSION );

	//Register Scripts
	wp_register_script( 'wc-variation-swatches', WC_VARIATION_SWATCHES_ASSETS_URL . "/js/admin.min.js", [
		'jquery',
		'wp-color-picker'
	], WC_VARIATION_SWATCHES_VERSION, true );

	//Localize Scripts
	wp_localize_script( 'wc-variation-swatches', 'wpwvs', [
		'ajaxurl'         => admin_url( 'admin-ajax.php' ),
		'placeholder_img' => WC()->plugin_url() . '/assets/images/placeholder.png',
		'nonce'           => 'wc-variation-swatches'
	] );

	//Enqueue media uploader
	wp_enqueue_media();

	//Enqueue Styles
	wp_enqueue_style( 'wp-color-picker' );
	wp_enqueue_style( 'wc-variation-swatches' );

	//Enqueue Scripts
	wp_enqueue_script( 'wc-variation-swatches' );

}

add_action( 'admin_enqueue_scripts', 'wc_variation_swatches_admin_enqueue_scripts' );
