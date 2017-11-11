/*
* @since  1.2.0
* @author  Deepen
*/
jQuery(function($) {
  $('.ina-hacking-select').select2();
  $(".ina-hacking-multi-select").select2({ width: '500px', placeholder: "Select Roles" });

  // Add Color Picker to all inputs that have 'color-field' class
  $( '.ina_color_picker' ).wpColorPicker();

  if( $('input[name="ina_full_overlay"]').is(":checked") ) {
    $('.ina_colorpicker_show').show();
  } else {
    $('.ina_colorpicker_show').hide();
  }

  $('input[name="ina_full_overlay"]').click(function(){
    if( $( this ).prop( "checked" )) {
      $('.ina_colorpicker_show').show();
    } else {
      $('.ina_colorpicker_show').hide();
    }
  });

  //FOR ADV SETTINGS MULTI ROLE ENABLE CHECKBOX
  if( $('#ina_enable_different_role_timeout').is(":checked") ) {
    $('.ina-multi-role-table, .hide-description-ina').show();
  } else {
    $('.ina-multi-role-table, .hide-description-ina').hide();
  }

  $('#ina_enable_different_role_timeout').click(function() {
    if( $( this ).prop( "checked" )) {
      $('.ina-multi-role-table, .hide-description-ina').show();
    } else {
      $('.ina-multi-role-table, .hide-description-ina').hide();
    }
  });

  /**
  * Reset all Advanced Data
  * @since  1.3.0
  * @author  Deepen
  */
  $('#ina-reset-adv-data').click(function() {
    var msg = confirm( $(this).data('msg') );
    if( msg ) {
      var send_data = { security: ina_other_ajax.ina_security, action: 'ina_reset_adv_settings' };
      $('#ina-cover-loading').show();
      $.post( ina_other_ajax.ajaxurl, send_data).done(function(response) {
        $('#ina-cover-loading').fadeOut("slow");
        $('#message').fadeIn().html('<p>' + response.msg + '</p>');
        setTimeout(function() {
          location.reload();
        }, 500);
      });
    } else {
      return false;
    }
  });
});
