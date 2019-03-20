<?php
/**
 * Plugin Name: WC Variation Swatches
 * Plugin URI:  https://www.pluginever.com
 * Description: The Best WordPress Plugin ever made!
 * Version:     1.0.0
 * Author:      pluginever
 * Author URI:  https://www.pluginever.com
 * Donate link: https://www.pluginever.com
 * License:     GPLv2+
 * Text Domain: wc-variation-swatches
 * Domain Path: /i18n/languages/
 */

/**
 * Copyright (c) 2018 pluginever (email : support@pluginever.com)
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License, version 2 or, at
 * your discretion, any later version, as published by the Free
 * Software Foundation.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program; if not, write to the Free Software
 * Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
 */

// don't call the file directly
if ( ! defined( 'ABSPATH' ) ) {
	exit;
}
/**
 * Main initiation class
 *
 * @since 1.0.0
 */

/**
 * Main WCVariationSwatches Class.
 *
 * @class WCVariationSwatches
 */
final class WCVariationSwatches {
	/**
	 * WCVariationSwatches version.
	 *
	 * @var string
	 */
	public $version = '1.0.0';

	/**
	 * Minimum PHP version required
	 *
	 * @var string
	 */
	private $min_php = '5.6.0';

	/**
	 * admin notices
	 *
	 * @since 1.0.0
	 *
	 * @var array
	 */
	protected $notices = array();

	/**
	 * The single instance of the class.
	 *
	 * @var WCVariationSwatches
	 * @since 1.0.0
	 */
	protected static $instance = null;

	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	public $plugin_name = 'WC Variation Swatches';

	/**
	 * WCVariationSwatches constructor.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );
		add_action( 'init', array( $this, 'localization_setup' ) );
		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );
		if ( $this->is_plugin_compatible() ) {
			$this->define_constants();
			$this->includes();
		}
	}

	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function activation_check() {

		if ( ! version_compare( PHP_VERSION, $this->min_php, '>=' ) ) {

			deactivate_plugins( plugin_basename( __FILE__ ) );

			$message = sprintf( '%s could not be activated The minimum PHP version required for this plugin is %1$s. You are running %2$s.', $this->plugin_name, $this->min_php, PHP_VERSION );
			wp_die( $message );
		}

	}


	/**
	 * Determines if the plugin compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_plugin_compatible() {
		include_once( ABSPATH . 'wp-admin/includes/plugin.php' );
		if ( ! is_plugin_active( 'woocommerce/woocommerce.php' ) ) {
			$this->add_notice( 'error', sprintf(
				'<strong>%s</strong> requires <strong>WooCommerce</strong> installed and active.',
				$this->plugin_name
			) );

			return false;
		}

		if ( ! is_plugin_active( 'wc-variation-swatches/wc-variation-swatches.php' ) ) {
			$this->add_notice( 'error', sprintf(
				'<strong>%s</strong> requires <strong><a href="%s">WooCommerce Serial Numbers</a></strong> installed and active.',
				$this->plugin_name, 'https://wordpress.org/plugins/wc-variation-swatches/'
			) );

			return false;
		}

		return true;
	}


	/**
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $class the notice class
	 * @param string $message the notice message body
	 */
	public function add_notice( $class, $message ) {

		$notices = $this->notices;
		if ( is_string( $message ) && is_string( $class ) && ! wp_list_filter( $notices, array( 'message' => $message ) ) ) {

			$this->notices[] = array(
				'message' => $message,
				'class'   => $class
			);
		}

	}


	/**
	 * Displays any admin notices added
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function admin_notices() {
		$notices = $this->notices;
		foreach ( $notices as $notice_key => $notice ) :
			?>
			<div class="notice notice-<?php echo sanitize_html_class( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], array(
						'a'      => array( 'href' => array() ),
						'strong' => array()
					) ); ?></p>
			</div>
		<?php
		endforeach;
	}


	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wc-variation-swatches', false, dirname( plugin_basename( __FILE__ ) ) . '/languages/' );
	}


	/**
	 * Plugin action links
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'admin.php?page=wc-variation-swatches-settings' ) . '">' . __( 'Settings', 'wc-variation-swatches' ) . '</a>';

		return $links;
	}


	/**
	 * Main WCVariationSwatches Instance.
	 *
	 * Ensures only one instance of WCVariationSwatches is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return WCVariationSwatches - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


	/**
	 * Define EverProjects Constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_constants() {
		//$upload_dir = wp_upload_dir( null, false );
		define( 'WPWVS_VERSION', $this->version );
		define( 'WPWVS_FILE', __FILE__ );
		define( 'WPWVS_PATH', dirname( WPWVS_FILE ) );
		define( 'WPWVS_INCLUDES', WPWVS_PATH . '/includes' );
		define( 'WPWVS_ADMIN', WPWVS_PATH . '/includes/admin' );
		define( 'WPWVS_URL', plugins_url( '', WPWVS_FILE ) );
		define( 'WPWVS_ASSETS_URL', WPWVS_URL . '/assets' );
		define( 'WPWVS_TEMPLATES_DIR', WPWVS_PATH . '/templates' );
	}


	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		//core includes
		include_once WPWVS_INCLUDES . '/core-functions.php';
		include_once WPWVS_INCLUDES . '/class-install.php';
		include_once WPWVS_INCLUDES . '/admin/class-admin.php';
		include_once WPWVS_INCLUDES . '/class-frontend.php';
	}


}

function wc_variation_swatches() {
	return WCVariationSwatches::instance();
}

//fire off the plugin
wc_variation_swatches();
