$(document).on("pageInit", "#scores_index", function(e, pageId, $page) {
	init_auto_load_data();
	 var mySwiper = new Swiper ('.j-score-type', {
		scrollbarHide: true,
        slidesPerView: 'auto',
        centeredSlides: false,
        grabCursor: true
	});
	 
	$(".signin").live("click",function(){
        var query = new Object();
        query.act="signin";
        $.ajax({
                url: INDEX_URL,
                data: query,
                type: "POST",
                dataType: "json",
                success: function (obj) {
                	if(obj.status==1){
                		$(".sign").removeClass('signin').find("span").html(obj.info);
                		$(".sign-day").html(obj.sign_info);
                		$(".user-info .score em").html(obj.score);
                		
                	}else{
                		$.alert(obj.info);
                	}
                },
        });
	});
	$(".ulogin").unbind("click");
	$(".ulogin").bind("click",function () {
		if(is_login==0){
			if(app_index=="app"){
				App.login_sdk();
			}else{
				$.router.load(login_url, true);
			}
		}
	});



});

