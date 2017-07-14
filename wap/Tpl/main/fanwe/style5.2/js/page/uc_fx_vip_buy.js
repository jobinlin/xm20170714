$(document).on("pageInit", "#uc_fx_vip_buy", function(e, pageId, $page) {
	
    $(".content").scroller('refresh');
	$(".fx_buy").click(function(){
		$.ajax({ 
            url: ajax_url,
            type: "POST",
            dataType: "json",
            success: function(data){
                if(data.status==1){
                    if(data.free){
                    	$.toast(data.info);
	                    setTimeout(function(){
	                    	$.router.load(data.jump, true);
	                    },1000);
                    }else{
                    	$.router.load(data.jump, true);
                    }
                }else{
                    $.toast(data.info);
                    if(data.jump){
	                    setTimeout(function(){
	                    	$.router.load(data.jump, true);
	                    },1000);
                    }
                }
            },
        });
	});
	
	$(document).on('click','.open-protocol', function () {
	    $.popup('.popup-protocol');
    });
});