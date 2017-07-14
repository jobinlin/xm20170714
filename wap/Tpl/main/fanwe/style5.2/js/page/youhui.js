$(document).on("pageInit", "#youhui", function(e, pageId, $page) {
	
	loadScript(jia_url);
	/*倒计时*/

	var nowtime = parseInt($(".j-LeftTime").attr("nowtime"));
	var endtime = parseInt($(".j-LeftTime").attr("endtime"));
	// var leftTime = (endtime - nowtime) / 1000;
	var leftTime = endtime - nowtime;
	leftTimeAct();
	setInterval(leftTimeAct,1000);
	
	function leftTimeAct(){
		if(leftTime > 0)
		{
			var day  = parseInt(leftTime / 24 /3600);
			var hour = parseInt((leftTime % (24 *3600)) / 3600);
			var min  = parseInt((leftTime % 3600) / 60);
			var sec  = parseInt((leftTime % 3600) % 60);
			$(".j-LeftTime").find(".day").html(day);
			$(".j-LeftTime").find(".hour").html(hour);
			$(".j-LeftTime").find(".min").html(min);
			$(".j-LeftTime").find(".sec").html(sec);
			leftTime--;
		}
	}
	// 优惠券领取方法
	$('.isActive').click(function() {
		var ajax_url = $(this).attr("data-src");
		$.ajax({
			url:ajax_url,
			data:'',
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.user_login_status==0){
					$.toast(obj.info);
					setTimeout(function(){
						$.router.load(obj.jump, true);
					}, 2000);
				}
				if(obj.status) {
					$.toast(obj.info);			
				} else {
					$.toast(obj.info);
					$('.isActive').addClass('isOver').removeClass('.isActive');
					setTimeout(function() {
						window.location.reload();
					}, 2000);
				}
			}
		});
	});

	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		youhui_del_collect(youhui_id);
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
				youhui_add_collect(youhui_id);
			}
		}
	});
});

// 收藏和取消收藏。。不确定是否需要
function youhui_add_collect(id){
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
function youhui_del_collect(id){
	var query = new Object();
	query.data_id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
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
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}

