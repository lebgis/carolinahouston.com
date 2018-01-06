var mobileBrowser;
/*
if( /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent) ) {
	mobileBrowser=true;
}
//alert(mobileBrowser);
if (mobileBrowser==true){
	$("#mainmobile").show();
	$("maindesktop").hide();
} else {
	$("#mainmobile").hide();
	$("maindesktop").show();
}
*/
document.getElementById('buttons').style.display = 'none';
function login() {
	window.location = "https://cityfitmap.com/login/main_login.php";
}


function signup() {
	window.location = "https://cityfitmap.com/login/signup.php";
}

/////////////////////////////////////////////////////////////////////////////
//                          MOBILE RESPONSIVE                              //
//           position items depending on the size of the browser           //
/////////////////////////////////////////////////////////////////////////////

$(document).ready(function() {
	windowwidth = $(window).width();
	windowheight = $(window).height();
	
	////popDivHeight = windowheight - 40; 
	//document.getElementById('popDiv').style.height = popDivHeight+'px';
	if (windowwidth < 800){
		mobileBrowser=true;
	} else {
		mobileBrowser=false;
	}
	if (mobileBrowser==true){
		$("#maindesktop").hide();
		$("#menuTable").hide();
		$("#mainmobile").show();
		$("#menuImage").show();
		$("#cityrun img").css("width","800px");
	    $("#cityrun").css("width","100%");
		$("#cityrun").css("height","400");
		$("#cityrun").css("overflow","hidden");
		$(".slidermessage").centerMobile();
	} else {
		$("#mainmobile").hide();
		$("#menuImage").hide();
		$("#maindesktop").show();
		$("#menuTable").show();
		$(".slidermessage").leftAlign();
	}
	
	
	// onclick for examples menu list item
	$("#popDiv li").click(function() {
		$("#sub_background").hide();
		$("#examples_menu").hide();
		//alert("Clicked list." + $(this).html());
		if($(this).html() == "Examples"){
			window.location= "https://cityfitmap.com/cityfitmap";
		}
		
	});

	
});


function rotateCaret(){
	$("#example_caret_white").hide();
		$("#example_caret_blue").show();
		
		$('#example_caret_blue').rotate({
		  angle: 0,
		  animateTo:90
		  });
}

$("#menu_cityfitmap").click(function(){
	
	if (mobileBrowser==true){
		window.location= "https://cityfitmap.com/cityfitmap";
		$("#menu_cityfitmap").remove();
		$("#menu_webdesign").remove();
		example_menu_open = false;
		$("#mobile_examples_menu").slideUp();
	} else {
		window.location= "https://cityfitmap.com/cityfitmap";
	}
	
});
var example_menu_open = false;

$("#menu_examples, #examples_menu").mouseenter(function(){


	if(mobileBrowser==true){
		var pos = $(this).position();
		var outerHeight = $(this).outerHeight(true);
		var offset = $(this).offset().top;
		var height=$(this).height();

		height= height + outerHeight + 16;
		$("#mobile_examples_menu").removeClass("sub_menu");
		$("mobile_examples_menu").addClass("mobile_sub_menu");
		rotateCaret();

		if (example_menu_open==true){
			$("#mobile_examples_menu").slideUp();
		} else {
			$("#mobile_examples_menu").css("top",(pos.top+height));
			$("#mobile_examples_menu").slideDown();
			example_menu_open=true;
			/*$("#menu_cityfitmap").click(function(){
				window.location= "https://cityfitmap.com/cityfitmap";
				$("#menu_cityfitmap").remove();
				$("#menu_webdesign").remove();
				example_menu_open = false;
				$("#mobile_examples_menu").slideUp();
				
			});
			*/
		}
	}else{

		var position = $("#menu_examples").position();
		var height = $("#menu_examples").height();
		var menuwidth = $("#examples_menu").width();
		var width_offset=(menuwidth/2)-55;
		var height_offset=(height+6);
		
		$("#sub_background").css("top",(position.top+height_offset));
		$("#examples_menu").css("left",(position.left-width_offset));
		$("#examples_menu").css("top",(position.top+height_offset));
		
		
		
		
		$("#sub_background").show();
		$("#examples_menu").show();
	}
}).mouseleave(function(){

	$("#sub_background").hide();
	$("#examples_menu").hide();

	
});

//adjust sizes on window resize
$(window).resize(function() {
	windowwidth = $(window).width();
	windowheight = $(window).height();
	
	//popDivHeight = windowheight - 40; 
	//document.getElementById('popDiv').style.height = popDivHeight+'px';
	if (windowwidth < 800){
		mobileBrowser=true;
	} else {
		mobileBrowser=false;
	}
	if (mobileBrowser==true){
		$("#maindesktop").hide();
		$("#menuTable").hide();
		$("#mainmobile").show();
		$("#menuImage").show();
		$(".slidermessage").centerMobile();
	} else {
		$("#mainmobile").hide();
		$("#menuImage").hide();
		$("#maindesktop").show();
		$("#menuTable").show();
		$(".slidermessage").leftAlign();
	}
	
});