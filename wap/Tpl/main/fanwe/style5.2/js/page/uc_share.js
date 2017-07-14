$(document).on("pageInit", "#uc_share", function(e, pageId, $page) {
	loadScript(jia_url);
	$(".content").scroller('refresh');
	$(".social_share").find(".flex-1").click(function(){
		$(".weixin-share-close").hide();
		$(".weixin-share-tip").hide();
		$(".flippedout").removeClass("z-open").removeClass("showflipped");
		$(".box_share").removeClass("z-open");
	});
	$(".j-weixin-share").on('click', function() {
		$(".weixin-share-close").show();
		$(".weixin-share-tip").show();
		$(".flippedout").addClass("z-open").addClass("showflipped");
	});
	$(".j-flippedout-close").on('click', function() {
		$(".weixin-share-close").hide();
		$(".weixin-share-tip").hide();
	});
});