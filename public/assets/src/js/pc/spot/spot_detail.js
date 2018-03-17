$(function () {

    //显示后一张图片
    function next_image_slide(controller) {
        var btn_prev = controller.closest('.div-image-detail').find('.btn-image-prev');
        var btn_next = controller.closest('.div-image-detail').find('.btn-image-next');
        var li = controller.find("li");
        var offset = li.width() * -1;

        //暂时使切换图片按钮无效
        btn_next.unbind('click');
        btn_prev.unbind('click');

        //图片切换
        li.not('li:first').css("opacity", 0);
        controller.stop().animate({ left: offset }, 1500, function() {
            controller.append(li.first());
            $(this).css("left", "0px");
        });
        li.first().animate({ opacity: "0" }, 1500, function() {});
        li.not('li:first').animate({ opacity: "1" }, 1500, function() {});

        //恢复切换图片按钮点击
        setTimeout(function(){
            controller.closest('.div-image-detail').find('.btn-image-next').click(function() {
                bind_btn_next($(this));
            });
        }, 1500);
        setTimeout(function(){
            btn_prev.click(function() {
                bind_btn_prev($(this));
            });
        }, 1500);
    };

    //点击向后切换图片按钮
    function bind_btn_next(controller) {
        var ul = controller.closest('.div-image-detail').find('.ul-img-detail-slide');
        next_image_slide(ul);
    }

    //点击向前切换图片按钮
    function bind_btn_prev(controller) {
        var btn_prev = controller.closest('.div-image-detail').find('.btn-image-prev');
        var btn_next = controller.closest('.div-image-detail').find('.btn-image-next');
        var ul = controller.closest('.div-image-detail').find('.ul-img-detail-slide');

        //暂时使切换图片按钮无效
        btn_prev.unbind('click');
        btn_next.unbind('click');

        //图片切换
        var offset = ul.find("li").width() * -1;
        var lastItem = ul.find("li").last();
        ul.prepend(lastItem);
        ul.find("li").first().css("opacity", 0);
        ul.css("left", offset);
        ul.animate({ left: "0px" }, 1500, function() {});
        ul.find("li").not('li:first').animate({ opacity: "0" }, 1500, function() {});
        ul.find("li").first().animate({ opacity: "1" }, 1500, function() {});

        //恢复切换图片按钮点击
        setTimeout(function(){
            btn_prev.click(function() {
                bind_btn_prev($(this));
            });
        }, 1500);
        setTimeout(function(){
            btn_next.click(function() {
                bind_btn_next($(this));
            });
        }, 1500);
    }

    //图片定时切换
    function timer_image_slide() {
        $('.ul-img-detail-slide').each(function(){
            next_image_slide($(this))
        });
    }

    //初始绑定点击向后切换图片按钮
    $(".btn-image-next").click(function() { 
        bind_btn_next($(this));
    });

    //初始绑定点击向前切换图片按钮
    $(".btn-image-prev").click(function() {
        bind_btn_prev($(this));
    });

    //图片自动切换定时器
    var scrollTimer = setInterval(timer_image_slide, 5000);
    $(".div-image-detail").hover(function() {
        clearInterval(scrollTimer); 
    }, function() {
        scrollTimer = setInterval(timer_image_slide,5000);
    });

});