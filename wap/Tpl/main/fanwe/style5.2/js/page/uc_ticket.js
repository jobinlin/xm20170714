$(document).on("pageInit", ".page", function(e, pageId, $page) {
	$(".page").on('click',".j-tab-link",function(){
		var rel = $(this).attr("rel");
		var con_width = $(this).parent().width();
		var item_width = $(this).width();
		var left = con_width - item_width;
		if(rel != 1){
			$(".float-line").css("left",left);
		}else{
			$(".float-line").css("left",0);
		}
	});

	$(".content").on('click',".j-show-more-quan",function(){
		var isOpen = $(this).hasClass("isOpen");
		if (isOpen) {
			$(this).removeClass("isOpen");
			var con_height = $(this).height();
			$(this).siblings(".quan-show").height(con_height);
			$.refreshScroller();
			$(".j-show-more-quan em").html("点击展开");

		} else {
			$(this).addClass("isOpen");
			var con_height = $(this).siblings(".quan-show").children(".quan-list").height();
			$(this).siblings(".quan-show").height(con_height);
			$.refreshScroller();
			$(".content").scroller('refresh');
			$(this).children("em").html("点击收起");

		}
	});

	$(".content").on('click',".j-open-quaninfo",function(){
		$(".pop-up").addClass("open");
		var src = $(this).attr("data");
		var id = $(this).attr("data-id");
		$(".pop-up").children(".img-box").addClass("open");
		$(".j-pop-img").attr("src",src);
		$(".j-quan-id").html(id);
		$(".content").addClass("noscroll");
	});

	$(".page").on('click',".close-pop,.j-close-pop-btn",function(){
		var rel = $(this).attr("rel");
		$(".pop-up").children(".img-box").removeClass("open");
		$(".pop-up").removeClass("open");
		if (rel == "ecv") {
			$(".input-ecv-exchange").val("");
		}else{
			$(".j-quan-id").html("");
			$(".content").removeClass("noscroll");
			setTimeout(function(){
				$(".j-pop-img").attr("src","");
			},300);
		}
	});
});