<?php 

	if ( ! function_exists( 'zoner_get_delete_agency_wnd' ) ) {
		function zoner_get_delete_agency_wnd() {
			?>
			
			<div id="lmDeleteAgencyWnd" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><?php _e('Delete agency', 'zoner'); ?></h4>
						</div>
						<div class="modal-body">
							<p><?php _e('Are you sure you want to delete agency?', 'zoner'); ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'zoner'); ?></button>
							<button id="deleteAgencyAct" type="button" class="btn btn-primary"><?php _e('Delete', 'zoner'); ?></button>
						</div>
					</div>
				</div>
			</div>
			
			<?php
		}
	}	
	 
	if ( ! function_exists( 'zoner_get_delete_property_wnd' ) ) {
		function zoner_get_delete_property_wnd() {
			?>
			
			<div id="lmDeletePropertyWnd" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><?php _e('Delete property', 'zoner'); ?></h4>
						</div>
						<div class="modal-body">
							<p><?php _e('Are you sure you want to delete property?', 'zoner'); ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'zoner'); ?></button>
							<button id="deletePropertyAct" type="button" class="btn btn-primary"><?php _e('Delete', 'zoner'); ?></button>
						</div>
					</div>
				</div>
			</div>
			
			<?php
		}
	}	 
	
	
	if ( ! function_exists( 'zoner_get_invite_agent_wnd' ) ) {
		function zoner_get_invite_agent_wnd() {
			?>
			
			<div id="lmDeleteinviteWnd" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><?php _e('Delete agent', 'zoner'); ?></h4>
						</div>
						<div class="modal-body">
							<p><?php _e('Are you sure you want to delete agent from invite list?', 'zoner'); ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'zoner'); ?></button>
							<button id="deleteInviteAgentAct" type="button" class="btn btn-primary"><?php _e('Delete', 'zoner'); ?></button>
						</div>
					</div>
				</div>
			</div>
			
			<?php
		}
	}	 
	
	if ( ! function_exists( 'zoner_get_property_chat_wnd' ) ) {
		function zoner_get_property_chat_wnd($userID) {
			global $post, $zoner, $zoner_config;
			
			$curr_user 	 = get_user_by('id', $userID);
			$author_name = zoner_get_user_name($curr_user);
			$author_id   = $userID;
			
			?>
			<div id="startChatWnd" class="modal center-dialog fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-lg">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><?php _e('Create new message', 'zoner'); ?>:</h4>
							<h5 class="modal-title"><?php printf(__( 'To <strong>%1s</strong>', 'zoner' ), $author_name); ?></h5>
						</div>
						<div class="modal-body">
							<textarea name="chatMessage" data-authorid="<?php echo $author_id; ?>" id="chatMessage" cols="30" rows="5" class="form-control" maxlength="512" placeholder="<?php _e('Type your message&#8230;', 'zoner'); ?>" required="required"></textarea>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'zoner'); ?></button>
							<button id="sendchatMessage" type="button" class="btn btn-primary"><?php _e('Send', 'zoner'); ?></button>
						</div>
					</div>
				</div>
			</div>
			
			<?php
		}
	}
	
	if ( ! function_exists( 'zoner_delete_conversation_wnd' ) ) {
		function zoner_delete_conversation_wnd() {
			?>
			
			<div id="lmDeleteConverstionWnd" class="modal fade" tabindex="-1" role="dialog">
				<div class="modal-dialog modal-sm">
					<div class="modal-content">
						<div class="modal-header">
							<button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
							<h4 class="modal-title"><?php _e('Delete Conversation', 'zoner'); ?></h4>
						</div>
						<div class="modal-body">
							<p><?php _e('Are you sure you want to delete conversation?', 'zoner'); ?></p>
						</div>
						<div class="modal-footer">
							<button type="button" class="btn btn-default" data-dismiss="modal"><?php _e('Cancel', 'zoner'); ?></button>
							<button id="deleteConversationAct" type="button" class="btn btn-primary"><?php _e('Delete', 'zoner'); ?></button>
						</div>
					</div>
				</div>
			</div>
			
			<?php
		}
	}	 