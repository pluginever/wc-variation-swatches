<?php

namespace Pluginever\WCVariationSwatches\Admin;

class Admin {
	/**
	 * The single instance of the class.
	 *
	 * @var Admin
	 * @since 1.0.0
	 */
	protected static $init = null;

	/**
	 * Admin Instance.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Admin - Main instance.
	 */
	public static function init() {

		if (is_null(self::$init)) {
			self::$init = new self();
			self::$init->setup();
		}

		return self::$init;
	}

	/**
	 * Initialize all Admin related stuff
	 *
	 * @since 1.0.0
	 * @return void
	 */
	public function setup() {

		$this->includes();
		$this->init_hooks();
		$this->instance();

	}

	/**
	 * Includes all files related to admin
	 */
	public function includes() {

		require_once dirname(__FILE__) . '/class-settings-api.php';
		require_once dirname(__FILE__) . '/class-settings.php';
		require_once dirname(__FILE__) . '/class-attribute-handler.php';

	}

	/**
	 *Run hooks on admin initialize
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */

	private function init_hooks() {

		add_action('init', array($this, 'includes'));
		add_action('admin_enqueue_scripts', array($this, 'enqueue_scripts'));
		add_filter('product_attributes_type_selector', array($this, 'add_attribute_types'));

	}


	/**
	 * Fire off all the instances
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */
	protected function instance() {

		new Settings();
		new Attribute_Handler();

	}


	/**
	 * Enqueue all requires css and js for admin backend
	 *
	 * @since 1.0.0
	 *
	 * @return void
	 */

	public function enqueue_scripts() {

		//Register Styles
		wp_register_style('wc-variation-swatches', WPWVS_ASSETS_URL . "/css/admin.css", [], WPWVS_VERSION);

		//Register Scripts
		wp_register_script('wc-variation-swatches', WPWVS_ASSETS_URL . "/js/admin/admin.js", ['jquery', 'wp-color-picker'], WPWVS_VERSION, true);

		//Localize Scripts
		wp_localize_script('wc-variation-swatches', 'wpwvs', [
			'ajaxurl'         => admin_url('admin-ajax.php'),
			'placeholder_img' => WC()->plugin_url() . '/assets/images/placeholder.png',
			'nonce'           => 'wc-variation-swatches'
		]);

		//Enqueue media uploader
		wp_enqueue_media();

		//Enqueue Styles
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_style('wc-variation-swatches');

		//Enqueue Scripts
		wp_enqueue_script('wc-variation-swatches');

	}

	/**
	 * Add extra color, image, label attribute types
	 *
	 * @since 1.0.0
	 *
	 * @param $types
	 *
	 * @return array attribute_types
	 */

	public function add_attribute_types($types) {

		$swatches_types = wc_variation_swatches_types();
		$types          = array_merge($types, $swatches_types);

		return $types;
	}

}

Admin::init();
