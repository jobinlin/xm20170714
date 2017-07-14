$(document).on("pageInit", "#order_view", function(e, pageId, $page) {
    $(".cancel_order").unbind("click").bind("click",function(){
        var message=$(this).attr("message");
        var url=$(this).attr("ajaxUrl");
		var button_type=$(this).attr("button-type");
        $.confirm(message, function () {
            $.showIndicator();
            $.ajax({
                url:url,
                dataType:"json",
                success:function(data){
                    if(data.status==0){
                        $.toast(data.info);
                    }else{
//                        $.alert(data.info,function(){
//                            window.location.href=data.jump;
//                        })
                    	$.toast(data.info);
                   	 	window.setTimeout(function(){
							if(button_type=="j-cancel"){
								window.location.href=location.href;
							}else{
								window.location.href=data.jump;
							}
    					},1500);
                    }
                }
            });
        });
    });

});