////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// jQuery
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
var Dropdown=function(){function h(a){for(var b=!1,d=0;d<e.length;d++)e[d].isOpen&&(b=!0);if(b){for(a=a.target;null!=a;){if(/\bdropdown\b/.test(a.className))return;a=a.parentNode}f()}}function f(a){for(var b=0;b<e.length;b++)e[b]!=a&&e[b].close()}function g(a){"string"==typeof a&&(a=document.getElementById(a));e.push(new c(a))}function c(a){this.node=a;a.className+=" dropdownJavaScript";"addEventListener"in a?(a.addEventListener("mouseover",this.bind(this.handleMouseOver),!1),a.addEventListener("mouseout",this.bind(this.handleMouseOut),!1),a.addEventListener("click",this.bind(this.handleClick),!1)):(a.attachEvent("onmouseover",this.bind(this.handleMouseOver)),a.attachEvent("onmouseout",this.bind(this.handleMouseOut)),a.attachEvent("onclick",this.bind(this.handleClick)));"createTouch"in document&&a.addEventListener("touchstart",this.bind(this.handleClick),!1)}var e=[];c.prototype.isOpen=!1;c.prototype.timeout=null;c.prototype.bind=function(a){var b=this;return function(){a.apply(b,arguments)}};c.prototype.handleMouseOver=function(a,b){this.clearTimeout();var d="target"in a?a.target:a.srcElement;for(;"LI"!=d.nodeName&&d!=this.node;)d=d.parentNode;"LI"==d.nodeName&&(this.toOpen=d,this.timeout=window.setTimeout(this.bind(this.open),b?0:250))};c.prototype.handleMouseOut=function(){this.clearTimeout();this.timeout=window.setTimeout(this.bind(this.close),250)};c.prototype.handleClick=function(a){f(this);var b="target"in a?a.target:a.srcElement;for(;"LI"!=b.nodeName&&b!=this.node;)b=b.parentNode;"LI"==b.nodeName&&0<this.getChildrenByTagName(b,"UL").length&&!/\bdropdownOpen\b/.test(b.className)&&(this.handleMouseOver(a,!0),"preventDefault"in a?a.preventDefault():a.returnValue=!1)};c.prototype.clearTimeout=function(){this.timeout&&(window.clearTimeout(this.timeout),this.timeout=null)};c.prototype.open=function(){this.isOpen=!0;for(var a=this.getChildrenByTagName(this.toOpen.parentNode,"LI"),b=0;b<a.length;b++){var d=this.getChildrenByTagName(a[b],"UL");if(0<d.length)if(a[b]!=this.toOpen)a[b].className=a[b].className.replace(/\bdropdownOpen\b/g,""),this.close(a[b]);else if(!/\bdropdownOpen\b/.test(a[b].className)){a[b].className+=" dropdownOpen";for(var c=0,e=d[0];e;)c+=e.offsetLeft,e=e.offsetParent;right=c+d[0].offsetWidth;0>c&&(a[b].className+=" dropdownLeftToRight");right>document.body.clientWidth&&(a[b].className+=" dropdownRightToLeft")}}};c.prototype.close=function(a){a||(this.isOpen=!1,a=this.node);a=a.getElementsByTagName("li");for(var b=0;b<a.length;b++)a[b].className=a[b].className.replace(/\bdropdownOpen\b/g,"")};c.prototype.getChildrenByTagName=function(a,b){for(var d=[],c=0;c<a.childNodes.length;c++)a.childNodes[c].nodeName==b&&d.push(a.childNodes[c]);return d};return{initialise:function(){"createTouch"in document&&document.body.addEventListener("touchstart",h,!1);for(var a=document.querySelectorAll("ul.dropdown"),b=0;b<a.length;b++)g(a[b])},applyTo:g}}();
var $ = jQuery.noConflict();

var ajaxurl   		= ZonerGlobal.ajaxurl;
var zoner_domain  	= ZonerGlobal.domain; 	
var is_general_page = ZonerGlobal.is_general_page;
var is_agency_page 	= ZonerGlobal.is_agency_page;
var is_agent_page 	= ZonerGlobal.is_agent_page;
var is_rtl  		= ZonerGlobal.is_rtl;
var is_mobile  		= ZonerGlobal.is_mobile;

if (is_rtl == 1) {
	is_rtl = true;
} else {
	is_rtl = false;
}

var start_lat = ZonerGlobal.start_lat;
var start_lng = ZonerGlobal.start_lng;
var locations = ZonerGlobal.locations;
var maps_zoom = parseInt(ZonerGlobal.maps_zoom);
var map_type  = parseInt(ZonerGlobal.map_type);
var source_path = ZonerGlobal.source_path;
var zoner_ajax_nonce = ZonerGlobal.zoner_ajax_nonce;
var icon_marker = ZonerGlobal.icon_marker;
var default_currency = ZonerGlobal.default_currency;

var min_price = parseInt(ZonerGlobal.min_price);
var max_price = parseInt(ZonerGlobal.max_price);
var header_variations = ZonerGlobal.header_variations;

var zoner_message_send_text = ZonerGlobal.zoner_message_send_text;
var zoner_message_faq_text  = ZonerGlobal.zoner_message_faq_text;
var zoner_default_compare_text  = ZonerGlobal.zoner_default_compare_text;

var	pl_text_property = ZonerGlobal.zoner_pl_img_text_property;
var pl_text_featured = ZonerGlobal.zoner_pl_img_text_featured;
var pl_text_logo = ZonerGlobal.zoner_pl_img_text_logo;

var zoner_stripe_message_1 = ZonerGlobal.zoner_stripe_message_1;
	
$(document).ready(function($) {
    "use strict";
	if( $('html').attr('dir') == 'rtl' ){
		$('[data-vc-full-width="true"]').each( function(i,v){
			$(this).css('right' , $(this).css('left') ).css( 'left' , 'auto');
		});
	}
	if ($('#searchform').length > 0) {
		$('#searchform button').on('click', function() {
			$('#searchform').submit();
		});
	}
	if ($('.file-custom').length){
		var $el = $('.file-custom'), initPlugin = function() {
        $el.fileinput({
            uploadUrl: '/file-cache',
            //dropZoneEnabled: true
		}).on('change', function(event) {
			$(this).parent().parent().find('.file-preview-frame').hide();
		}).on('fileloaded', function(event, file, previewId, index, reader) {
    		$(this).parent().parent().find('.fileinput-remove').show();
		}).on('fileclear', function(event, file, previewId, index, reader) {
    		$(this).parent().parent().find('.fileinput-remove').hide();
		});
   	 };
    // initialize plugin
    initPlugin();	
	}
	
	/*Message System*/
	
	if ($('a.notifications').length > 0) {
		$(document).on('heartbeat-tick', function(e, data) {			
			var vNotifications = data['notifications'] + 0;
			if (vNotifications > 0) {
				$('a.notifications').find('strong').text(vNotifications);
				$('a.notifications').addClass('active');
			} else {
				$('a.notifications').removeClass('active');
				$('a.notifications').find('strong').text('0');
			}			
		});
		
		$(document).on('heartbeat-send', function(e, data) {
			data['is_notification_update'] = '1';
		});
	}	
		
	if ($('#list-messages').length > 0) {
		$('#list-messages').perfectScrollbar();
	}
	
	
	if ($('#chat-wnd').length > 0) {
		$('#chat-wnd').on('click', function() {
			$("#startChatWnd").modal('show');
			return false;
		});
	}
	
	$("#chatMessage").on('keydown', function(e) {
		if (e.keyCode == 13) {
			if (!e.shiftKey) {
				$('#sendchatMessage').click();	
				return false;
			}
		}
	});
		
	$('#sendchatMessage').on('click', function() {
		var vMsgObj   = $("#chatMessage");
		var vMessage  = vMsgObj.val(); 
		var vAuthorID = vMsgObj.data('authorid');
		var vData = { action : 'create_new_conversation', 
				chatMessage : vMessage, 
				authorID	: vAuthorID,
				   security : zoner_ajax_nonce 
			};
		
		if (vMessage == '')	 {
			$("#chatMessage").addClass('error');
		} else {
			$("#chatMessage").removeClass('error');
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				async: false,
				data: vData,
				dataType: 'html',
				success: function (messages) {
					$("#startChatWnd").find('textarea').val('');
					$("#startChatWnd").modal('hide');	
				}
			});
		}
			
			
			return false;
	});
	
			
	function openMsg() {
		var vMsg      = $(this);
		var vConvId   = vMsg.data('convid');
		var vListMsg  = vMsg.closest('.list-messages');
		var vRollSpin = vMsg.find('.roll-spin');
			vRollSpin.fadeIn();
		
			$('#zoner-messages .list-messages li').unbind('click', openMsg);
			var vData = { action : 'get_all_msg', 
				 conv_id : vConvId,
					security : zoner_ajax_nonce 
					   
			};
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				async: true,
				data: vData,
				dataType: 'html',
				success: function (messages) {
					vListMsg.stop().animate({'marginLeft' : '-=1000px'}).hide()
					vRollSpin.fadeOut();
					
					if (messages) {
						$('#zoner-messages .list-messages-wrapper .chat-message').append(messages);
						$('#zoner-messages .list-messages-wrapper .chat-message').fadeIn();
						$('#zoner-messages .list-messages-wrapper .chat-messages').perfectScrollbar();
						$('#zoner-messages .list-messages-wrapper .chat-messages').scrollTop($('#zoner-messages .list-messages-wrapper .chat-messages')[0].scrollHeight);
						
					}
					
					$('#zoner-messages .list-messages li').bind('click', openMsg);
					
				},
				error: function (errorThrown) {}
			});
		return false;
	}
	$('#zoner-messages .list-messages li a').on('click', function(e){
		e.stopPropagation();
	});
	if ($('#zoner-messages .list-messages li').length > 0) {
		$('#zoner-messages .list-messages li').on('click', openMsg);
	}
	
	$('.del-conv').on('click', function(e) {
		e.preventDefault();
		e.stopPropagation()
		var vConv   = $(this).closest('.message');
		var vConvId = vConv.data('convid');
			
		$("#lmDeleteConverstionWnd").modal('show');
		$('#deleteConversationAct').live('click', function () {
		var vData = { action : 'delete_conversation', 
					 conv_id : vConvId,
					security : zoner_ajax_nonce 
			};
				
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				async: true,
				data: vData,
				success: function (messages) {
					$("#lmDeleteConverstionWnd").modal('hide');
					vConv.stop().fadeOut(function() {
						$(this).remove();
					});
				
				},
				error: function (errorThrown) {
				}
			});
			return false;
		});
		return false;
	});
		
	$('.back-btn').live('click', function(e) {
		e.preventDefault();
		e.stopPropagation();
		var vWrapper = $(this).closest('.list-messages-wrapper');
			$('#zoner-messages .list-messages-wrapper .chat-message').fadeOut();
			vWrapper.find('.list-messages').stop().animate({'marginLeft' : '0'}).show();
			vWrapper.find('.chat-messages').remove();
			vWrapper.find('#form-reply').remove();
		return false;
	});

	var sent_once = 0;
	$('#form-reply').live('submit', function() {
		var vFormData = $(this).serializeArray();
		var outArr = {};
		
		 $.each(vFormData, function() {
			if (outArr[this.name] !== undefined) {
				if (!outArr[this.name].push) {
					 outArr[this.name] = [outArr[this.name]];
				}
				outArr[this.name].push(this.value || '');
			} else {
				outArr[this.name] = this.value || '';
			}
		});

		var vConvId  = outArr['conv_id'];
		var vMessage = outArr['type-message'];
		 
		
		var vData = { action 	 : 'new_chat_message', 
					 conv_id 	 : vConvId,
					 chatMessage : vMessage, 
					    security : zoner_ajax_nonce 
				};
		 $.ajax({
			type: 'POST',
			url: ajaxurl,
			async: true,
			data: vData,
			dataType: 'html',
			beforeSend: function(xhr){
				if (sent_once) {
					xhr.abort();
				}else{
					sent_once = 1;
				}
			},
			success: function (message) {
				sent_once = 0;
				$('#zoner-messages .list-messages-wrapper .chat-messages').append(message);
				$('#form-reply').find('textarea').val('');
				$('#zoner-messages .list-messages-wrapper .chat-messages').scrollTop($('#zoner-messages .list-messages-wrapper .chat-messages')[0].scrollHeight);
			},
			error: function (errorThrown) {
			}
		});
		return false;
	});
	
	

	/*Currency calculator*/
	
	if ($('.form-currency-calculator').length  > 0) {
		$('.form-currency-calculator').on('submit', function() {
			var vForm = $(this);
			
			var data = { action: 'zoner_currency_calculate', 
						 fCurrency : vForm.find('.currency-from option:selected').val(),
						 tCurrency : vForm.find('.currency-to option:selected').val(),
						 amount :    parseFloat(vForm.find('.currency-amount').val())
					    };
			var outRes = vForm.find('.out-results');
				outRes.html('');			
						 
			$.post(ajaxurl, data,  function(calcResult) {
				var inCurrency = $.parseJSON(calcResult);
				var outHtml = '';
					
				
				if (inCurrency) {
					
					var vTo   = inCurrency['to'];
					var vfrom = inCurrency['from'];
					var vV 	  = inCurrency['v'];
					var vRate = inCurrency['amount'];
					
					outHtml += '<span class="tag price">'	+ parseFloat(vForm.find('.currency-amount').val()) + ' ' + vfrom + '</span>';
					outHtml += '<span class="exchange"><strong><i class="fa fa-exchange"></i></strong></span>';
					outHtml += '<span class="tag price">'	+ vV + ' ' + vTo + '</span>';
					
					outRes.html(outHtml);
				}	
			});
			return false;	
		});
	}
	
	/*Change Currency*/
	
	if ($('#submit-currency').length > 0) {
		$('#submit-currency').on('change', function() {
			var curr_val = $('#submit-currency option:selected').text();
			var pos_str  = curr_val.indexOf(' ');

			if($('#submit-price').length > 0) {
		       $('#submit-price').parent().find('span.input-group-addon').html(curr_val.substr(0,pos_str));
			}
		});
	}
	
	
	
	
	/* navigation height fix */
	$('.navigation .navbar-nav  ul.child-navigation  .has-child  .child-navigation').each(function() {
	    var child_nav_offset = $(this).offset();
	    var child_nav_height = $(this).height() + child_nav_offset.top;
	    var wrapper_height = $('.wrapper').height();
	        if (child_nav_height > wrapper_height) {
	            $(this).css({'overflow-y':'auto', 'max-height':'400px'});
	        }
	})
	
	/*Faq's votes*/
	if ($('.answer-votes').length > 0) {
		$('.answer-votes a.faq-help-yes').on('click', function() {
			var elem = $(this);
			var	faq_id = elem.data('faqid');
			var data = { action: 'zoner_helpful_faq', 
						 faq_id : faq_id,
						 choose : 'yes'};
			
			$.post(ajaxurl, data,  function(response) {
				$.jGrowl(zoner_message_faq_text, { position : "bottom-left" });
				elem.parent().fadeOut('slow', function() {
					$(this).remove();
				});
			});
			return false;
		});
		
		
		$('.answer-votes a.faq-help-no').on('click', function() {
			var elem = $(this);
			var	faq_id = elem.data('faqid');
			var data = { action: 'zoner_helpful_faq', 
						 faq_id : faq_id,
						 choose : 'no' };
			
			$.post(ajaxurl, data,  function(response) {
				$.jGrowl(zoner_message_faq_text, { position : "bottom-left" });
				elem.parent().fadeOut('slow', function() {
					$(this).remove();
				});
			});
			
			return false;
		});
	}
	
	if ($('.blog-post .meta .tags').length > 0) {
		$('.blog-post .meta .tags').each(function() {
			
			  if ($(this).outerHeight() > 26) {
				   $(this).css({'margin-top':'10px'});
				   $(this).css({'margin-bottom':'10px'});
				   $(this).find('a').css({'margin-bottom':'10px'});
			  }
		});
	}
	/*Delete agency*/
	if ($('.delete-agency').length > 0) {
		var agency_id = '';
		
		$('.delete-agency').on('click', function() {
			$("#lmDeleteAgencyWnd").modal('show');
			agency_id = $(this).data('agencyid');			
			return false;
		});
		
		if ($('#deleteAgencyAct').length > 0) {
			$('#deleteAgencyAct').on('click', function() {
			var data = { action: 'delete_agency_act', agencyID : agency_id, security : zoner_ajax_nonce };
			$.post(ajaxurl, data,  function(response) {
				$("#lmDeleteAgencyWnd").modal('hide');
				location.reload();
			});
				return false;
			});
		}
	}
	
	/*Delete Property*/
	if ($('.delete-property').length > 0) {
		
		var property_id = '';
		
		$('.delete-property').on('click', function() {
			$("#lmDeletePropertyWnd").modal('show');
			property_id = $(this).data('propertyid');
			return false;
		});
		
		
		if ($('#deletePropertyAct').length > 0) {
			$('#deletePropertyAct').on('click', function() {
			var data = { action: 'delete_property_act', property_id : property_id, security : zoner_ajax_nonce };
			$.post(ajaxurl, data,  function(response) {
				$("#lmDeletePropertyWnd").modal('hide');
				location.reload();
			});
				return false;
			});
		}
	}
	
	/*Delete Property*/
	if ($('.delete-invite-agent').length > 0) {
		var invite_id = '';
		
		$('.delete-invite-agent').on('click', function() {
			invite_id = $(this).data('inviteid');			
			$("#lmDeleteinviteWnd").modal('show');
			return false;
		});
		
		
		if ($('#deleteInviteAgentAct').length > 0) {
			$('#deleteInviteAgentAct').on('click', function() {
			
			var data = { 
				action: 'delete_invite_agent', 
				invite_id : invite_id, 
				security  : zoner_ajax_nonce 
			};
				
			$.post(ajaxurl, data,  function(response) {
				$("#deleteInviteAgentAct").modal('hide');
				location.reload();
			});
				return false;
			});
		}
	}
	
	
	/*Profile - remove profile avatar*/
	$('#profile .avatar-wrapper .remove-btn').on('click', function() {
		$(this).parent().remove();
		$('#form-account-avatar').val('');
		$('#form-account-avatar-id').val('');
		
		return false;
	});

	function readURL(input, img) {
		if (input.files && input.files[0]) {
			var reader = new FileReader();
			reader.onload = function (e) {
				$(img).attr('src', e.target.result);
			}
			reader.readAsDataURL(input.files[0]);
		}
	}
	

	if ($('.file-inputs').length > 0) {
		$('.file-inputs').bootstrapFileInput();
	}	
	
	$('#form-account-avatar-file').change (function() {
		readURL(this, '#avatar-image');
	});
	$('#property-featured-image').change (function() {
		readURL(this, '#prop-featured-image');
	});
	
	
	$('#agency-featured-image-file').change (function() {
		readURL(this, '#agency-featured-image');
	});
	
	$('#agency-logo-image-file').change (function() {
		readURL(this, '#agency-logo-image');
	});
	
	
	if ($('.remove-agency-featured').length > 0)  {
		$('.remove-agency-featured').on('click', function() {
		
		$('#agency-featured-image-exists').val('');
		$(this).parent().find('img').remove();
		$(this).parent().append('<img id="agency-featured-image" class="img-responsive" data-src="galleryFrontEnd.holder/200x200?text=' + pl_text_featured + '"/>');
		
		Holder.run({ domain: "galleryFrontEnd.holder", renderer: "canvas" });
		return false;
		});

	}
	
	if ($('#form-account-password').length > 0) {
		$('#form-account-password').validate({});		
		
		$('#form-account-password-current').rules( "add", {
			required: true,
			minlength : 6,
			remote: {
				url: ajaxurl,
				type: "post",
				data: {
					action: 'zoner_check_user_password'
				}
			}
		});
		
		$( "#form-account-password-new" ).rules( "add", {
			minlength : 6,
			required: true,
		});

		$( "#form-account-password-confirm-new" ).rules( "add", {
			required: true,
			minlength: 6,
			equalTo : "#form-account-password-new"
		});
	}	
	
	
	if ($('.remove-agency-logo').length > 0)  {
		$('.remove-agency-logo').on('click', function() {
		
		$('#agency-logo-image-exists').val('');
		$(this).parent().find('img').remove();
		$(this).parent().append('<img id="agency-logo-image" class="img-responsive" data-src="galleryFrontEnd.holder/200x200?text=' + pl_text_logo +'"/>');
		
		Holder.run({ domain: "galleryFrontEnd.holder", renderer: "canvas" });
		return false;
		});

	}


	$('.zoner-property-sort' ).on( 'change', function() {
		var selectedOptionValue = $(this).val();
		var select_name = $(this).attr('name');
		doRedirectPageReset(select_name, selectedOptionValue);
	});
	
	
	if ($('.remove-prop-featured').length > 0)  {
		$('.remove-prop-featured').on('click', function() {
		
		$('#prop-featured-image-exists').val('');
		$(this).parent().find('img').remove();
		$(this).parent().append('<img width="100%"  id="prop-featured-image" class="img-responsive" src="galleryFrontEnd.holder/410x410?text=' + pl_text_featured + '"/>');
		
		Holder.run({ domain: "galleryFrontEnd.holder", renderer: "canvas" });
		return false;
		});

	}
	
    equalHeight('.equal-height');

    $('.nav > li > ul li > ul').css('left', $('.nav > li > ul').width());

    var navigationLi = $('.nav > li');
    navigationLi.hover(function() {
        if ($('body').hasClass('navigation-fixed-bottom')){
            if ($(window).width() > 768) {
                var spaceUnderNavigation = $(window).height() - ($(this).offset().top - $(window).scrollTop());
                if(spaceUnderNavigation < $(this).children('.child-navigation').height()){
                    $(this).children('.child-navigation').addClass('position-bottom');
                } else {
                    $(this).children('.child-navigation').removeClass('position-bottom');
                }
            }
        }
    });

    setNavigationPosition();

    $('.tool-tip').tooltip();

    var select = $('.form-submit select, .form-sort select, .form-search select, .form-currency-calculator select');
    if (select.length > 0 ) {
        select.selectpicker({size: 10, style:''});
    }

    var bootstrapSelect = $('.bootstrap-select');
    var dropDownMenu = $('.dropdown-menu');

    bootstrapSelect.on('shown.bs.dropdown', function () {
        dropDownMenu.removeClass('animation-fade-out');
        dropDownMenu.addClass('animation-fade-in');
    });

    bootstrapSelect.on('hide.bs.dropdown', function () {
        dropDownMenu.removeClass('animation-fade-in');
        dropDownMenu.addClass('animation-fade-out');
    });

    bootstrapSelect.on('hidden.bs.dropdown', function () {
        var _this = $(this);
        $(_this).addClass('open');
        setTimeout(function() {
            $(_this).removeClass('open');
        }, 100);
    });

    select.change(function() {
        if ($(this).val() != '') {
            $('.form-search .bootstrap-select.open').addClass('selected-option-check');
        }else {
            $('.form-search  .bootstrap-select.open').removeClass('selected-option-check');
        }
    });

//  Contact form
	
	if ($(".mail-form-sending").length > 0) {
		$(".mail-form-sending").validate({
            submitHandler: function(form) {
                var data = '';
					data = { action: 'zoner_mail_form_sending', formData : $(form).serialize() };
				$.post(ajaxurl, data,  function(response) {
					if (response > 0) {
						$.jGrowl(zoner_message_send_text, { position : "bottom-left" });
						$(form).find('input, textarea').val('');
					}
                });
                return false;
            }
        });
	
	}
	
	
	if ($('.tool-tip-info').length > 0) {
		$('.tool-tip-info').tooltip({
			placement : 'bottom'
		});
	}
	

	
//  Price slider
    var $priceSlider = $(".price-input");
	 if($priceSlider.length > 0) {
		$priceSlider.each(function() {
			$(this).slider({
				from: min_price,
				to:   max_price,
				smooth: true, 
				round: 0,		
				format: { format: "###,###", locale: 'en' },
				dimension : "&nbsp;" + default_currency
			});
		});
    }

//  Parallax scrolling and fixed header after scroll

    $('#map .marker-style').css('opacity', '.5 !important');
    $('#map .marker-style').css('bakground-color', 'red');

    $(window).scroll(function () {
        var scrollAmount = $(window).scrollTop() / 1.5;
        scrollAmount = Math.round(scrollAmount);
        if ( $("body").hasClass("navigation-fixed-bottom") ) {
            if ($(window).scrollTop() > $(window).height() - $('.navigation').height() ) {
                $('.navigation').addClass('navigation-fix-to-top');
            } else {
                $('.navigation').removeClass('navigation-fix-to-top');
            }
        }

        if ($(window).width() > 768) {
            if($('#map').hasClass('has-parallax')){
                $('#map .gm-style').css('margin-top', scrollAmount + 'px');
                $('#map .leaflet-map-pane').css('margin-top', scrollAmount + 'px');
            }
            if($('#slider').hasClass('has-parallax')){
                $(".homepage-slider").css('top', scrollAmount + 'px');
            }
        }
    });


//  Smooth Navigation Scrolling
		
	
	
	if (is_mobile == 1) {
		var vMainMenuID = $('.navigation .nav.navbar-nav').attr('id');
		if (vMainMenuID)
			Dropdown.applyTo(vMainMenuID);
	}
	
	
    $('.navigation .nav a[href^="#"], a[href^="#"].roll').on('click',function (e) {
        e.preventDefault();
        var target  = this.hash,
            $target = $(target);
        if (target.length > 1) {
			if ($(window).width() > 768) {
				$('html, body').stop().animate({ 'scrollTop': $target.offset().top - $('.navigation').height()}, 2000)
			} else {
				$('html, body').stop().animate({ 'scrollTop': $target.offset().top}, 2000)
			}
		}	
    });

//  Rating

    var ratingOverall = $('.rating-overall');
    if (ratingOverall.length > 0) {
        ratingOverall.raty({
            path: source_path + '/img',
            readOnly    : true,
			half        : true, // Enables half star selection.
			halfShow    : true,   

            score: function() {
                return $(this).attr('data-score');
            }
        });
    }
	
    var ratingIndividual = $('.rating-individual');
    if (ratingIndividual.length > 0) {
        ratingIndividual.raty({
            path: source_path + '/img',
            readOnly: true,
            score: function() {
                return $(this).attr('data-score');
            }
        });
    }
		
    var ratingUser = $('.rating-user');
    if (ratingUser.length > 0) {
		
        $('.rating-user .inner').raty({
            path: source_path + '/img',
            starOff :  'big-star-off.png',
            starOn  :  'big-star-on.png',
            width: 160,
            //target : '#hint',
            targetType : 'number',
            targetFormat : 'Rating: {score}',
            click: function(score, evt) {
                showRatingForm();
				if ($('#form-rating').length > 0) {
					$('#form-rating input#form-rating-score').val(score);
				
				}
            }
        });
    }

//  Agent State

    $('#agent-switch').on('ifClicked', function(event) {
        agentState();
    });

    $('#create-account-user').on('ifClicked', function(event) {
        $('#agent-switch').data('agent-state', '');
        agentState();
    });

// Print property page
	var printButton = $(".print-page");

	printButton.on('click', function(event) {
        event.preventDefault();
		
		var property_id   = $(this).data('propertyid');
		var printWndTitle = $(this).closest('.property-title').find('h1').text();
        var printWnd      = window.open('',printWndTitle,'width=600, height=800');
		
        $.ajax({    
            type: 'POST',
            url: ajaxurl, 
			data: {
				'action' :  'zoner_print_property',
				'property_id' :   property_id, 
			},
            success:function(data) {  
				printWnd.document.write(data); 
				printWnd.document.close();
                printWnd.focus();
            },
            error: function(errorThrown){ }

        });
    });
	
// Set Bookmark button attribute

  var bookmarkButtons = $(".bookmark");
  var bookmarkButton;

  bookmarkButtons.on("click", function() {
		bookmarkButton = $(this);
		var is_choose = 0;
		var	property_id = bookmarkButton.data('propertyid');
		bookmarkButton.toggleClass('bookmark-added');
		if ( bookmarkButton.hasClass('bookmark-added') ) {
			is_choose = 1;
		}
		var data = { action: 'add_user_bookmark', property_id : property_id, is_choose : is_choose };
		$.post(ajaxurl, data,  function(response) {
			/*after insert*/
		});
		return false;
  });
	
	var compareButtons = $(".compare");
	var compareButton;

  compareButtons.on("click", function() {
		compareButton = $(this);
		var is_choose = 0;
		var	property_id = compareButton.data('propertyid');
		compareButton.toggleClass('compare-added');
		if ( compareButton.hasClass('compare-added') ) {
			is_choose = 1;
		}
		var data = { action: 'add_user_compare', property_id : property_id, is_choose : is_choose };
		$.post(ajaxurl, data,  function(response) {
			if (response) {
				$('.add-your-compare').addClass('active');
				$('.add-your-compare a span.text').text(response);
			} else {
				$('.add-your-compare').removeClass('active');
				$('.add-your-compare a span.text').text(zoner_default_compare_text);
			}
		});
		
		return false;
  });
	
	if ($('.remove-compare-property').length > 0) {
		$('.remove-compare-property').on('click', function() {
			var vPropId = $(this).data('propertyid');
			var vTdIndx = $(this).parent().index() + 1;
			var data = { action: 'remove_item_from_cl', property_id : vPropId, is_choose : 0 };
			
			$.post(ajaxurl, data,  function(response) {
				$(".compare-list table th").remove(":nth-child(" + vTdIndx + ")");
				$(".compare-list table td").remove(":nth-child(" + vTdIndx + ")");
				
				if (response) {
					$('.add-your-compare').addClass('active');
					$('.add-your-compare a span.text').text(response);
				} else {
					$('.add-your-compare').removeClass('active');
					$('.add-your-compare a span.text').text(zoner_default_compare_text);
					window.location.reload();
				}
			});
		
			return false;
		});
	}
	
	if ($('body').hasClass('navigation-fixed-bottom')){
        var admin_bar_h = 0;
		
		if ($('#wpadminbar').length > 0) {
			admin_bar_h = $('#wpadminbar').outerHeight();
			$('#page-content').css('padding-top', $('.navigation').height() - admin_bar_h);
		} else {
			$('#page-content').css('padding-top',$('.navigation').height());
		}    
  }
	setNavigationOffset();

//  Masonry grid listing

    if($('.property').hasClass('masonry')) {
	
        var container = $('.properties.masonry .grid');
        
		container.imagesLoaded( function() {
			if( $('html').attr('dir') == 'rtl' ){
				container.masonry ({
					gutter: 15,
					isOriginLeft: false,
					itemSelector: '.property.masonry'
				});
			} else {
				container.masonry ({
					gutter: 15,
					itemSelector: '.property.masonry'
				});
			}
			if ($('.properties.masonry').hasClass('masonry-loaded')) {
				$('.properties.masonry').addClass('loaded');
				setTimeout(function(){ $('.properties.masonry').removeClass('masonry-loaded'); }, 1000);
			}
			
        });
		
		if ($(window).width() > 991) {
			$('.property.masonry').hover(function() {
                    $('.property.masonry').each(function () {
                        $('.property.masonry').addClass('masonry-hide-other');
                        $(this).removeClass('masonry-show');
                    });
                    $(this).addClass('masonry-show');
                }, function() {
                    $('.property.masonry').each(function () {
                        $('.property.masonry').removeClass('masonry-hide-other');
                    });
                }
            );

            var config = {
					after: '0s',
					enter: 'bottom',
					move: '20px',
					over: '.5s',
					easing: 'ease-out',
					viewportFactor: 0.33,
					reset: false,
					init: true
				};
				
            window.scrollReveal = new scrollReveal(config);
        }
    }

//  Magnific Popup

    var imagePopup = $('.image-popup');
    if (imagePopup.length > 0) {
        imagePopup.magnificPopup({
            type:'image',
            removalDelay: 300,
            mainClass: 'mfp-fade',
            overflowY: 'scroll',
            gallery:{enabled:true}
        });
    }
	
	if ($('.zoner-gallery-shortcode').length > 0) {
		$('.zoner-gallery-shortcode').each(function() {
		
			$(this).magnificPopup({
				delegate: 'a.thumbnail',
				type:'image',
				removalDelay: 300,
				mainClass: 'mfp-fade',
				overflowY: 'scroll', 
				gallery:{enabled:true}
			});
		});
		
	
	}

//  iCheck

    if ($('.checkbox').length > 0) {
        $('input').iCheck();
    }

    if ($('.radio').length > 0) {
        $('input').iCheck();
    }

//  Pricing Tables in Submit page

    if($('.submit-pricing').length >0 ){
        $('.buttons .btn').click(function() {
                $('.submit-pricing .buttons td').each(function () {
                    $(this).removeClass('package-selected');
                });
                $(this).parent().css('opacity','1');
                $(this).parent().addClass('package-selected');

            }
        );
    }

//Build header map params
    if ($('#map').length > 0) {
        if (header_variations>=1 && header_variations<=5) {
			if (ZonerGlobal.gm_or_osm == 0) {
				createHomepageGoogleMap(start_lat, start_lng, locations, source_path);
			} else {
				createHomepageOSM(start_lat, start_lng, locations, source_path);
			}
		}
    }

//Ajax Map Params

	// if ($('#form-map').length > 0) {
		// $('#form-map').on('submit', function() {
			// var formData = $(this).serialize();
			
			// $.ajax({
				// type: 'POST',
				// url: ajaxurl,
				// data: {
					// 'action'        : 'zoner_ajax_map_search',
					// 'formData'		: formData
					// 'security' 		: zoner_ajax_nonce
				// },
				// success: function (data) {
					// try {
						
					// } catch (e) {
						// location.reload();
					// }
				// },
				// error: function (errorThrown) {}
			// });
			
			
			// return false;
		// });
	// }
	
//Centered Search box
	centerSearchBox();
	
	
	if(	jQuery('.dsidx-results .dsidx-prop-summary').length > 0) {
		jQuery('.dsidx-results .dsidx-prop-summary').each(function() {
			var vElem = jQuery(this).find('.dsidx-prop-title').next();
				vElem.addClass('dsidx-prop-thumbnail');
		});
	}
	
	if(	jQuery('.dsidx-prop-title b').length > 0) {
		var str = jQuery('.dsidx-prop-title b');
		str.each(function() {
			var inStr = jQuery(this).html();
			var indx = inStr.indexOf(':');
  
			var selected_text = jQuery(this).html().substring(0, indx-1);
			jQuery(this).html(jQuery(this).html().substring(indx + 1) + '<div class="dsidx-price">' + jQuery.trim(selected_text) + '</div>');
  
		});
	}
	
	/*Memberhip*/
	/*Calc total price*/
	
	if ($('.is_featured_submit').length > 0) {

		$('.is_featured_submit').on('ifChecked', function(event){
			var check_elem  = $(this).closest( ".price-info" ).find('.total-price');
			var data = { action: 'get_total_price', check: 1 };
			$.post(ajaxurl, data,  function(total_price) {
				check_elem.html(total_price);
			});
		});
		
		$('.is_featured_submit').on('ifUnchecked', function(event){
			var check_elem  = $(this).closest( ".price-info" ).find('.total-price');
			var data = { action: 'get_total_price', check: 0 };
			$.post(ajaxurl, data,  function(total_price) {
				check_elem.html(total_price);
			})
		});
	}
	
	
	/*Pay system*/
	
	/*by each property*/
	if ($('.pay-paypal').length > 0) {
		$('.pay-paypal').on('click', function() {
			var property_id 	 = parseInt($(this).data('propertyid'));
			var check_featured   = $(this).closest( ".info" ).find('.is_featured_submit');
			var is_featured = 0;
			var is_upgrade  = 0;
			
			if (check_featured.prop('checked')) {
				is_featured = 1;	
			}
			
			if ($(this).hasClass('is-upgrade')) {
				is_upgrade = 1;
			}
			
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_paypal_paid_per_property',
					'property_id'   :   property_id,
					'is_featured'   :   is_featured,
					'is_upgrade'    :   is_upgrade
				},
				success: function (data) {
					
					try {
						var error_code = $.parseJSON(data);
						$.jGrowl(error_code[0] + '<br />' + 
								 error_code[1] + '<br />' + 
								 error_code[2] + '<br />' +
								 error_code[3] + '<br />', 
						{ position : "bottom-left" });
					} catch (e) {
						window.location.href = data;
					}
				},
					error: function (errorThrown) {
				}
			});
			
			return false;
		});
	}
	
	
	function StripePaymentProcess(tokenID, propertyID, isFeatured, isUpgrade) {
		var data  = { 
						action: 'zoner_complete_stripe_payment', 
						'tokenID'		: tokenID,
						'property_id'   : propertyID,
						'is_featured'   : isFeatured,
						'is_upgrade'    : isUpgrade
					};
			
			$.post(ajaxurl, data,  function(data) {
				var charge_data = $.parseJSON(data);
				if (charge_data[0] == 1) {
					 location.reload(true);
				} else {
					$.jGrowl(charge_data[1], { position : "bottom-left" });	
				}
			});
	}
	
	function StripePackagePaymentProcess(tokenID,packageID,recurring) {
		var data  = { 
						action: 'zoner_complete_stripe_package_payment', 
						'tokenID'		: tokenID,
						'packageID'		: packageID,
						'recurring'		: recurring
					};
		
		$.post(ajaxurl, data,  function(data) {
			var charge_data = $.parseJSON(data);
			if (charge_data[0] == 1) {
				 location.reload(true);
			} else {
				$.jGrowl(charge_data[1], { position : "bottom-left" });	
			}
		});			
	}
	
	if ($('.pay-stripe').length > 0) {
		$('.pay-stripe').on('click', function() {
			var property_id 	 = parseInt($(this).data('propertyid'));
			var key 			 = $(this).data('key');
			var amount 			 = $(this).data('amount');
			var name			 = $(this).data('name');
			var email			 = $(this).data('email');
			var description		 = $(this).data('description');
			var currency		 = $(this).data('currency');
			var	image			 = $(this).data('image');
			
			var check_featured   = $(this).closest( ".info" ).find('.is_featured_submit');
			var is_featured = 0;
			var is_upgrade  = 0;
			
			if (check_featured.prop('checked')) {
				is_featured = 1;	
			}
			
			if ($(this).hasClass('is-upgrade')) {
				is_upgrade = 1;
			}
			
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_get_stripe_payment_data',
					'property_id'   :   property_id,
					'is_featured'   :   is_featured,
					'is_upgrade'    :   is_upgrade
				},
				success: function (data) {
					try {
							var stripe_data = $.parseJSON(data);
							var handler = StripeCheckout.configure({
								key:   key,
								image: image,
								email: email,
								token: function(token) {
									var tokenID = token['id'];
										$.jGrowl(zoner_stripe_message_1, { position : "bottom-left" });	
										StripePaymentProcess(tokenID,property_id,is_featured,is_upgrade);
								}
							});
						
							handler.open({
								name: name,
								description: stripe_data[0],
								amount: stripe_data[1]
							});	
						
							$(window).on('popstate', function() {
								handler.close();
							});
					
						} catch (e) {
							/**/
						}
					
					
				},
				error: function (errorThrown) {
				}
			});
			
			return false;	
		});
	}	
	
	/*by each property*/
	if ($('.pay-bacs').length > 0) {
		$('.pay-bacs').on('click', function() {
			var property_id 	 = parseInt($(this).data('propertyid'));
			var check_featured   = $(this).closest( ".info" ).find('.is_featured_submit');
			var is_featured = 0;
			var is_upgrade  = 0;
			
			if (check_featured.prop('checked')) {
				is_featured = 1;	
			}
			
			if ($(this).hasClass('is-upgrade')) {
				is_upgrade = 1;
			}
			
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_bacs_paid_per_property',
					'property_id'   :   property_id,
					'is_featured'   :   is_featured,
					'is_upgrade'    :   is_upgrade
				},
				success: function (data) {

					try {
						var error_code = $.parseJSON(data);

						if (error_code[0] == 0) {
							$.jGrowl(error_code[1], { position : "bottom-left" });
						} else {
							$.jGrowl(error_code[0] + '<br />' +
								error_code[1] + '<br />' +
								error_code[2] + '<br />' +
								error_code[3] + '<br />',
								{ position : "bottom-left" });
						}
					} catch (e) {
						window.location.href = data;
					}
				},
					error: function (errorThrown) {
				}
			});
			
			return false;
		});
	}
	/*Package Payments*/
	if ($('#payment-bacs-pack').length > 0) {
		$('#payment-bacs-pack').on('click', function() {
			var vPackSelected = $('.submit-pricing.packages').find('.select-package.package-selected'); 
			var vPackID =  vPackSelected.data('packageid');
			var isrecurringPayments = 0;
			
			if ($('#recurring_payments').length > 0) {
				if ($('#recurring_payments').prop('checked')) {
					isrecurringPayments = 1;	
				}
			}
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_bacs_paid_per_package',
					'package_id'    :   vPackID,
					'recurring'		:   isrecurringPayments
				},
				success: function (data) {
					try {
						var error_code = $.parseJSON(data);
						
						if (error_code[0] == 0) {
							$.jGrowl(error_code[1], { position : "bottom-left" });
						} else {
							$.jGrowl(error_code[0] + '<br />' + 
									 error_code[1] + '<br />' + 
									 error_code[2] + '<br />' +
								     error_code[3] + '<br />', 
						    { position : "bottom-left" });
						}
						
					} catch (e) {
						window.location.href = data;
					}
				},
					error: function (errorThrown) {
				}
			});
			
			return false;
		});
	}

	if ($('#payment-paypal-pack').length > 0) {
		$('#payment-paypal-pack').on('click', function() {
			var vPackSelected = $('.submit-pricing.packages').find('.select-package.package-selected'); 
			var vPackID =  vPackSelected.data('packageid');
			var isrecurringPayments = 0;
			
			if ($('#recurring_payments').length > 0) {
				if ($('#recurring_payments').prop('checked')) {
					isrecurringPayments = 1;	
				}
			}
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_paypal_paid_per_package',
					'package_id'    :   vPackID,
					'recurring'		:   isrecurringPayments
				},
				success: function (data) {
					try {
						var error_code = $.parseJSON(data);
						
						if (error_code[0] == 0) {
							$.jGrowl(error_code[1], { position : "bottom-left" });
						} else {
							$.jGrowl(error_code[0] + '<br />' + 
									 error_code[1] + '<br />' + 
									 error_code[2] + '<br />' +
								     error_code[3] + '<br />', 
						    { position : "bottom-left" });
						}
						
					} catch (e) {
						window.location.href = data;
					}
				},
					error: function (errorThrown) {
				}
			});
			
			return false;
		});
	}
	
	if ($('#payment-stripe-pack').length > 0) {
		$('#payment-stripe-pack').on('click', function() {
			var vPackSelected = $('.submit-pricing.packages').find('.select-package.package-selected'); 
			var vPackID =  vPackSelected.data('packageid');
			var isrecurringPayments = 0;
			
			if ($('#recurring_payments').length > 0) {
				if ($('#recurring_payments').prop('checked')) {
					isrecurringPayments = 1;	
				}
			}	
			
			$.ajax({
				type: 'POST',
				url: ajaxurl,
				data: {
					'action'        :   'zoner_get_stripe_package_payment_data',
					'package_id'    :   vPackID,
					'recurring'		:   isrecurringPayments
					
				},
				success: function (data) {
					try {
							var stripe_data = $.parseJSON(data);
							
							var vSuccess = stripe_data[0];
							if (vSuccess == 0) {
								var vErrorMess = stripe_data[1];
									$.jGrowl(vErrorMess, { position : "bottom-left" });
							} else {
								
								var stripe_pk 			= stripe_data[1];
								var stripe_pack_id 		= stripe_data[2];
								var stripe_plans 		= stripe_data[3];
								var stripe_currency 	= stripe_data[4];
								var stripe_name 		= stripe_data[5];
								var stripe_amount 		= stripe_data[6];
								var stripe_user_email 	= stripe_data[7];
								var stripe_desc 		= stripe_data[8];
								var stripe_logo_site	= stripe_data[9];
								var stripe_recurring     = stripe_data[10];
								
								var handler = StripeCheckout.configure({
									key:   stripe_pk,
									image: stripe_logo_site,
									email: stripe_user_email,
									token: function(token) {
										var tokenID = token['id'];
											$.jGrowl(zoner_stripe_message_1, { position : "bottom-left" });	
											StripePackagePaymentProcess(tokenID,stripe_pack_id,stripe_recurring);
											
									}
								});
						
								handler.open({
									name: stripe_name,
									description: stripe_desc,
									amount: stripe_amount
								});	
						
								$(window).on('popstate', function() {
									handler.close();
								});
							}
							
					
						} catch (e) {
							/**/
						}
				},
				error: function (errorThrown) {
				}
			});
			
			
			
			
			
			return false;
		});
	}
	
	/*by package*/
	if ($('.stripe-payment').length > 0) {
		$('.stripe-payment').hide();
		$('.stripe-payment').find('button').removeClass();
		$('.stripe-payment button').find('span').removeAttr("style");
		$('.stripe-payment').find('button').addClass('btn btn-default small pay-stripe');
		$('.stripe-payment').show();
	} 
	
	
	/*Property featured by front-end*/
	
	if ($('.featured-property').length > 0) {
		$('.featured-property').click('on', function() {
			var propertyID = $(this).data('propertyid');
			var is_featured = 0;
			var vElem = $(this);	
			
			if (!vElem.hasClass('is-featured')) {
				is_featured = 1;
				vElem.addClass('is-featured');
			} else {
				vElem.removeClass('is-featured');
			}
			
			if (propertyID) {
				var data = { action		: 'zoner_featured_toggle', 
							propertyID 	: propertyID,
							featured   	: is_featured
						};
			
				$.post(ajaxurl, data,  function(res) {
					try {
						var msg = $.parseJSON(res);
						if (msg[0] == 0) {
							$.jGrowl(msg[1], { position : "bottom-left" });	
						} else {
							$.jGrowl(msg[1], { position : "bottom-left" });	
							
							if (is_featured) {
								vElem.find('i').removeClass('fa-star-o').addClass('fa-star');
								vElem.closest('#property-' + propertyID).find('.featured-status').addClass('yes').removeClass('no');
								vElem.closest('#property-' + propertyID).find('.featured-status').text(msg[2]);
							} else {
								vElem.find('i').removeClass('fa-star').addClass('fa-star-o');	
								vElem.closest('#property-' + propertyID).find('.featured-status').addClass('no').removeClass('yes');
								vElem.closest('#property-' + propertyID).find('.featured-status').text(msg[2]);
							}							
						}
					} catch (e) {
						
					}
				});
				
			}
			
			return false;	
		});
	}
	
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// On RESIZE
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
$(window).on('resize', function(){
    setNavigationPosition();
    setCarouselWidth();
    equalHeight('.equal-height');
    centerSlider();
	setNavigationOffset();
	centerSearchBox();
	
	drawFooterThumbnails();
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// On LOAD
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

$(window).load(function(){
	 // advanced search box right
	$('.advanced_search_box_right').each( function() {
			if ( $('#wrapper-rs').length > 0 ) {
				var slider_height = $('#wrapper-rs').height();
			} else if( $('#slider').length > 0 ) {
				var slider_height = $('#slider').height();
			} else if( $('#map').length > 0 )  { 
				var slider_height = $('#map').height();
			}
			
			var search_height = $('.advanced_search_box_right .search-box').height() + 100;
			if ( slider_height < search_height) {
				if ($(window).width() >= 768) {
					$('.advanced_search_box_right').addClass('advanced_search_box_right_relative');
				}
				
			}
		})
	
	
	
//  Show Search Box on Map

    $('.search-box.map').addClass('show-search-box');

//  Show All button

    showAllButton();

//  Draw thumbnails in the footer

    drawFooterThumbnails();

//  Show counter after appear
	
	if ($('.number').length > 0 ) {
		$('.number').each(function() {
			$( this ).waypoint(
				function() { 
					initCounter($( this )); 
				}, { 
					offset: '100%',  
					triggerOnce: true 
					});
		});	
    }

    agentState();
	
//  Owl Carousel

    if ($('.owl-carousel').length > 0) {

		
		$(".featured-properties-carousel").each(function() {
			var vCarouselID = '#' + $(this).attr('id');
			var is_loop = true;
			var is_autoplay = $(this).attr('is_autoplay');
			if ( is_autoplay == 1 ) {
				is_autoplay = true;
			} else {
				is_autoplay = false;
			}
			if ($(vCarouselID).find('.property').length == 1) {
				is_loop = false;
			}	
		
			$(vCarouselID).owlCarousel({
				rtl:is_rtl,
				responsiveClass:true,
				responsiveBaseElement:vCarouselID,
				loop:is_loop,
				autoHeight: true,
				nav:false,
				dots:false,
				responsive:{  
					0:{ items:1 },
					640:{ items:3 },
					1700:{ items:4 },
					1900:{ items:5 }
				},
				autoplay: is_autoplay,
				autoplayTimeout: 3500,
				autoplayHoverPause: false
			});
		});
		
		$(".testimonials-carousel").each(function() {
			var vCarouselID = '#' + $(this).attr('id');
			var is_loop = true;
			
			if ($(vCarouselID).find('.testimonial').length == 1) {
				is_loop = false;
			}	
		
   			  $(vCarouselID).owlCarousel({
					rtl:is_rtl,
					items: 1,
					loop:is_loop,
					autoHeight: true,
					responsive:{  0:{ items:1 },
								640:{ items:1 },
							   1700:{ items:1 },
							   1900:{ items:1 }
							},
					responsiveClass:true,
					responsiveBaseElement: vCarouselID + " .testimonials-carousel",
					dots:true,
					nav:false,
			  });
		});
		
        $(".property-carousel").each(function() {
			var vCarouselID = '#' + $(this).attr('id');
			var is_loop = is_nav = true;
			
			if ($(vCarouselID).find('.property-slide').length == 1) {
				is_loop = false;
				is_nav  = false;
			}
			
			  $(vCarouselID).owlCarousel({
					rtl:is_rtl,
					items: 1,
					responsiveClass:true,
					autoHeight: true,
					responsiveBaseElement: vCarouselID + " .property-slide",
					responsive:{  0:{ items:1 },
								640:{ items:1 },
							   1700:{ items:1 },
							   1900:{ items:1 }
							},
					nav:is_nav,
					dots:false,
					loop:is_loop,
					navText: ["",""],
				});	
		});
		
		if ($(".homepage-slider").length > 0) {
			var is_loop = true;
			
			if ($(".homepage-slider").find('.slide').length == 1) {
				is_loop = false;
			}
		
			$(".homepage-slider").owlCarousel({
				mouseDrag:false,
				autoplayTimeout: 10000,
				autoplay:true,
				nav:true,
				rtl:is_rtl,
				loop:is_loop,
				mouseDrag: false,
				items: 1,
				responsive:{  	  0:{ items:1 },
								640:{ items:1 },
							   1700:{ items:1 },
							   1900:{ items:1 }
							},
				responsiveClass:true,
				responsiveBaseElement: ".slide",
				smartSpeed:600,
				navText: ["",""],
				onInitialize :   sliderLoaded,
				onInitialized :  animateDescription,
				onDrag  : animateDescription
			});
		}

		if ($('.carousel-full-width').length > 0) {
			setCarouselWidth();
		}
    }
	
    function sliderLoaded(){
        $('#slider').removeClass('loading');
        $("#loading-icon").remove();
        centerSlider();
    }
	
    function animateDescription(){
        var $description = $(".slide .overlay .info");
        $description.addClass('animate-description-out');
        $description.removeClass('animate-description-in');
        setTimeout(function() {
            $description.addClass('animate-description-in');
        }, 400);
    }
	
//  Gmap shortcode select fix	
	$('.gmap-shortcode .bootstrap-select').on('click', function() {
		var dropdown_menu_h = $(this).find('.dropdown-menu').height();
		$(this).find('.dropdown-menu').css('top', -dropdown_menu_h);
	});

	
});

////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
// Functions
////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////

// Mobile Slider

function centerSlider(){

    var $navigation = $('.navigation');
    $('#slider .slide').height($(window).height() - $navigation.height());
    $('#slider').height($(window).height() - $navigation.height());

    var imageWidth = $('#slider .slide img').width();
    var viewPortWidth = $(window).width();
    var centerImage = ( imageWidth/2 ) - ( viewPortWidth/2 );
    $('#slider .slide img').css('left', -centerImage);
}

// Set height of the map

function setMapHeight(){
    var $body = $('body');
    if($body.hasClass('has-fullscreen-map')) {
				$('#map').height($(window).height() - $('.navigation').height());
        $(window).on('resize', function(){
            $('#map').height($(window).height() - $('.navigation').height());
            var mapHeight = $('#map').height();
            var contentHeight = $('.search-box').height();
            var top;
            top = (mapHeight / 2) - (contentHeight / 2);
            $('.search-box-wrapper').css('top', top);
        });
    }
    if ($(window).width() < 768) {
        $('#map').height($(window).height() - $('.navigation').height());
    }
}

function setNavigationPosition(){
    $('.nav > li').each(function () {
        if($(this).hasClass('has-child')){
            var fullNavigationWidth = $(this).children('.child-navigation').width() + $(this).children('.child-navigation').children('li').children('.child-navigation').width();
            if(($(this).children('.child-navigation').offset().left + fullNavigationWidth) > $(window).width()){
                $(this).children('.child-navigation').addClass('navigation-to-left');
            }
        }
    });
}

function setNavigationOffset() {
	// 	Not visible fixed navigation
	if ( ZonerGlobal.global_fixed_header == 1 ) {
		$('body').addClass('navigation-fixed-top');
		var admin_bar_h = 0;
		if ($('#wpadminbar').length > 0) {
			admin_bar_h = $('#wpadminbar').outerHeight();
		}
		if ($(window).width()>782) {
			$('#page.site>.navigation').css({'top': admin_bar_h + 'px',})
			$('#page.site').css('padding-top', $('#page.site>.navigation')[0].clientHeight + 'px');
		}
	} else if ($('body').hasClass('navigation-fixed-top')) {
    var admin_bar_h = 0;
		if ($('#wpadminbar').length > 0) {
			admin_bar_h = $('#wpadminbar').outerHeight();
			if ($(window).width() > 767) {
				$('.navigation-fixed-top .navigation').css('top', admin_bar_h);
			} else {
				$('.navigation-fixed-top .navigation').css('top', 0);
			}	
		}	
  }
}

// Agent state - Fired when user change the state if he is agent or doesn't

function agentState(){
    var _originalHeight = $('#agency .form-group').height();
    var $agentSwitch = $('#agent-switch');
    var $agency = $('#agency');

    if ($agentSwitch.data('agent-state') == 'is-agent') {
        $agentSwitch.iCheck('check');
        $agency.removeClass('disabled');
        $agency.addClass('enabled');
        $agentSwitch.data('agent-state', '');
    } else {
        $agentSwitch.data('agent-state', 'is-agent');
        $agency.removeClass('enabled');
        $agency.addClass('disabled');
    }
}

function initCounter(elem){
    elem.countTo({
        speed: 3000,
        refreshInterval: 50
    });
}

function showAllButton() {
	
    var rowsToShow = 2; // number of collapsed rows to show
    var $layoutExpandable    = $('.layout-expandable');
	if ($layoutExpandable.data('layoutrow') < 4) {
		rowsToShow = 1;
	}
	
    var layoutHeightOriginal = $layoutExpandable.height();
		
		$layoutExpandable.height($('.layout-expandable .row').height()*rowsToShow-5);
    
	$('.show-all').on("click", function() {
        if ($layoutExpandable.hasClass('layout-expanded')) {
            $layoutExpandable.height($('.layout-expandable .row').height()*rowsToShow-5);
            $layoutExpandable.removeClass('layout-expanded');
            $('.show-all').removeClass('layout-expanded');
        } else {
            $layoutExpandable.height(layoutHeightOriginal);
            $layoutExpandable.addClass('layout-expanded');
            $('.show-all').addClass('layout-expanded');
        }
    });

}

//  Center Search box Vertically

function centerSearchBox() {
    var $searchBox  = $('.search-box-wrapper');
    var $navigation = $('.navigation');
    var positionFromBottom = 20;
	var admin_bar_h = 0;
	if ($('#wpadminbar').length > 0) {
		admin_bar_h = $('#wpadminbar').outerHeight();
	}
	
	if ($('#wrapper-rs').length > 0) {
		if ($('body').hasClass('navigation-fixed-top')){
			$('#wrapper-rs').css('margin-top', $navigation.outerHeight());
			$searchBox.css('z-index',98);
		} else {
			$('#wrapper-rs').css('margin-top', -$('.navigation header').outerHeight());
		}
		
		if ($(window).width() >= 768) {
			$('#wrapper-rs').each(function () {
				if (!$('body').hasClass('horizontal-search-float')) {
					var mapHeight = $(this).height();
					var contentHeight = $('.search-box').height();
					var top;
					
					if($('body').hasClass('has-fullscreen-map')) {
						top = (mapHeight / 2) - (contentHeight / 2);
					} else {
						top = (mapHeight / 2) - (contentHeight / 2) + $('.navigation').height();
					}
					$('.search-box-wrapper').css('top', top);
				} else {
					$searchBox.css('top', $(this).height() + $navigation.outerHeight() - $searchBox.outerHeight() - positionFromBottom);
					if ($('body').hasClass('has-fullscreen-map')) {
						$('.search-box-wrapper').css('top', $(this).outerHeight() - $('.navigation').outerHeight());
					}
				}
			});	
			
		}	
	} else {
		if ($('body').hasClass('navigation-fixed-top')){
			if ( ZonerGlobal.global_fixed_header != 1 ) {
				$('#page.site').css('padding-top', $navigation[0].clientHeight);
			}
			$searchBox.css('z-index',98);
		} else {
			$('.leaflet-map-pane').css('top', -50);
			$(".homepage-slider").css('margin-top', -$('.navigation header').outerHeight());
		}
	
		if ($(window).width() >= 768) {
			$('#slider .slide .overlay').css('margin-bottom', $navigation.height());
			$('#map, #slider').each(function () {
				if (!$('body').hasClass('horizontal-search-float')) {
					var mapHeight = $(this).height();
					var contentHeight = $('.search-box').height();
					var top;
					
					if($('body').hasClass('has-fullscreen-map')) {
						top = (mapHeight / 2) - (contentHeight / 2);
					}
					else {
						top = (mapHeight / 2) - (contentHeight / 2) + $('.navigation').height();
					}
					
					if ($('.search-box-wrapper').length > 0) {
						$('#map').next().css('top', top);
					}
					
				} else {
					$searchBox.css('top', $(this).height() + $navigation.height() - $searchBox.height() - positionFromBottom);
					$('#slider .slide .overlay').css('margin-bottom',$navigation.height() + $searchBox.height() + positionFromBottom);
					if ($('body').hasClass('has-fullscreen-map')) {
						$('.search-box-wrapper').css('top', $(this).height() - $('.navigation').height());
					}
				}
			});
		}	
    }
}

// Set Owl Carousel width
function setCarouselWidth(){
    $('.carousel-full-width').each(function() {
		$(this).css('width', $(window).width());
		info = $('.carousel-full-width .additional-info');
		info.each(function(i){
			$(this).css('width', $(this).closest('.owl-item').css('width'));
		});	});
}

// Show rating form

function showRatingForm(){
    $('.rating-form').css('height', $('.rating-form form').height() + 85 + 'px');
}

//  Equal heights

function equalHeight(container){

    var currentTallest = 0,
        currentRowStart = 0,
        rowDivs = new Array(),
        $el,
        topPosition = 0;
    $(container).each(function() {

        $el = $(this);
        $($el).height('auto');
        topPostion = $el.position().top;

        if (currentRowStart != topPostion) {
            for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
                rowDivs[currentDiv].height(currentTallest);
            }
            rowDivs.length = 0; // empty the array
            currentRowStart = topPostion;
            currentTallest = $el.height();
            rowDivs.push($el);
        } else {
            rowDivs.push($el);
            currentTallest = (currentTallest < $el.height()) ? ($el.height()) : (currentTallest);
        }
        for (currentDiv = 0 ; currentDiv < rowDivs.length ; currentDiv++) {
            rowDivs[currentDiv].height(currentTallest);
        }
    });
}

//  Creating property thumbnails in the footer

function drawFooterThumbnails(){
    var thumbnailsPerRow = 1; 
	var count = 1;
	// Create thumbnail function
	function createThumbnail() {
		var $thumbnail = $('.footer-thumbnails .property-thumbnail');
			$thumbnail.each(function() {
				$(this).css('width', 100/thumbnailsPerRow + '%');
				$(this).find('img').show();
				
				count++;
			});
	}

    if ($(window).width() < 768) {
        thumbnailsPerRow = 5;
		createThumbnail();
	} else if ($(window).width() >= 768 && $(window).width() < 1199 ) {
		thumbnailsPerRow = 10;
		createThumbnail();
	} else if ($(window).width() >= 1200) {
		thumbnailsPerRow = 20;
		createThumbnail();
	}
}
var doRedirect = function(getkey, getvalue)
{
	var map = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		map[key] = value;
	});
	var link = window.location.href;

	if ($.isEmptyObject(map)) {
		var link = link + '?' + getkey + '=' + getvalue;
		window.location.replace( link );
	} else if (map[getkey]) {
		link = link.replace(map[getkey], getvalue);
		window.location.replace( link );
	} else if( map ) {
		link = link + '&' + getkey + '=' + getvalue;
		window.location.replace( link );
	};
}

var doRedirectPageReset = function(getkey, getvalue)
{
	var map = {};
	var parts = window.location.href.replace(/[?&]+([^=&]+)=([^&]*)/gi, function(m,key,value) {
		map[key] = value;
	});
	var link = window.location.href;

	if ($.isEmptyObject(map)) {
		var link = link + '?' + getkey + '=' + getvalue +'&paged=1';
		window.location.replace( link );
	} else if (map[getkey]) {
		link = link.replace(map[getkey], getvalue)+'&paged=1';
		window.location.replace( link );
	} else if( map ) {
		link = link + '&' + getkey + '=' + getvalue +'&paged=1';
		window.location.replace( link );
	};
}

// target="_blank" attr for flickr links
$('a[href*="flickr.com/photos/"]').attr('target', '_blank');
