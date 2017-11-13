<?php
/**
 * File contains functions for Logout settings.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Main Class Defined
 *
 * @since  1.0.0
 * @author  Deepen
 */
final class Inactive_Logout_Main {

	const INA_VERSION = '5.0';

	const DEEPEN_URL = 'https://deepenbajracharya.com.np';

	/**
	 * Directory of plugin.
	 *
	 * @var $plugin_dir
	 */
	public $plugin_dir;

	/**
	 * Plugin filesystem directory path
	 *
	 * @var $plugin_path
	 */
	public $plugin_path;

	/**
	 * Plugin directory url.
	 *
	 * @var $plugin_url
	 */
	public $plugin_url;

	/**
	 * Plugin name.
	 *
	 * @var $plugin_name
	 */
	public $plugin_name;

	/**
	 * Class instance.
	 *
	 * @access protected
	 *
	 * @var $instance
	 */
	protected static $instance;

	/**
	 * Return class instance.
	 *
	 * @return static Instance of class.
	 */
	public static function instance() {
		if ( ! isset( self::$instance ) ) {
			self::$instance = new static();
		}
		return self::$instance;
	}

	/**
	 * Inactive_Logout_Main constructor.
	 */
	protected function __construct() {
		$this->plugin_path = trailingslashit( dirname( plugin_dir_path( __FILE__ ) ) );
		$this->plugin_dir  = trailingslashit( basename( $this->plugin_path ) );
		$this->plugin_url  = plugins_url( $this->plugin_dir );

		add_action( 'init', array( $this, 'ina_load_text_domain' ) );
		$this->ina_plugins_loaded();
	}

	/**
	 * Plugin activation callback.
	 *
	 * @see register_deactivation_hook()
	 */
	public static function ina_activate() {
		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;

			// Get all blog ids.
			$blogids = $wpdb->get_col( "SELECT blog_id FROM $wpdb->blogs" ); // WPCS: db call ok, cache ok.
			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );
				self::instance()->_ina_activate_multisite();
			}
			switch_to_blog( $old_blog );
			return;
		} else {
			self::instance()->_ina_activate_multisite();
		}

		// Load Necessary Components after activation.
		self::instance()->ina_plugins_loaded();
	}

	/**
	 * Saving options for multisite.
	 */
	protected function _ina_activate_multisite() {
		$time = 15 * 60; // 15 Minutes

		$msg = sprintf(
			'<p>%s</p><p>%s</p>',
			esc_html__( 'You are being timed-out out due to inactivity. Please choose to stay signed in or to logoff.', 'inactive-logout' ),
			esc_html__( 'Otherwise, you will be logged off automatically.', 'inactive-logout' )
		);

		$options = array(
			'__ina_logout_time'         => $time,
			'__ina_popup_overlay_color' => '#000000',
			'__ina_logout_message'      => $msg,
		);

		update_option( '__ina_inactive_logout_options', $options );
	}

	/**
	 * Managing things when plugin is deactivated.
	 */
	public static function ina_deactivate() {

		if ( function_exists( 'is_multisite' ) && is_multisite() ) {
			global $wpdb;
			$old_blog = $wpdb->blogid;

			// Get all blog ids.
			$blogids = $wpdb->get_col( "SELECT blog_id FROM {$wpdb->blogs}" ); // WPCS: db call ok, cache ok.

			foreach ( $blogids as $blog_id ) {
				switch_to_blog( $blog_id );

				delete_option( '__ina_inactive_logout_options' );

				delete_site_option( '__ina_inactive_logout_options' );
			}
			switch_to_blog( $old_blog );
			return;
		} else {
			delete_option( '__ina_inactive_logout_options' );
		}
	}

	/**
	 * Manging things when plugin is loaded.
	 */
	protected function ina_plugins_loaded() {
		$options = get_option( '__ina_inactive_logout_options' );
		if ( ! isset( $options['__ina_popup_overlay_color'] ) && empty( $options['__ina_popup_overlay_color'] ) ) {
			$options['__ina_popup_overlay_color'] = '#000000';
			update_option( '__ina_inactive_logout_options', $options );
		}

		if ( is_user_logged_in() ) {
			if ( $this->ina_supported_version( 'WordPress' ) && $this->ina_supported_version( 'php' ) ) {
				$this->ina_add_hooks();
				$this->ina_load_dependencies();
				$this->ina_define_them_constants();
			} else {
				// Either PHP or WordPress version is inadequate so we simply return an error.
				$this->ina_display_not_supported_error();
			}
		}

	}

	/**
	 * Define Constant Values
	 */
	public function ina_define_them_constants() {
		$ina_helpers = Inactive_Logout_Helpers::instance();
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VERSION', self::INA_VERSION );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_SLUG', 'inactive-logout' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_VIEWS', $this->plugin_path . 'views' );
		$ina_helpers->ina_define( 'INACTIVE_LOGOUT_ASSETS_URL', $this->plugin_url . 'assets/' );
	}

	/**
	 * Require Dependencies files.
	 */
	protected function ina_load_dependencies() {
		// Loading Helpers.
		require_once $this->plugin_path . 'src/class-inactive-logout-helpers.php';

		// Loading Admin Views.
		require_once $this->plugin_path . 'src/class-inactive-logout-admin-views.php';
		require_once $this->plugin_path . 'src/class-inactive-logout-functions.php';
	}

	/**
	 * Add filters and actions
	 */
	protected function ina_add_hooks() {
		add_action( 'admin_enqueue_scripts', array( $this, 'ina_admin_scripts' ) );
		add_action( 'wp_enqueue_scripts', array( $this, 'ina_admin_scripts' ) );
	}

	/**
	 * Loading Backend Scripts.
	 *
	 * @param string $hook_suffix Suffix for hooks.
	 */
	public function ina_admin_scripts( $hook_suffix ) {
		global $current_user;

		if ( is_user_logged_in() ) {

			$override = false;

			// Check if multisite.
			if ( is_multisite() ) {
				$options = get_site_option( '__ina_inactive_logout_options' );

				$override = ( isset( $options['__ina_overrideby_multisite_setting'] ) ) ? $options['__ina_overrideby_multisite_setting'] : false;
			}

			// If overide is empty then take default option.
			if ( empty( $override ) ) {
				$options = get_option( '__ina_inactive_logout_options' );
			}

			$ina_logout_time          = ( isset( $options['__ina_logout_time'] ) ) ? $options['__ina_logout_time'] : null;
			$idle_disable_countdown   = ( isset( $options['__ina_disable_countdown'] ) ) ? $options['__ina_disable_countdown'] : 10;

			$ina_meta_data                             = array();
			$ina_meta_data['ina_timeout']              = ( isset( $ina_logout_time ) ) ? $ina_logout_time : 15 * 60;
			$ina_meta_data['ina_disable_countdown']    = $idle_disable_countdown;

			wp_enqueue_script( 'ina-logout-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout.js', array( 'jquery' ), time(), true );
			wp_localize_script( 'ina-logout-js', 'ina_meta_data', $ina_meta_data );

			if ( 'settings_page_inactive-logout' === $hook_suffix || 'toplevel_page_inactive-logout' === $hook_suffix ) {
				wp_enqueue_script( 'ina-logout-inactive-logoutonly-js', INACTIVE_LOGOUT_ASSETS_URL . 'js/inactive-logout-other.js', array( 'jquery', 'wp-color-picker' ), time(), true );

				wp_localize_script(
					'ina-logout-inactive-logoutonly-js', 'ina_other_ajax', array(
						'ajaxurl'      => admin_url( 'admin-ajax.php' ),
						'ina_security' => wp_create_nonce( '_ina_nonce_security' ),
					)
				);
			}
			wp_enqueue_style( 'ina-logout', INACTIVE_LOGOUT_ASSETS_URL . 'css/inactive-logout.css', false, time() );

			wp_localize_script(
				'ina-logout-js', 'ina_ajax', array(
					'ajaxurl'      => admin_url( 'admin-ajax.php' ),
					'ina_security' => wp_create_nonce( '_checklastSession' ),
				)
			);
		}
	}

	/**
	 * Test PHP and WordPress versions for compatibility
	 *
	 * @param string $checking - checking to be tested such as 'php' or 'WordPress'.
	 *
	 * @return boolean - is the existing version of the checking supported?
	 */
	public function ina_supported_version( $checking ) {

		$supported = false;

		switch ( strtolower( $checking ) ) {
			case 'wordpress': // WPCS: spelling ok.
				$supported = version_compare( get_bloginfo( 'version' ), '4.0', '>=' );
				break;
			case 'php':
				$supported = version_compare( phpversion(), '5.2', '>=' );
				break;
		}

		return $supported;
	}

	/**
	 * Display a WordPress or PHP incompatibility error
	 */
	public function ina_display_not_supported_error() {
		if ( ! $this->ina_supported_version( 'WordPress' ) ) {
			// translators: Minimum required WordPress version.
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires WordPress %s or higher. Please upgrade your WordPress install.', 'inactive-logout' ), '4.0' ) . '</p>';
			exit;
		}
		if ( ! $this->ina_supported_version( 'php' ) ) {
			// translators: Minimum required PHP version.
			echo '<p>' . sprintf( esc_html__( 'Sorry, Inactive User Logout requires PHP %s or higher. Talk to your Web host about moving you to a newer version of PHP.', 'inactive-logout' ), '5.4' ) . '</p>';
			exit;
		}
	}

	/**
	 * Load the text domain.
	 */
	public function ina_load_text_domain() {
		$domain = 'inactive-logout';
		$locale = apply_filters( 'plugin_locale', get_locale(), $domain );
		load_plugin_textdomain( $domain, false, $this->plugin_dir . 'lang/' );
	}
}
