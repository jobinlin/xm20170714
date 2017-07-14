//首页的部份演示脚本
$(document).ready(function(){
				
	//空表单的submit点击
	$("button.noform").bind("click",function(){
		$.showErr("橙色提交空表单按钮被点击");
	});
	
	//以下代码testformbtn的点击事件，不会被执行，因为有表单事件，已被绑定为表单的submit
	$("button.testformbtn").bind("click",function(){
		$.showErr("该事件不会被执行");
	});
	
	$("form[name='testform']").bind("submit",function(){
		if($("#test1").val()=="")
		{
			$.showConfirm("确认为空",function(){
				$.showSuccess("确认成功");
			});
			return false;
		}
	});
	
	//评分星级的测试
	$("#demostar_ipt").bind("onchange",function(){		
		alert("当前"+$(this).val()+"星");
	});
	$("#demostar_ipt").bind("uichange",function(){		
		$("#starcontent").html("当前"+$(this).attr("sector")+"星");
	});
	$("#demostar").click(function(){
		$("#demostar_ipt").val(Math.random()*5);
		$("#demostar_ipt").ui_starbar({refresh:true});
	});
	
	//上传控件
	$("#upfile_btn").ui_upload({url:K_UPLOAD_URL,multi:false,FilesAdded:function(files){
		//选择文件后判断
		if($("#pub_upload_img_box").find("span").length+files.length>8)
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
				$("#pub_upload_img_box").append(dom);	
			}
			uploading = true;
			return true;
		}
	},FileUploaded:function(responseObject){
		if(responseObject.error==0)
		{
			var first_loader = $("#pub_upload_img_box").find("span div.loader:first");
			var box = first_loader.parent();			
			first_loader.remove();
			
			var html = '<a href="javascript:void(0);"></a>'+
			'<img src="'+APP_ROOT+responseObject.url+'" />';
			
			$(box).html(html);
			$(box).find("a").bind("click",function(){
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
	
	
	$("#editor").ui_editor({url:K_UPLOAD_URL,width:"500",height:"300"});
	$("input[name='time_input']").datetimepicker({timeFormat: "HH:mm:ss",dateFormat: "yy-mm-dd"});
});

