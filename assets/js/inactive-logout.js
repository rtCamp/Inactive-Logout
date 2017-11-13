var timeoutID;
var tabID;
var timeoutMessage;
var ina_timeout = ina_meta_data.ina_timeout;
var timeout_defined = ina_timeout * 1000; //Minutes
var messageBox = 0;

/**
 * Initial setup.
 */
function setup() {
	this.addEventListener("mousemove", resetTimer, false);
	this.addEventListener("mousedown", resetTimer, false);
	this.addEventListener("keypress", resetTimer, false);
	this.addEventListener("DOMMouseScroll", resetTimer, false);
	this.addEventListener("mousewheel", resetTimer, false);
	this.addEventListener("touchmove", resetTimer, false);
	this.addEventListener("MSPointerMove", resetTimer, false);

	//First get the broswer id
	tabID = ( sessionStorage.tabID && '2' !== sessionStorage.closedLastTab ) ? sessionStorage.tabID : sessionStorage.tabID = Math.random();
	sessionStorage.closedLastTab = '2';
	jQuery(window).on('unload beforeunload', function() {
		sessionStorage.closedLastTab = '1';
	});
	localStorage.setItem("ina__browserTabID", tabID);

	startTimer();
}
setup();

/**
 * Starting timeout timer to go into inactive state after 15 seconds
 * if any event like mousemove is not triggered.
 */
function startTimer() {
	timeoutID = window.setTimeout(goInactive, 15000);
}

/**
 * Resetting the timer.
 *
 * @param e Event object.
 */
function resetTimer(e) {
	window.clearTimeout(timeoutID);
	window.clearTimeout(timeoutMessage);
	localStorage.setItem("ina__browserTabID", tabID);
	goActive();
}

/**
* User is inactive now save last session activity time here.
*/
function goInactive() {

	if ( 0 === parseInt( messageBox ) ) {
		var dateTime = Date.now();
		var timestamp = Math.floor(dateTime / 1000);

		jQuery(document).ready(function($) {
			//Update Last Active Status
			var postData = { action: 'ina_checklastSession', do: 'ina_updateLastSession', security: ina_ajax.ina_security, timestamp: timestamp };

			$.post( ina_ajax.ajaxurl, postData ).done(function() {
				var browserTabID = localStorage.getItem("ina__browserTabID");
				if( browserTabID == tabID ) {
					timeoutMessage = window.setTimeout(showTimeoutMessage, timeout_defined);
				}
			});
		});
	}
}

/**
 * Make an paragraph html element.
 *
 * @returns object Returns paragraph element.
 */
function inaMakeParagraph() {
	return jQuery( '<p />' );
}

/**
 * Make user logout and Add msg and reload link in popup for user.
 */
function inaChecklastSession() {

	// Disabled Countdown but directly logout.
	var postData = { action: 'ina_checklastSession', do: 'ina_logout', security: ina_ajax.ina_security };
	jQuery.post( ina_ajax.ajaxurl, postData).done(function(op) {

		if( op.redirect_url ) {
			window.location = op.redirect_url;
		} else {
			var opMsg = inaMakeParagraph();
			opMsg.text( op.msg );

			var opLink = inaMakeParagraph();
			opLink.attr( 'class', 'ina-dp-noflict-btn-container' );

			var link = jQuery( '<a />' );
			link.attr( 'class', 'btn-timeout' );
			link.attr( 'href', 'javascript:void(0);' );
			link.attr( 'onclick', 'window.location.reload();' );
			link.text( 'OK' );

			opLink.append( link );

			var dialogBox = jQuery('#ina__dp_logout_message_box .ina-dp-noflict-modal-body');

			dialogBox.html( opMsg );
			dialogBox.append( opLink );
		}
		return false;
	});
}

/**
 * Show timeout Message Now.
 */
function showTimeoutMessage() {
	var countdown = ina_meta_data.ina_disable_countdown;
	var t;

	jQuery(function($) {
		document.onkeydown = function(evt) {
			var keycode = evt.charCode || evt.keyCode;
			//Disable all keys except F5
			if ( 116 !== parseInt( keycode ) ) return false;
		};

		//Disable Right Click
		window.oncontextmenu = function() {
			return false;
		};

		var ina_popup_bg_enalbed = $('.ina__no_confict_popup_bg').data('bgenabled');
		if ( ina_popup_bg_enalbed ) {
			var ina_popup_bg = $('.ina__no_confict_popup_bg').data('bg');
			$('#ina__dp_logout_message_box').css('background', ina_popup_bg);
		}

		messageBox = 1;
		$('#ina__dp_logout_message_box').show();
		var setting_countdown = setInterval(function() {
			if ( countdown >= 0 ) {
				t = countdown--;
				$(".ina_countdown").html( '(' + t + ')' );
			}

			if ( t == 0 ) {
				clearTimeout(setting_countdown);

				// Checking last session.
				inaChecklastSession();
			}
		}, 1000);

		$('.ina_stay_logged_in').click(function() {
			document.onkeydown = function (evt) { return true; }
			window.oncontextmenu = null;
			clearTimeout( setting_countdown );
			countdown = 10;
			messageBox = 0;
			$('#ina__dp_logout_message_box').hide();
			$('.ina_countdown').text('');
		});
	});
}

/**
* User is actively Working and Browsing
*/
function goActive() {
	startTimer();
}
