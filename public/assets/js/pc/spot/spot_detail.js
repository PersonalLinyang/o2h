$(function(){function next_image_slide(controller){var btn_prev=controller.closest(".div-image-detail").find(".btn-image-prev");var btn_next=controller.closest(".div-image-detail").find(".btn-image-next");var li=controller.find("li");var offset=li.width()*-1;btn_next.unbind("click");btn_prev.unbind("click");li.not("li:first").css("opacity",0);controller.stop().animate({left:offset},1500,function(){controller.append(li.first());$(this).css("left","0px")});li.first().animate({opacity:"0"},1500,function(){});li.not("li:first").animate({opacity:"1"},1500,function(){});setTimeout(function(){controller.closest(".div-image-detail").find(".btn-image-next").click(function(){bind_btn_next($(this))})},1500);setTimeout(function(){btn_prev.click(function(){bind_btn_prev($(this))})},1500)}function bind_btn_next(controller){var ul=controller.closest(".div-image-detail").find(".ul-img-detail-slide");next_image_slide(ul)}function bind_btn_prev(controller){var btn_prev=controller.closest(".div-image-detail").find(".btn-image-prev");var btn_next=controller.closest(".div-image-detail").find(".btn-image-next");var ul=controller.closest(".div-image-detail").find(".ul-img-detail-slide");btn_prev.unbind("click");btn_next.unbind("click");var offset=ul.find("li").width()*-1;var lastItem=ul.find("li").last();ul.prepend(lastItem);ul.find("li").first().css("opacity",0);ul.css("left",offset);ul.animate({left:"0px"},1500,function(){});ul.find("li").not("li:first").animate({opacity:"0"},1500,function(){});ul.find("li").first().animate({opacity:"1"},1500,function(){});setTimeout(function(){btn_prev.click(function(){bind_btn_prev($(this))})},1500);setTimeout(function(){btn_next.click(function(){bind_btn_next($(this))})},1500)}function timer_image_slide(){$(".ul-img-detail-slide").each(function(){next_image_slide($(this))})}$(".btn-image-next").click(function(){bind_btn_next($(this))});$(".btn-image-prev").click(function(){bind_btn_prev($(this))});var scrollTimer=setInterval(timer_image_slide,5000);$(".div-image-detail").hover(function(){clearInterval(scrollTimer)},function(){scrollTimer=setInterval(timer_image_slide,5000)})});