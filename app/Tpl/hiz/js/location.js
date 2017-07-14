/**
 * 
 */
$(document).ready(function(){
	
	$(".content").on('click',".j-del-men",function(){
		var url=$(this).attr('data-href');
		
		$.ajax({
			url:url,
			type:"POST",
			dataType : "json",
			success:function(obj)
			{
				if(obj.status==true){
					$.showSuccess(obj.info);
					$.ajax({
						url:location_page_url,
						type:"POST",
						success:function(html)
						{
							$(".j-ajax-content").html($(html).find(".j-ajax-content").html());
						},
						error:function()
						{

						}
					});
				}else{
					$.showErr(obj.info);
				}
			},
			error:function()
			{
				$.showErr("删除失败");	
			}
		});
	});
	
	$(".content").on('click',".j-status",function(){
		var url=$(this).attr('data-href');
		var data_id=$(this).attr('data-id');
		
		$.ajax({
			url:url,
			type:"POST",
			dataType : "json",
			success:function(obj)
			{
				if(obj.status==true){
					$.showSuccess(obj.info,function(){
						if(obj.is_effect==1)
							$(".j-status[data-id='"+data_id+"']").html("正常");
						else
							$(".j-status[data-id='"+data_id+"']").html("禁用");
					});
					
				}
				else{
					$.showErr(obj.info);
				}
			},
			error:function()
			{
				$.showErr("操作失败");
			}
		});
		
	});
	
});