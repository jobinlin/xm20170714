$(document).on("pageInit", "#dc_res_cart", function(e, pageId, $page) {

	//打开送货时间选择
	$(".j-open-time").on('click', function() {
		$(".dc-mask").addClass('active');
		$(".time-select").addClass('active');
		var send_time=$(this).find('input').attr('value');
		$(".j-time-choose").each(function() {
			if ($(this).attr('value')==send_time) {
				$(this).addClass('active');
			}
		});
	});
	//关闭送货时间选择
	$(".j-close-time").on('click', function() {
		$(".dc-mask").removeClass('active');
		$(".time-select").removeClass('active');
	});
	//选择时间
	$(".j-time-choose").on('click', function() {
        if ($(this).hasClass('timeerror')) {
            $.toast('本时段无法预订');
            return;
        }
        if ($(this).hasClass('fullbuy')) {
            $.toast('本时段预订已满');
            return;
        }
		$(".j-time-choose").removeClass('active');
		$(this).addClass('active');
		$(".j-res-time").html($(this).find('p').html());
		$("#time-value").attr('value', $(this).attr('value'));
	});
	//打开备注
	$(".j-open-memo").on('click', function() {
		$("#memo").focus();
		$(".dc-mask").addClass('active');
		$(".memo-box").addClass('active');
	});
	//关闭备注
	$(".j-close-memo").on('click', function() {
        var memo = $.trim($('textarea[name="dc_comment"]').val()).substr(0,100);
        $('#memo').val(memo);
		close_memo();
	});
	//确认备注
	$(".j-memo").on('click', function() {
        var memo = $.trim($('textarea[name="dc_comment"]').val());
        if (memo == "") {
            $(".j-res-memo").html('<span class="default-txt">备注您的口味、偏好等</span>');
        }else {
            if (memo.length > 100) {
                $.toast('备注不超过100字,当前'+memo.length+'字');
                return;
            }
            $(".j-res-memo").html(memo);
        }
        close_memo();
	});
    function close_memo() {
        $(".dc-mask").removeClass('active');
        $(".memo-box").removeClass('active');
    }
	//选择只订座
	$(".j-only-res").on('click', function() {
        if ($(this).hasClass('active')) {
            return;
        }
		$(".res-way").removeClass('active');
		$(this).addClass('active');
		$("#res-way").attr('value', $(this).attr('value'));
        $('.res-goods-info').hide();
        pay_price_format();
		/* Act on the event */
	});
	//打开菜单
	$(document).on('click', '.j-open-menu', function() {
        /*if ($(this).hasClass('active')) {
            return;
        }*/
        if ($(this).hasClass('res-way')) {
            if ($('.res-goods-info').find('.goods-list li').length > 0) {
                $(".res-way").removeClass('active');
                $(this).addClass('active');
                $("#res-way").attr('value', $(this).attr('value'));
                $('.res-goods-info').show();
                pay_price_format();
                return;
            }
        }
        var param = {
            'lid': location_id, 
            'table_menu_id': table_menu_id,
            'act': 'res_cart_item'
        };
        $.ajax({
            url: CART_URL,
            data: param,
            dataType: 'json',
            success: function(data) {
                $('.j-shop-item').html(data.html);
                $.popup('.popup-menu');
            }
        })
	});
	//关闭菜单
	$(document).on('click', '.j-close-popup', function() {
        refresh_dc_cart();
        $(this).parents('.popup').show();
	    $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
        pay_price_format();
	});
	//购物车脚本
    $(".plus").bind('click', function() {
    	$(this).parent().removeClass('no-num');
    });
    $(".menu").on('click', '.j-cate-select',function() {
    	$(".m-cate-list").unbind('scroll');
    	$(".j-cate-select").removeClass('active');
    	$(this).addClass('active');
    	var menu_top=$(".menu").offset().top;
    	var list_top=$(".m-cate-list").scrollTop();
    	var cate_top=$(".dc-cate-list").eq($(this).index()).offset().top;
    	s_height=cate_top-menu_top+list_top;
    	$(".m-cate-list").scrollTo({toT:s_height});
    });
    $(".m-cate-list").bind('touchstart', function() {
	    $(".m-cate-list").bind('scroll', function() {
			cate_scroll();
	    });
    });

    function cate_scroll() {
    	var menu_top=$(".menu").offset().top;
    	$(".cate-title").each(function(){
    		var cate_top=$(this).offset().top;
    		if (cate_top<=menu_top) {
    			$(".j-cate-select").removeClass('active');
    			$(".j-cate-select").eq($(this).parent().index()).addClass('active');
    		}
    	});
    }
    $(document).on('click',".j-show-edit", function() {
        if($('.cart-count').hasClass("hide")==false){
            $(".cart-count").toggleClass('active');
            $(".cart-mask").toggleClass('active');
        }
    });
    $(document).on('click', ".j-empty-edit",function() {
        $.toast("购物车是空的");
    });
    $(".no-goods-btn").bind('click', function() {
        $.toast("还未达到最低价格");
    });
    $(document).on('click', ".j-close-edit", function() {
        refresh_dc_cart();
        $(this).parents('.popup').hide();
        $(this).parents('.popup').removeClass('modal-in').addClass('modal-out');
    	// $(".cart-count").removeClass('active');
    	// $(".cart-mask").removeClass('active');
        
    });
    $(".j-open-detail").bind('click', function() {
    	$(".dc-shop-detail").addClass('active');
    });
    $(".j-close-detail").bind('click', function() {
    	$(".dc-shop-detail").removeClass('active');
    });
    $(".menu").on('click', '.m-cate-list .plus',function() {
    	$(".m-fly").addClass('active').css({
    		left: $(this).offset().left,
    		top: $(this).offset().top
    	});
        $(".cart-bar .cart-ico .iconfont").addClass('active');
    	bool.init();
    	bool.setOptions({
    		targetEl: $("#target")
    	});
    	bool.start();
    });
    var bool = new Parabola({
    	el: ".m-fly",
		curvature: 0.004,
		duration: 300,
    	callback:function(){
            setTimeout('$(".cart-bar .cart-ico .iconfont").removeClass("active")', 300);
    		$(".m-fly").removeClass('active');
    	}
    });

    // 确认支付
    $('.res-pay').on('click', function() {
        if ($(this).hasClass('disable')) {
            return;
        }
        // 进本信息判断
        var time_id = $('input[name="order_delivery_time"]').val();
        if (time_id == 0) {
            $.toast('请选择到店时间');
            return;
        }
        var consignee = $.trim($('input[name="consignee"]').val());
        if (!consignee) {
            $.toast('请输入预订人的姓名');
            return;
        }
        var mobile = $.trim($('input[name="mobile"]').val());
        if (mobile == '') {
            $.toast('请输入预订人的手机号');
            return;
        }
        if (/^1[34578]\d{9}$/.test(mobile) == false) {
            $.toast('手机号码格式有误');
            return;
        }
        var dc_comment = $.trim($('textarea[name="dc_comment"]').val());

        // 订座定金
        var res_price = Number($('.res-price').attr('data-value'));
        // 预订方式
        var rs_type = Number($('#res-way').val());
        var count_price = Number($('.count-price').attr('data-value'));
        if (res_price > 0 && rs_type == 2) { // 有订单有点菜
            if (count_price < res_price) {
                $.toast('点菜的金额需要超过定金金额');
                return;
            }
        }
        var param = {
            'lid': location_id,
            'item_time_id': time_id,
            'table_menu_id': table_menu_id,
            'consignee': consignee,
            'mobile': mobile,
            'dc_comment': dc_comment,
            'rs_type': rs_type,
            'rs_date': rs_date,
            'act': 'res_make_order',
            // 'act': 'old_make_order',
        };

        $.ajax({
            url: CART_URL,
            data: param,
            dataType: 'json',
            type: 'post',
            success: function(data) {
                if (data.user_login_status == 0) {
                    $.toast('未登录');
                } else {
                    $.toast(data.info);
                    if (data.status == 1) {
                        setTimeout(function() {
                            $.router.load(data.jump, true);
                        }, 2000);
                    }
                }
            }
        });
    });
    // refresh_dc_cart();
    // 获取实时的购物车信息
    function refresh_dc_cart() {
        var param = {
            'location_id': location_id,
            'table_menu_id': table_menu_id,
            'act': 'dc_res_cart_list',
        };

        $.ajax({
            url: DC_AJAX_URL,
            data: param,
            dataType: 'json',
            type: 'post',
            success: function(data) {
                if (data.list.length > 0) {
                    var list_html = '';
                    // var total_html = '';
                    var list = data.list;
                    for (i in list) {
                        list_html += '<li class="flex-box">' + 
                        '<p class="goods-name flex-1">'+list[i].name+'</p>' + 
                        '<p class="goods-num">x'+list[i].num+'</p>' + 
                        '<p class="goods-price" data-value="'+list[i].unit_price+'">'+list[i].format_unit_price+'</p>' + 
                        '</li>';
                    }
                    $('.res-goods-info .goods-list').html(list_html);
                    $('.count-price').html(data.format_total_price);
                    $('.count-price').attr('data-value', data.total_price);
                    $(".res-way").removeClass('active');
                    $('.j-open-menu').addClass('active');
                    $("#res-way").attr('value', 2);
                    $('.res-goods-info').show();
                } else { // 未点菜
                    $(".res-way").removeClass('active');
                    $('.j-only-res').addClass('active');
                    $("#res-way").attr('value', 1);
                    $('.res-goods-info').hide();
                }
                pay_price_format();
            }
        });
    }

    function pay_price_format() {
        var res_way = Number($('#res-way').val());
        if (res_way != 1 && res_way != 2) {
            res_way = 1;
        }
        item_price = Number(item_price);
        var rht = '预订定金';
        var rp = item_price;
        if (res_way == 1) {
            $('.res-pay').removeClass('disable');
        } else {
            var cp = Number($('.count-price').attr('data-value'));
            if (cp >= item_price) {
                rht = '预订菜金';
                rp = cp;
                $('.res-pay').removeClass('disable');
            } else {
                rht = '还差';
                rp = Math.round((item_price - cp) * 100)/100;
                $('.res-pay').addClass('disable');
            }
        }
        $('.res-content').html(rht);
        $('.res-price').html('&yen;' + rp);
    }
    pay_price_format();
});


function dc_change_res_num(id,count,num) {
    var menu_id=parseInt(id);
    var number=parseInt(num);
    var number_total=parseInt(count)+num;
    if(number_total<0){
        // $.toast("该商品数量无法再减少");
        return;
    }
    if(num==1){
        if(count==0){
            $(".goods-info[data_id='"+id+"']").find(".goods-num-box").removeClass("no-num").addClass("num");
            $item=$("<li class='flex-box b-line'  data_id='"+id+"'>"
                +"<p class='goods-name flex-1'>"+$(".goods-info[data_id='"+id+"']").find(".goods-name").html()+"</p>"
                +"<p class='edit-price' price='"+$(".goods-info[data_id='"+id+"']").find(".price").attr("price")+"'>"+$(".goods-info[data_id='"+id+"']").find(".price").html()+"</p>"
                +"<div class='goods-num-box flex-box'>"
                +"<a href='javascript:void(0);' class='min iconfont' data_id='{$item.menu_id}' onclick='dc_change_res_num("+id+","+number_total+",-1);'>&#xe915;</a>"
                +"<p class='goods-num' data_id='"+id+"'>"+number_total+"</p>"
                +"<a href='javascript:void(0);' class='iconfont plus' data_id='{$item.menu_id}' onclick='dc_change_res_num("+id+","+number_total+",1);'>&#xe685;</a>"
                +"</div></li>");
            $(".edit-list").prepend($item);
        }
    } else{
        if(count==1){
            $(".edit-list").find("li[data_id='"+id+"']").remove();
            $(".goods-info[data_id='"+id+"']").find(".goods-num-box").removeClass("num").addClass("no-num");
        }
    }
    $(".goods-num[data_id='"+id+"']").html(number_total);
    $(".min[data_id='"+id+"']").attr("onclick","dc_change_res_num("+id+","+number_total+",-1);");
    $(".plus[data_id='"+id+"']").attr("onclick","dc_change_res_num("+id+","+number_total+",1);");
    res_cart_total_price();
    
    var query=new Object();
    query.menu_id=menu_id;
    query.number=number;
    query.number_total=number_total;
    // query.tid=tid;
    query.table_menu_id = table_menu_id;
    query.location_id=location_id;
    // query.supplier_id=supplier_id;
    query.act='dc_add_cart';
    $.ajax({
        url:DC_AJAX_URL,
        data:query,
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status==1){
                
            }else{
                $.toast(data.info);
                setTimeout(function(){
                    window.location.reload();
                },500);
            }
        }
    });
}

//计算购物车商品价格
function res_cart_total_price(){
    var cart_num=0;
    var total_price=0;
    $(".edit-list").find("li[data_id]").each(function(){
        var num=parseInt($(this).find(".goods-num").html());
        var price=parseFloat($(this).find(".edit-price").attr("price"));
        cart_num+=num;
        total_price+=price*num;
    });

    $(".no-goods-btn").remove();
    $(".cart-btn").remove();
    if(total_price>0){
        total_price=total_price.toFixed(2);


        $(".no-goods-txt").hide();
        $(".cart-price").show();
        $(".send-price").show();
        $(".cart-price").html("￥"+total_price);
        $(".cart-ico").removeClass('j-empty-edit');
        $(".cart-ico").addClass('j-show-edit');
    }else{
        $(".no-goods-txt").show();
        $(".cart-price").hide();
        $(".send-price").hide();
        $(".cart-ico").removeClass('j-show-edit');
        $(".cart-ico").addClass('j-empty-edit');
        $(".cart-count").removeClass('active');
        $(".cart-mask").removeClass('active');
        setTimeout('$(".edit-list").empty()', 500);
        $(".goods-info").each(function(){
            $(this).find(".goods-num-box").addClass("no-num");
            $(this).find(".min").attr("onclick","dc_change_res_num("+$(this).attr("data_id")+",0,-1);");
            $(this).find(".plus").attr("onclick","dc_change_res_num("+$(this).attr("data_id")+",0,1);");
        });
    }
    
    $(".num-count").html(cart_num);
    if(cart_num==0){
        $(".num-count").addClass("hide");
    }else{
        $(".num-count").removeClass("hide");
    }
    if(total_price>0){  
        var btn=$("<a class='cart-btn j-close-edit'>确认</a>");
        btn.appendTo($(".cart-bar"));
    }
    else{
        var btn=$("<div class='no-goods-btn cart-btn'>未点菜</div>");
        btn.appendTo($(".cart-bar"));
    }
}

//清空购物车
function dc_res_cart_clear(){
    var query=new Object();
    query.table_menu_id=table_menu_id;
    query.location_id=location_id;
    query.act='dc_cart_clear';
    $.ajax({
        url:DC_AJAX_URL,
        data:query,
        type:'post',
        dataType:'json',
        success:function(data){
            if(data.status==1){
                $('.goods-list').html('');
                $('.res-goods-info').hide();
                $(".edit-list").empty();
                res_cart_total_price();
            }
        }
    });
}