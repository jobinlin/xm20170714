$(document).ready(function(){
	
	function ajax_do_submit(action,query)
	{
		$.ajax({
			url:action,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				if(obj.status)
				{					
					$.weeboxs.close("refund_form");
					alert(obj.info);
					location.reload();
				}
				else
				{
					alert(obj.info);
				}
			}
		});
	}
	
	$(".do_refund").bind("click",function(){		
		var action = $(this).attr("action");
		var query = new Object();
		query.ajax = 1;
		$.ajax({
			url:action,
			type:"POST",
			data:query,
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.weeboxs.open(obj.html, {boxid:"refund_form",contentType:'text',showButton:false,title:"退款处理",width:530,onopen:function(){
						
						var form = $("#refund_form").find("form[name='refund_form']");
						
						$("#confirm").bind("click",function(){
							var query = $(form).serialize();
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
						$("#refuse").bind("click",function(){
							var query = $(form).serialize();
							var action = $(this).attr("action");
							ajax_do_submit(action,query);
						});
					}});
				}
				else
				{
					alert(obj.info);
                    if(obj.jumpUrl){
                           location.href=obj.jumpUrl;
                    }
				}
				
			}
		});
	});
	
	
	$(".do_verify").bind("click",function(){	
		if(confirm("确认该项操作吗？"))
		{
			var action = $(this).attr("action");
			var query = new Object();
			query.ajax = 1;
			$.ajax({
				url:action,
				type:"POST",
				data:query,
				dataType:"json",
				success:function(obj){
					if(obj.status)
					{
						alert(obj.info);
						location.reload();
					}
					else
					{
						alert(obj.info);
					}
					
				}
			});
			
		}
		
	});

	// 分配驿站
	$(".dist_choose").bind("click",function(){
		var action = $(this).attr('action');
		// html = '<div><input type="text" name="dist_skey"><input type="button" class="dist_search" value="搜索"></div>';
		// html += '<div><select name="dist_result"><option value="0">未选择</option></select></div>';
		var html = '<table class="form" cellpadding=0 cellspacing=0 style="text-align:center;"><tr><td colspan=2 class="topTd"></td></tr><tr><td colspan=2><input type="text" name="dist_skey"><input type="button" class="dist_search" value="搜索"></td></tr><tr><td><select name="dist_result" style="width:220px;"><option value="0">未选择</option></select></td></tr></table>';
		$.weeboxs.open(html, {boxid:"dist_form",contentType:'text',showButton:true,title:"驿站分配",width:355,onopen:function(){		
			$('.dist_search').bind('click', function() {
				var sKey = $('input[name="dist_skey"]').val();
				sKey = $.trim(sKey);
				if (!sKey) {
					alert('请输入搜索关键字');
					return false;
				}
				var query = {'key': sKey, 'ajax': 1};
				$.ajax({
					url: dist_search_url,
					type: "POST",
					data: query,
					dataType: "json",
					success: function(obj) {
						if (obj.status) {
							$('select[name="dist_result"]').html(obj.info);
						} else {
							alert(obj.info);
						}
					}
				});
			});
		}, onok: function() {
			// 确定按钮事件
			var did = $('select[name="dist_result"]').val();
			if (did == 0) {
				alert('请分配一个驿站');
				return false;
			}
			var query = {'did': did, 'ajax': 1};
			$.ajax({
				url: action,
				type: 'POST',
				data: query,
				dataType: 'json',
				success: function(obj) {
					if (obj.status) {
						$.weeboxs.close("dist_form");
						alert(obj.info);
						location.reload();
					} else {
						alert(obj.info);
					}
				}
			})
		}});
	});

	
});