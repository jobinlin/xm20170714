$(document).ready(function(){


	var biz_root = window.parent.biz_root;
	var app_root = window.parent.app_root;
	
	var tool_bar='<div class="pic_tool_box"><a class="up-btn tip_icon" href="javascript:void(0);" onclick="up_img_box(this)"><div class="tool_tip">上移</div></a><a class="down-btn tip_icon" href="javascript:void(0);" onclick="down_img_box(this)"><div class="tool_tip">下移</div></a><a class="close-btn tip_icon" href="javascript:void(0);" onclick="del_img_box(this)"><div class="tool_tip">删除</div></a></div>';
	
	var phone_field=$("#phone_description",window.parent.document).attr("name");  //定义字段名
	
	var re = new RegExp('src="./public',"g");
	var content = $("#phone_description",window.parent.document).val().replace(re,'src="'+app_root+'/public');
	
	var rex = new RegExp('contenteditable="false"',"g");
	var content = content.replace(rex,'contenteditable="true"');
	
	$("#image-lib").html(content);
	$("#image-lib .review_unit").append(tool_bar);
	
	var re = new RegExp(app_root,"g");
	var content = $("input[name='"+phone_field+"']",window.parent.document).val().replace(re,".");
	$("input[name='"+phone_field+"']",window.parent.document).val(content);

	/*图片上传*/
	var img_index = 0;
	$("#file-btn").live("change",function(){

		 if(this.files[0].type=='image/png'||this.files[0].type=='image/jpeg'||this.files[0].type=='image/gif'){	 
			$("form[name='phone_upload']").submit(); 

		}else{
			alert("上传的文件格式有误");
		}
	});
	
	
	$(".add_text").bind("click",function(){
		var text_box='<div class="review_unit"><div class="text_edit" contenteditable="true"><br/></div>'+tool_bar+'</div>';
		$("#image-lib").append(text_box);
		sys_description(phone_field);
		
	});
	
	$(".text_edit").live("keyup",function(){
		sys_description(phone_field);
	});
	
	
	
	$("form[name='phone_upload']").submit(function(){
		//var file_url = $("form[name='phone_upload']").attr("action");
		var file_url = biz_root;
		var query = $("form[name='phone_upload']").serialize();
		file_url+='?'+query;
            $.ajaxFileUpload
            (
                {
                    url: file_url, //用于文件上传的服务器端请求地址
                    secureuri: false, //是否需要安全协议，一般设置为false
                    fileElementId: 'file-btn', //文件上传域的ID
                    dataType: 'json', //返回值类型 一般设置为json
                    success: function (data, status)  //服务器成功响应处理函数
                    {
					
						if(data.status==1){
							var img_url='<div class="review_unit"><img class="u_img" src="'+data.file_url+'" />'+tool_bar+'</div>';
							$("#image-lib").append(img_url);
							
							sys_description(phone_field);
						}else{
						
							alert(data.info);
						}
						
					
			
                    },
                    error: function (data, status, e)//服务器响应失败处理函数
                    {
                        alert(e);
                    }
                }
            )
            return false;
	}); 

});
	
		
function del_img_box(o){
	$(o).parents('.review_unit').remove();
	var phone_field=$("#phone_description",window.parent.document).attr("name");  //定义字段名
	sys_description(phone_field);
}

function up_img_box(o){
	var obj=$(o).parents('.review_unit');
	var obj_before=$(obj).prev();
	$(obj).after($(obj_before));
	var phone_field=$("#phone_description",window.parent.document).attr("name");  //定义字段名
	sys_description(phone_field);
}

function down_img_box(o){
	var obj=$(o).parents('.review_unit');
	var obj_next=$(obj).next();
	$(obj).before($(obj_next));
	var phone_field=$("#phone_description",window.parent.document).attr("name");  //定义字段名
	sys_description(phone_field);
}

function sys_description(phone_field){
	var app_root = window.parent.app_root;
	var content_obj = $("#image-lib").clone();
	$("#hidden_box").append(content_obj);
	$("#hidden_box").find(".pic_tool_box").remove();
	//var content = $("#hidden_box #image-lib").html().replace(app_root,"."); //只替换第一个
	var re = new RegExp(app_root,"g");
	var content = $("#hidden_box #image-lib").html().replace(re,".");
	
	var rex = new RegExp('contenteditable="true"',"g");
	var content = content.replace(rex,'contenteditable="false"');
	
	$("input[name='"+phone_field+"']",window.parent.document).val(content);
	$("#hidden_box").empty();

}
