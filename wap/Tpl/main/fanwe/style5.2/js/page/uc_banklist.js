$(document).on("pageInit", "#uc_banklist", function(e, pageId, $page) {
	
	$('.del').on('click', function () {
		var obj = $(this);
		var id = new Array();
		var id = obj.parents("li").attr("data-id");
//		alert(id);
		$.confirm('确定删除这张银行卡？', function () {
		  del_bank(id);
		  obj.parents("li").remove();
		});
	});

	function del_bank(id){
		var query = new Object();
		query.id = id;
			$.ajax({
				url: ajax_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					
					$.toast(obj.info);
		//			if(obj.status==0 && obj.user_login_status==0){
		//				$.alert(obj.info,function(){
		//					window.location.href=obj.jump;
		//				});
		//			}
		//			if(obj.status == 1){
		//				$.toast(obj.info);
		//				//setTimeout("location.reload()",1000);
		//				
		//			}
				},
				error:function(ajaxobj)
				{
					
		//			if(ajaxobj.responseText!='')
		//			alert(ajaxobj.responseText);
				}
		});
	}
});
