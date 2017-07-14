$(document).ready(function(){
	
	load_consignee();
	
	$(".del").bind("click",function(){
		var url=$(this).attr("url");
		$.showConfirm("确定要删除吗？",function(){				
			del_address(url);
		});			
	});
	
	set_default();
	
});

function del_address(url){
	var ajaxurl = url;
	$.ajax({ 
		url: ajaxurl,
		dataType: "json",
		type: "GET",
		success: function(obj){
			console.log(obj);
			if(obj.status==2){
				ajax_login();
			}else if(obj.status==1){
				$.showSuccess("删除成功",function(){
					location.reload();
				});				
			}else{
				$.showErr(obj.info);
			}
		},
		error:function(ajaxobj)
		{
			
		}
	});	
}

//装载配送地区
function load_consignee()
{	
	var consignee_id = $("#cart_consignee").attr("rel");
	var query = new Object();
	query.act = "load_consignee";
	query.id = consignee_id;
	$.ajax({ 
		url: AJAX_URL,
		data:query,
		dataType:"json",
		type:"POST",
		success: function(data){
			$("#cart_consignee").html(data.html);				
			init_region_ui_change();
			init_ui_select();
			init_ui_textbox();		
			init_ui_button();
			bind_subm();
			if (!consignee_id) {
				$("select[name='region_lv1']").trigger('change');
			}
			$('.country').hide();
		}
	});

}

/**
 * 初始化地区切换事件
 */
function init_region_ui_change(){	

	$.load_select = function(lv)
	{
		var name = "region_lv"+lv;
		var next_name = "region_lv"+(parseInt(lv)+1);
		var id = $("select[name='"+name+"']").val();
		var region = '选择国家';
		if(lv==1){
			region = '选择省份';
			var evalStr="regionConf.r"+id+".c";
		}
		if(lv==2){
			region = '选择城市';
			var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+id+".c";
		}
		if(lv==3){
			region = '选择县区';
			var evalStr="regionConf.r"+$("select[name='region_lv1']").val()+".c.r"+$("select[name='region_lv2']").val()+".c.r"+id+".c";
		}
		
		if(id==0) {
			var html = "<option value='0'>"+region+"</option>";
		} else {
			var regionConfs=eval(evalStr);
			evalStr+=".";
			var html = "<option value='0'>"+region+"</option>";
			for(var key in regionConfs)
			{
				html+="<option value='"+eval(evalStr+key+".i")+"'>"+eval(evalStr+key+".n")+"</option>";
			}
		}
		$("select[name='"+next_name+"']").html(html);
		$("select[name='"+next_name+"']").ui_select({refresh:true});
		if(lv == 4) {
			//load_delivery();
		} else {
			$.load_select(parseInt(lv)+1);
		}	
	};
	
	$("select[name='region_lv1']").bind("change",function(){
		$.load_select("1");
	});
	$("select[name='region_lv2']").bind("change",function(){
		$.load_select("2");
	});
	$("select[name='region_lv3']").bind("change",function(){
		$.load_select("3");
	});	
	$("select[name='region_lv4']").bind("change",function(){
		$.load_select("4");
	});	
}

function bind_subm() {
	$('#sub_address').bind('click', function() {
		var query = $("form[name='my_address']").serialize();
		var ajaxurl = $("form[name='my_address']").attr("action");
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			data:query,
			type: "post",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==3){
					$.showErr("配送地址最多5个")
				}else if(obj.status==1){
					$.showSuccess("地址保存成功",function(){
						location.href = obj.url;
					});				
				}else{
					$.showErr(obj.info);
				}
			},
			error:function(ajaxobj) {
				
			}
		});	
	});
}

function set_default(){
	$(".default").bind("click",function(){
		var ajaxurl = $(this).attr("dfurl");
		$.ajax({ 
			url: ajaxurl,
			dataType: "json",
			type: "GET",
			success: function(obj){
				if(obj.status==2){
					ajax_login();
				}else if(obj.status==1){
					$.showSuccess("设置成功",function(){
						location.reload();
					});				
				}else{
					$.showErr("设置失败");
				}
			},
			error:function(ajaxobj) {
				
			}
		});		
	});
}



