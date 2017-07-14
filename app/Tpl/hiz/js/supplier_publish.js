$(document).ready(function() {
	//初始化地图
	ini_map();
	init_img_del();
	
	supplier_submit();
	
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
	
	$(".first-cate").bind('click', function() {
		$(".first-cate").removeClass('active');
		$(this).addClass('active');
		$(".sub-cate-wrap").hide();
		$(".sub-cate-wrap").eq($(this).parent().index()).show();
		console.log($(this).parent().index());
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
			'<input type="hidden" name="h_supplier_logo" value="'+responseObject.url+'" />';
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
	
	$(".location_upbtn input.location_btn").ui_upload({multi:false,FilesAdded:function(files){
		//选择文件后判断
		if($(".location_upload_box").find("span").length+files.length>1)
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
			$(".location_upbtn").hide();
			first_loader.remove();
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+'/'+responseObject.web_40+'" />'+
			'<input type="hidden" name="h_supplier_image" value="'+responseObject.url+'" />';
			$(box).html(html);
			$(box).find("a").bind("click",function(){
				$(this).parent().remove();
				$(".location_upbtn").show();
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
	
	$(".license_upbtn input.license_btn").ui_upload({multi:false,FilesAdded:function(files){
		//选择文件后判断
		if($(".license_upload_box").find("span").length+files.length>1)
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
				$(".license_upload_box").append(dom);	
			}
			uploading = true;
			return true;
		}
	},FileUploaded:function(responseObject){
		if(responseObject.error==0)
		{
			var first_loader = $(".license_upload_box").find("span div.loader:first");
			var box = first_loader.parent();
			$(".license_upbtn").hide();
			first_loader.remove();
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+'/'+responseObject.web_40+'" />'+
			'<input type="hidden" name="h_license" value="'+responseObject.url+'" />';
			$(box).html(html);
			$(box).find("a").bind("click",function(){
				$(this).parent().remove();
				$(".license_upbtn").show();
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
	
	$(".other_upbtn input.other_btn").ui_upload({multi:false,FilesAdded:function(files){
		//选择文件后判断
		if($(".other_upload_box").find("span").length+files.length>1)
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
				$(".other_upload_box").append(dom);	
			}
			uploading = true;
			return true;
		}
	},FileUploaded:function(responseObject){
		if(responseObject.error==0)
		{
			var first_loader = $(".other_upload_box").find("span div.loader:first");
			var box = first_loader.parent();
			$(".other_upbtn").hide();
			first_loader.remove();
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+'/'+responseObject.web_40+'" />'+
			'<input type="hidden" name="h_other_license" value="'+responseObject.url+'" />';
			$(box).html(html);
			$(box).find("a").bind("click",function(){
				$(this).parent().remove();
				$(".other_upbtn").show();
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
});


function supplier_submit(){
	$(".supplier-submit").click(function(){
        var h_mobile = $("input[name='h_tel']").val();
        var mobile = $("input[name='account_mobile']").val();
        if(!$.checkMobilePhone(mobile))
        {
            $.showErr("手机号格式不正确");
            return false;
        }
        if(!$.checkMobilePhone(h_mobile))
        {
            $.showErr("法人联系电话，格式不正确");
            return false;
        }
		var form = $("form[name='supplier_publish_form']");
		
		$(".supplier-submit").attr("disabled","disabled");
		var query = $(form).serialize();
		var url = $(form).attr("action");
		
		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$(".supplier-submit").removeAttr("disabled");
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

function init_img_del(){
	$(".pub_upload_img_box").find("a").bind("click",function(){
		$(this).parent().remove();
	});
}

