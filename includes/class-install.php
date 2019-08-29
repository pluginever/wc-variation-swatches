<?php

defined( 'ABSPATH' ) || exit;

class WC_Variation_Swatches_Install {

	public static function activate() {
		if ( ! is_blog_installed() ) {
			return;
		}

		if ( get_option( 'wc_variation_swatches_install_date' ) ) {
			return;
		}

		// Check if we are not already running this routine.
		if ( 'yes' === get_transient( 'wc_variation_swatches_installing' ) ) {
			return;
		}

		self::create_options();
	}

	/**
	 * Save option data
	 */
	private static function create_options() {
		//save db version
		update_option( 'wc_variation_swatches_version', WC_VARIATION_SWATCHES_VERSION );

		//save install date
		update_option( 'wc_variation_swatches_install_date', current_time( 'timestamp' ) );
	}

}
