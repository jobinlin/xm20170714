$(document).on("pageInit", "#event", function(e, pageId, $page) {

	loadScript(jia_url);
	/*倒计时*/

	var nowtime = parseInt($(".j-LeftTime").attr("nowtime"));
	var endtime = parseInt($(".j-LeftTime").attr("endtime"));
	var leftTime = (endtime - nowtime) / 1000;
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

	
	/*
	 *下拉导航收藏按钮
	 *如果已经收藏则执行以下操作，否则本阶段不执行操作
	 */
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
				event_add_collect(id);
			}
		}
	});
	/*
	 *取消收藏按钮弹出后的取消
	 */
	$(".cancel-shoucan .j-cancel").click(function(){
		$(".cancel-shoucan").removeClass("z-open");
	});

	/*
	 *取消收藏按钮弹出后的确认
	 */
	$(".cancel-shoucan .j-yes").click(function(){
		event_del_collect(id);
		$(".cancel-shoucan").removeClass("z-open");
	});
	
	$("#event_submit").unbind("click");
	$("#event_submit").bind("click",function(){
		$.confirm("你确认要报名吗？",function(){
			var url=$(this).attr("url");
			var query = new Object();
			query.event_id = id;
			query.act = "do_submit";
			$.ajax({
				url: url,
				data: query,
				dataType: "json",
				type: "post",
				success:function(data){
					if(data.status==1){
						$.toast(data.info);
						setTimeout(function(){
							window.location.href=data.jump;
						},2000);
					}else{
						$.toast(data.info);
					}
				}
			});
		});
	});

	$(".login_submit").unbind("click");
	$(".login_submit").bind("click",function () {
		if(is_login==0){
			if(app_index=="app"){
				App.login_sdk();
			}else{
				$.router.load(login_url, true);
			}
		}
	});
});

function event_add_collect(id){
	var query = new Object();
	query.id = id;
	query.act = "add_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(data){
			if(data.status==0 && data.user_login_status==0){
				$.toast("请先登录");
				setTimeout(function(){
					window.location.href=data.jump;
				},1000);
			}
			if(data.status==1){
				$("i.icon-collection").addClass("isCollection");
				$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont icon-noshoucan'>&#xe615;</i><i class='iconfont icon-shoucan'>&#xe63d;</i><em>"+data.collect_count+"</em></div>");
				$.toast(data.info);
				$(".j-head-collect").attr("data-isdel",1);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			}
		},
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}

function event_del_collect(id){
	var query = new Object();
	query.id = id;
	query.act = "del_collect";
	$.ajax({
		url: ajax_url,
		data: query,
		dataType: "json",
		type: "post",
		success: function(data){
			if(data.status==0 && data.user_login_status==0){
				$.alert(data.info,function(){
					window.location.href=data.jump;
				});
			}
			if(data.status==1){
				$.toast(data.info);	
				$("i.icon-collection").removeClass("isCollection");
				if(data.collect_count>0){
					$("div.is_Sc").html("<div class='shoucan isSc'><i class='iconfont'>&#xe615;</i><em>"+data.collect_count+"</em></div>");
				}else{
					$("div.is_Sc").html('<i class="iconfont" id="is_Sc" style="font-size: 1.2rem;">&#xe615;</i>');
				}
				$(".j-head-collect").attr("data-isdel",0);
				$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open");
				$(".m-nav-dropdown").removeClass("showdropdown");
				$(".nav-dropdown-con").removeClass("dropdown-open");
			}
		},
		error:function(ajaxobj)
		{
//					if(ajaxobj.responseText!='')
//					alert(ajaxobj.responseText);
		}
	});
}
