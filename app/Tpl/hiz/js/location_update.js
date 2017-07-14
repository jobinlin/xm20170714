/**
 * 
 */

$(document).ready(function(){
	//初始化地图
	ini_map();
	init_img_del();
	
	search_suppllier();
	
	location_submit();
	update_submit();

	$("#is_dc_set").hide();
	$(".area-list").hide();
	$("#sub_area_"+area_item).show();
	
	$("#area_select").change(function(){
		var rel=$(this).val();
		
		$(".sub_area").prop("checked",false);
		$(".area-checkbox").removeClass("common_cbo_checked").addClass("common_cbo");
		$(".area-list").hide();
		$("#sub_area_"+rel).show();
	});
	
	$(".first-cate").bind('click', function() {
		$(".first-cate").removeClass('active');
		$(this).addClass('active');
		$(".sub-cate-wrap").hide();
		$(".sub-cate-wrap").eq($(this).parent().index()).show();
		$(".sub-cate").removeClass('active');
	});
	$(".sub-cate").bind('click', function() {
		$(this).toggleClass('active');
	});
	
	$(".preview_upbtn input.preview_btn").ui_upload({multi:false,FilesAdded:function(files){
		//选择文件后判断
		if($(".preview_upload_box").find("span").length+files.length>1)
		{
			$.showErr("最多只能传1张图片");
			return false;
		}
		else
		{
			for(i=0;i<files.length;i++)
			{
				var html = '<span><div class="loader"></div></span>';
				var dom = $(html);		
				$(".preview_upload_box").append(dom);	
			}
			uploading = true;
			return true;
		}
	},FileUploaded:function(responseObject){
		if(responseObject.error==0)
		{
			var first_loader = $(".preview_upload_box").find("span div.loader:first");
			var box = first_loader.parent();
			first_loader.remove();
			$(".preview_upbtn").hide();
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+'/'+responseObject.web_40+'" />'+
			'<input type="hidden" name="preview" value="'+responseObject.url+'" />';
			$(box).html(html);
			$(box).find("a").bind("click",function(){
				$(".preview_upbtn").show();
				$(this).parent().remove();
			});
		}
		else
		{
			$.showErr(responseObject.message);
		}
	},UploadComplete:function(files){
		//全部上传完成
		uploading = false;
	},Error:function(errObject){
		$.showErr(errObject.message);
	}});
	
	
	$(".location_upbtn input.location_btn").ui_upload({multi:true,FilesAdded:function(files){
		//选择文件后判断
		if($(".location_upload_box").find("span").length+files.length>8)
		{
			$.showErr("最多只能传8张图片");
			return false;
		}
		else
		{
			for(i=0;i<files.length;i++)
			{
				var html = '<span><div class="loader"></div></span>';
				var dom = $(html);		
				$(".location_upload_box").append(dom);	
			}
			uploading = true;
			return true;
		}
	},FileUploaded:function(responseObject){
		if(responseObject.error==0)
		{
			var first_loader = $(".location_upload_box").find("span div.loader:first");
			var box = first_loader.parent();
			first_loader.remove();
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+'/'+responseObject.web_40+'" />'+
			'<input type="hidden" name="supplier_location_images[]" value="'+responseObject.url+'" />';
			$(box).html(html);
			if($(".location_upload_box").find("span").length>=8)
			{
				$(".location_upbtn").hide();
			}
			$(box).find("a").bind("click",function(){
				$(this).parent().remove();
				if($(".location_upload_box").find("span").length<8)
				{
					$(".location_upbtn").show();
				}
			});
		}
		else
		{
			$.showErr(responseObject.message);
		}
	},UploadComplete:function(files){
		//全部上传完成
		uploading = false;
	},Error:function(errObject){
		$.showErr(errObject.message);
	}});

	$("select[name='supplier_id']").live('change',function(){
		change_supplier_name();
	});
	change_supplier_name();

	$("input[name='is_dc']").click(function(){
		if($(this).prop("checked")==true){
			$("#is_dc_set").show();
		}
		else
		{
			$("#is_dc_set").hide();
		}
	});
	
});

function location_submit(){
	$(".location-submit").click(function(){
		var form = $("form[name='location_publish_form']");
		
		$(".location-submit").attr("disabled","disabled");
		var query = $(form).serialize();
		var url = $(form).attr("action");
		
		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$(".location-submit").removeAttr("disabled");
					$.showErr(data.info);
				}else if(data.status==1){
					$.showSuccess(data.info,function(){window.location = data.jump;});
				}
				return false;
			}
		});
		
		return false;
	});
}

function update_submit(){
	$(".location-update").click(function(){
		var form = $("form[name='location_update_form']");
		
		$(".location-submit").attr("disabled","disabled");
		var query = $(form).serialize();
		var url = $(form).attr("action");
		
		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$(".location-submit").removeAttr("disabled");
					$.showErr(data.info);
				}else if(data.status==1){
					$.showSuccess(data.info,function(){
						if(data.jump){
							window.location = data.jump;
						}else{
							window.location.reload();
						}
					});
				}
				return false;
			}
		});
		
		return false;
	});
}

/*地图初始化*/
function ini_map()
{
	var xpoint ='119.3';
	var ypoint ='26.1';
	if($("input[name='xpoint']").val()){
		 xpoint = $("input[name='xpoint']").val();
	}
	
	if($("input[name='ypoint']").val()){
		 ypoint = $("input[name='ypoint']").val();
	}
	draw_map(xpoint,ypoint);	
	$("#search_api").bind("click", function() {
		var api_address = $("input[name='api_address']").val();		
		var city = $(".selected_city span").html();
	    if($.trim(api_address) == '') {
			$.showErr("请先输入地址");
		} else {
			search_api(api_address,city);
		}
	});
	
	$("#container_front").hide();
	$("#cancel_btn").bind("click", function() {
		$("#container_front").hide();
	});
	$("#chang_api").bind("click", function() {
		if($("input[name='xpoint']").val()){
			 xpoint = $("input[name='xpoint']").val();
		}
		
		if($("input[name='ypoint']").val()){
			 ypoint = $("input[name='ypoint']").val();
		}
		editMap(xpoint, ypoint);
	});		
}

//搜索商家
function search_suppllier(){
	$(".search_supplier").click(function(){
		var url=$(this).attr('data-href');
		var key=$(".search_key").val();
		url += "&key="+key;
		
		$.ajax({
			url:url,
			type:"POST",
			dataType : "json",
			success:function(obj)
			{
				if(obj.status==1){
					
					$("#supplier_list").empty();
					$("dl[name=supplier_id] .ui-select-drop").empty();
					
					var list=$("<option value=''>选择商家</option>");
					list.appendTo($("#supplier_list"));
					
					var dd=$("<a href='javascript:void(0);' value='' class='current' style='display: block;'>选择商家</a>");
					dd.appendTo($("dl[name=supplier_id] .ui-select-drop"));
					
					for(var i=0;i<obj.list.length;i++)
					{
						list=$("<option value='"+obj['list'][i]['id']+"'>"+obj['list'][i]['name']+"</option>");
						list.appendTo($("#supplier_list"));
						
						dd=$("<a href='javascript:void(0);' value='"+obj['list'][i]['id']+"' style='display: block;'>"+obj['list'][i]['name']+"</a>");
						dd.appendTo($("dl[name=supplier_id] .ui-select-drop"));
					}
				}
				else{
					$.showErr(obj.info);
				}
			},
			error:function()
			{

			}
		});
	});
}

function init_img_del(){
	$(".preview_upload_box").find("a").bind("click",function(){
		$(".preview_upbtn").show();
		$(this).parent().remove();
	});
	$(".location_upload_box").find("a").bind("click",function(){
		$(this).parent().remove();
		if($(".location_upload_box").find("span").length<8)
		{
			$(".location_upbtn").show();
		}
	});
}

function change_supplier_name(){
	//选择的商户是否开通了达达配送
	var id = $("select[name='supplier_id']").val();
	var location_id = $("input[name='id']").val();
	var query = new Object();
	query.id = id;
	query.location_id = location_id;
	$.ajax({
		url: url,
		data: query,
		dataType:"json",
		type: "POST",
		success:function(data) {
			$(".delivery_box").html(data);
		}

	});

}
