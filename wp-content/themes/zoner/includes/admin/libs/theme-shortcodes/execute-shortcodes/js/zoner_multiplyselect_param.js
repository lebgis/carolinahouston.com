!function($) {
	
	if ($("select.multiplyselect").length > 0) {
		$("select.multiplyselect").each(function() {
			var instance = $(this);
			  $(instance).select2();
		});
	}	
	
	$('select.multiplyselect').select2().on("change", function(e) {
		var eVal = e.val;
		var vhInput = $(this).data('hiddenid');
		var vhInputElem = $("#"+vhInput);
		
		if (!eVal) {
			eVal = $(this).find('option:selected').val()
		} 
		vhInputElem.val(eVal.join(","));
	});
	
	
}(window.jQuery);