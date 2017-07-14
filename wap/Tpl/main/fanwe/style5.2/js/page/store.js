$(document).on("pageInit", "#store", function(e, pageId, $page) {
	qrcode_box();
	$(".j-open-store-detail").click(function(){
		var con_height = $(".content").height();
		var top_height = $(".banner-con").height();
		var margin_height = parseInt($(".m-store-banner").css("margin-bottom").replace("px"));
		var height = parseInt(con_height) - parseInt(top_height) -margin_height;
		if($(".store-detail-info").height() == 0){
			$(".store-detail-info").height(height);
			$(this).addClass("isOver");
			setTimeout('$(".other-content").addClass("hide");',200);
			$(".store-detail-info").scroller('refresh');
			$(".content").scroller('refresh');
			$(".store-detail-info").scrollTop(0);
		}else{
			$(".store-detail-info").height(0);
			$(this).removeClass("isOver");
			$(".other-content").removeClass("hide");
			$(".store-detail-info").scroller('refresh');
		}
	});


	$(".youhui-item").bind("click",function(){
		var url=$(this).attr("url");
		$.ajax({
			url: url,
			dataType: "json",
			type: "POST",
			success: function(obj){
				if(obj.status==0){
					$.toast(obj.info);
					if(obj.jump){
						$.router.load(obj.jump, true);
					}
				}else if(obj.status==1){
					$.toast(obj.info);
				}
			},
			error:function()
			{
				$.toast("服务器提交错误");
			}
		});
	});



});