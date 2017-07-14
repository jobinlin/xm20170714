$(document).on("pageInit", "#tuan", function(e, pageId, $page) {
	screen_bar();
	init_list_scroll_bottom();//下拉刷新加载
	//
	//星星评分
	//$(".tuan-item").each(function(){
	//    $(this).find(".start-num").css("width",$(this).find(".start-num").parent().parent().attr("data")+"%");
	//});
	//隐藏数量为0的2级分类
	$(".goods-num").filter(function(index){
		if($(this).parent().attr("data-cid")=='0'&&$(this).parent().attr("data-tid")=='0')
			return false;
　　　　return $(this).text()=="0";
　　	}).parent().hide();

	//团购列表展开
	$(document).on("click",".tuan-list-more",function() {
		var height = $(this).parent().find('.tuan-list li').height();
		var num = $(this).parent().find('.tuan-list li').length;
		$(this).parent().find('.tuan-list').css('max-height', height*num);
		$(this).hide();
	});
	if(address==""){
		//if(navigator.geolocation)
		//{
			 //var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
			 //navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
			 
		//}
		position();
	}
	$(document).on("click",".address-info",function() {
		$(".refresh").addClass('rotate');
		//if(navigator.geolocation)
		//{
		//	 var geolocationOptions={timeout:10000,enableHighAccuracy:true,maximumAge:5000};
		//	 navigator.geolocation.getCurrentPosition(getPositionSuccess, getPositionError, geolocationOptions);
		//}
		position();
	});
	
	function getPositionSuccess(p){
		has_location = 1;//定位成功;
	    m_latitude = p.coords.latitude; //纬度
	    m_longitude = p.coords.longitude;
		userxypoint(m_latitude, m_longitude);
	}

	function getPositionError(error){
		switch(error.code){
		    case error.TIMEOUT:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>定位连接超时，请重试");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		        //alert("定位连接超时，请重试");
		        break;
		    case error.PERMISSION_DENIED:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>您拒绝了使用位置共享服务，查询已取消");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		        //alert("您拒绝了使用位置共享服务，查询已取消");
		        break;
		    default:
		    	$(".address").html("<i class='iconfont'>&#xe62f;</i>定位失败");
		    	$(".refresh").removeClass('rotate');
		    	//setCookie("cancel_geo",0,1);
		    	//alert("定位失败");
		}
	}
});

