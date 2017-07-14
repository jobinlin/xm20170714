$(function(){

	$(".delivery_form_btn").bind("click",function(){

		var form = $("form[name='delivery_form']");
		var query = $(form).serialize();
		var url = $(form).attr("action");

		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$.showErr(data.info,function(){
						if(data.jump){
							window.location = data.jump;
						}
					});
				}else if(data.status==1){
					$.showSuccess(data.info,function(){
						if(data.jump){
							window.location = data.jump;
						}
					});
				}
			}
		});
		
	});
	
	
	
});//JQUERY END




