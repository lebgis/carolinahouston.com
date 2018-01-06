!function($) {
	$("#icons-type").bind("keydown change", function(){
		var eSel = $(this);
		setTimeout(function() {
			eSel.parent().find('.icon-preview > span').removeClass();
			eSel.parent().find('.icon-preview > span').addClass('fa ' + eSel.val());
		}, 0);
	});
	
	$("#icons-type").change();
}(window.jQuery);