<?php
/**
 * Plugin Name: WooCommerce Variation Swatches
 * Plugin URI:  https://www.pluginever.com
 * Description: Provides a much nicer way to display variations of variable products!
 * Version:     1.0.1
 * Author:      pluginever
 * Author URI:  https://manik.me
 * Donate link: https://www.pluginever.com
 * License:     GPLv2+
 * Text Domain: wc-variation-swatches
 * Domain Path: /i18n/languages/
 * Tested up to: 5.2.4
 * WC requires at least: 3.0.0
 * WC tested up to: 3.7.1
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
 * Main WC_Variation_Swatches Class.
 *
 * @class WC_Variation_Swatches
 */
final class WC_Variation_Swatches {
	/**
	 * WC_Variation_Swatches version.
	 *
	 * @var string
	 */
	public $version = '1.0.1';

	/**
	 * Minimum PHP version required
	 *
	 * @var string
	 */
	private $min_php = '5.6.0';


	/**
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $min_wp = '4.0.0';

	/**
	 * Minimum woocommerce version
	 *
	 * @since 1.0.0
	 *
	 * @var string
	 */
	protected $min_wc = '3.0.0';

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
	 * @var WC_Variation_Swatches
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
	 * WC_Variation_Swatches constructor.
	 */
	public function __construct() {
		register_activation_hook( __FILE__, array( $this, 'activation_check' ) );

		add_action( 'admin_init', array( $this, 'check_environment' ) );
		add_action( 'admin_init', array( $this, 'add_plugin_notices' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ), 15 );

		add_action( 'init', array( $this, 'localization_setup' ) );

		add_filter( 'plugin_action_links_' . plugin_basename( __FILE__ ), array( $this, 'plugin_action_links' ) );

		// if the environment check fails, initialize the plugin
		if ( $this->is_environment_compatible() ) {
			include_once dirname( __FILE__ ) . '/includes/class-install.php';
			register_activation_hook( __FILE__, array( 'WC_Variation_Swatches_Install', 'activate' ) );
			add_action( 'plugins_loaded', array( $this, 'init_plugin' ) );
		}
	}

	/**
	 * Checks the environment on loading WordPress, just in case the environment changes after activation.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function check_environment() {

		if ( ! $this->is_environment_compatible() && is_plugin_active( plugin_basename( __FILE__ ) ) ) {

			$this->deactivate_plugin();

			$this->add_admin_notice( 'bad_environment', 'error', $this->plugin_name . ' has been deactivated. ' . $this->get_environment_message() );
		}
	}

	/**
	 * Adds notices for out-of-date WordPress and/or WP Content Pilot versions.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function add_plugin_notices() {

		if ( ! $this->is_wp_compatible() ) {

			$this->add_admin_notice( 'update_wordpress', 'error', sprintf(
				'%s requires WordPress version %s or higher. Please %supdate WordPress &raquo;%s',
				'<strong>' . $this->plugin_name . '</strong>',
				$this->min_wp,
				'<a href="' . esc_url( admin_url( 'update-core.php' ) ) . '">', '</a>'
			) );
		}

		if ( ! $this->is_wc_compatible() ) {
			$this->add_admin_notice( 'update_wc', 'error', sprintf(
				'%s requires WooCommerce version %s or higher installed and active.',
				$this->plugin_name,
				$this->min_wc
			) );
		}
	}

	/**
	 * Determines if the server environment is compatible with this plugin.
	 *
	 * Override this method to add checks for more than just the PHP version.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_environment_compatible() {

		return version_compare( PHP_VERSION, $this->min_php, '>=' );
	}

	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wp_compatible() {

		return version_compare( get_bloginfo( 'version' ), $this->min_wp, '>=' );
	}

	/**
	 * Determines if the WooCommerce installed.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wc_installed() {
		return is_plugin_active( 'woocommerce/woocommerce.php' );
	}

	/**
	 * Determines if the WordPress compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function is_wc_compatible() {
		return defined( 'WC_VERSION' ) && version_compare( WC_VERSION, $this->min_wc, '>=' );
	}

	/**
	 * Determines if the required plugins are compatible.
	 *
	 * @since 1.0.0
	 *
	 * @return bool
	 */
	protected function plugins_compatible() {
		return $this->is_wp_compatible() && $this->is_wc_compatible();
	}

	/**
	 * Deactivates the plugin.
	 *
	 * @since 1.0.0
	 */
	protected function deactivate_plugin() {

		deactivate_plugins( plugin_basename( __FILE__ ) );

		if ( isset( $_GET['activate'] ) ) {
			unset( $_GET['activate'] );
		}
	}


	/**
	 * Returns the message for display when the environment is incompatible with this plugin.
	 *
	 * @since 1.0.0
	 *
	 * @return string
	 */
	protected function get_environment_message() {

		return sprintf( 'The minimum PHP version required for this plugin is %1$s. You are running %2$s.', $this->min_php, PHP_VERSION );
	}

	/**
	 * Checks the server environment and other factors and deactivates plugins as necessary.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function activation_check() {

		if ( ! $this->is_environment_compatible() ) {

			$this->deactivate_plugin();

			wp_die( $this->plugin_name . ' could not be activated. ' . $this->get_environment_message() );
		}

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
	 * Adds an admin notice to be displayed.
	 *
	 * @since 1.0.0
	 *
	 * @param string $slug the notice slug
	 * @param string $class the notice class
	 * @param string $message the notice message body
	 */
	public function add_admin_notice( $slug, $class, $message ) {

		$this->notices[ $slug ] = array(
			'class'   => $class,
			'message' => $message
		);
	}

	/**
	 * Displays any admin notices added
	 *
	 * @internal
	 *
	 * @since 2.8.0
	 */
	public function admin_notices() {
		$notices = (array) array_merge( $this->notices, get_option( 'wc_variation_swatches_admin_notifications', [] ) );
		foreach ( $notices as $notice_key => $notice ) :

			?>
			<div class="notice <?php echo sanitize_html_class( $notice['class'] ); ?>">
				<p><?php echo wp_kses( $notice['message'], array( 'a' => array( 'href' => array() ) ) ); ?></p>
			</div>
			<?php
			update_option( 'wc_variation_swatches_admin_notifications', [] );
		endforeach;
	}

	/**
	 * Initializes the plugin.
	 *
	 * @internal
	 *
	 * @since 1.0.0
	 */
	public function init_plugin() {
		if ( $this->plugins_compatible() ) {
			$this->define_constants();
			$this->includes();
			do_action( 'wc_variation_swatches_loaded' );
		}
	}

	/**
	 * Define EverProjects Constants.
	 *
	 * @since 1.0.0
	 * @return void
	 */
	private function define_constants() {
		//$upload_dir = wp_upload_dir( null, false );
		define( 'WC_VARIATION_SWATCHES_VERSION', $this->version );
		define( 'WC_VARIATION_SWATCHES_FILE', __FILE__ );
		define( 'WC_VARIATION_SWATCHES_PATH', dirname( WC_VARIATION_SWATCHES_FILE ) );
		define( 'WC_VARIATION_SWATCHES_INCLUDES', WC_VARIATION_SWATCHES_PATH . '/includes' );
		define( 'WC_VARIATION_SWATCHES_ADMIN', WC_VARIATION_SWATCHES_PATH . '/includes/admin' );
		define( 'WC_VARIATION_SWATCHES_URL', plugins_url( '', WC_VARIATION_SWATCHES_FILE ) );
		define( 'WC_VARIATION_SWATCHES_ASSETS_URL', WC_VARIATION_SWATCHES_URL . '/assets' );
		define( 'WC_VARIATION_SWATCHES_TEMPLATES_DIR', WC_VARIATION_SWATCHES_PATH . '/templates' );
	}


	/**
	 * Include required core files used in admin and on the frontend.
	 */
	public function includes() {
		//core includes
		include_once WC_VARIATION_SWATCHES_INCLUDES . '/class-install.php';
		include_once WC_VARIATION_SWATCHES_INCLUDES . '/core-functions.php';
		include_once WC_VARIATION_SWATCHES_INCLUDES . '/script-functions.php';
		include_once WC_VARIATION_SWATCHES_INCLUDES . '/hook-functions.php';
		include_once WC_VARIATION_SWATCHES_INCLUDES . '/class-single-variation.php';

		//admin includes
		if ( is_admin() ) {
			require_once WC_VARIATION_SWATCHES_INCLUDES . '/admin/class-settings-api.php';
			require_once WC_VARIATION_SWATCHES_INCLUDES . '/admin/class-settings.php';
			require_once WC_VARIATION_SWATCHES_INCLUDES . '/admin/class-attribute-handler.php';
		}
	}

	/**
	 * Initialize plugin for localization
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	public function localization_setup() {
		load_plugin_textdomain( 'wc-variation-swatches', false, dirname( plugin_basename( __FILE__ ) ) . '/i18n/languages/' );
	}


	/**
	 * Plugin action links
	 *
	 * @param  array $links
	 *
	 * @return array
	 */
	public function plugin_action_links( $links ) {
		$links[] = '<a href="' . admin_url( 'edit.php?post_type=product&page=wc-variation-swatches' ) . '">' . __( 'Settings', 'wc-variation-swatches' ) . '</a>';

		return $links;
	}

	/**
	 * Main WC_Variation_Swatches Instance.
	 *
	 * Ensures only one instance of WC_Variation_Swatches is loaded or can be loaded.
	 *
	 * @since 1.0.0
	 * @static
	 * @return WC_Variation_Swatches - Main instance.
	 */
	public static function instance() {
		if ( is_null( self::$instance ) ) {
			self::$instance = new self();
		}

		return self::$instance;
	}


}

function wc_variation_swatches() {
	return WC_Variation_Swatches::instance();
}

//fire off the plugin
wc_variation_swatches();
