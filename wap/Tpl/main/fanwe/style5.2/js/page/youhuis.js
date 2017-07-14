$(document).on("pageInit", "#youhuis", function(e, pageId, $page) {
	init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);//下拉刷新加载
	function tab_line() {
		var init_width=$(".m-events-tab .active").width();
		var init_left=$(".m-events-tab .active").offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: init_width,
			left: init_left
		});
	}
	var tab_length =$(".m-events-tab li").length;
	if(tab_length<6){
		$(".m-events-tab ul").addClass('flex-box');
		$(".m-events-tab ul li").addClass('flex-1');
	}
	else{
		var w_width=$(window).width();
		var item_width=w_width/5.5;
		$(".m-events-tab li").css('width', item_width);
		$(".m-events-tab ul").css('width', item_width*tab_length);
		$(".m-events-tab ul li").addClass('tab-item');
	}
	tab_line();
    $(document).on('click','.j-choose-cate', function () {
    	$(".j-choose-cate").removeClass('active');
		$(this).addClass('active');
		tab_line();
    });


	$(".m-events-tab a").click(function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		$(".m-events-tab a").removeClass('active');
		$(this).addClass('active');
		$(".m-youhui-list").hide();
		var item_width=$(this).width();
		var item_left=$(this).offset().left+$(".m-events-tab").scrollLeft();
		$(".events-tab-line").css({
			width: item_width,
			left: item_left
		});
		var url=$(this).attr("data-src");
		var cate_id=$(this).attr("cate-id");
		$(".j_ajaxlist_"+cate_id).show();
		$(".content").scrollTop(1);
		if($(".j_ajaxlist_"+cate_id).html()==null){
			$.ajax({
				url:url,
				type:"POST",
				success:function(html)
				{
					//console.log("成功");
					$(".content").append($(html).find(".content").html());
					init_listscroll(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
				},
				error:function()
				{
					$(".j_ajaxlist_"+cate_id).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
					//console.log("加载失败");
				}
			});
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j_ajaxlist_"+cate_id,".j_ajaxadd_"+cate_id);
			}
		}
	});


	var lock = false;
	if(!lock){
	$(document).on("click",".youhui-item",function(){
		if(is_login==0 && app_index=="app"){
            App.login_sdk();
            return false;
        }
		
			if(lock)return ;

			lock  = true;
		var data_id=$(this).attr("data-id");
			var url=$(this).attr("url");
		if(url){
			$.ajax({
				url: url,
				dataType: "json",
				type: "POST",
				success: function(obj){
					$.toast(obj.info);
					if(obj.status==0){
						if(obj.jump){
							$.router.load(obj.jump, true);
						}
					}else if(obj.status==8){
						if(obj.jump){
							$(".youhui-item[data-id='"+data_id+"']").html("立即使用");
							$(".youhui-item[data-id='"+data_id+"']").removeClass("youhui-item");
							$(".youhui-btn[data-id='"+data_id+"']").removeAttr("url");
							$(".youhui-btn[data-id='"+data_id+"']").attr("href",obj.jump);
						}
					}
				},
				error:function()
				{
					$.toast("服务器提交错误");
				}
			});
				lock = false;
		}
	});
	}

});