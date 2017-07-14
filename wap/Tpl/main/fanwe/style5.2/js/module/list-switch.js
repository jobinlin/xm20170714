$(document).on("pageInit", ".page", function(e, pageId, $page) {
	//列表类型切换
	$(".j-type-btn").click(function() {
		$(this).hide();
	});
	$("#type-cube").click(function() {
		$("#type-list").show();
		$(".m-goods-list ul").removeClass('type-cube').addClass('type-list');
	});
	$("#type-list").click(function() {
		$("#type-cube").show();
		$(".m-goods-list ul").removeClass('type-list').addClass('type-cube');
	});
});