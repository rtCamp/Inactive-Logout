<?php
/**
 * Template for Modal.
 *
 * @package inactive-logout
 */

?>

<?php
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

$ina_full_overlay        = ( isset( $options['__ina_full_overlay'] ) ) ? $options['__ina_full_overlay'] : '';
$ina_popup_overlay_color = ( isset( $options['__ina_popup_overlay_color'] ) ) ? $options['__ina_popup_overlay_color'] : '';

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
			<?php $message_content = ( isset( $options['__ina_logout_message'] ) ) ? $options['__ina_logout_message'] : ''; ?>
			<?php echo apply_filters( 'the_content', $message_content ); // WPCS: XSS ok. ?>
			<p class="ina-dp-noflict-btn-container"><a class="button button-primary ina_stay_logged_in" href="javascript:void(0);"><?php esc_html_e( 'Continue', 'inactive-logout' ); ?> <span class="ina_countdown"></span></a></p>
		</div>
	</div>
</div>
<!--END INACTIVE LOGOUT MODAL CONTENT-->
