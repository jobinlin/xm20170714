$(function(){
	
	load_filter_box(); //初始化筛选关键词
	$("select[name='shop_cate_id']").bind("change",function(){
		 load_filter_box();
	});
	load_data();
	load_is_pick();
    check_carriage();
	$("select[name='is_delivery']").bind("change",function(){
		load_is_pick();
	});
    $("select[name=carriage_template_id]").bind("change",function(){
        refresh_delivery_type_relate();
    });
	$("input[name='delivery_type']").bind("click",function(){
        refresh_delivery_type_relate();
    });
	/*发布*/
	$("form[name='goods_publish_form']").submit(function(){
		var form = $("form[name='goods_publish_form']");
		if(check_goods_form_submit()){
			//$(".sub_form_btn").html('<button class="ui-button" rel="disabled" type="button">提交</button>');
			init_ui_button();
			var query = $(form).serialize();
			var url = $(form).attr("action");
			$.ajax({
				url:url,
				data:query,
				type:"post",
				dataType:"json",
				success:function(data){
					if(data.status == 0){
						$(".sub_form_btn").html('<button class="ui-button " rel="orange" type="submit">确认提交</button>');
						init_ui_button();
						$.showErr(data.info,function(){
							if(data.jump&&data.jump!="")
							{
								location.href = data.jump;
							}	
						});
					}else if(data.status==1){
						$.showSuccess(data.info,function(){
							if($("input[name='continue_add']").is(':checked')){
								window.location = location.href;
							}else{
								window.location = data.jump;
							}
						});
					}
					return false;
				}
			});
		}
		return false;
	});
	
//end jquery	
});
function load_data(){
    refresh_delivery_type_relate();
}
function check_carriage(){
    if(!carriage_number){
        $.showConfirm("运费模板为空请先添加！",function(){
            window.location.href=carriage_add_url;
        });
    }
}
//更新与delivery_type有关的
function refresh_delivery_type_relate(){
    var delivery_type=$("input[name=delivery_type]:checked").val();
    if(delivery_type==1){
        $("#tr_carriage_template_id").show();
        var template_selected=$("select[name=carriage_template_id]").find("option:selected").attr('valuation_type');
        $("select[name=is_delivery]").val(1);
        $("select[name=is_delivery]").trigger("change");
        if(template_selected==2){
            $("#tr_weight").show();
        }else{
            $("#tr_weight").hide();
        }
    }else if(delivery_type==2){
        $("select[name=is_delivery]").val(0);
        $("select[name=is_delivery]").trigger("change");
        $("#tr_carriage_template_id").hide();
        $("#tr_weight").hide();
        $("select[name=carriage_template_id]").val(0);
        $("select[name=carriage_template_id]").ui_select({refresh:true});
        $("input[name=weight]").val(0);
    }
    ajax_carriage_tempate();
}
function ajax_carriage_tempate(){
    var carriage_template_id=parseInt($("select[name=carriage_template_id]").val());
    $("#J_hintDefault").hide();
    $("#deliver-warn").hide();
    if(carriage_template_id==0)return;
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
        var type=$("select[name=carriage_template_id]").find("option:selected").attr('valuation_type');
        var text='';
        if(type==2){
            text="默认运费："+detail['express_start']+"千克内"+detail['express_postage']+"元，每增加"+detail['express_plus']+"千克，加"+detail['express_postage_plus']+"元"
        }else{
            text="默认运费："+detail['express_start']+"件内"+detail['express_postage']+"元，每增加"+detail['express_plus']+"件，加"+detail['express_postage_plus']+"元"
        }
        $("#carriage_default_carriage").html(text);
        $("#J_hintDefault").show();
    }
}
/*表单提交验证*/
function check_goods_form_submit(){
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
	//分类
	if(parseInt($("select[name='shop_cate_id']").val())<=0){
		$.showErr("请选择分类");
		return false;
	}
	//支持门店
	if($("input.location_id_item:checked").length<=0){
		$.showErr("至少支持一个门店");
		return false;
	}
	//团购缩略图
	if($(".img_icon_upload_box span").length<=0){
		$.showErr("请上传缩略图");
		return false;
	}
	//团购图片集
	if($(".focus_imgs_upload_box span").length<=0){
		$.showErr("请上传图集");
		return false;
	}
	

	return true;
	
}


/**
 * 加载是否允许自提
 */
function load_is_pick()
{
	var is_delivery = $("input[name='delivery_type']:checked").val();
	if(is_delivery==1)
	{
		$("#is_pick").show();
	}
	else
	{
		$("#is_pick").hide();
		$("select[name='is_pick']").val(0);
	}
}
