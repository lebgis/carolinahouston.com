jQuery(document).ready(function($) {
	if ($('#form-create-account').length > 0) {
		$('#form-create-account').validate({});		
		
		$( "#ca-email" ).rules( "add", {
			required: true,
			email: 	  true,
			remote: {
				url: ZonerGlobal.ajaxurl,
				type: "post",
				data: {
					action: 'zoner_reg_user_account_is_email_exists'
				}
			},
			messages: {
                remote: zonerRegisterUserForm.valid_email_mess
            }
		});
		
		$( "#ca-login-name" ).rules( "add", {
			required: true,
			minlength : 2,
			remote: {
				url: ZonerGlobal.ajaxurl,
				type: "post",
				data: {
					action: 'zoner_reg_user_account_is_login_exists'
				}
			},
			messages: {
                remote: zonerRegisterUserForm.valid_login_mess
            }
			
		});
		
		$( "#ca-first-name, #ca-last-name" ).rules( "add", {
			minlength : 2,
			required: true,
		});
	
		// $( "#ca-password" ).rules( "add", {
			// minlength : 6,
			// required: true,
		// });

		// $( "#ca-confirm-password" ).rules( "add", {
			// required: true,
			// minlength: 6,
			// equalTo : "#ca-password"
		// });
	}	
				
});
