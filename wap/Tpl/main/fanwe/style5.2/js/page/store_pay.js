


$(document).on("pageInit", "#store_pay_index", function(e, pageId, $page) {
    $("input[name='money']").val('');
    $("input[name='other_money']").val('');
    $('.discount_money .integer').text('0');
    $('.discount_money .point').text('00');
    $('.actual_pay .integer').text('0');
    $('.actual_pay .point').text('00');
	count_amount();
    init_money_change();
    order_submit();
});

// 监听输入金额的变动
function init_money_change(){

    $("input[name='money']").bind('input propertychange', function() {
        pre_check('money');
        count_amount();
    });

    $("input[name='other_money']").bind('input propertychange', function() {
        pre_check('other_money');
        count_amount();
    });
	$("input[name='all_score']").bind('change', function() {
		count_amount();
	});
}

function pre_check(type) {
    var money = $.trim($("input[name='"+type+"']").val());
    var pattern = /^(\d+(\.\d{0,2})?)/;
    if (money != '') {
        var re = money.toString().match(pattern);
        $("input[name='"+type+"']").val(re[1]);
    }
}


// 计算最终应支付的金额
function count_amount() {
    var final_pay = 0;
    var money = $.trim($("input[name='money']").val());
    var other_money = $.trim($("input[name='other_money']").val());
    /*var pattern = /^(\d+(\.\d{0,2})?)$/;
    if (money != '' && !pattern.test(money)) {
        $.toast('输入的金额格式错误');
        money = 0;
    }
    if (other_money != '' && !pattern.test(other_money)) {
        $.toast('输入的金额格式错误');
        other_money = 0;
    }*/
    // money = Number(money).toFixed(2);
    var pay_money = money - other_money;
    var discount = count_discount(pay_money);
    final_pay = money - discount;
	//积分抵现new
	if(pay_money>0){
		var query = new Object();
		query.pay_money=pay_money;
		query.final_pay=final_pay;
		if($('input[name="all_score"]:checked').length>0){
			var all_score=1;
		}else{
			var all_score=0;
		}
		query.all_score=all_score;
		query.act='score_purchase_count';
		$.ajax({ 
			url: custom_ajax_url,
			data:query,
			type: "POST",
			dataType: "json",
			success: function(data){
				if(data.score_purchase_switch==1&&data.exchange_money>0){
					$(".score_purchase").show();
					$(".score_purchase .u-score").text(data.user_score);
					$(".score_purchase .u-use-score").text(data.user_use_score);
					$(".score_purchase .u-money").text(data.exchange_money);
					if(all_score ==1){
						final_pay = final_pay - data.exchange_money;
					}
					count_amount_continuation(discount,final_pay);
				}else{
					$("input[name='all_score']").prop("checked",false);
					$(".score_purchase").hide();
					count_amount_continuation(discount,final_pay);
				}
			},
			error:function(ajaxobj)
			{
				$("input[name='all_score']").prop("checked",false);
				$(".score_purchase").hide();
				count_amount_continuation(discount,final_pay);
			}
		});
	}else{
		$("input[name='all_score']").prop("checked",false);
		$(".score_purchase").hide();
		count_amount_continuation(discount,final_pay);
	}//end
    
	
}
function count_amount_continuation(discount,final_pay) {
	var discount_integer, discount_point;
    discount = discount.toString();
    var i = discount.toString().indexOf('.');
    if (i == -1) {
        discount_integer = discount;
        discount_point = '00';
    } else {
        discount_integer = discount.substring(0, i);
        discount_point = discount.substring(i + 1);
    }
    var final_pay_integer, final_pay_point;
    final_pay_s = Math.round(final_pay * 100).toString();
    final_pay_point = final_pay_s.substr(-2);
    if (final_pay_point.length == 1) {
        final_pay_point = '0' + final_pay_point;
    }
    final_pay_integer = final_pay_s.substr(0, final_pay_s.length - 2);
    final_pay_integer = final_pay_integer ? final_pay_integer : 0;
    $('.discount_money .integer').text(discount_integer);
    $('.discount_money .point').text(discount_point);
    $('.actual_pay .integer').text(final_pay_integer);
    $('.actual_pay .point').text(final_pay_point);
}
// 计算支付金额的可优惠部分
function count_discount(pay_money) {
    var discount = 0;
    var limit = 0;
    $('.discount').each(function(index, domEle) {
        limit = $(domEle).find('.limit').text();
        if (limit <= pay_money) {
            discount += Number($(domEle).find('.amount').text());
        }
        
    });
    return discount.toFixed(2);
}

function order_submit(){
    $(".btn-con").bind("click",function(){
		if($('input[name="all_score"]:checked').length>0){
			var all_score=1;
		}else{
			var all_score=0;
		}
		var exchange_money= $(".score_purchase .u-money").text();
		
        var pay = $('.actual_pay .integer').text();
        var point = $('.actual_pay .point').text();
        if ((pay != 0 || point !=0)||(pay == 0 && point ==0 && all_score==1 && exchange_money>0)) {
            var query=$("#submit_dp").serialize();
            var url=$("#submit_dp").attr('action');
            $.ajax({ 
                url: url,
                data:query,
                type: "POST",
                dataType: "json",
                success: function(data){
                    if (data.user_login_status == 0) {
                        if (app_index == 'app') {
                            App.login_sdk();
                        } else {
                            $.router.load(data.jump, true);
                        }
                    } else {
                        if (data.status == 1) {
                            $.router.load(data.jump, true);
                        } else {
                            $.toast(data.info);
                            if (data.jump) {
                                setTimeout(function() {
                                    $.router.load(data.jump, true);
                                }, 2000);
                            }
                        }
                    }
                },

            });
        } else if ($('input[name=money]').val() != '') {
            $.toast('输入的金额格式错误');
        } else {
            $.toast('请输入消费金额');
        }
        return false;

    });
}