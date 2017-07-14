$(document).on("pageInit","#idvalidate",function(){
	$("form[name='idvalidate_scanId']").unbind("submit");
	$("form[name='idvalidate_scanId']").bind("submit", function(event){
		var action = $(this).attr('action');
		var query = $(this).serialize();
		$.ajax({
			url:action,
			data:query,
			type:"POST",
			dataType:"json",
			success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					$.loadPage(location.href);
				}else{
					$.toast(obj.info);
				}
			}
		});
		return false;
	});
	$(".idvalidate_del").unbind("click");
	$(".idvalidate_del").bind("click", function(event){
		$.ajax({
			url:$(this).attr('data-url'),
			type:"POST",
			dataType:"json",
			success: function(obj){
				if(obj.status == 1){
					$.toast(obj.info);
					$.loadPage(location.href);
				}else{
					$.toast(obj.info);
				}
			}
		});
	});
});