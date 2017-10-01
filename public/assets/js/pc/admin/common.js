$(function(){
	$('#link-logout').click(function(){
		$('#logout-form').submit();
	});

  //ヘッダナビにマウスを通過する動作
  $(".header-navi li").hover(function(){
    if($(this).hasClass("js-navi-sub")) {
      //サブナビがある場合
      $(this).addClass("active-sub");
      $(this).find("div.header-sub-navi").stop().show();
    } else {
      //サブナビがない場合
      $(this).addClass("active");
    }
  }, function(){
    if($(this).hasClass("js-navi-sub")) {
      //サブナビがある場合
      $(this).find("div.header-sub-navi").stop().hide();
      $(this).removeClass("active-sub");
    } else {
      //サブナビがない場合
      $(this).removeClass("active");
    }
  });

  //サイドバーニュース部分自動切り替え
  $('.sidebar-slide-content').each(function(){
    var $targetObj = $(this);
    var $targetUl = $targetObj.children('ul');
    var $targetLi = $targetObj.find('li');
    var $setList = $targetObj.find('li:first');

    var listHeight = $targetLi.height();
    $targetLi.css({top:'0',left:'0',position:'absolute'});

    var liCont = $targetLi.length;

    $setList.css({top:'50px',display:'block',opacity:'0'}).stop().animate({top:'0',opacity:'1'},1000,'swing').addClass('showlist');
    if(liCont > 1) {
      setInterval(function(){
        var $activeShow = $targetObj.find('.showlist');
        $activeShow.animate({top:'-50px',opacity:'0'},1000,'swing').next().css({top:'50px',display:'block',opacity:'0'})
          .animate({top:'0',opacity:'1'},1000,'swing').addClass('showlist').end().appendTo($targetUl).removeClass('showlist');
      },5000);
    }
  });
});