$(document).ready(function(){
	$(".j-check-shop").bind('click', function() {
		$(".mask").addClass('active');
		$(".shop-box").addClass('active');
		var supplier_id = $("input[name='supplier_id']").val();
		var youhui_id = $("input[name='youhui_id']").val();
		var query = new Object();
 		query.supplier_id = supplier_id;
 		query.youhui_id = youhui_id;
		$.ajax({
			url:ajax_url,
			type:"POST",
			data:query,
			dataType:"json",
			success:function(obj)
			{
				if(obj.is_online == 1 ){
					$(".shop-tip").html('本券限以下店铺在线购物使用：');
				}else{
					$(".shop-tip").html('本券限以下店铺线下实体门店消费使用：');
				}

				$(".shop-list").empty();
				var length=obj.shop_list.length;
				var location_li="";
				for(var i=0;i<length;i++){
					location_li+="<li>"+
							"<a href="+obj.shop_list[i]['url']+" class='shop-name' target='_blank'>"+obj.shop_list[i]['stroe_name']+"</a>"+
							"<p class='shop-address'>"+obj.shop_list[i]['address']+"</p>"+
							"</li>";
				}
				$(location_li).appendTo($(".shop-list"));

			}

		});
	});
	$(".j-close").bind('click', function() {
		$(".mask").removeClass('active');
		$(".shop-box").removeClass('active');
	});



	
	$(".youhui-sms").bind("click",function(){
		var dom = $(this);
		$.ajax({
			url:$(dom).attr("action"),
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status==1000)
				{
					ajax_login();
				}
				else if(obj.status==1)
				{
					IS_RUN_CRON = 1;
					$.showSuccess(obj.info,function(){
						location.reload();
					});
				}
				else
				{
					$.showErr(obj.info,function(){
						if(obj.jump)
						{
							location.href = obj.jump;
						}
					});
				}
			}
		});
	});
});