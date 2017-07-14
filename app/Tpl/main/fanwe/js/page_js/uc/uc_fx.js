$(function(){
    init_my_mall_box();
});
function init_my_mall_box(){
    $(".my_mall").hover(function(){		
			$(".my_mall .img_box").stopTime();
			$(".my_mall .img_box").oneTime(300,function(){
				$(".my_mall .img_box").slideDown("fast");	
			});						
		},function(){
			$(".my_mall .img_box").stopTime();
			$(".my_mall .img_box").oneTime(300,function(){
				$(".my_mall .img_box").slideUp("fast");
			});
		});
}