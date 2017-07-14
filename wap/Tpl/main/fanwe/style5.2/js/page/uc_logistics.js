$(document).on("pageInit", "#uc_logistic", function(e, pageId, $page) {
	
	if($(".buttons-tab .tab-link").length>0){
	    var _width=$(".buttons-tab .tab-link.active").find("span").width();
	    var _left=$(".buttons-tab .tab-link.active").find("span").offset().left;
	
	    var btm_line=$(".buttons-tab .bottom_line");
	    btm_line.css({"width":_width+"px","left":_left+"px"});
	
	    var _tabs=$(".tabBox .tab_box");
	}
    $(".buttons-tab .tab-link").click(function () {
        var _wid=$(this).find("span").width();
        var _lef=$(this).find("span").offset().left;

        btm_line.css({"width":_wid+"px","left":_lef+"px"});
        var _index=$(this).index();

        $(this).addClass("active").siblings(".tab-link").removeClass("active");
        _tabs.eq(_index).addClass("active").siblings(".tab_box").removeClass("active");
        init_confirm_button();

    });
    
    if($(".no_delivery").hasClass("active") &&
	   $("input[type='checkbox']").length==$("input[disabled='disabled']").length
	){
		$("#uc_logistic nav.bar-tab .confirm_order").hide();
		$("#uc_logistic nav.bar-tab").addClass('line-white');
	}else{
		init_confirm_button();
	}

	$(".no_delivery_deal").click(function(){
    	if($("input[type='checkbox']").length==$("input[disabled='disabled']").length){
			$("#uc_logistic nav.bar-tab .confirm_order").hide();
			$("#uc_logistic nav.bar-tab").addClass('line-white');
		}else{
			$("#uc_logistic nav.bar-tab .confirm_order").show();
			$("#uc_logistic nav.bar-tab").removeClass('line-white');
		}
    });
	
	var is_confirm=0;
	$(this).find(".confirm_order").unbind("click");
	$(this).find(".confirm_order").bind("click",function(){
		if(is_confirm){
			$.toast("请勿重复点击！");
			return false;
		}
		is_confirm=1
		$.confirm('确认收货？', function() {
			var data_id = $(".tabBox .tab_box.active").attr("data_id");	
			var query = new Object();
			if(data_id){
				query.item_id = data_id;
				query.act = 'verify_delivery';
			}else{
				var order_ids=new Array();
				$(".tabBox .tab_box.active").find("input[name='my-radio']").each(function(){
					order_ids.push($(this).attr("data_id"));
				});
				query.order_ids=JSON.stringify(order_ids);
				query.act = 'verify_no_delivery';
			}
			$.ajax({
				url: order_url,
				data: query,
				dataType: "json",
				type: "POST",
				success: function(obj){
					if(obj.status==0){

						$.toast(obj.info);
						is_confirm=0;
					}else if(obj.status == 1){
						$.toast(obj.info);
						window.setTimeout(function(){
							$("#uc_logistic .tabBox .tab_box.active").attr("is_arrival",1);
							init_confirm_button();
							window.location.href=obj.jump;
						},1500);
					}
				},
				error:function(ajaxobj)
				{
					is_confirm=0;
					//if(ajaxobj.responseText!='')
					//alert(ajaxobj.responseText);
				}
						
			});
		},function() {is_confirm=0;})
		
	});
});

function init_confirm_button(){
	var status = $("#uc_logistic .tabBox .tab_box.active").attr("status");
	if(status==1){
		$("#uc_logistic nav.bar-tab .confirm_order").hide();
		$("#uc_logistic nav.bar-tab").addClass('line-white');
	}else{
		$("#uc_logistic nav.bar-tab .confirm_order").show();
		$("#uc_logistic nav.bar-tab").removeClass('line-white');
	}
}