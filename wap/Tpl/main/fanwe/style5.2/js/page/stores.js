$(document).on("pageInit", "#stores", function(e, pageId, $page) {
	init_list_scroll_bottom();//下拉刷新加载
	//星星评分
	$(".stores-item").each(function(){
	    $(this).find(".start-num").css("width",$(this).find(".start-num").parent().parent().attr("data")+"%");
	});
	//隐藏数量为0的2级分类
	/*$(".goods-num").filter(function(index){
　　　　return $(this).text()=="0";
　　	}).parent().hide();*/
	screen_bar();
	if(address==""){
		position();
	}
	$(".address-info").click(function() {
		position();
	});
});
