/**
 * 
 */
$(document).ready(function(){
	
	$(".content").on('click','.search-btn',function(){
		var url=$(this).attr('data-href');
		var key=$("#key").val();
		url += "&key="+key;
		
		$.ajax({
			url:url,
			type:"POST",
			success:function(html)
			{
				$(".j-ajax-content").html($(html).find(".j-ajax-content").html());
			},
			error:function()
			{

			}
		});
	});
	
	$(".content").on('click',".j-del-shop",function(){
		var url=$(this).attr('data-href');
		
		$.ajax({
			url:url,
			type:"POST",
			dataType : "json",
			success:function(obj)
			{
				if(obj.status==true){
					$.showSuccess(obj.info,function(){
						$.ajax({
							url:publish_page_url,
							type:"POST",
							success:function(html)
							{
								$(".j-ajax-content").html($(html).find(".j-ajax-content").html());
							},
							error:function()
							{

							}
						});
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
	
});