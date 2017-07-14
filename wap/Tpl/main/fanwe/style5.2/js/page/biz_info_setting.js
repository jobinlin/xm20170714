
$(document).on("pageInit", "#biz_info_setting", function(e, pageId, $page)  {

    //退出登录
	$(".btn-con").click(function(){
		var exit_url=$(this).attr("data-url");
		var query = new Object();
		query.act='loginout';
		$.ajax({
			url:exit_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.toast(obj.info);
					setTimeout(function(){
						$.router.load(obj.jump,true);
					},1500);
				}
				else
				{
					$.toast(obj.info);
					return false;
				}
			},
			error:function(){
			$.toast("服务器提交错误");
			return false;
			}
		});
		return false;
	});

});