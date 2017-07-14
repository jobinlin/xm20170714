$(document).on("pageInit", "#uc_lottery", function(e, pageId, $page) {
	$(".j-close-warning").click(function(){
		$(this).parent(".m-warning").height(0);
	});
});