<?php
/**
 * Template for Basic settings page.
 *
 * @package inactive-logout
 */

?>

<h1><?php esc_html_e( 'Inactive User Logout Settings', 'inactive-logout' ); ?></h1>

<form method="post" action="?page=inactive-logout&tab=ina-basic">
	<?php wp_nonce_field( '_nonce_action_save_timeout_settings', '_save_timeout_settings' ); ?>
  <table class="ina-form-tbl form-table">
	<tbody>
		<?php if ( is_network_admin() ) { ?>
	  <tr>
		<th scope="row"><label for="idle_overrideby_multisite_setting"><?php esc_html_e( 'Override for all sites', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_overrideby_multisite_setting" type="checkbox" id="idle_overrideby_multisite_setting" <?php echo ! empty( $idle_overrideby_multisite_setting ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php esc_html_e( 'When checked below settings will be effective and used for all sites in the network.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
		<?php } ?>
	  <tr>
		<th scope="row"><label for="idle_timeout"><?php esc_html_e( 'Idle Timeout', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_timeout" min="1" type="number" id="idle_timeout" value="<?php echo ( isset( $time ) ) ? esc_attr( $time / 60 ) : 30; ?>" >
		  <i><?php esc_html_e( 'Minute(s)', 'inactive-logout' ); ?></i>
		</td>
	  </tr>
	  <tr class="ina_hide_message_content">
		<th scope="row"><label for="idle_timeout"><?php esc_html_e( 'Idle Message Content', 'inactive-logout' ); ?></label></th>
		<td>
			<?php
			$settings        = array(
				'media_buttons' => false,
				'teeny'         => true,
				'textarea_rows' => 15,
			);
			$message_content = get_option( '__ina_logout_message' );
			$content         = $message_content ? $message_content : null;
			wp_editor( $content, 'idle_message_text', $settings );
			?>
		  <p class="description"><?php esc_html_e( 'Message to be shown when idle timeout screen shows.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="ina_full_overlay"><?php esc_html_e( 'Popup Background', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="ina_full_overlay" type="checkbox" <?php echo ! empty( $ina_full_overlay ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php esc_html_e( 'Choose a background color to hide after logout. Enabling this option will remove tranparency.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr class="ina_colorpicker_show">
		<th scope="row"><label for="ina_color_picker"><?php esc_html_e( 'Popup Background Color', 'inactive-logout' ); ?></label></th>
		<td>
		  <input type="text" name="ina_color_picker" value="<?php echo ( ! empty( $ina_popup_overlay_color ) ) ? esc_attr( $ina_popup_overlay_color ) : ''; ?>" class="ina_color_picker" >
		  <p class="description"><?php esc_html_e( 'Choose a popup background color.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
	  <tr>
		<th scope="row"><label for="idle_disable_countdown"><?php esc_html_e( 'Disable Timeout Countdown', 'inactive-logout' ); ?></label></th>
		<td>
		  <input name="idle_disable_countdown" type="checkbox" id="idle_disable_countdown" <?php echo ! empty( $countdown_enable ) ? 'checked' : false; ?> value="1" >
		  <p class="description"><?php esc_html_e( 'When timeout popup is shown user is not logged out instantly. It gives user a chance to keep using or logout for 10 seconds. Remove this feature and directly log out after inactive.', 'inactive-logout' ); ?></p>
		</td>
	  </tr>
  </tbody>
</table>
<p class="submit"><input type="submit" name="submit" id="submit" class="button button-primary" value="<?php esc_html_e( 'Save Changes', 'inactive-logout' ); ?>"></p>
</form>
