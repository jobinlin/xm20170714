
$(document).on("pageInit", "#uc_fx", function(e, pageId, $page) {
	loadScript(jia_url);
	init_list_scroll_bottom();
	$(".j-openshare").click(function(){
		var id=$(this).attr("data_id");
		var img_url=deal_json[id]['icon'];
		var share_url=deal_json[id]['share_url'];
		var title=deal_json[id]['name'];
		jiathis_config = {
		    siteNum:6,
		    sm:"weixin,tssina,cqq,qzone,douban,copy",
		    url:share_url,
		    title:title,
		    pic:img_url
		}
	});
	$(".social_share").find(".flex-1").click(function(){
		$(".flippedout").removeClass("z-open").removeClass("showflipped");
		$(".box_share").removeClass("z-open");
	});
	
	$("#uc_fx").on("click",".j-app-share-btn",function(){
		
		var share_data={};
		share_data["share_content"]=$(this).attr("data-content");
		share_data["share_url"]=$(this).attr("data-url");;
		share_data["key"]='';
		share_data['sina_app_api']=1;
		share_data['qq_app_api']=1;
		share_data["share_imageUrl"]=$(this).attr("data-img");;
		share_data['share_title'] = $(this).attr("data-title");;
		share_data=JSON.stringify(share_data);
		try{
			App.sdk_share(share_data);
		}catch(e){

		}
	});
	
	$("#uc_fx").on('click',".goods-down",function(){
		var id=$(this).attr("data_id");
		
		var data_url=$(this).attr("data-url");
		var data_img=$(this).attr("data-img");
		var data_title=$(this).attr("data-title");
		
		var query = new Object();
		query.act="do_is_effect";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$(".goods-down[data_id='"+id+"']").html("上架");
					
					if(APP_INDEX=="app"){
						$(".goods-down[data_id='"+id+"']").parent().find(".j-app-share-btn").remove();
					}else{
						$(".goods-down[data_id='"+id+"']").parent().find(".j-openshare").remove();
					}
					var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 cancle-fx' data_id='"+id+"' data-url='"+data_url+"' data-img='"+data_img+"' data-title='"+data_title+"'>取消网宝</a>");
					$(".goods-down[data_id='"+id+"']").parent().append($content);
					$(".goods-down[data_id='"+id+"']").removeClass("goods-down").addClass("goods-up");
				}
			}
		});
	});
	
	$("#uc_fx").on('click',".goods-up",function(){
		var id=$(this).attr("data_id");
		var query = new Object();
		
		var data_url=$(this).attr("data-url");
		var data_img=$(this).attr("data-img");
		var data_title=$(this).attr("data-title");
		
		query.act="do_is_effect";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$(".goods-up[data_id='"+id+"']").html("下架");
					$(".goods-up[data_id='"+id+"']").parent().find(".cancle-fx").remove();
					
					if(APP_INDEX=="app"){
						var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 share j-app-share-btn' data_id='"+id+"' data-url='"+data_url+"' data-img='"+data_img+"' data-title='"+data_title+"'>分享</a>");
					}else{
						var $content=$("<a href='javascript:void(0)' class='fx-btn flex-1 share j-openshare' data_id='"+id+"'>分享</a>");
					}
					$(".goods-up[data_id='"+id+"']").parent().append($content);
					$(".goods-up[data_id='"+id+"']").removeClass("goods-up").addClass("goods-down");
				}
			}
		});
	});
	
	$("#uc_fx").on("click",".cancle-fx",function(){
		var id=$(this).attr("data_id");
		var query = new Object();
		query.act="del_user_deal";
		query.deal_id = id;
		$.ajax({
			url: ajax_url,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				$.toast(obj.info);
				if(obj.status==1){
					$.toast(obj.info);
					if(obj.status==1){
						$(".fx-list").find("li[data_id='"+id+"']").remove();
					}
				}
			}
		});
	});
});