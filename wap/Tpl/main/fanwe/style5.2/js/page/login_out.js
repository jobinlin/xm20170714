
$(document).on("pageInit", "#login_out", function(e, pageId, $page)  {

    //退出登录
	$(".btn-con").click(function(){
		var cookarr=$.fn.cookie('cookobj');
		$.fn.cookie('cookobj',cookarr,{ expires: -1 });
		if(app_index=='app'){
			App.logout();
			return false;
		}
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
						window.location.href=obj.jump;
					},1500);
				}
				else
				{
					$.toast(obj.info);
				}
			}
		});
	});

});