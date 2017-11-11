<?php
/**
 * File contains class related to Admin views.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Admin Views Class
 *
 * @since  1.0.0
 * @author  Deepen
 */
class Inactive_Logout_Admin_Views {

	/**
	 * Helper.
	 *
	 * @var Inactive_Logout_Helpers
	 */
	public $helper;

	/**
	 * Inactive_Logout_Admin_Views constructor.
	 */
	public function __construct() {
		add_action( 'admin_menu', array( $this, 'ina_create_options_menu' ) );

		// Add Menu for multisite network.
		add_action( 'network_admin_menu', array( $this, 'ina_menu_multisite_network' ) );

		add_action( 'ina_before_settings_wrapper', array( $this, 'ina_before_settings_wrap' ) );
		add_action( 'ina_after_settings_wrapper', array( $this, 'ina_after_settings_wrap' ) );

		$this->helper = Inactive_Logout_Helpers::instance();
	}

	/**
	 * Add a Menu Option in settings
	 */
	public function ina_create_options_menu() {
		if ( is_multisite() ) {
			$idle_overrideby_multisite_setting = get_site_option( '__ina_overrideby_multisite_setting' );
			if ( empty( $idle_overrideby_multisite_setting ) ) {
				add_options_page(
					__( 'Inactive User Logout Settings', 'inactive-logout' ),
					__( 'Inactive Logout', 'inactive-logout' ),
					'manage_options',
					'inactive-logout',
					array( $this, 'ina__render_options' )
				);
			}
		} else {
			add_options_page(
				__( 'Inactive User Logout Settings', 'inactive-logout' ),
				__( 'Inactive Logout', 'inactive-logout' ),
				'manage_options',
				'inactive-logout',
				array( $this, 'ina__render_options' )
			);
		}
	}

	/**
	 * Add menu page.
	 */
	function ina_menu_multisite_network() {
		add_menu_page(
			__( 'Inactive User Logout Settings', 'inactive-logout' ),
			__( 'Inactive Logout', 'inactive-logout' ),
			'manage_options',
			'inactive-logout',
			array( $this, 'ina__render_options' )
		);
	}

	/**
	 * Rendering the output.
	 */
	public function ina__render_options() {
		$saved = false;

		$submit = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_STRING );

		if ( isset( $submit ) ) {
			$saved = $this->ina__process_basic_settings();
		}

		// Css rules for Color Picker.
		wp_enqueue_style( 'wp-color-picker' );

		// Include Template.
		do_action( 'ina_before_settings_wrapper' );

		// BASIC.
		if ( is_network_admin() && is_multisite() ) {
			$idle_overrideby_multisite_setting = get_site_option( '__ina_overrideby_multisite_setting' );
		}

		$time                     = get_option( '__ina_logout_time' );
		$countdown_enable         = get_option( '__ina_disable_countdown' );
		$ina_full_overlay         = get_option( '__ina_full_overlay' );
		$ina_popup_overlay_color  = get_option( '__ina_popup_overlay_color' );

		require_once INACTIVE_LOGOUT_VIEWS . '/tpl-inactive-logout-basic.php';

		do_action( 'ina_after_settings_wrapper' );
	}

	/**
	 * Manages Basic settings.
	 *
	 * @return bool|void
	 */
	public function ina__process_basic_settings() {

		$sm_nonce = filter_input( INPUT_POST, '_save_timeout_settings', FILTER_SANITIZE_STRING );
		$nonce    = isset( $sm_nonce ) ? $sm_nonce : '';
		$submit   = filter_input( INPUT_POST, 'submit', FILTER_SANITIZE_STRING );

		if ( isset( $submit ) && ! wp_verify_nonce( $nonce, '_nonce_action_save_timeout_settings' ) ) {
			wp_die( 'Not Allowed' );
			return;
		}

		$idle_timeout               = filter_input( INPUT_POST, 'idle_timeout', FILTER_SANITIZE_NUMBER_INT );
		$idle_timeout_message       = wp_kses_post( filter_input( INPUT_POST, 'idle_message_text' ) );
		$idle_disable_countdown     = filter_input( INPUT_POST, 'idle_disable_countdown', FILTER_SANITIZE_NUMBER_INT );

		$ina_background_popup = trim( filter_input( INPUT_POST, 'ina_color_picker' ) );
		$ina_background_popup = strip_tags( stripslashes( $ina_background_popup ) );

		$ina_full_overlay         = filter_input( INPUT_POST, 'ina_full_overlay', FILTER_SANITIZE_NUMBER_INT );

		do_action( 'ina_before_update_basic_settings' );

		// If Mulisite is Active then Add these settings to mulsite option table as well.
		if ( is_network_admin() && is_multisite() ) {
			$idle_overrideby_multisite_setting = filter_input( INPUT_POST, 'idle_overrideby_multisite_setting', FILTER_SANITIZE_NUMBER_INT );
			update_site_option( '__ina_overrideby_multisite_setting', $idle_overrideby_multisite_setting );

			$save_minutes = $idle_timeout * 60; // 60 minutes
			if ( $idle_timeout ) {
				update_site_option( '__ina_logout_time', $save_minutes );
				update_site_option( '__ina_logout_message', $idle_timeout_message );
				update_site_option( '__ina_disable_countdown', $idle_disable_countdown );
				update_site_option( '__ina_full_overlay', $ina_full_overlay );
				update_site_option( '__ina_popup_overlay_color', $ina_background_popup );

			}
		}

		$save_minutes = $idle_timeout * 60; // 60 minutes
		if ( $idle_timeout ) {
			update_option( '__ina_logout_time', $save_minutes );
			update_option( '__ina_logout_message', $idle_timeout_message );
			update_option( '__ina_disable_countdown', $idle_disable_countdown );
			update_option( '__ina_full_overlay', $ina_full_overlay );
			update_option( '__ina_popup_overlay_color', $ina_background_popup );

			return true;
		}

		do_action( 'ina_after_update_basic_settings' );
	}

	/**
	 * Settings wrapper html element.
	 */
	public function ina_before_settings_wrap() {
		echo '<div id="ina-cover-loading" style="display: none;"></div><div class="wrap">';
	}

	/**
	 * Settings wrapper html element.
	 */
	public function ina_after_settings_wrap() {
		echo '</div>';
	}
}
new Inactive_Logout_Admin_Views();
