<?php
	global $zoner, 
		   $zoner_config, 
		   $invNum, 
		   $userRole, 
		   $userEmail;
		   
	$gltel = $glemail = '';
		   
	$res_path = $zoner->emails->zoner_get_email_resource_template_path();		
	$bg_image_url = $res_path . 'email_bg.jpg';		
	
	if (!empty($zoner_config['logo']['url'])) 
	$logo_url = esc_url($zoner_config['logo']['url']);
	
	if (!empty($zoner_config['header-phone'])) 
	$gltel   = $zoner_config['header-phone']; 
	
	if (!empty($zoner_config['header-email'])) 
	$glemail = $zoner_config['header-email']; 
	
	if (!empty($zoner_config['emails-footer-text'])) 
	$footer_text = nl2br($zoner_config['emails-footer-text']);

	$invoice = array();
	$invoice = $zoner->membership->zoner_get_invoice_info_by_id($invNum);
	
	$payment_recurring = __('No', 'zoner');
	if ($invoice->payment_recurring == 'on') 
		$payment_recurring = __('Yes', 'zoner');
	
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
	<table <?php echo $rtl ?> align="cneter" width="100%" id="mainTable" class="mainTable" class="center"  border="0" cellspacing="0" cellpadding="0" style="background-color:#fff; padding:0; margin:0; font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;">
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
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php printf(__('Invoice #%1s!', 'zoner'), $invNum); ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Details of payment', 'zoner'); ?>: <?php echo $invoice->detail_of_payment_name; ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Payment recurring', 'zoner'); ?>: <?php echo $payment_recurring; ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Package ID', 'zoner'); ?>: <?php echo $invoice->package_id; ?></strong></p>
							<?php if(!empty($invoice->property_id)){?><p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Property ID', 'zoner'); ?>: <?php echo $invoice->property_id; ?></strong></p><?php } ?>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Payment price', 'zoner'); ?>: <?php echo $zoner->currency->get_zoner_property_price($invoice->payment_price, $invoice->payment_currency, 0, null, null, false); ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Payment currency', 'zoner'); ?>: <?php echo $invoice->payment_currency; ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('Purchase date', 'zoner'); ?>: <?php echo $invoice->purchase_date; ?></strong></p>
							<p style="font-family:'Roboto',sans-serif; font-size:14px; color:#2a2a2a; font-weight:normal;"><strong><?php _e('User ID', 'zoner'); ?>: <?php echo $invoice->user_id; ?></strong></p>
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