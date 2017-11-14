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
}
