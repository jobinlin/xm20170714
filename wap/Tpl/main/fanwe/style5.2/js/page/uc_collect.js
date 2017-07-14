$(document).on("pageInit", "#uc_collect", function(e, pageId, $page) {
	refreshdata([".uc_collect_change"]);

/*
 *初始化tab的下划线
*/	
	init_listscroll(".j_ajaxlist_"+sc_status,".j_ajaxadd_"+sc_status);
	
	bottom_line(0);
	$(".m-collect-list").addClass("hide");
	$("#tb0").removeClass("hide");
	$(".j-tab-btn").removeClass("active");
	$(".j-tab-btn").eq(0).addClass("active");

/*
 *tab切换
 *参数说明：left  点击的tab距离左边的距离，width  点击的tab的宽度，
 *rel 对应的类别  0为商品团购 1为优惠券 2为活动 3为店铺，isload  标识出所选择的类别内容是否已经加载
*/

	$(".j-tab-btn").click(function(){
		var rel = $(this).attr("rel");
		
		var isload = $(this).attr("data-isload");
		var isEdit = $(this).parent(".m-tab-btn-list").attr("data-isedit");
		
		if (isEdit == 0) {
			$(document).off('infinite', '.infinite-scroll-bottom');
			 
			$(".j-tab-btn").removeClass("active");
			$(this).addClass("active");
			
			bottom_line(rel);
			
			$(".m-collect-list").addClass("hide");
			$("#tb"+rel).removeClass("hide");
			
			$('.content').scrollTop(1);
			if($.trim($("#tb"+rel).html()) == ""){
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
			
			if (isload == 0) {
				//ajax加载内容
				console.log("ajax加载内容");
				$(this).attr("data-isload",1);
			}else{
				console.log("我加载完了····");
			}

			$(".j-edit").attr("rel",rel);
			$(".j-all-check").attr("rel",rel);
			$(".j-cancel").attr("rel",rel);
			now_sc = rel;
			console.log(now_sc);
		}else{
			console.log("编辑状态不能切换");
		}
	});


/*
 *编辑按钮
*/
	$(".j-edit").click(function(){
		var rel = $(this).attr("rel");
		var isEdit = $(this).attr("data-isedit");
		if(isEdit == 0){
			var item_length = $('.m-collect-list[rel = "'+rel+'"]').find(".collect-item").length;
			if (item_length > 0) {
				$('.m-collect-list[rel = "'+rel+'"]').addClass("isEdit");
				$('.m-collect-list[rel = "'+rel+'"]').find(".no-href").show();
				$(this).html("完成");
				$(this).attr("data-isedit",1);
				$(".m-operation").addClass("isShow");
				$(".m-tab-btn-list").attr("data-isedit",1);
			}else{
				$.toast("当前没有收藏！！");
			}
		}else{
			$('.m-collect-list[rel = "'+rel+'"]').removeClass("isEdit");
			$(".j-all-check").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".j-check-box").removeClass("isCheck");
			$(this).html("编辑");
			$(this).attr("data-isedit",0);
			$(".m-operation").removeClass("isShow");
			$(".m-tab-btn-list").attr("data-isedit",0);
			$('.m-collect-list[rel = "'+rel+'"]').children(".collect-item").children(".j-check-box").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".no-href").hide();
		}
	});

/*
 *勾选
*/
	$(".page").on("click",".j-check-box" , (function(){
		var isCheck = $(this).children(".iconfont").hasClass("isCheck");
			if(isCheck){
				$(this).children(".iconfont").removeClass("isCheck");
				$(".j-all-check").children(".iconfont").removeClass("isCheck");
			}else{
				$(this).children(".iconfont").addClass("isCheck");
				var rel = $(this).parents(".m-collect-list").attr("rel");
				var check_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").length;
				var item_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").length;
				if (check_length == item_length) {
					$(".j-all-check").children(".iconfont").addClass("isCheck");
				}
			}
		})
	);

/*
 *全选
*/
	$(".j-all-check").click(function(){
		var isCheck = $(this).children(".iconfont").hasClass("isCheck");
		var rel = $(this).attr("rel");
		if(isCheck){
			$(this).children(".iconfont").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").children(".iconfont").removeClass("isCheck");
		}else{
			$(this).children(".iconfont").addClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".j-check-box").children(".iconfont").addClass("isCheck");
		}
	});

/*
 *取消收藏
 *参数说明： data  数组  保存已选择的子项的id，type  保存已选择的子项类别
*/
	$(".j-cancel").on("click",function(){
		var rel = $(this).attr("rel");
		if($('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").length != 0){
			var data = new Array();
			var type = $('.m-collect-list[rel = "'+rel+'"]').attr("data-type");
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").each(function(index){
				data[index] = $(this).parent(".j-check-box").attr("data-id");
			});
			var id=data.join(","); 
			uc_del_collect(type,id);
			console.log(type);
			console.log(data);
			//console.log(id);


			//还原页面到未编辑状态
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").parents(".collect-item").remove();
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".isCheck").parents(".collect-item").attr("data-isdel",1);
			$('.m-collect-list[rel = "'+rel+'"]').removeClass("isEdit");
			$(".j-all-check").children(".iconfont").removeClass("isCheck");
			$('.m-collect-list[rel = "'+rel+'"]').find(".j-check-box").children(".iconfont").removeClass("isCheck");
			$(".j-edit").html("编辑");
			$(".j-edit").attr("data-isedit",0);
			$(".m-operation").removeClass("isShow");
			$(".m-tab-btn-list").attr("data-isedit",0);
			$('.m-collect-list[rel = "'+rel+'"]').children(".collect-item").children(".j-check-box").children(".iconfont").removeClass("isCheck");
			
			//判断是否全部删除，如果全部删除这显示无内容文本
			var del_length = 0;
			var item_length = $('.m-collect-list[rel = "'+rel+'"]').children().find(".collect-item").length;
			$('.m-collect-list[rel = "'+rel+'"]').children().find(".collect-item").each(function(){
				if ($(this).attr("data-isdel") == 1) {
					del_length++;
				}
			});
			if (del_length == item_length) {
				$('.m-collect-list[rel = "'+rel+'"]').append('<div class="tipimg no_data">暂无收藏</div>');
			}
		}else{
			$.toast("请选择要取消的收藏！！");
		}
		
	});
});/*页面结束初始化*/


/*
 *初始化tab的下划线
*/
function bottom_line(index){
	var left = $(".j-tab-btn").eq(index).children("em").offset().left;
	var width = $(".j-tab-btn").eq(index).children("em").width();
	$(".bottom-line").css({
		"left" : left,
		"width" : width
	});
}

function uc_del_collect(type,id){
	var query = new Object();
	query.id = id;
	query.type = type;
	//alert(ajax_url);
	$.ajax({
				url: ajax_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					if(obj.status==0 && obj.user_login_status==0){
						$.alert(obj.info,function(){
							window.location.href=obj.jump;
						});
					}
					if(obj.status == 1){
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
