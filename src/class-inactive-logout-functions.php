<?php
/**
 * File contains functions for Logout.
 *
 * @package inactive-logout
 */

// Not Permission to agree more or less then given.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Core Functions
 *
 * @since  1.0.0
 * @author  Deepen
 */
class Inactive_Logout_Functions {

	/**
	 * Inactive_Logout_Functions constructor.
	 */
	public function __construct() {
		add_action( 'wp_footer', array( $this, 'ina_logout_dialog_modal' ) );
		add_action( 'admin_footer', array( $this, 'ina_logout_dialog_modal' ) );

		// Ajax for checking last session.
		add_action( 'wp_ajax_ina_checklastSession', array( $this, 'ina_checking_last_session' ) );
		add_action( 'wp_ajax_nopriv_ina_checklastSession', array( $this, 'ina_checking_last_session' ) );

		// Ajax for resetting.
		add_action( 'wp_ajax_ina_reset_adv_settings', array( $this, 'ina_reset_adv_settings' ) );

		// Ajax for User Roles only.
		add_action( 'wp_ajax_ina_save_disabled_roles', array( $this, 'ina_save_disabled_roles' ) );
		add_action( 'wp_ajax_ina_get_enabled_roles', array( $this, 'ina_get_enabled_roles' ) );
	}

	/**
	 * Check Last Session and Logout User
	 */
	public function ina_checking_last_session() {
		check_ajax_referer( '_checklastSession', 'security' );

		$timestamp = filter_input( INPUT_POST, 'timestamp', FILTER_SANITIZE_STRING );
		$timestamp = ( isset( $timestamp ) ) ? $timestamp : null;

		$do = filter_input( INPUT_POST, 'do', FILTER_SANITIZE_STRING );

		if ( is_user_logged_in() ) {
			switch ( $do ) {
				case 'ina_updateLastSession':
					update_user_meta( get_current_user_id(), '__ina_last_active_session', $timestamp );
					break;

				case 'ina_logout':

					// Logout Current Users.
					wp_logout();
					wp_send_json(
						array(
							'msg'          => esc_html__( 'You have been logged out because of inactivity.', 'inactive-logout' ),
							'redirect_url' => false,
						)
					);
					break;

				default:
					break;
			}
		}

		wp_die();
	}

	/**
	 * Reset Advanced Settings
	 *
	 * @since  1.3.0
	 */
	public function ina_reset_adv_settings() {
		check_ajax_referer( '_ina_nonce_security', 'security' );
		delete_option( '__ina_roles' );

		if ( is_network_admin() && is_multisite() ) {
			delete_site_option( '__ina_roles' );
		}

		wp_send_json(
			array(
				'code' => 1,
				'msg'  => esc_html__( 'Reset advanced settings successful.', 'inactive-logout' ),
			)
		);
		wp_die();
	}

	/**
	 * Adding Dialog in footer
	 */
	public function ina_logout_dialog_modal() {
		require_once INACTIVE_LOGOUT_VIEWS . '/tpl-inactive-logout-dialog.php';
	}

}
new Inactive_Logout_Functions();
