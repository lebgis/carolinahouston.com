var zoner_created_user 			= zonerSignIn.zoner_created_user;
var zoner_message_created_user 	= zonerSignIn.zoner_message_created_user;
var valid_pass_mess  			= zonerSignIn.valid_pass_mess;
var valid_email_mess 			= zonerSignIn.valid_email_mess;
var frg_pass_button_text 		= zonerSignIn.frg_pass_button_text;

jQuery(document).ready(function($) {
	if (zoner_created_user) {
		$.jGrowl(zoner_message_created_user, { position : "bottom-left" });
	}
	
	if ($('#form-signin').length > 0) {
		$('#form-signin').validate({});		
		
		$( "#si-email" ).rules( "add", {
			required: true,
			email: 	  true,
			remote: {
				url: ajaxurl,
				type: "post",
				data: {
					action: 'zoner_signin_user_email_exists'
				}
			},
			messages: {
                remote: valid_email_mess
            }
		});
		
		$( "#si-password" ).rules( "add", {
			required: true,
			minlength : 6,
			remote: {
				url: ajaxurl,
				type: "post",
				data: {
					action: 'zoner_signin_user_pass_exists',
					si_email : function () {
						return $( "#si-email" ).val();
					}
				}
			},
			messages: {
                remote: valid_pass_mess
            }
			
		});
		
		
		$('#frg-password').on('click', function() {
			$(this).fadeOut('slow', function() {
				$(this).remove();
			});
			
			if ($('#si-password').length > 0) {
				$('#si-password').parent().fadeOut("slow", function() {
					$('#form-signin .form-group.is-reset-password').fadeIn('slow');
					$('input#type-form').val('2');
					$('button#account-submit').text(frg_pass_button_text);
					$('#si-password').parent().remove();
				});
				
			}
			return false;
		});
		
		$('#form-signin .social.btn.btn-facebook, #form-signin .social.btn.btn-google-plus').on('click', function() {
			var vAction = $(this).data('socialact');

			jQuery.ajax({
				type: 'POST',
				url: ajaxurl,
				data: { 
					'action' : 'zoner_ajax_social_login_' + vAction 
				},
				success: function (RedirectUrl) {
					var inArray = $.parseJSON(RedirectUrl);
					var link    = inArray.link;
					var errMsg  = inArray.errorMessage;
					if (!link) {
						$.jGrowl(errMsg, { position : "bottom-left" });
					} else {
						window.location.href = link;
					}
				},
				error: function (errorConnect) {
				}
			});
			return false;
		});
	}	
});
