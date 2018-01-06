function sgpbBlackFriday() {

}
sgpbBlackFriday.prototype.init = function()
{
	if (!jQuery('.sg-dont-show-agin').length) {
		return false;
	}
	var that = this;

	jQuery('.sg-dont-show-agin').bind('click', function() {
		var nonce = jQuery(this).attr('data-ajaxnonce');
		var discountDay = 1;/*jQuery(this).attr('data-discount');*/

		var data = {
			action: 'sgpbBlackFriday',
			nonce: nonce
		};

		jQuery('.sg-info-panel-wrapper').remove();
		jQuery.post(ajaxurl, data, function(responce) {
		});
	});
};

jQuery(document).ready(function() {
	var obj = new sgpbBlackFriday();
	obj.init();
});
