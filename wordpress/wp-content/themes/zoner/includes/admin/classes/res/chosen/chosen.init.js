jQuery(document).ready(function($) {
	
	if ($('#chosen-agents').length > 0) {
		$('#chosen-agents').chosen({
			width: "95%"
		}).change(function(evt, params) {
			
			$.each(params, function(key, id_agent) {
				if (key == 'selected') {
					var data = { 
								 action: 'admin_add_agent_to_agency', 
								 user_id : id_agent,
								 post_id : $('#post_ID').val()
								 
						};
					
					$.post(ajaxurl, data, function(response) {
						if (response) {
							var array_in = jQuery.parseJSON(response);
							var vHtml = array_in[0];
							var vInviteID = array_in[1];
					
							var selectedOption = $('select#chosen-agents option[value="'+ id_agent +'"]');
								selectedOption.data('inviteid', vInviteID)		
								selectedOption.attr('selected', 'selected');
							
							$('table#agents-grid tbody').append(vHtml);
						}		
					});
					
				} else {
					var invite_id = $('select#chosen-agents option[value="'+ id_agent +'"]').prop('selected',true).data('inviteid');
					
					if (invite_id) {
						var data = { 
								 action: 'admin_delete_agent_from_agency', 
								 invite_id : invite_id,
								 post_id : $('#post_ID').val()
						};
					
						$.post(ajaxurl, data, function(response) {
							$('table#agents-grid tbody tr#agent-link-' + invite_id).fadeOut('400', function() {
								$(this).remove();
							});		
						});
					}	
				}
				
			});
		

		});
	}	
});