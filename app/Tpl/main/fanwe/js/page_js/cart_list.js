$(document).ready(function(){
	load_cart_list();
});



function init_cart_ui()
{
	$(".goods-item .check-num .minus,.goods-item .check-num .add").hover(function(){
		$(this).addClass("hover");
	},function(){
		$(this).removeClass("hover");
	});

	//删除的绑定
	$(".goods-item .check-edit a").bind("click",function(){
		var id = $(this).attr("rel");
		$.showConfirm("确定要从购物车中移除该项目吗？",function(){
			//执行删除
			del_cart(id);
		});
	});

	//计数统计
	$(".goods-item .check-num i.minus").bind("click",function(){
		var id = $(this).attr("rel");
		var num = parseInt(jsondata[id].number);
		if(num-1<=0)
		{
			$.showConfirm("确定要从购物车中移除该项目吗？",function(){
				//执行删除
				del_cart(id);
			});
		}
		else
		{
			num = num - 1;
			recount_total(id,num);
		}
	});
	$(".goods-item .check-num i.add").bind("click",function(){
		var id = $(this).attr("rel");
		var num = parseInt(jsondata[id].number);
		if(num+1>9999)
			num = 9999;
		else
			num = num + 1;
		recount_total(id,num);
	});
	$(".goods-item .check-num .num_ipt").bind("blur",function(){
		var id = $(this).attr("rel");
		var num = 1;
		if($.trim($(this).val())!=""&&!isNaN($(this).val()))
		{
			num = parseInt($(this).val());
		}
		if(num<=0)num=1;
		recount_total(id,num);
	});
	$(".goods-item .check-num .num_ipt").bind("focus",function(){
		$(this).select();
	});

	//清空购物车
	$(".check-goods-info .remove-select").bind("click",function(){
		if(ids && ids.length > 0){
			$.showConfirm("确定要删除选中的商品吗？",function(){
				//执行删除
				del_cart(ids);
			});
		}else{
			$.showErr("未选择商品");
		}

	});
	$(".check-sub").bind("click",function(){
		var len=$(".ui-checkbox[is_sub='1'] input:checked").length;
		if(len>0){
			$("form[name='cart_form']").submit();
		}else{
			$.showErr("未选择商品");
		}
		
	});
	//提交购物车
	$("form[name='cart_form']").bind("submit",function(){
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status==1)
				{
					location.href = obj.jump;
				}
				else if(obj.status==-1)
				{
					ajax_login();
				}
				else
				{
					$(".cart_table tr").removeClass("warning");
					$(".cart_table tr[rel='"+obj.id+"']").addClass("warning");
					$(".cart_table tr[rel='"+obj.id+"']").stopTime();
					$.showErr(obj.info,function(){
						$(".cart_table tr[rel='"+obj.id+"']").oneTime(1500,function(){
							$(this).removeClass("warning");
						});
					});
				}
			}
		});
		return false;
	});
}

function recount_total(id,num)
{
	jsondata[id].number = parseInt(num);
	jsondata[id].total_price = jsondata[id].number * parseFloat(jsondata[id].unit_price);
	var total_price = 0;
	$.each(jsondata,function(id,row){
		$(".goods-item[rel='"+row.id+"']").find(".num_ipt").val(parseInt(row.number));
		var goods_total_price=Math.round(parseFloat(row.total_price)*100)/100;
		$(".goods-item[rel='"+row.id+"']").find(".check-count span").html(goods_total_price.toFixed(2));
		
		//$(".cart_table tr[rel='"+row.id+"']").find(".num_ipt").val(parseInt(row.number));
		//$(".cart_table tr[rel='"+row.id+"']").find(".w_total span").html(total_price);
	});
	//$("#sum").html(Math.round(total_price*100)/100);
	//$(".check-all-count em").html((Math.round(total_price*100)/100).toFixed(2));
	check_total();
}
function all_check() {
	//全部选中
	$("label.ui-checkbox[is_all='1'] input").bind("checkon", function() {
		$(".ui-checkbox[is_item='1'] input").each(function(i, o) {
			$(this).attr("checked", true);
			$(this).parents('.goods-item').addClass('active');
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		$(".ui-checkbox[is_all='1'] input").each(function(i, o) {
			$(this).attr("checked", true);
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		check_num();
	});
	//全部取消
	$("label.ui-checkbox[is_all='1'] input").bind("checkoff", function() {
		$(".ui-checkbox[is_item='1'] input").each(function(i, o) {
			$(this).attr("checked", false);
			$(this).parents('.goods-item').removeClass('active');
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		$(".ui-checkbox[is_all='1'] input").each(function(i, o) {
			$(this).attr("checked", false);
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		check_num();
	});
	//商家全选
	$("label.ui-checkbox[is_main='1'] input").bind("checkon", function() {
		var shop_id = $(this).parent().attr("shop_id");
		$(".ui-checkbox input[shop_id='" + shop_id + "']").each(function(i, o) {
			$(this).attr("checked", true);
			$(this).parents('.goods-item').addClass('active');
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		check_num();
	});
	//商家全取消
	$("label.ui-checkbox[is_main='1'] input").bind("checkoff", function() {
		var shop_id = $(this).parent().attr("shop_id");
		$(".ui-checkbox input[shop_id='" + shop_id + "']").each(function(i, o) {
			$(this).attr("checked", false);
			$(this).parents('.goods-item').removeClass('active');
			$(this).parent().ui_checkbox({
				refresh : true
			});
		});
		check_num();
	});
	//单个选中关联商家全选
	$("label.ui-checkbox[is_sub='1'] input").bind("checkon", function() {
		var shop_id = $(this).attr("shop_id");
		$(this).parents('.goods-item').addClass('active');
		var total_count = $(".ui-checkbox input[shop_id='" + shop_id + "']").length;
		var all_count = $(".ui-checkbox[is_sub='1'] input").length;
		var count = 0;
		var all_check =0;
		//商家
		$(".ui-checkbox input[shop_id='" + shop_id + "']").each(function(i, o) {
			if($(this).attr("checked")) {
				count++;
			}
		});
		if(total_count == count) {
			$("label.ui-checkbox[shop_id='" + shop_id + "'] input").attr("checked", true);
			$("label.ui-checkbox[shop_id='" + shop_id + "']").ui_checkbox({
				refresh : true
			});
		}
		//全部
		$(".ui-checkbox[is_sub='1'] input").each(function(i, o) {
			if($(this).attr("checked")) {
				all_check++;
			}
		});
		if(all_count == all_check) {
			$("label.ui-checkbox[is_all='1'] input").attr("checked", true);
			$("label.ui-checkbox[is_all='1']").ui_checkbox({
				refresh : true
			});
		}
		check_num();
	});
	//单个取消关联商家全选
	$("label.ui-checkbox[is_sub='1'] input").bind("checkoff", function() {
		var shop_id = $(this).attr("shop_id");
		$(this).parents('.goods-item').removeClass('active');
		var total_count = $(".ui-checkbox input[shop_id='" + shop_id + "']").length;
		var all_count = $(".ui-checkbox[is_sub='1'] input").length;
		var count = 0;
		var all_check =0;
		//商家
		$(".ui-checkbox input[shop_id='" + shop_id + "']").each(function(i, o) {
			if($(this).attr("checked")) {
				count++;
			}
		});
		if(count < total_count) {
			$("label.ui-checkbox[shop_id='" + shop_id + "'] input").attr("checked", false);
			$("label.ui-checkbox[shop_id='" + shop_id + "']").ui_checkbox({
				refresh : true
			});
		}
		//全部
		$(".ui-checkbox[is_sub='1'] input").each(function(i, o) {
			if($(this).attr("checked")) {
				all_check++;
			}
		});
		if(all_check < all_count) {
			$("label.ui-checkbox[is_all='1'] input").attr("checked", false);
			$("label.ui-checkbox[is_all='1']").ui_checkbox({
				refresh : true
			});
		}
		check_num();
	});
	//单个选中关联全部全选
	//单个选中关联商家全选
	$("label.ui-checkbox[is_item='1'] input").bind("checkon", function() {
		var shop_id = $(this).attr("shop_id");
		$(this).parents('.goods-item').addClass('active');
		var all_count = $(".ui-checkbox[is_item='1'] input").length;
		var all_check =0;
		//全部
		$(".ui-checkbox[is_item='1'] input").each(function(i, o) {
			if($(this).attr("checked")) {
				all_check++;
			}
		});
		if(all_count == all_check) {
			$("label.ui-checkbox[is_all='1'] input").attr("checked", true);
			$("label.ui-checkbox[is_all='1']").ui_checkbox({
				refresh : true
			});
		}
		check_num();
	});
	//单个取消关联全部全选
	$("label.ui-checkbox[is_item='1'] input").bind("checkoff", function() {
		var shop_id = $(this).attr("shop_id");
		$(this).parents('.goods-item').removeClass('active');
		var all_count = $(".ui-checkbox[is_item='1'] input").length;
		var all_check =0;
		//全部
		$(".ui-checkbox[is_item='1'] input").each(function(i, o) {
			if($(this).attr("checked")) {
				all_check++;
			}
		});
		if(all_check < all_count) {
			$("label.ui-checkbox[is_all='1'] input").attr("checked", false);
			$("label.ui-checkbox[is_all='1']").ui_checkbox({
				refresh : true
			});
		}
		check_num();
	});
}
var ids = new Array();
//选中商品个数
function check_num() {
	//console.log(0);
	ids = new Array();
	var select_num=0;
	$(".ui-checkbox[is_sub='1'] input").each(function(i, o) {
		if($(this).attr("checked")) {
			ids.push($(this).val());
			select_num++;
		}
	});
	$(".select-num").html(select_num);
	check_total();
}
//选中商品金额
function check_total() {
	var total_price = 0;
	$.each(jsondata,function(id,row){
		$(".goods-item[rel='"+row.id+"']").find(".num_ipt").val(parseInt(row.number));
		var goods_total_price=Math.round(parseFloat(row.total_price)*100)/100;
		$(".goods-item[rel='"+row.id+"']").find(".check-count span").html(goods_total_price.toFixed(2));
		if($(".goods-item[rel='"+row.id+"']").find("input[type='checkbox']").attr("checked")){
			total_price+=parseFloat(row.total_price);
		}
		//$(".cart_table tr[rel='"+row.id+"']").find(".num_ipt").val(parseInt(row.number));
		//$(".cart_table tr[rel='"+row.id+"']").find(".w_total span").html(total_price);
	});
	$(".check-all-count em").html((Math.round(total_price*100)/100).toFixed(2));
	//$(".select-num").html(select_num);
}
function load_cart_list()
{
		$("#cart_list").html("<div class='loading'></div>");
		var query = new Object();
		query.act = "load_cart_list";
		$.ajax({
			url:AJAX_URL,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				$("#cart_list").html(obj.html);
				init_ui_textbox();
				init_ui_checkbox();
				init_ui_button();
				init_cart_ui();
				all_check();
				check_num();
				submit_bar();
			}
		});

}
function submit_bar() {
	function submit_bar_scroll() {
		var w_height = $(window).height();
		var ct_height=$("#cart_list").offset().top;
		var c_height =$("#cart_list").height();
		if (c_height+ct_height>w_height) {
			$(".cart-list-bd ul").addClass('active');
		}
		if ($(window).scrollTop()>ct_height + c_height - w_height) {
			$(".cart-list-bd ul").removeClass('active');
		}else {
			$(".cart-list-bd ul").addClass('active');
		}
	}
	submit_bar_scroll();
	$(window).scroll(function() {
		submit_bar_scroll();
		console.log($(window).scrollTop());
	});
	window.onresize = function(){
		submit_bar_scroll();
	}
}