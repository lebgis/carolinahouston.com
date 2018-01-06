<?php
	global $zoner, 
		   $zoner_config, 
		   $author, 
		   $recipient, 
		   $recipient_id, 
		   $message;
	
	$res_path = $zoner->emails->zoner_get_email_resource_template_path();		
	if (!empty($zoner_config['logo']['url'])) 
		$logo_url = esc_url($zoner_config['logo']['url']);
	
	$bg_image_url 	= $res_path . 'email_bg.jpg';		
	$gltel = $glemail = '';
	
	if (!empty($zoner_config['header-phone'])) 		 $gltel 	  = $zoner_config['header-phone']; 
	if (!empty($zoner_config['header-email'])) 		 $glemail 	  = $zoner_config['header-email']; 
	if (!empty($zoner_config['emails-footer-text'])) $footer_text = nl2br($zoner_config['emails-footer-text']);
	
	$myMessagesLink = add_query_arg(array('profile-page' => 'my_messages'), get_author_posts_url($recipient_id));
	$rtl = is_rtl() ? 'dir="rtl"' : '';	
?>

<!DOCTYPE html>
<html>
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    <title><?php echo get_bloginfo( 'name' ); ?></title>
	<link href='http://fonts.googleapis.com/css?family=Roboto:400,100,100italic,300,300italic,400italic,500,500italic,700,700italic,900,900italic' rel='stylesheet' type='text/css'>
</head>
<body leftmargin="0" marginwidth="0" topmargin="0" marginheight="0" offset="0">
	<table <?php echo $rtl ?> align="cneter" width="100%" id="mainTable" class="mainTable" class="center"  border="0" cellspacing="0" cellpadding="0" style="background-color:#fff; padding:0; margin:0;font-family:'Roboto',sans-serif;font-size:14px; color:#2a2a2a;font-weight:normal;">
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="650" id="mainHeader" style="border-bottom:1px solid #d7d7d7;">
					<tr>
						<td align="left" style="padding:30px 0 30px 50px;">
							<a href="<?php echo esc_url(site_url()); ?>" title="<?php echo get_bloginfo( 'name' ); ?>"><img id="headerImage" src="<?php echo $logo_url; ?>"/></a>
						</td> 
						<td align="right" style="padding:30px 50px 30px 0;">
							<?php if (!empty($gltel)) { ?>
								<span class="tel" style="font-family:'Roboto',sans-serif; color:#999; font-size:12px; font-weight:bold;"><?php _e('Phone', 'zoner'); ?>: <?php echo $gltel; ?></span>
							<?php } ?>	
							
							<?php if (!empty($glemail)) { ?>
								<span class="tel" style="font-family:'Roboto',sans-serif; color:#999; font-size:12px; font-weight:bold;"><?php _e('E-mail', 'zoner'); ?>: <?php echo $glemail; ?></span>
							<?php } ?>	
							
						</td>	
					</tr> 
				</table>
			</td>
		</tr>
		
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="650" id="mainBody" style="background-image:url(<?php echo esc_url($bg_image_url); ?>);">
					<tr>
						<td style="padding:55px 50px;">
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php printf(__('Hello, %1s!', 'zoner'), $recipient); ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('You have received a new notification', 'zoner'); ?>:</strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><?php echo $message; ?></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><?php printf(__('From,  %1s!', 'zoner'), $author); ?></p>
						</td>
					</tr>
				</table>
			</td>
		</tr>
			
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="650" id="mainBody">
					<tr>
						<td colspan="2" style="padding:55px 50px 0 50px;">
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><a title="<?php _e('View All Messages', 'zoner'); ?>" href="<?php echo $myMessagesLink; ?>"><?php _e('View All Messages', 'zoner'); ?></a></strong></p> 
						</td>
					</tr>
				</table>
			</td>
		</tr>
		
		<tr>
			<td>
				<table border="0" cellpadding="0" cellspacing="0" width="650" id="mainFooter">
					<tr>
						<td align="left" style="padding:50px 50px;">
							<p style="font-family:'Roboto',sans-serif;font-size:14px; color:#2a2a2a;font-weight:normal;"><?php echo $footer_text; ?></p>						
						</td>
						<td align="right"></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>
</body>
</html>