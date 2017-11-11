/*
* @since  1.2.0
* @author  Deepen
*/
jQuery(function($) {

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
});
