$(document).ready(function () { 
	select_cate_change();
	add_relate_goods();
	del_relate();
	load_attr_html_1();
	if(is_shop==1){
		cate_brand();
	}
	$("select[name='deal_goods_type']").die().live("change",function(){
		load_attr_html_1();
		init_max_bought();
	});
	$("input[name^='stock_cfg_num']").live("keyup change",function(){
		init_max_bought();
	});
	$("button.syn_price_setting").live('click',function(){
		var syn_price= $("input[name='syn_price']").val();
		var syn_add_balance_price= syn_price*parseInt($("input[name='publish_verify_balance']").val())/100;
		var syn_stock_cfg= $("input[name='syn_stock_cfg']").val();

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
			init_add_balance_price()	
		}
		
	});
	$("input[name='deal_attr_price[]']").live('keyup',function(){
		init_add_balance_price();
	});
	function init_add_balance_price(){
		var publish_verify_balance = $("input[name='publish_verify_balance']").val();
		$("#stock_table input[name='deal_attr_price[]']").each(function(i,obj){
			var price = $(obj).val();
			var add_balance_obj = $(obj).parent('td').siblings();
			var add_balance = (price * publish_verify_balance /100).toFixed(2) ;
			if(isNaN(add_balance)){
				add_balance=0;
			}
			$(add_balance_obj).find("input[name='deal_add_balance_price[]']").val(add_balance);
			$(add_balance_obj).find("input[name='deal_add_balance_price[]']").next().text(add_balance);
		});
	}
	price_statistics();
	input_limit();
	var set_meal_editor = $("#set_meal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html($("textarea[name='set_meal']").val())} });
	var pc_setmeal_editor = $("#pc_setmeal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html($("textarea[name='pc_setmeal']").val())} });
	$("select[name='tc_pc_moban']").bind("change",function(){
		load_tc_pc_html();
	});
	$("select[name='tc_mobile_moban']").bind("change",function(){
		load_tc_mobile_html();
	});
});
function select_cate_change(){
	if(is_shop==1){
		$("select[name='shop_cate_1']").bind("change",function(){ 
			var shop_cate_1_id=$(this).val();
			var html = "";
			if(!shop_cate_2[shop_cate_1_id]){
				$("select[name='shop_cate_2']").html("");
				return false;
			}
			$.each(shop_cate_2[shop_cate_1_id],function(k,v){
				html+="<option value='"+v.id+"' data-name='"+v.name+"'>"+v.name+"</option>";
			});
			$("select[name='shop_cate_2']").html(html);
		}); 
		$(".addcate").unbind("click").bind("click",function(){
			var shop_cate_1_id=$("select[name='shop_cate_1']").val();
			var shop_cate_2_id=$("select[name='shop_cate_2']").val();
			if(shop_cate_1_id==null){
				$.showErr("请选择分类");
				return false;
			}
			if(shop_cate_2_id!=null){
				if($(".addcate_info p[data-id='"+shop_cate_2_id+"']").length>0){
					$.showErr("已添加该分类");
					return false;
				}
				var html='<p data-id="'+shop_cate_2_id+'"><span>'+$("option[value='"+shop_cate_1_id+"']").attr("data-name")+'  >  '+$("option[value='"+shop_cate_2_id+"']").attr("data-name")+'</span> <a onclick="del_cate(this)">删除</a></p>';
				//<input type="hidden" name="shop_cate_id[]" value="'+shop_cate_2_id+'">
			}else{
				if($(".addcate_info p[data-id='"+shop_cate_1_id+"']").length>0){
					$.showErr("已添加该分类");
					return false;
				}
				var html='<p data-id="'+shop_cate_1_id+'"><span>'+$("option[value='"+shop_cate_1_id+"']").attr("data-name")+'</span> <a onclick="del_cate(this)">删除</a></p>';
			}
			
			$(".addcate_info").append(html);
			cate_ids();
			return false;
		});
	}else{
		$("#tuan_cate_id").bind("change",function(){ 
			var cate_id = $("#tuan_cate_id").val();
			var select_sub_cate = $("input[name='select_sub_cate']").val();
			var edit_type = $("input[name='edit_type']").val();
			var html="";
			if(cate_id>0)
			{
				
				var query = new Object();
				query.act = "load_sub_cate";
				query.cate_id = cate_id;
				query.edit_type = edit_type;
				query.id = $("input[name='id']").val();
				query.select_sub_cate = select_sub_cate;
				$.ajax({ 
					url: ajax_url, 
					data: query,
					dataType: "json",
					success: function(obj){
						if(obj.status)
						{
							$.each(obj.sub_cate_list,function(k,v){
								html+="<option value='"+v.id+"' data-name='"+v.name+"'>"+v.name+"</option>";
							});
							$("select[name='tuan_cate_id_2']").html(html);
							//$("#sub_cate_box").show();
							//$("#sub_cate_box").find(".item_input").html(obj.html);
						}
						else
						{
							$("#tuan_cate_id_2").html("");
						}
						
						init_ui_checkbox();
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
				$("#tuan_cate_id_2").html("");
				//$("#sub_cate_box").hide();
				//$("#sub_cate_box").find(".item_input").html("");
			}
		}); 
		$(".addcate").unbind("click").bind("click",function(){
			var shop_cate_1_id=$("#tuan_cate_id").val();
			var shop_cate_2_id=$("#tuan_cate_id_2").val();
			//if($(".addcate_info input[value='"+shop_cate_2_id+"']").length>0){
			//	$.showErr("已添加该分类");
			//	return false;
			//}
			//var html='<p><span>'+$("#tuan_cate_id option[value='"+shop_cate_1_id+"']").text()+'  >  '+$("#tuan_cate_id_2 option[value='"+shop_cate_2_id+"']").text()+'</span> <input type="hidden" name="deal_cate_id[]" value="'+shop_cate_1_id+'"><input type="hidden" name="deal_cate_type_id[]" value="'+shop_cate_2_id+'"><a href="">删除</a></p>';
			$(".addcate_info").append(html);
			
			if(shop_cate_1_id==null){
				$.showErr("请选择分类");
				return false;
			}
			if(shop_cate_2_id!=null){
				if($(".addcate_info p[data-id='"+shop_cate_2_id+"']").length>0){
					$.showErr("已添加该分类");
					return false;
				}
				var html='<p class="id2" pid="'+shop_cate_1_id+'" data-id="'+shop_cate_2_id+'"><span>'+$("#tuan_cate_id option[value='"+shop_cate_1_id+"']").attr("data-name")+'  >  '+$("#tuan_cate_id_2 option[value='"+shop_cate_2_id+"']").attr("data-name")+'</span> <a onclick="del_cate(this)">删除</a></p>';
				//<input type="hidden" name="shop_cate_id[]" value="'+shop_cate_2_id+'">
			}else{
				if($(".addcate_info p[data-id='"+shop_cate_1_id+"']").length>0){
					$.showErr("已添加该分类");
					return false;
				}
				var html='<p class="id" data-id="'+shop_cate_1_id+'"><span>'+$("#tuan_cate_id option[value='"+shop_cate_1_id+"']").attr("data-name")+'</span> <a  onclick="del_cate(this)">删除</a></p>';
			}
			
			$(".addcate_info").append(html);
			cate_ids();
			cate_del_hide_show();
			return false;
		});
	}
	
}
function del_cate(obj){
	$(obj).parent().remove();
	cate_ids();
	cate_del_hide_show();
}
function cate_ids(){
	if(is_shop==1){
		var cate_ids=new Array();
		$(".addcate_info p").each(function(){
			cate_ids.push($(this).attr('data-id'));
		});
		var str=cate_ids.join(",");
		$("input[name='shop_cate_id']").val(str);
		cate_brand();
	}else{
		var cate_id_1=new Array();
		$(".addcate_info p.id").each(function(){
			cate_id_1.push($(this).attr('data-id'));
		});
		var str=cate_id_1.join(",");
		$("input[name='cate_id']").val(str);
		var cate_id_2=new Array();
		$(".addcate_info p.id2").each(function(){
			cate_id_2.push($(this).attr('data-id'));
		});
		var str=cate_id_2.join(","); 
		$("input[name='deal_cate_type_id']").val(str);
	}
}
function add_relate_goods(){
	/*关联商品*/
	$("button#add_relate_goods").unbind("click").bind("click",function(){
		var query = new Object();
		query.deal_id=$("input[name='id']").val();
		query.edit_type=$("input[name='edit_type']").val();
		query.act = "add_related_deal";
		
		$('.relate_deal tr.alt').live('mouseover',function(){
			$(this).addClass('relate_cur');
		});
		$('.relate_deal tr.alt').live('mouseout',function(){
			$(this).removeClass('relate_cur');
		});
		
		$.ajax({
			url:AJAX_URL,
			data:query,
			type:"post",
			dataType:"json",
			success:function(result){
				$.weeboxs.open(result.html, {boxid:'add_goods_type_weebox',contentType:'text',showButton:false,title:"设置关联商品",width:900,height:635,type:'wee',onopen:function(){
						init_ui_button();
						init_ui_textbox();
						$("#relate2 .add_icon").hide();
						//$('#relate1').hide();
						if($('#relate2 .relate_row').length>0){							
							$('#relate1').html($('#relate2').html());
						}
						load_relate();
						
						
						add_relate();
						$('.sure_relate').bind('click',function(){
							$('#relate2').html($('#relate1').html());
							if($("#relate2 .relate_row").length<=5){
								$("#relate2 .add_icon").show();
							}
							add_relate_goods();
							$.weeboxs.close();
						});

						$("#relate_deal .pages a").live("click",function(){							
							var url = $(this).attr("href");
							var query = new Object();
							query.deal_id=$("input[name='id']").val();
							query.is_shop = is_shop;
							query.related_deal=$("#relate1 input[name='related_deal']").val();
							query.act="load_relate";
							$.ajax({
								url:url,
								data:query,
								type:"post",
								dataType:"json",
								success: function(obj){									
									$("#relate_deal").html(obj.html);
									
								}
							});	
							return false;
						});						
						//提交数据
						$("form[name='search_relate']").submit(function(){
							keyword=$("input[name='search_relate_deal']").val();
							load_relate(keyword);
							return false;
						});
						
					},onclose:function(){	$("#relate2 .add_icon").show();relate_unbind();}
				});
			}
		});
	});	
}
function load_relate(keyword){
	var query = new Object();
	query.deal_id=$("input[name='id']").val();
	query.act = "load_relate";
	query.is_shop = is_shop;
	query.related_deal=$("#relate1 input[name='related_deal']").val();//alert(query.related_deal);
	if(keyword!='')query.keyword=keyword;
	
	$.ajax({
		url:AJAX_URL,
		data:query,
		type:"post",
		dataType:"json",
		success: function(obj){
			$("#relate_deal").html(obj.html);
			
		}
	});	

}
function add_relate(){
	$('.relate_deal tr.alt').die().live('click',function(){
		if($('#relate1 .relate_row').length>=6){
			$.showErr('关联商品最多6个,请先删除后再添加'); 
		}else{
			relate_name=$(this).find('td.relate_name').html();
			relate_img=$(this).find('img').attr('src');
			relate_id=$(this).attr('rel');
			relate_ids=$("#relate1 input[name='related_deal']").val();
			if(relate_ids == ''){
				$("#relate1 input[name='related_deal']").val(relate_id);
			}else{
				$("#relate1 input[name='related_deal']").val(relate_ids+','+relate_id);
			}
			$(this).hide();
			//$("#relate1 ul:first").append('<li rel="'+relate_id+'"><a href="javascript:void(0);" title="'+relate_name+'"></a><div class="relate_img"><img  src="'+relate_img+'" style="width:70px;height:50px;"></div><div class="relate_name">'+relate_name.substr(0, 5)+'</div></li>');
			$("#relate1 .relate_goods_box").append('<div class="relate_row" rel="'+relate_id+'" title="'+relate_name+'"><div class="relate_left"><span class="dl_img"><a href="" title="'+relate_name+'" target="_blank"><img src="'+relate_img+'" width="100" height="70"></a></span></div><div class="relate_right"><a href="" title="'+relate_name+'" target="_blank">'+relate_name+'</a><a class="relate_close_btn deal_relate" href="javascript:void(0);" ><img src="'+tmpl+'/images/delete_icon.png"></a></div></div>');
		}
	});
	
}
function del_relate(){
	$('.select_relate .deal_relate').die().live('click',function(){

		relate_name=$(this).parents('.relate_row').attr('title');
		relate_img=$(this).parents('.relate_row').find('.dl_img img').attr('src');
		relate_id=$(this).parents('.relate_row').attr('rel');
		$(this).parents('.relate_row').remove();
		if($("#relate1").length>0){
			select_relates=$('#relate1 .relate_row');
		}else{
			select_relates=$('#relate2 .relate_row');
		}
		var str_relate_ids=[];
		for(i=0;i<select_relates.length;i++){			
			str_relate_ids.push($(select_relates[i]).attr('rel'));
		}		
		if($("#relate1").length>0){
			$("#relate1 input[name='related_deal']").val(str_relate_ids.join());
			$(".relate_deal table").append('<tr class="alt" rel="'+relate_id+'"><td class="add_relate">+</td><td class="detail"><img  src="'+relate_img+'" style="width:70px;height:50px;"></td><td class="relate_name">'+relate_name+'</td> </tr>');
		}else{
			$("#relate2 input[name='related_deal']").val(str_relate_ids.join());
			if($("#relate2 .relate_row").length<=5){
				$("#relate2 .add_icon").show();
			}
		}

	});
	
}
/**
 * 初始化属性
 */
function load_attr_html_1()
{
		var deal_goods_type = $("select[name='deal_goods_type']").val();
		var id = $("input[name='id']").val();
		if(deal_goods_type>0)
		{
			var query = new Object();
			query.act = "load_attr_html";
			query.id = id;
			query.edit_type = $("input[name='edit_type']").val();
			query.deal_goods_type = deal_goods_type;
			
			$("#deal_attr_row").show();
			$.ajax({ 
				url:AJAX_URL, 
				data:query,
				success: function(obj){
					$("#deal_attr2").html(obj);
					$("#deal_attr2_tr").show();
					init_ui_checkbox();
				}
			});
		}
		else
		{
			$(".deal_attr_row").hide();
			$("#deal_attr2").html("");
			$("#deal_attr2_tr").hide();
			$("#stock_table").html("");
		}
}
//加载属性库存表
function load_attr_stock_1(obj)
{
	if(obj)
	{
		 attr_cfg_json = '';
		 attr_stock_json = '';
	}


	/*if($(".deal_attr_stock:checked").length>0)
	{
			$(".max_bought_row").find("input[name='max_bought']").val("");
			$(".max_bought_row").hide();
	}
	else
	{
			$(".max_bought_row").show();
	}*/
	var attr_row_arr = new Array();
	$("#deal_attr2 .attr_row").each(function(){
		var data_name = $(this).find('.attr_name').text();
		var attr_row = new Object();
		attr_row.name = data_name;
		
		var attr_row_unit = new Array();
		
		$(this).find('.attr_content .attr_item').each(function(index,obj_attr){
			var attr_value_obj = $(obj_attr).find(".attr_value");
			var attr_row_data = new Object();
			if($(attr_value_obj).hasClass("textbox")){
				var attr_row_value = $(attr_value_obj).val();
				var deal_attr_id =  $(attr_value_obj).attr('deal_attr_id');
				if(attr_row_value!=''){
					$(obj_attr).parent().parent().find(".deal_attr_stock_hd").val(1);
				}else{
					$(obj_attr).parent().parent().find(".deal_attr_stock_hd").val(0);
				}
			}else{
				var attr_row_value = $(attr_value_obj).find("option:selected").val();
				var deal_attr_id =  $(attr_value_obj).attr('deal_attr_id');
				$(obj_attr).parent().parent().find(".deal_attr_stock_hd").val(1);
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
	
	var query = new Object();
	query.ajax = 1;
	query.attr_row_arr = attr_row_arr;
	query.deal_id = $("input[name='id']").val();
	query.edit_type = $("input[name='edit_type']").val();
	query.is_shop = is_shop;
	query.act="attr_table";
	$.ajax({ 
		url: AJAX_URL, 
		data: query,
		success: function(obj){
			$("#stock_table").html(obj);
			if($.trim(obj)==""){
				$(".deal_attr_row").hide();
			}else{
				$(".deal_attr_row").show();
				$("#deal_attr2_tr").show();	
				var deal_goods_type = $("select[name='deal_goods_type']").val();
				if(deal_goods_type==0){
					$(".attr_box").hide();
				}else{
					$(".attr_box").show();
				}
				init_max_bought();
			}
		}
	});
	
}
/*表单提交验证*/
function check_goods_form_submit(){
	//支持门店
	if($("input.location_id_item:checked").length<=0){
		$.showErr("至少支持一个门店");
		return false;
	}
	var shop_cate_id=$.trim($("input[name='shop_cate_id']").val());
	if(shop_cate_id!=""){
		var arr=shop_cate_id.split(","); 
		var length=arr.length;
	}else{
		var length=0;
	}
	//分类
	if(length==0){
		$.showErr("请选择分类");
		return false;
	}
	
	if($("input[name='img[]']").length<1){
		$.showErr("最少上传1张商品图片");
		return false;
	}
	//团购名称
	if($.trim($("input[name='name']").val())==''){
		$.showErr("请输入商品名称",function(){$("input[name='name']").focus();});
		return false;
	}
	//简短名称
	if($.trim($("input[name='sub_name']").val())==''){
		$.showErr("请输入简短名称",function(){$("input[name='sub_name']").focus();});
		return false;
	}
    if($("input[name=delivery_type]:checked").val()==1&&$("select[name=carriage_template_id]").val()==0){
        $.showErr("请选择运费模板");
        return false;
    }
	return true;
	
}
function init_max_bought(){
	var deal_goods_type = $("select[name='deal_goods_type']").val();
	if(deal_goods_type==0){
		$("input[name='max_bought']").attr('readonly',false);
		$("input[name='max_bought']").parents('tr.max_bought_row').show();
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
			$("input[name='max_bought']").parents('tr.max_bought_row').hide();
		}
	}
}
/**
 * 结算价，毛利率，毛利额计算，
 */
function price_statistics()
{
	var current_price=$("input[name='current_price']").val();
	var publish_verify_balance=$("input[name='publish_verify_balance']").val();
	var balance_price;
	
	var gross_margin;
	var gross_profit_amount;
	price_statistics_1();
	$("input[name='current_price']").bind('keyup change',function(){
		current_price=$(this).val();
		price_statistics_1();
	});
	function price_statistics_1(){
		balance_price=(current_price*publish_verify_balance/100).toFixed(2);
		gross_profit_amount=(current_price-balance_price).toFixed(2);
		if(parseFloat(current_price)==0||current_price==''){
			gross_margin='0.00';
		}else{
			gross_margin=((current_price-balance_price)*100/current_price).toFixed(2);
		}
		$("input[name='balance_price").val(balance_price);
		$("input[name='balance_price").parents("tr").find("p").text("毛利率："+gross_margin+"%   毛利额："+gross_profit_amount);
	}
}
function input_limit()
{
	//标题
	$("input[name='name']").bind('keypress',function(){
		if(event.keyCode == 32)event.returnValue = false;
	});
	$("input[name='name']").bind('keyup',function(){
		$(this).next().text($(this).val().length+'/30');
	});
	//url别名
	$("input[name='uname']").bind('keypress',function(){
		if(event.keyCode >47 && event.keyCode <58)event.returnValue = false;
	});
	//简称
	$("input[name='sub_name']").bind('keypress',function(){
		if(event.keyCode == 32)event.returnValue = false;
	});
	$("input[name='sub_name']").bind('keyup',function(){
		$(this).next().text($(this).val().length+'/18');
	});
	//卖点
	$("input[name='brief']").bind('keypress',function(){
		if(event.keyCode == 32)event.returnValue = false;
	});
	$("input[name='brief']").bind('keyup',function(){
		$(this).next().text($(this).val().length+'/60');
	});
	$("input[name='name']").next().text($("input[name='name']").val().length+'/30');
	$("input[name='sub_name']").next().text($("input[name='sub_name']").val().length+'/18');
	$("input[name='brief']").next().text($("input[name='brief']").val().length+'/60');
}
function load_tc_mobile_html()
{
	var tc_file = $("select[name='tc_mobile_moban']").val();
	if(tc_file !=0){
		$.ajax({
				url:APP_ROOT + "/mapi/mobile_tc/tc_mobile/"+tc_file,
				dataType:"html",
				success:function(result){
					//KE.util.setFullHtml("set_meal",result);
					//$("#set_meal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html(result)} });
					//$("textarea[name='set_meal']").prev().find(".ke-content").html(result);
					$("#set_meal").prev().find("iframe").contents().find("body").html(result);
					$("#set_meal").val(result);
				}
		});
	}else{
		var html =$("#set_meal").html();
		//KE.util.setFullHtml("set_meal",html);
		//$("#set_meal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html(result)} });
		$("textarea[name='set_meal']").prev().find("iframe").contents().find("body").html(html);
		$("#set_meal").val(html);
		
	}
}
function load_tc_pc_html()
{
	var tc_file = $("select[name='tc_pc_moban']").val();
	if(tc_file !=0){
		$.ajax({
				url:APP_ROOT + "/mapi/mobile_tc/tc_pc/"+tc_file,
				dataType:"html",
				success:function(result){
					//$("#pc_setmeal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html("ssssssssss")} });
					//KE.util.setFullHtml("pc_setmeal",result);
					$("#pc_setmeal").prev().find("iframe").contents().find("body").html(result);
					$("#pc_setmeal").val(result);
				}
		});
	}else{
		var html =$("#pc_setmeal").html();
		//KE.util.setFullHtml("pc_setmeal",html);
		//$("#pc_setmeal").ui_editor({url:K_UPLOAD_URL,width:"450",height:"250",fun:function(){$(this).html(html)} });
		$("#pc_setmeal").prev().find("iframe").contents().find("body").html(html);
		$("#pc_setmeal").val(html);
	}
}
/**
 * 初始化属性
 */
function cate_brand()
{
	var shop_cate_id = $("input[name='shop_cate_id']").val();
	var query = new Object();
	query.act = "cate_brand";
	query.shop_cate_id = shop_cate_id;
	query.brand_id = brand_id;
	$.ajax({ 
		url:AJAX_URL, 
		data:query,
		success: function(html){
			$("#brand").html(html);
			init_ui_select();
		}
	});
}
function cate_del_hide_show(){
	if(is_shop==0){
		$("p.id a").show();
		$("p.id").each(function(){
			//$(".select_item.tuan_second_id[pid='"+$(this).attr("data_id")+"']").hide();
			if($("p.id2[pid='"+$(this).attr("data-id")+"']").length>0){
				$(this).find("a").hide();
			}
		});
	}
}