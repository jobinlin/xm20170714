$(document).ready(function(){
	$(".j-receive").bind('click',function(){
		var id = $(this).attr('data-id');
		$.showConfirm("确定要兑换吗？",function(){
			exchange(ajax_url,id);
		});
	});

	$("#sn_exchange").bind("submit",function(){
		if($.trim($(this).find("input[name='sn']").val())=="")
		{
			$.showErr("请输入序列号");
			return false;
		}
		var ajaxurl = $(this).attr("action");
		var query = $(this).serialize();
		$.ajax({ 
			url: ajaxurl,
			data:query,
			dataType: "json",
			type: "POST",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess("兑换成功",function(){
						location.href = obj.jump;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		
		return false;
	});
	
});



function exchange(url,id){
	var ajax_url = url;
	var query = new Object();
	query.id = id;
	$.ajax({
		url: ajax_url,
		data:query,
			dataType: "json",
		type: "post",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess(obj.info,function(){
						location.reload();
					});
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj)
			{
				
			}
		});		

}



