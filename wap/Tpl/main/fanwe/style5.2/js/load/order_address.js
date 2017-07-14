/**
 * Created by Administrator on 2016/9/8.
 */
	$("#order_address").on("click",".load_page",function(){
		load_page($(this));
	});

$("#order_address").on("click",".load_page2",function(){
    load_page2($(this));
});
	
    $("#order_address").on('click','.confirm-address', function () {
        var _this=$(this);
        var address_id=$(this).attr("data-id");
        var order_address=$("#pay_box").find("input[name='address_id']").val();
        $.confirm('确定要删除该地址吗？', function () {
        	if(address_id==order_address){
        		$.alert("正在使用的地址无法删除");
        	}else{
	        	$.ajax({
					url: _this.attr('del_url'),
					data: {},
					dataType: "json",
					type: "post",
					success: function(obj){
						if(obj.status == 1){
							_this.parents("li").remove();
						}else{
							$.alert(obj.info);
						}
					},
	        	});
        	}
        });
    });
var shop_list_height = $(".pick-shop-list li").eq(0).height()+$(".pick-shop-list li").eq(1).height();
var  shop_list_all = 0;
$(".pick-shop-list li").each(function(index, el) {
     shop_list_all = shop_list_all + $(el).height();
});
$(".pick-shop-list").css('height', shop_list_height);
$("#order_address").on('click', '.check-more', function() {
    $(this).hide();
    $('.check-back').show();
    $(".pick-shop-list").css('height', shop_list_all);
});
$("#order_address").on('click', '.check-back', function() {
    $(this).hide();
    $('.check-more').show();
    $(".pick-shop-list").css('height', shop_list_height);
});
    $("#order_address").on("change",".j-address-set input[type=radio]",function () {
        if($(this).prop('checked')==true){

			var vobj=$(this);
        	$.ajax({
				url: $(this).attr('dfurl'),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status == 1){
						vobj.parents(".j-address-set").find(".u-set-default").addClass("j-address-color");
						vobj.parents("li").siblings("li").find(".u-set-default").removeClass("j-address-color");
					}else{
						$.toast("失败");
					}
				},
        	});
            
        }
    });
    
    $("#order_address").on("click",".address",function(){
    	var is_default=$(this).parent().parent().attr("is_default");
    	var id=$(this).attr("data-id");
    	var url=$(this).attr("url");
    	$(".page-current").remove();
		$(".page").last().addClass('page-current');
		$.ajax({
            url:url,
            type:"POST",
            success:function(data){
            	$(".page-current").find(".content").html($(data).find(".content").html());
            	$(".page-current").find(".popup-box .j-trans-way").html($(data).find(".popup-box .j-trans-way").html());
            	$(".page-current").find(".popup-box .j-red-reward").html($(data).find(".popup-box .j-red-reward").html());
            	count_buy_total();
            }
        });
    	/*if(is_default){
	    	var name=$(this).parent().find(".item-title").html();
	    	var url=$(this).attr("url");
	    	var phone=$(this).parent().find(".item-after").html();
	    	var address=$(this).parent().find(".u-address-mess").html();
	    	$("#delivery-address").find(".user-name").html("收货人:"+name+"<span class='u-phoneNum'>"+phone+"</span>");
	    	$("#delivery-address").find(".user-address").html(address);
	    	$("#pay_box").attr("action",url);
	    	$("#pay_box").find("input[name='address_id']").val(id);
	    	$(".page-current").remove();
			$(".page").last().addClass('page-current');
    	}else{
    		var query=new Object();
    		query.id=id;
    		$.ajax({
				url: set_default_url,
				data: query,
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status){
						window.location.reload();
					}else{
						$.toast("地址选择失败");
					}
				},
        	});
    	}*/
    });
    
    
    
    $("#order_address").on("click",".pick_address",function(){
    	var url=$(this).attr("url");
    	$(".page-current").remove();
		$(".page").last().addClass('page-current');
		$.ajax({
            url:url,
            type:"POST",
            success:function(data){
            	$(".page-current").find(".content").html($(data).find(".content").html());
            	$(".page-current").find(".popup-box .j-trans-way").html($(data).find(".popup-box .j-trans-way").html());
            	$(".page-current").find(".popup-box .j-red-reward").html($(data).find(".popup-box .j-red-reward").html());
            	count_buy_total();
            }
        });
    });
    
    function count_buy_total()
    {
        ajaxing = true;
        var query = new Object();

        //获取配送方式
        var delivery_id = $("input[name='delivery']:checked").val();

        if(!delivery_id)
        {
            delivery_id = 0;
        }
        query.delivery_id = delivery_id;

        var address_id = $("input[name='address_id']").val();

        //全额支付
        if($("input[name='all_account_money']").attr("checked"))
        {
            query.all_account_money = 1;
        }
        else
        {
            query.all_account_money = 0;
        }
		//积分抵现
		if($('input[name="all_score"]:checked').length>0)
		{
			query.all_score = 1;
		}
		else
		{
			query.all_score = 0;
		}
		
		//优惠券
		var youhui =new Object();
		$(".j-trans-way ul").each(function(){
			var data_id=$(this).attr("data-id");
			youhui[data_id]=$("input[name='youhui_log_id["+data_id+"]']:checked").val();

		});
		query.youhui_ids = youhui;

        //代金券
        var ecvsn = $("input[name='ecvsn']:checked").val();

        if(!ecvsn)
        {
            ecvsn = '';
        }

        var ecvpassword = $("input[name='ecvpassword']").val();
        if(!ecvpassword)
        {
            ecvpassword = '';
        }

        var buy_type = $("input[name='buy_type']").val();
        query.ecvsn = ecvsn;
        query.ecvpassword = ecvpassword;
        query.address_id = address_id;
        query.buy_type = buy_type;
        //支付方式
        var payment = $("input[name='payment']:checked").val();
        if(!payment)
        {
            payment = 0;
        }
        query.payment = payment;
        query.bank_id = $("input[name='payment']:checked").attr("rel");
		var id = $("input[name='id']").val();
		query.id = id;
        query.order_id = order_id;
        //query.reward = reward;
        query.act = "count_buy_total";
        $.ajax({
            url: AJAX_URL,
            data:query,
            type: "POST",
            dataType: "json",
            success: function(data){
                if(data.total_price==0 && $('div').is('.voucher_box')){
                    $(".voucher_box").remove();
                    count_buy_total();
                }
                $("#cart_total").html(data.html);
                $(".total_price_box").html(data.pay_price_html);
                ajaxing = false;
				if($('input[name="all_score"]').length){
					$("input[name='all_score']").unbind('change');
					$("input[name='all_score']").bind("change",function () {
						count_buy_total();
					});
				}
            }
        });
    }