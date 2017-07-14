$(document).on("pageInit", "#uc_ecv", function(e, pageId, $page) {
	//alert(2);
	function tab_line() {
		var init_width=$(".uc-ecv-tab .active").width();
		var init_left=$(".uc-ecv-tab .active").offset().left;
		$(".ecv-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	init_listscroll(".j_ajaxlist_"+valid,".j_ajaxadd_"+valid);
	tab_line();
	
	$(".uc-ecv-tab a").click(function() {
		//alert(1);
		$(".uc-ecv-tab a").removeClass('active');
		$(this).addClass('active');
		tab_line();
		$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = $(this).attr("rel");
		$(".m-ecv-list").removeClass('hide');
		if(rel==0)
		$("#tab1").addClass('hide');
		if(rel==1)
		$("#tab0").addClass('hide');
		$('.content').scrollTop(1);
		if($.trim($("#tab"+rel).html()) == "" && $("#tab"+rel).length==0 ){
			var ajax_url =url[rel];
			$.ajax({
				url:ajax_url,
				type:"POST",
				success:function(html)
				{
					$(".content").append($(html).find(".content").html());
					init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				},
				error:function()
				{
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
		
	});
	/*$(".can-use").click(function() {
		$(".m-ecv-list").removeClass('hide');
		$(".used-ecv").addClass('hide');
	});
	$(".cant-use").click(function() {
		$(".m-ecv-list").addClass('hide');
		$(".used-ecv").removeClass('hide');
	});*/

	$(".page").on('click',".j-open-ecv-exchange",function(){
		$(".pop-up").addClass("open");
		$(".pop-up").children(".img-box").addClass("open");
		$(".content").addClass("noscroll");
		$(".close-pop,.j-close-pop-btn").attr("rel","ecv");
	});

	$(".page").on('click',".j-ecv-exchange",function(){
		var sn = $(".input-ecv-exchange").val();
		if(sn.length < 1){
			$.toast("请输入红包兑换码");
		}else{
			var form = $("form[name='exchange_form']");
			var url=$(form).attr('action');
			var query = new Object();
			query.sn = sn;
			$.ajax({
				url:url,
				data:query,
				type:'post',
				dataType:'json',
				success:function(obj){
					if(obj.status==1){
						console.log(obj.info);
						$.toast(obj.info);
						$(".pop-up").children(".img-box").removeClass("open");
						$(".pop-up").removeClass("open");
						$(".input-ecv-exchange").val("");
					}else{
						$.toast(obj.info);
						$(".input-ecv-exchange").val("");
					}
					return false;
				}
			});
			
		}
		return false;
	});
});