/**
 * 商品
 */
$(document).ready(function(){
	
var require_str='<span class="require_title">*</span>';
$(".row_left_require").prepend(require_str);
	
$(".first_cate li").first().addClass('active');
var first_cate_id = $(".first_cate li.active").attr('data_id');
syn_second_cate(first_cate_id,is_shop);	
$(".second_cate li").live('click',function(){

	$(this).addClass('active').siblings().removeClass('active');
});

$(".first_cate li").click(function(){
	if(!$(this).hasClass('active')){
		$(this).addClass('active').siblings().removeClass('active');
		var first_cate_id = $(".first_cate li.active").attr('data_id');
		syn_second_cate(first_cate_id,is_shop);
	}

});
init_next_button();
cate_del_hide_show();
var buy_type = $("input[name='buy_type']").val();
var id = parseInt($("input[name='id']").val());
if(id > 0 && buy_type==0){
	set_price_profit();
}


$("input[name='current_price']").bind('keyup',function(){
	init_balance_price();
	set_price_profit($(this));
	get_user_discount_price();
});

$("input[name='publish_verify_balance']").live('keyup',function(){
	init_balance_price();
	set_price_profit();
	
});


$("input[name='balance_price'] , input[name='origin_price']").bind('keyup',function(){
	set_price_profit($(this));
});


$("input[name^='stock_cfg_num']").live('keyup',function(){
	check_stock_cfg_num($(this)); 
	init_max_bought();
});

$("input[name='max_bought']").live('keyup',function(){
	check_stock_cfg_num($(this)); 
});



$("input[name='allow_user_discount']").click(function(){
	init_user_discount();
});

$(".add_cate").click(function(){
	
	var first_cate = $(".first_cate").find("li.active").text();
	var second_cate = $(".second_cate").find("li.active").text();
	var first_cate_id = $(".first_cate").find("li.active").attr('data_id');
	var second_cate_id = $(".second_cate").find("li.active").attr('data_id');
	
	if(first_cate==''){
		alert('请选择一级分类');
		return false;
	}
   
	var delete_button = '<span class="selected_cate_delete">删除</span>'

	if(is_shop == 1){  //商城
		var selected_shop_cate_id = $(".selected_shop_cate input[name='shop_cate_id']").val();
		var selected_shop_cate_arr=selected_shop_cate_id.split(',');
	    var second_shop_cate_id_str = '';
	    
		if(second_cate==''){
			second_cate_id=first_cate_id;			
		}else{
			second_cate = " > " + second_cate
		}
		
		if(selected_shop_cate_id!=''){

			if($.inArray(second_cate_id,selected_shop_cate_arr) ==-1){
				selected_shop_cate_arr.push(second_cate_id);
				second_shop_cate_id_str = selected_shop_cate_arr.join(',');
			}else{
				alert('该分类已添加');
				return false;
			}
			
		}else{
			second_shop_cate_id_str = second_cate_id;
		}


		
		var select_cate_str='<div class="select_item shop_id" data_id="'+second_cate_id+'">' + first_cate + second_cate + delete_button +'</div>';
		var select_cate_str_two='<div class="select_item" data_id="'+second_cate_id+'">' + first_cate + second_cate +'</div>';
		
		$(".selected_shop_cate").append(select_cate_str).find("input[name='shop_cate_id']").val(second_shop_cate_id_str);
		
	}else{ //团购
		var selected_tuan_first_cate_id = $(".selected_shop_cate input[name='cate_id']").val();	
		var selected_tuan_second_cate_id = $(".selected_shop_cate input[name='second_cate_id']").val();
		
		var selected_tuan_first_cate_arr=selected_tuan_first_cate_id.split(',');		
		var selected_tuan_second_cate_arr=selected_tuan_second_cate_id.split(',');
		//团购一级分类

	    var second_tuan_first_cate_id_str = '';
	    if(second_cate==''){
			if(selected_tuan_first_cate_id!=''){
	
				if($.inArray(first_cate_id,selected_tuan_first_cate_arr) ==-1){
					selected_tuan_first_cate_arr.push(first_cate_id);
					second_tuan_first_cate_id_str = selected_tuan_first_cate_arr.join(',');
				}else{
					alert('该分类已添加');
					return false;
				}
				
			}else{
				second_tuan_first_cate_id_str = first_cate_id;
			}
			
			
			var select_cate_str='<div class="select_item tuan_first_id" data_id="'+first_cate_id+'">' + first_cate + delete_button +'</div>';
			var select_cate_str_two='<div class="select_item" data_id="'+first_cate_id+'">' + first_cate +'</div>';
			$(".selected_shop_cate").append(select_cate_str).find("input[name='cate_id']").val(second_tuan_first_cate_id_str);
	
	    }else{
			//团购二级分类
		    var second_tuan_second_cate_id_str = '';
			if(selected_tuan_second_cate_id!=''){

				if($.inArray(second_cate_id,selected_tuan_second_cate_arr) ==-1){
					selected_tuan_second_cate_arr.push(second_cate_id);
					second_tuan_second_cate_id_str = selected_tuan_second_cate_arr.join(',');
				}else{
					alert('该分类已添加');
					return false;
				}
				
			}else{
				second_tuan_second_cate_id_str = second_cate_id;
			}

			var select_cate_str='<div class="select_item tuan_second_id" pid="'+first_cate_id+'" data_id="'+second_cate_id+'">' + first_cate + ' > ' +second_cate + delete_button +'</div>';
			var select_cate_str_two='<div class="select_item" data_id="'+second_cate_id+'">' + first_cate + ' > ' + second_cate +'</div>';
			$(".selected_shop_cate").append(select_cate_str).find("input[name='second_cate_id']").val(second_tuan_second_cate_id_str);
			
	    }
		cate_del_hide_show();
	}
	
	$(".selected_shop_cate_two").append(select_cate_str_two);
	get_shop_brand();
	init_next_button();
	
});

$(".selected_cate_delete").live('click',function(){

	var index = $(this).parents('.selected_shop_cate').find(".select_item").index($(this).parents('.select_item'));
	$(this).parents('.select_item').remove();
	$(".selected_shop_cate_two").find(".select_item").eq(index).remove();
	cate_del_hide_show();
	 syn_shop_cate();
	 get_shop_brand();
	 init_next_button();

});

$(".go_next_step").live('click',function(){
	if(is_shop == 1){  //商城
		var selected_shop_cate_id = $(".selected_shop_cate input[name='shop_cate_id']").val();
		if(selected_shop_cate_id==''){
			alert('请选择分类');
			return false;
		}
	}else{ //团购
		var selected_tuan_cate_id = $(".selected_shop_cate input[name='cate_id']").val();
		var selected_second_tuan_cate_id = $(".selected_shop_cate input[name='second_cate_id']").val();
		
		if(selected_tuan_cate_id=='' && selected_second_tuan_cate_id==''){
			alert('请选择分类');
			return false;
		}
	}
	


	if(deal_type==2){
		var supplier_id = $("select[name='supplier_id']").val();
		if(supplier_id==0){
			alert('请选择商户');
			return false;
		}
		
		var location = new Array();
		$("input[name^='location_id']:checked").each(function(i,obj){
			var location_id = $(obj).val();
			location.push(location_id);
		});
		if(location.length==0){
			alert('必须选择一家门店');
			return false;
		}

	}
	
	$(".shop_box_one").hide();
	$(".shop_box_two").show();
});

$(".go_first_step").click(function(){
	
	$(".shop_box_one").show();
	$(".shop_box_two").hide();
});


get_user_discount_price();
init_user_discount();

$(".syn_price_setting").live('click',function(){

	var p_ele = $(this).parents(".syn_box");
	var syn_price= $(p_ele).find("input[name='syn_price']").val();
	var syn_add_balance_price= $(p_ele).find("input[name='syn_add_balance_price']").val();
	var syn_stock_cfg= $(p_ele).find("input[name='syn_stock_cfg']").val();

	if(syn_price == '' && syn_add_balance_price == '' && syn_stock_cfg == '' ){		
		alert('请输入预设值');
	}else{
	
		if(syn_price){
			if(isNaN(syn_price)){
				alert('请输入有效递增销售价');
				return false;
			}else{
				$("input[name^='deal_attr_price']").val(syn_price);
			}
			
		}
		if(syn_add_balance_price){
			if(isNaN(syn_add_balance_price)){
				alert('请输入有效递增成本价');
				return false;
			}else{
				$("input[name^='deal_add_balance_price']").val(syn_add_balance_price);	
			}	
		}
		if(syn_stock_cfg){
			var ex = /^\d+$/;
			if(isNaN(syn_stock_cfg) || syn_stock_cfg < 0 || !ex.test(syn_stock_cfg)){
				alert('请输入有效库存');
				return false;
			}else{
				$("input[name^='stock_cfg_num']").val(syn_stock_cfg);
				$(".attr_box .error_row").remove();
				init_max_bought();
			}
			
		}
		 init_add_balance_price();
			
	}
	
});
init_fx_unit();
$("input[name='fx_salary_type']").live('click',function(){
	init_fx_unit();
});


init_is_fx();
$("input[name='is_allow_fx']").live('click',function(){
	init_is_fx();
});

$("select[name='supplier_id']").live("change",function(){
	supplier_id_change();
	init_supplier_location();
	load_delivery_type();
	load_carriage_template_selectbox();
	load_delivery_box();

});

$("input[name^='location_id']").live('click',function(){
	init_location_name();
});

$(".cancel_deal").bind('click',function(){
	var type = $("input[name='type']").val();
	location.href = cancel_jump ;
});

init_coupon_setting();
$("input[name='is_coupon'],input[name='coupon_time_type']").click(function(){
	init_coupon_setting();
});

init_balance_price();

$("input[name='deal_attr_price[]']").live('keyup',function(){
	var price = $(this).val();
	if(isNaN(price)){
		var error_tip= '请输入有效递增销售价';
	}else{
		var error_tip= '';
	}	
	
	show_error_tip($(this),error_tip);
	init_add_balance_price();
});

$("input[name='deal_add_balance_price[]']").live('keyup',function(){
	var price = $(this).val();
	if(isNaN(price)){
		var error_tip= '请输入有效递增成本价';
	}else{
		var error_tip= '';
	}		
	show_error_tip($(this),error_tip);

});


});


function check_stock_cfg_num(obj){
	var syn_stock_cfg = $(obj).val();
	if(!isIntNum(syn_stock_cfg)){
		var error_tip = '请输入有效库存';
	}else{
		var error_tip = '';
	}
	show_error_tip($(obj),error_tip);
}

function check_attr()
{
	var attr_select = $(".attr_select_box");
	if(attr_select.length>0)
	{
		for(i=0;i<attr_select.length;i++)
		{
			if($(attr_select[i]).val()=='')
			{
				alert(LANG['ATTR_SETTING_EMPTY']);
				return false;
			}
		}
	}	
	return true;
}
function init_dealform()
{
	$("select[name='is_refund']").bind("change",function(){
		init_refund();
	});

	$("input[name='supplier_key_btn']").bind("click",function(){
		search_deal_supplier();
	});

	$("form").attr("on_submit","check_attr");
	//绑定副标题20个字数的限制
	$("input[name='sub_name']").bind("keyup change",function(){
		if($(this).val().length>20)
		{
			$(this).val($(this).val().substr(0,20));
		}
	});

	init_refund();
	$("select[name='cate_id']").bind("change",function(){
		init_sub_cate();
	});
	init_sub_cate();


	init_supplier_location();//初始化商户子门店

	//绑定团购商品类型，显示属性
	$("select[name='deal_goods_type']").bind("change",function(){
		load_attr_html();
	});



	load_attr_html();

	init_delivery_type_radiobox();//配送方式类型
	load_delivery_box();//配送相关模块




	$("select[name='carriage_template_id']").bind("change",function () {
		load_weight();
        ajax_carriage_tempate();
	});


}
function ajax_carriage_tempate(){
    var carriage_template_id=parseInt($("select[name=carriage_template_id]").val());
    $("#J_hintDefault").hide();
    $("#deliver-warn").hide();
    if(!carriage_template_id)return;
    $.ajax({
        url:carriage_detail_url,
        data:{id:carriage_template_id},
        dataType:"json",
        type:"post",
        success:function(da){
            show_carriage_detail_by_data(da);
        }
    });

}
function show_carriage_detail_by_data(da){
    if(da.address){
        $("#carriage_teplate_address").html(da.address);
        $("#deliver-warn").show();
    }
    if(da.carriage_template_detail){
        var detail=da.carriage_template_detail;
        var type=$("select[name=carriage_template_id]").find("option:selected").attr('data-valuation-type');
        var text=da.delivery_info + "<br/>";
        if(type==2){
            text +="默认运费："+detail['express_start']+"千克内"+detail['express_postage']+"元，每增加"+detail['express_plus']+"千克，加"+detail['express_postage_plus']+"元"
        }else{
            text +="默认运费："+detail['express_start']+"件内"+detail['express_postage']+"元，每增加"+detail['express_plus']+"件，加"+detail['express_postage_plus']+"元"
        }
        $("#carriage_default_carriage").html(text);
        $("#J_hintDefault").show();
    }
}


/**
 * 加载是否允许自提
 */
function load_is_pick()
{
	var delivery_type = $('input[name="delivery_type"]:checked').val();
	var supplier_id = $("select[name='supplier_id']").val();
	var buy_type = $("select[name='buy_type']").val();
	if(buy_type == 1){ //积分商品商户ID 强制为0
		supplier_id = 0;
	}
	//只有物流配送同时为商户的时候才会有自提
	if(delivery_type==1 && supplier_id!=0)
	{
		$("#is_pick").show();
	}
	else
	{
		$("#is_pick").hide();
		$("select[name='is_pick']").val(0);
	}
}



function init_refund()
{
	if($("select[name='is_coupon']").length>0)
	{
		var is_refund = $("select[name='is_refund']").val();
		var is_coupon = $("select[name='is_coupon']").val();
		if(is_coupon==1&&is_refund==1)
		{
			$("#coupon_refund").show();
		}
		else
		{
			$("#coupon_refund").hide();
		}
		
	}	
}



function load_attr_html()
{
		var deal_goods_type = $("select[name='deal_goods_type']").val();
		var id = $("input[name='id']").val();
		var edit_type = $("input[name='edit_type']").val();

		if(deal_goods_type>0)
		{
			var query = new Object();
			query.id = id;
			query.deal_goods_type = deal_goods_type;
			query.edit_type = edit_type;
			query.ajax = 1;
			$.ajax({ 
				url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=attr_html", 
				data: query,
				success: function(obj){

					$("#deal_attr").html(obj).show();

					load_attr_stock();
				}
			});
		}
		else
		{
			$("#deal_attr").hide().html("");
			load_attr_stock();
		}
}


function load_weight()
{
	var delivery_type = $('input[name="delivery_type"]:checked').val();
	var valuation_type = $("select[name='carriage_template_id']").find("option:selected").attr('data-valuation-type');
		if((delivery_type==1 || delivery_type==3) && valuation_type==2)
		{
			$(".weight_box").show();
		}
		else
		{
			$("input[name='weight']").val(0);
			$(".weight_box").hide();
		}
}
function quanx(obj){
	if($(obj).attr("checked")){
		$(".deal_attr_stock").attr("checked", true);
	}else{
		$(".deal_attr_stock").attr("checked", false);
	}
	load_attr_stock("1");
	
}
//加载属性库存表
function load_attr_stock()
{
	var buy_type = $("input[name='buy_type']").val();
	var supplier_id = $("input[name='supplier_id']").val();
	if(buy_type==1){
		$(".score_box").css('display','block');
		return false;	
	}else{
		$(".deal_box").css('display','block');
		$(".deal_box_tr").css('display','table-row');
	}
	
	var attr_row_arr = new Array();
	$("#deal_attr .attr_row").each(function(){
		var data_name = $(this).find('.attr_name').attr('data_name');
		var attr_row = new Object();
		attr_row.name = data_name;
		var attr_row_unit = new Array();

		$(this).find('.attr_content .attr_item').each(function(index,obj_attr){
			var attr_value_obj = $(obj_attr).find(".attr_value");
			var attr_row_data = new Object();
			if($(attr_value_obj).hasClass("textbox")){
				var attr_row_value = $(attr_value_obj).val();
				var deal_attr_id =  $(attr_value_obj).attr('deal_attr_id');

			}else{
				var attr_row_value = $(attr_value_obj).find("option:selected").val();
				var deal_attr_id =  $(attr_value_obj).attr('deal_attr_id');
			}
			
			attr_row_data.attr_name = attr_row_value;
			attr_row_data.key = deal_attr_id;
			
			if(attr_row_value!=''){
				attr_row_unit.push(attr_row_data);
			}
			
		});
		attr_row.attr = attr_row_unit;
		if(attr_row_unit.length > 0){
			attr_row_arr.push(attr_row);	
		}
		

	});
	var edit_type = $("input[name='edit_type']").val();
	var query = new Object();
	query.ajax = 1;
	query.attr_row_arr = attr_row_arr;
	query.deal_id = $("input[name='id']").val();
	if($("select[name='supplier_id']").length >0){
		query.supplier_id = $("select[name='supplier_id']").val();
	}else{
		query.supplier_id = $("input[name='supplier_id']").val();
	}	
	query.publish_verify_balance = $("input[name='publish_verify_balance']").val();
	query.edit_type = edit_type;
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=attr_table", 
		data: query,
		success: function(obj){
			$("#stock_table").html(obj);	
			var deal_goods_type = $("select[name='deal_goods_type']").val();
			if(deal_goods_type==0){
				$(".attr_box").hide();
			}else{
				if(obj){
					$(".attr_box").show();
				}else{
					$(".attr_box").hide();
				}
				
			}
			init_max_bought();
		}
	});
		
}


function bianh(obj){
	if(parseInt($(obj).val())>-1){
		$(obj).val(parseInt($(obj).val()));
	}else{
		$(obj).val("-1");
	}
}
//检测当前行的配置
function check_same(obj)
{
	var selectbox = $(obj).parent().parent().find("select");
	var row_value = '';
	for(i=0;i<selectbox.length;i++)
	{
		if($(selectbox[i]).val()!='')
			row_value += $(selectbox[i]).val();
		else
		{
			$(obj).parent().parent().find("input[name='stock_cfg[]']").val("");
			return;
		}
	}
	//开始检测是否存在该配置
	var stock_cfg = $("input[name='stock_cfg[]']");
	for(i=0;i<stock_cfg.length;i++)
	{
		if(row_value==$(stock_cfg[i]).val()&&row_value!=''&&stock_cfg[i]!=obj)
		{
			alert(LANG['SPEC_EXIST']);
			$(obj).parent().parent().find("input[name='stock_cfg[]']").val("");
			$(obj).val("");
			return;
		}
	}
	$(obj).parent().parent().find("input[name='stock_cfg[]']").val(row_value);
}



function init_sub_cate()
{
	var cate_id = $("select[name='cate_id']").val();
	var id = $("input[name='id']").val();

	if(cate_id>0)
	{
		var query = new Object();
		query.ajax = 1;
		query.cate_id = cate_id;
		query.id = id;
		query.edit_type = $("input[name='edit_type']").val();
		
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_sub_cate", 
			data: query,
			dataType: "json",
			success: function(obj){
				if(obj.status)
				{
					$("#sub_cate_box").show();
					$("#sub_cate_box").find(".item_input").html(obj.data);
				}
				else
				{
					$("#sub_cate_box").hide();
				}
				
			},
			error:function(ajaxobj)
			{
				if(ajaxobj.responseText!='')
				alert(ajaxobj.responseText);
			}
		
		});
	}
	else
	{
		$("#sub_cate_box").hide();
		$("#sub_cate_box").find(".item_input").html("");
	}
}


function init_supplier_location()
{
	
	if($("select[name='supplier_id']").length >0){
		var supplier_id = $("select[name='supplier_id']").val();
	}else{
		var supplier_id = $("input[name='supplier_id']").val();
	}
	var id = $("input[name='id']").val();	

	if(supplier_id>0)
	{		
		var query = new Object();
		query.id = id;
		query.supplier_id = supplier_id;
		query.edit_type = $("input[name='edit_type']").val();
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=load_supplier_location", 
			data: query,
			dataType: "json",
			success: function(obj){
				if(obj.status)
				{
					$("#supplier_location").html(obj.data).show();
					init_location_name();
				}
				else
				{
					$("#supplier_location").hide();
					init_location_name();
				}
				
			},
			error:function(ajaxobj)
			{
				if(ajaxobj.responseText!='')
				alert(ajaxobj.responseText);
			}
		
		});
	}
	else
	{
		$("#supplier_location").html("").hide();
		init_location_name();
	}
	
}


function search_deal_supplier()
{
	var key = $("input[name='supplier_key']").val();
	if($.trim(key)=='')
	{
		alert(INPUT_KEY_PLEASE);
	}
	else
	{
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"=SupplierLocation&"+VAR_ACTION+"=search_supplier", 
			data: "ajax=1&key="+key,
			type: "POST",
			success: function(obj){
				$("#supplier_list").html(obj);
				$("select[name='supplier_id']").live("change",function(){
					supplier_id_change();
					init_supplier_location();
					load_delivery_type();
					load_carriage_template_selectbox();
					load_delivery_box();
				});
			}
		});
	}
}
//点击重新加载运费模板
function load_carriage_template_selectbox(){
	var supplier_id = $("select[name='supplier_id']").val();
	var delivery_type = $("input[name='delivery_type']:checked").val();
	var buy_type = $("select[name='buy_type']").val();
	if(buy_type == 1){ //积分商品商户ID 强制为0
		supplier_id = 0;
	}
	if(delivery_type!=2){
		$.ajax({
			url:carriage_template_ajax_url,
			data:{"supplier_id":supplier_id},
			type:"post",
			dataType:"json",
			success: function(obj){
				var str = '<option value="0">'+'==请选择运费模板=='+'</option>';
				for (i in obj) {
					str += '<option value="'+obj[i].id+'" data-valuation-type="'+obj[i].valuation_type+'">' + obj[i].name + '</option>';
				}
				$("select[name='carriage_template_id']").html(str);
			}
		});
	}
}

//商户改变
function supplier_id_change(){

	var supplier_id = $("select[name='supplier_id']").val();
	var supplier_name = $("select[name='supplier_id']").find("option:selected").text();

	if(supplier_id==0){
		$(".delivery-type-3").show();
		$('.delivery-type-1').find('input').attr('checked',false);
		$('.delivery-type-3').find('input').attr('checked','checked');
		$(".delivery-3").show();
		$(".delivery-1").hide();
		$("select[name='is_pick']").val(0);
		$(".supplier").html('');
	}else{
		$(".delivery-type-3").hide();
		$(".delivery-3").hide();
		$(".delivery-1").show();
		$("input[name='dist_service_rate']").val('');
		$('.delivery-type-3').find('input').attr('checked',false);
		$('.delivery-type-1').find('input').attr('checked','checked');
		$(".supplier").html(supplier_name);
		
	}
	$("#relate_goods_box .relate_row").remove();
	syn_publish_verify_balance();
	load_weight();
	load_is_pick();
}

function init_location_name(){
	var location = new Array();
	$("input[name^='location_id']:checked").each(function(i,obj){
		var location_name = $(obj).attr('location_name');
		location.push(location_name);
	});
	if(location.length > 0){
		var location_str = location.join(',');	
	}else{
		var location_str = '';
	}
	
	$("#location").html(location_str);
	
}
//关于配送部分
function init_delivery_type_radiobox() {
	load_delivery_type();
	$("input[name='delivery_type']").bind("click",function () {
		load_carriage_template_selectbox();
		load_delivery_type();
		load_delivery_box();
		load_weight();
		load_is_pick();
        ajax_carriage_tempate();
	});
}

function load_delivery_type(){ //判断配送类型，物流和驿站是需要发货的
	var delivery_type =  $('input[name="delivery_type"]:checked').val();
	if(delivery_type == 1 || delivery_type==3){
		$("select[name='is_delivery']").val(1);
	}else{
		$("select[name='is_delivery']").val(0);
		$("select[name='carriage_template_id']").val(0);
		$("select[name='dist_service_rate']").val('');
	}
}


function load_delivery_box() {
	var buy_type = $("select[name='buy_type']").val();
	if(buy_type == 1){ //积分商品商户ID 强制为0
		supplier_id = 0;
		$(".delivery-type-3").hide();	//隐藏驿站配送的选择
		$(".delivery-3").hide();
	}else{
		$(".delivery-type-3").show();	//显示驿站配送的选择
		$(".delivery-3").show();
	}
	var delivery_type =  $('input[name="delivery_type"]:checked').val();
	var supplier_id = $("select[name='supplier_id']").val();

	//根据条件载入运费模板
	if(supplier_id > 0){//如果有商户存在，不支持驿站

		$(".delivery-type-3").hide();	//隐藏驿站配送的选择
		if(delivery_type == 1){//物流配送
			$(".carriage-tpl").show();	//运费模板
			$(".delivery-3").hide();
			$(".pick_box").show();
			$("input[name='dist_delivery_rate']").val(0);	//驿站服务费率设置为0
			$(".weight_box").show();
		}else{
			$(".carriage-tpl").hide();	//运费模板
			$(".delivery-3").hide();
			$(".pick_box").hide();
			$(".weight_box").hide();
			$("input[name='is_pick']").attr('checked',false);
		}
	}else{
		if(delivery_type == 1){//物流配送
			$(".carriage-tpl").show();	//运费模板
			$(".pick_box").show();
			$(".delivery-3").hide();
			$("input[name='dist_delivery_rate']").val(0);	//驿站服务费率设置为0
			$(".weight_box").show();
		}else if(delivery_type == 3){
			$(".carriage-tpl").show();	//运费模板
			$(".delivery-3").show();
			$(".delivery-1").hide();
			$(".pick_box").hide();
			$(".weight_box").show();
			$("input[name='is_pick']").attr('checked',false);
		}else{
			$(".carriage-tpl").hide();	//运费模板
			$(".delivery-3").hide();
			$(".pick_box").hide();
			$(".weight_box").hide();
			$("input[name='is_pick']").attr('checked',false);
		}
	}
	load_weight();
	//是否支持到店自提
	load_is_pick();

}


function syn_shop_cate(){

	var selected_shop_cate_arr = new Array();
	var selected_tuan_first_cate_arr = new Array();
	var selected_tuan_second_cate_arr = new Array();
	$(".selected_shop_cate .select_item").each(function(i,obj){
		var shop_cate_id = $(obj).attr("data_id");
		
		if($(obj).hasClass('tuan_first_id')){  //团购一级分类
			selected_tuan_first_cate_arr.push(shop_cate_id);
		}else if($(obj).hasClass('tuan_second_id')){  //团购二级分类
			selected_tuan_second_cate_arr.push(shop_cate_id);
		}else{// 商城分类
			selected_shop_cate_arr.push(shop_cate_id);
		}
	});  
   var shop_cate_id_str = selected_shop_cate_arr.join(',');
   var tuan_first_cate_id_str = selected_tuan_first_cate_arr.join(',');
   var tuan_second_cate_id_str = selected_tuan_second_cate_arr.join(',');
    
	if(is_shop==0){   //团购
		 $(".selected_shop_cate").find("input[name='cate_id']").val(tuan_first_cate_id_str);
		 $(".selected_shop_cate").find("input[name='second_cate_id']").val(tuan_second_cate_id_str);
	}else{ //商城
		 $(".selected_shop_cate").find("input[name='shop_cate_id']").val(shop_cate_id_str);
	}
	
   
}

function syn_second_cate(first_cate_id,is_shop){
	var query = new Object();
	query.cate_id = first_cate_id;
	query.is_shop = is_shop;
	query.ajax = 1;
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=syn_second_cate", 
		data: query,
		type: "POST",
		success: function(obj){
			$(".second_cate").html(obj);
		}
	});
	
}


function set_price_profit(obj){
	var origin_price = parseFloat($("input[name='origin_price']").val());

	
	if(deal_type==2){
		var publish_verify_balance = $("input[name='publish_verify_balance']").val();
		var current_price = $("input[name='current_price']").val();
		if(isNaN(current_price)){
			current_price=0;
		}
		if(isNaN(publish_verify_balance)){
			publish_verify_balance=0;
		}		
		var balance_price = publish_verify_balance * current_price / 100;

	}else{
		var balance_price =  parseFloat($("input[name='balance_price']").val());
	}
	
	var current_price = parseFloat($("input[name='current_price']").val());
		
	if(obj){
		var price = $(obj).val();
		
		if(isNaN(price)){
			if(isNaN(current_price) || isNaN(balance_price)){
				var price_profit_precentage = '0.00%';
				var price_profit = '0.00';
			}
			var error_tip='请填写有效价格';

		}else if(price==0){
			if(current_price==0 || balance_price==0){
				var price_profit_precentage = '0.00%';
				var price_profit = '0.00';
			}
			var error_tip='请填写有效价格';
		}else{


			if(isNaN(current_price) || isNaN(balance_price)){
				var price_profit_precentage = '0.00%';
				var price_profit = '0.00';
			}else{
				var price_profit_precentage =( ( current_price - balance_price ) / current_price * 100 ).toFixed(2) + '%';
				var price_profit = ( current_price - balance_price ).toFixed(2);
			}
			var error_tip='';
		}
		show_error_tip(obj,error_tip);
		
	}else{ 

		if(isNaN(current_price) || isNaN(balance_price) || isNaN(origin_price)){
		
			if(isNaN(current_price) || isNaN(balance_price)){
				var price_profit_precentage = '0.00%';
				var price_profit = '0.00';
			}

			var error_tip='请填写有效价格';
		}else if(current_price==0 || balance_price==0 || origin_price==0){
			
			if(current_price==0 || balance_price==0){
				var price_profit_precentage = '0.00%';
				var price_profit = '0.00';
			}
			var error_tip='请填写有效价格';
		}else{
			var price_profit_precentage =( ( current_price - balance_price ) / current_price * 100 ).toFixed(2) + '%';
			var price_profit = ( current_price - balance_price ).toFixed(2);
			var error_tip='';
		}
		show_error_tip($("input[name='current_price']"),error_tip);
	}
	$(".price_profit_precentage").html(price_profit_precentage);
	$(".price_profit").html(price_profit);

}


function init_user_discount(){
	get_user_discount_price();

	if($("input[name='allow_user_discount']").is(':checked')){
		$(".user_discount").show();
	}else{
		$(".user_discount").hide();
	}
}
function get_user_discount_price(){
	user_group_json = $.parseJSON(user_group);
	var current_price = parseFloat($("input[name='current_price']").val());
	var user_discount_str = '';
	if(isNaN(current_price)){
		current_price=0;
	}
	for(var i in user_group_json){
		var obj = user_group_json[i];		
		var discount_price=(current_price * obj.discount).toFixed(2);
		user_discount_str+='<div class="user_discount_row"><div class="user_discount_name">'+obj.name+'</div><div class="user_discount_price">'+discount_price+'</div></div>';

	}	
	$(".user_discount .row_right .user_discount_box").html(user_discount_str);
}

function init_max_bought(){
	var deal_goods_type = $("select[name='deal_goods_type']").val();
	if(deal_goods_type==0){
		$("input[name='max_bought']").attr('readonly',false);
	}else{

		var len = $("input[name^='stock_cfg_num']").length;
		if(len > 0){
			var max_bought = 0;
			$("input[name^='stock_cfg_num']").each(function(){
				var stock = parseInt($(this).val());
				if(isNaN(stock)){
					stock=0;
				}
				max_bought += stock;
			});
			$("input[name='max_bought']").val(max_bought).attr('readonly',true);
			$("input[name='max_bought']").siblings('.error_row').remove();
		}
	}
}

function init_fx_unit(){
	var fx_salary_type = $("input[name='fx_salary_type']:checked").val();
	if(fx_salary_type==0){  //定额
		$(".fx_unit").html('元');
	}else{  //比例
		$(".fx_unit").html('%');
	}
}

function init_is_fx(){

	if($("input[name='is_allow_fx']").is(":checked")){  //定额
		$(".fx_box").show();
		
		if($("input[name='is_fx']:checked").val()==null){
			$("input[name='is_fx']:eq(0)").attr('checked',true);
		}
				
	}else{  //比例
		$(".fx_box").hide();
	}
}

function init_coupon_setting(){
	if($("input[name='is_coupon']").is(':checked')){		
		var coupon_time_type = $("input[name='coupon_time_type']:checked").val();

		$(".coupon_box").show();
		if(coupon_time_type==0){  //固定时期
			$(".coupon_time").show();
			$(".coupon_day").hide();
		}else if(coupon_time_type==1){  //指定天数
			$(".coupon_time").hide();
			$(".coupon_day").show();
		}

	}else{
		$(".coupon_box").hide();
	}
}

function init_balance_price(){
	
	if(deal_type==2){
		var publish_verify_balance = $("input[name='publish_verify_balance']").val();
		var current_price = $("input[name='current_price']").val();
		if(isNaN(current_price)){
			current_price=0;
		}
		if(isNaN(publish_verify_balance)){
			publish_verify_balance=0;
		}
		
		var balance_price = (publish_verify_balance * current_price / 100).toFixed(2);
		$("input[name='balance_price']").attr('readonly',true).val(balance_price);
		init_add_balance_price();
	}
}


function syn_publish_verify_balance(){
	
	var supplier_id = $("select[name='supplier_id']").val();
	var query = new Object();
	query.supplier_id = supplier_id;
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=syn_publish_verify_balance", 
		data: query,
        dataType:"json",
		success: function(obj){

			if(obj.status==1){
				$("input[name='publish_verify_balance']").val(obj.publish_verify_balance);
				init_balance_price();
			}
			
		}
	});
}

function get_shop_brand(){
	
	if(is_shop==1){
		var shop_cate_id = $("input[name='shop_cate_id']").val();
		var query = new Object();
		query.shop_cate_id = shop_cate_id;
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=get_shop_brand", 
			data: query,
			success: function(obj){
				$(".brand_box").html(obj);
				
			}
		});
	}

}

function show_error_tip(obj,error_tip){

	if(error_tip){
		
		var error_row = '<div class="error_row">'+error_tip+'</div>';
		var len = $(obj).parents(".info_row .row_right").find(".error_row").length ;
		if(len==0){
			$(obj).parents(".info_row .row_right").append(error_row);
		}else{
			$(obj).parents(".info_row .row_right .error_row").replaceWith(error_row);
		}
		
	}else{
		$(obj).parents(".info_row .row_right").find(".error_row").remove();
	}

}

function init_next_button(){
	
	if(is_shop==1){
		var shop_cate_id = $("input[name='shop_cate_id']").val();
		if(shop_cate_id==''){
			$(".go_next_step").addClass('go_next_step_disable').removeClass('go_next_step');
		}else{
			$(".go_next_step_disable").addClass('go_next_step').removeClass('go_next_step_disable');
		}
	}else{
		var cate_id = $("input[name='cate_id']").val();
		var second_cate_id = $("input[name='second_cate_id']").val();
		if(cate_id=='' && second_cate_id==''){
			$(".go_next_step").addClass('go_next_step_disable').removeClass('go_next_step');
		}else{
			$(".go_next_step_disable").addClass('go_next_step').removeClass('go_next_step_disable');
		}
	}
}


function init_add_balance_price(){
	
		if($("select[name='supplier_id']").length >0){
			var supplier_id = $("select[name='supplier_id']").val();
		}else{
			var supplier_id = $("input[name='supplier_id']").val();
		}

		if(supplier_id > 0){
			var publish_verify_balance = $("input[name='publish_verify_balance']").val();
			$("#stock_table input[name='deal_attr_price[]']").each(function(i,obj){
				var price = $(obj).val();
				var add_balance_obj = $(obj).parent('td').siblings();
				var add_balance = (price * publish_verify_balance /100).toFixed(2) ;
				if(isNaN(add_balance)){
					add_balance=0;
				}
				$(add_balance_obj).find("input[name='deal_add_balance_price[]']").val(add_balance);
				$(add_balance_obj).find(".balance_item").html(add_balance);
			});
		}

}
/**
 * 判断是否是正数
 * @param s
 * @returns {Boolean}
 */
function isIntNum(num) {

	 var reg = /^[1-9]\d*$/; 
	 if (reg.test(num)){
	  return true;
	 } else{
	  return false;
	 } 
}
function cate_del_hide_show(){
	if(is_shop==0){
		$(".select_item.tuan_first_id span.selected_cate_delete").show();
		$(".select_item.tuan_first_id").each(function(){
			//$(".select_item.tuan_second_id[pid='"+$(this).attr("data_id")+"']").hide();
			if($(".select_item.tuan_second_id[pid='"+$(this).attr("data_id")+"']").length>0){
				$(this).find("span.selected_cate_delete").hide();
			}
		});
	}
}

