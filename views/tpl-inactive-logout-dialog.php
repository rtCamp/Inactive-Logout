<?php
/**
 * Template for Modal.
 *
 * @package inactive-logout
 */

?>

<?php
$override = is_multisite() ? get_site_option( '__ina_overrideby_multisite_setting' ) : false;
if ( ! empty( $override ) ) {
	$ina_full_overlay         = get_site_option( '__ina_full_overlay' );
	$ina_popup_overlay_color  = get_site_option( '__ina_popup_overlay_color' );
} else {
	$ina_full_overlay         = get_option( '__ina_full_overlay' );
	$ina_popup_overlay_color  = get_option( '__ina_popup_overlay_color' );
}
$bg = isset( $ina_popup_overlay_color ) ? $ina_popup_overlay_color : false;
?>

<!--START INACTIVE LOGOUT MODAL CONTENT-->
<span data-bg="<?php echo esc_attr( $bg ); ?>" class="ina__no_confict_popup_bg" data-bgenabled="<?php echo esc_attr( $ina_full_overlay ); ?>"></span>

<div id="ina__dp_logout_message_box" class="ina-dp-noflict-modal">
	<div class="ina-dp-noflict-modal-content">
		<div class="ina-modal-header">
			<h3><?php esc_html_e( 'Session Timeout', 'inactive-logout' ); ?></h3>
		</div>
		<div class="ina-dp-noflict-modal-body">
			<?php
			if ( ! empty( $override ) ) {
				$message_content = get_site_option( '__ina_logout_message' );
			} else {
				$message_content = get_option( '__ina_logout_message' );
			}
			?>
			<?php echo apply_filters( 'the_content', $message_content ); // WPCS: XSS ok. ?>
			<p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?> <span class="ina_countdown"></span></a></p>
		</div>
	</div>
</div>
<!--END INACTIVE LOGOUT MODAL CONTENT-->
