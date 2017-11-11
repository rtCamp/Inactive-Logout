<?php
/**
 * File contains functions for logout helpers.
 *
 * @package inactive-logout
 */

// Don't load directly.
if ( ! defined( 'ABSPATH' ) ) {
	die( '-1' );
}

/**
 * Class with a few helpers
 */
class Inactive_Logout_Helpers {

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
	 * Define constant if not already set.
	 *
	 * @param  string      $name  Constant name.
	 * @param  string|bool $value Constant value.
	 *
	 * @since  2.0.0
	 *
	 * @author  Deepen Bajracharya
	 */
	public function ina_define( $name, $value ) {
		if ( ! defined( $name ) ) {
			define( $name, $value );
		}
	}

	/**
	 * Convert seconds to minutes.
	 *
	 * @param int $value Number of seconds.
	 *
	 * @return string
	 */
	public function ina_convert_to_minutes( $value ) {
		$minutes = floor( $value / 60 );
		return $minutes . ' ' . esc_html__( 'Minute(s)', 'inactive-logout' );
	}

	/**
	 * Manages reloading page.
	 */
	public function ina_reload() {
		?>
		<script type="text/javascript">location.reload();</script>
		<?php
	}

	/**
	 * Check to disable the Inactive for certain user role
	 *
	 * @author  Deepen
	 * @return BOOL
	 */
	public function ina_check_user_role() {
		$user      = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
		$result    = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( 1 === intval( $role['disabled_feature'] ) ) {
					if ( in_array( $role['role'], (array) $user->roles, true ) ) {
						$result = true;
					}
				}
			}
		}

		return $result;
	}

	/**
	 * Check to disable the Inactive for certain user role
	 *
	 * @author  Deepen
	 * @since  1.6.0
	 * @return BOOL
	 */
	public function ina_check_user_role_concurrent_login() {
		$user      = wp_get_current_user();
		$ina_roles = get_option( '__ina_multiusers_settings' );
		$result    = false;
		if ( $ina_roles ) {
			foreach ( $ina_roles as $role ) {
				if ( ! empty( $role['disabled_concurrent_login'] ) && 1 === intval( $role['disabled_concurrent_login'] ) ) {
					if ( in_array( $role['role'], (array) $user->roles, true ) ) {
						$result = true;
					}
				}
			}
		}

		return $result;
	}
}
