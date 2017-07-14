function check_selfyouhui(){
	$("input[name='name']").bind("change keyup",function(){
		var length=$(this).val().length;

		$("#name_count").html(length+"/15");
	});


	$("#seleyouhui_btn").bind("click",function() {
		var name = $.trim( $("input[name='name']").val() ); //优惠券名称
		var begin_time = $.trim( $("input[name='begin_time']").val() ); //发放开始时间
		var end_time = $.trim( $("input[name='end_time']").val() ); //发放结束时间
		var youhui_value = $.trim( $("input[name='youhui_value']").val() ); //面额
		var total_num = $.trim( $("input[name='total_num']").val() ); //发放总数量
		var user_limit = $.trim( $("input[name='user_limit']").val() ); //每人最多可领取
		var user_everyday_limit = $.trim( $("input[name='user_everyday_limit']").val() ); //每天最多只能领取
		var start_use_price = $.trim( $("input[name='start_use_price']").val() ); //使用限制（订单满多少可用）
		var valid_type = $.trim( $("input[name='valid_type']").val() ); //有效期设置
		var use_begin_time = $.trim( $("input[name='use_begin_time']").val() ); //有效期开始时间
		var use_end_time = $.trim( $("input[name='use_end_time']").val() ); //有效期截止时间
		var expire_day = $.trim( $("input[name='expire_day']").val() ); //有效天数

		var youhui_value_rul = /^(?!0)\d{1,3}$/;
		var integer_rul = /^[0-9]\d*$/;

		if(name.length > 15){
			alert('优惠券名称不能超过15个字！');
			return false;
		}
		if(!youhui_value_rul.test(youhui_value)){
			alert('面额只能输入1-999的之间的整数！');
			return false;
		}
		if(start_use_price && !integer_rul.test(start_use_price)){
			alert('使用限制金额必需为非负整数！');
			return false;
		}
		if(( user_limit && !integer_rul.test(user_limit) ) || (user_everyday_limit && !integer_rul.test(user_everyday_limit))){
			alert('领券限制设置必需为非负整数！');
			return false;
		}
		if(total_num && !integer_rul.test(total_num)){
			alert('发放总数量要为非负整数！');
			return false;
		}
		if(valid_type == 1){
			//领券后固定有效天数
			if(!integer_rul.test(expire_day)) {
				alert('请输入整数天数！');
				return false;
			}
		}
	});
}


$(document).ready(function(){
	$("input[name=valid_type]").click(function(){
		var valid_type = $("input[name=valid_type]:checked").val();
		if(valid_type==2){
			$(".valid_time").show();
			$(".valid_day").hide();
			$("#valid_day").attr("value","");
		}
		if(valid_type==1){
			$(".valid_time").hide();
			$(".valid_day").show();
			$("#use_begin_time").attr("value","");
			$("#use_end_time").attr("value","");
		}
	});
	check_selfyouhui();
});
