//プロダクトエリアスライド
$(function(){
    //下のリンクをクリックするとき
    $('.js-project-slide-title').click(function(){
        if($(this).hasClass('active')){
            $(this).removeClass('active');
            if($(this).parent().parent().find('.js-project-slide-title.active').length ==0) {
                $('.js-project-top').removeClass('active');
                $('.js-project-top-content').slideDown(600);
            }
            $(this).parent().find('.js-project-slide-content').slideUp(600);
        } else {
            $('.js-project-top-content').slideUp(600);
            $(this).addClass('active');
            $('.js-project-top').addClass('active');
            $(this).parent().find('.js-project-slide-content').slideDown(600);
        }
    });

    //上のリンクをクリックするとき
    $('.js-project-top').click(function(){
        if($(this).hasClass('active')) {
            $(this).removeClass('active');
            $('.js-project-slide-title').removeClass('active');
            $('.js-project-slide-content').slideUp(600);
            $(this).find('.js-project-top-content').slideDown(600);
        }
    });
});