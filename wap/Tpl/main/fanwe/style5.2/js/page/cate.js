$(document).on("pageInit", "#cate", function(e, pageId, $page) {
	var active_length=$(".cate-list li.active").length;
	if(active_length==0){
		$(".cate-list li").eq(0).addClass('active');
		$(".cate-info ul").eq(0).addClass('active');
	}
	$(".cate-list li").click(function() {
		$(".cate-list li").removeClass('active');
		$(".cate-info ul").removeClass('active');
		$(this).addClass('active');
		$(".cate-info ul").eq($(this).index()).addClass('active');
	});;
});
