
function vip_buy(){
	$.ajax({ 
        url: ajax_url,
        type: "POST",
        dataType: "json",
        success: function(data){
            if(data.status==1){
                if(data.free){
                	$.showSuccess(data.info,function(){
                		window.location.href=data.jump;
            		});
                }else{
                	window.location.href=data.jump;
                }
            }else{
            	if(data.jump){
	            	$.showErr(data.info,function(){
	            		window.location.href=data.jump;
	        		});
            	}else{
            		$.showErr(data.info);
            	}
            }
        },

    });
};
