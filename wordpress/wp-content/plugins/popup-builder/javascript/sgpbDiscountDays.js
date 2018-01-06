function sgpbDiscountDays() {

}

sgpbDiscountDays.prototype.init = function()
{
	if (!jQuery('.sg-dont-show-again-day11').length) {
		return false;
	}
	var that = this;

	jQuery('.sg-dont-show-again-day11').bind('click', function() {
		jQuery('.sg-info-panel-wrapper-day11').remove();
		var nonce = jQuery(this).attr('data-ajaxnonce');
		var discountDay = jQuery(this).attr('data-discount');

		var data = {
			action: 'discountDays',
			discountDay: discountDay,
			nonce: nonce
		};

		jQuery.post(ajaxurl, data, function(responce) {
			
		});
	});
};

jQuery(document).ready(function() {
	var obj = new sgpbDiscountDays();
	obj.init();
});