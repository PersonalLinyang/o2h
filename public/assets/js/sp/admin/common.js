$(function(){  
    //顶部菜单呼出按钮
    $('.header-menu-link').unbind('click').click(function(){
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('#menu-area').slideUp();
        } else {
            $(this).addClass('active');
            $('#menu-area').slideDown();
        }
    });

    //顶部菜单左侧按钮点击
    $('.menu-main-button').click(function(){
        $('.menu-main-button').removeClass('active');
        $(this).addClass('active');
        $value = $(this).data('link');
        $('.menu-sub-list').fadeOut(200);
        setTimeout(function () { $('#menu-sub-list-' + $value).fadeIn(); }, 200);
    });
});