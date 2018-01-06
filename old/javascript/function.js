$('#showbox').click(function(){

	$("#popDiv").css("top","40px");
	
	//show the div
	$('#popDiv').slideDown(function(){
		
		document.body.addEventListener('click', boxCloser, false);
	});
});


// set initial div sizes by window size
windowWidth = $(window).width();
windowHeight = $(window).height();

//popDivHeight = windowHeight - 40; 
//document.getElementById('popDiv').style.height = popDivHeight+'px';


function boxCloser(e){
	if(e.target.id != 'popDiv' && e.target.id !="menu_examples" && e.target.id !="images/caret_blue.png" && e.target.id !="images/caret_white.png"){
		document.body.removeEventListener('click', boxCloser, false);
		$('#popDiv').slideUp();
		//$("#popDIv li").find('menu_cityfitmap').remove();
		//	$("#popDIv li").find('menu_webdesign').remove();
		$("#mobile_examples_menu").hide();
		example_menu_open = false;
	} else if(e.target.id =="menu_examples" && example_menu_open==true){
		document.body.removeEventListener('click', boxCloser, false);

		$("#mobile_examples_menu").slideUp();
	example_menu_open=false;
	}
}

function logout() {
	request = $.ajax({
        url: "../login/logout.php",
        type: "post",
        data: true,
    });
	request.done(function(){
		location.reload();
	});
}



(function($){
    $.fn.extend({
        center: function () {
            return this.each(function() {
                var top = ($("#cityrun").height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
                $(this).css({position:'absolute', margin:0, top:(top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);
(function($){
    $.fn.extend({
        leftAlign: function () {
            return this.each(function() {
                var top = ($("#cityrun").height() - $(this).outerHeight()) / 6;
                var left = ($(window).width() - $(this).outerWidth()) / 12;
                $(this).css({position:'absolute', margin:0, top:(top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);
(function($){
    $.fn.extend({
        centerMobile: function () {
            return this.each(function() {
                var top = ($("#cityrun").height() - $(this).outerHeight()) / 2;
                var left = ($(window).width() - $(this).outerWidth()) / 2;
				left=left+17;
                $(this).css({position:'absolute', margin:0, top:(top > 0 ? top : 0)+'px', left: (left > 0 ? left : 0)+'px'});
            });
        }
    }); 
})(jQuery);