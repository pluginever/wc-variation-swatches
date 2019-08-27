<?php

defined( 'ABSPATH' ) || exit;

class WPWVS_Frontend {

	public function __construct() {
		$this->includes();
		add_action( 'wp_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
	}

	/**
	 * Includes all frontend related files
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function includes() {
		include_once dirname( __FILE__ ) . '/class-single-variation.php';
	}


	/**
	 * Loads all frontend scripts/styles
	 *
	 * @param $hook
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function enqueue_scripts( $hook ) {
		$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		wp_register_style( 'wc-variation-swatches', WPWVS_ASSETS_URL . "/css/frontend{$suffix}.css", [], WPWVS_VERSION );
		wp_register_script( 'wc-variation-swatches', WPWVS_ASSETS_URL . "/js/frontend/frontend{$suffix}.js", [ 'jquery' ], WPWVS_VERSION, true );

		wp_enqueue_style( 'wc-variation-swatches' );
		wp_enqueue_script( 'wc-variation-swatches' );

		wp_localize_script( 'wc-variation-swatches', 'wpwvs', [
			'ajaxurl' => admin_url( 'admin-ajax.php' ),
			'nonce'   => 'wc-variation-swatches'
		] );
	}

}

new WPWVS_Frontend();
