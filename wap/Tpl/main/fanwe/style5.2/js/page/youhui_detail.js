$(document).on("pageInit", "#youhui_detail", function(e, pageId, $page) {
	
	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		youhui_detail_del_collect(youhui_id);
		$(".cancel-shoucan").removeClass("z-open");
		
	});

	/*
	 *取消收藏按钮弹出后的取消
	 */
	$(".cancel-shoucan .j-cancel").click(function(){
		$(".cancel-shoucan").removeClass("z-open");
		$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
		$(".m-nav-dropdown").removeClass("showdropdown");
		$(".nav-dropdown-con").removeClass("dropdown-open");
	});

	$(".j-head-collect").on("click",function(){

		var is_del = $(this).attr("data-isdel");
		if(is_del == 1){
		 	//打开取消框
			$(".cancel-shoucan").addClass("z-open");
		}else{
			if(is_login==0){
				if(app_index=="app"){
					App.login_sdk();
				}else{
					$.router.load(login_url, true);
				}
			}else{
				youhui_detail_add_collect(youhui_id);
			}

		}
	});
});

// 收藏和取消收藏。。不确定是否需要
function youhui_detail_add_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "add_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(obj){
			if (obj.user_login_status) {
				if(obj.status == 1){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+obj.collect_count+"</em></div>");
					$.toast(obj.info);	
					$(".j-head-collect").attr("data-isdel",1);
					$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
					$(".m-nav-dropdown").removeClass("showdropdown");
					$(".nav-dropdown-con").removeClass("dropdown-open");
				}else{
					$.toast(obj.info);
				}
			} else {
				$.toast("请先登录");
				setTimeout(function(){
					window.location.href=obj.jump;
				},1000);	
			}
		},
		error:function(ajaxobj) {
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}
function youhui_detail_del_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "get",
		success: function(obj){
			if(obj.status == 1){
				$.toast(obj.info);
				if(obj.collect_count>0){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+obj.collect_count+"</em></div>");
				}else{
					$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
				}
				$(".j-head-collect").attr("data-isdel",0);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			} else{
				$.toast(obj.info);
			}
		},
		error:function(ajaxobj){
			$.toast('网络异常..')
		}
	});
}

