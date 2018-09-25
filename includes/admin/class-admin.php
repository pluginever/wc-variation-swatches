<?php

namespace Pluginever\WcVariationSwatches\Admin;

class Admin {
	/**
	 * The single instance of the class.
	 *
	 * @var Admin
	 * @since 1.0.0
	 */
	protected static $init = null;

	/**
	 * Frontend Instance.
	 *
	 * @since 1.0.0
	 * @static
	 * @return Admin - Main instance.
	 */
	public static function init() {
		if ( is_null( self::$init ) ) {
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
		require_once dirname( __FILE__ ) . '/class-metabox.php';
		require_once dirname( __FILE__ ) . '/class-attribute-type.php';
		require_once dirname( __FILE__ ) . '/class-term-meta.php';
		require_once dirname( __FILE__ ) . '/class-settings-api.php';
		require_once dirname( __FILE__ ) . '/class-settings.php';
	}

	private function init_hooks() {
		add_action( 'admin_init', array( $this, 'buffer' ), 1 );
		add_action( 'init', array( $this, 'includes' ) );
		add_action( 'admin_enqueue_scripts', array( $this, 'enqueue_scripts' ) );
		add_action( 'init', array( $this, 'taxonomy_field_manage'));
	}


	/**
	 * Fire off all the instances
	 *
	 * @since 1.0.0
	 */
	protected function instance() {
		new MetaBox();
		new Settings();
		new Attribute_type();
	}

	/**
	 * Output buffering allows admin screens to make redirects later on.
	 *
	 * @since 1.0.0
	 */
	public function buffer() {
		ob_start();
	}


	public function enqueue_scripts( $hook ) {
		$suffix = ( defined( 'WP_DEBUG' ) && WP_DEBUG ) ? '' : '.min';
		wp_register_style('wc-variation-swatches', WPWVS_ASSETS_URL."/css/admin{$suffix}.css", [], WPWVS_VERSION);
		wp_register_script('wc-variation-swatches', WPWVS_ASSETS_URL."/js/admin/admin{$suffix}.js", ['jquery', 'wp-color-picker'], WPWVS_VERSION, true);
		wp_localize_script('wc-variation-swatches', 'wpwvs', ['ajaxurl' => admin_url( 'admin-ajax.php' ), 'nonce' => 'wc-variation-swatches']);
		wp_enqueue_style('wc-variation-swatches');
		wp_enqueue_script('wc-variation-swatches');
		wp_enqueue_style( 'wp-color-picker' );
		
	}

	public function taxonomy_field_manage(){
		global $pagenow;
		
		if( !empty($_GET['taxonomy']) && !empty($_GET['post_type'])){
			if($pagenow == 'edit-tags.php'){
				if($_GET['post_type']== 'product'){
					$taxonomy =  $_GET['taxonomy'];
			        $attr_names = explode("pa_",$taxonomy);
			        unset($attr_names[0]);
			        $attr_name = implode(" ",$attr_names);

			        global $wpdb;
        			$attribute_type = $wpdb->get_var("SELECT attribute_type FROM {$wpdb->prefix}woocommerce_attribute_taxonomies WHERE attribute_name='$attr_name'");
        			if(!empty($attribute_type)){
        				new Term_Meta($taxonomy, $attribute_type, $attr_name);
        			}
				}
			}
		}
	}


}

Admin::init();
