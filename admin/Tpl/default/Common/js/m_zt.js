$(document).ready(function(){
	load_zt_preview();
	$("select[name='zt_moban']").bind("change",function(){
		load_zt_preview();
	});
});

function load_zt_preview()
{
	var zt_file = $("select[name='zt_moban']").val();
	var t = zt_file.split(".");
	var zt_moban = t[0];
	//$("#preview").html("<img src='"+APP_ROOT+"/mapi/mobile_zt/preview/"+zt_key+".png' />");

	$.ajax({
		url: ROOT + "?" + VAR_MODULE + "=" + MODULE_NAME + "&" + VAR_ACTION + "=load_html&zt_moban=" + zt_moban+"&zt_id="+zt_id,
		type: "POST",
		success: function (html) {
			$("#preview").html(html);
			$(".zt_moban img").bind("click",function(){
				var zt_img = $(this).attr('class');
				var zt_img_pic = $(this).attr('src');
				var mobile_type = $("#mobileTypeSelect").val();
				var type=$(this).siblings("input[name='type']").val();
				var ctl_name=$(this).siblings("input[name='ctl_name']").val();
				var ctl_value=$(this).siblings("input[name='ctl_value']").val();
				open_zt_box(zt_moban,zt_img,mobile_type,type,ctl_name,ctl_value,zt_img_pic);
			});
		},
	});

	// 隐藏设置链接无效的页面的选项
	if (zt_moban.indexOf('3') > 0 || zt_moban.indexOf('6') > 0) {
		$('table.form tr').eq(8).hide();
		$('table.form tr').eq(9).hide();
	} else {
		$('table.form tr').eq(8).show();
		$('table.form tr').eq(9).show();
	}
}


function open_zt_box(zt_moban,zt_img,mobile_type,type,ctl_name,ctl_value,zt_img_pic){

	var url= ROOT + "?" + VAR_MODULE + "=" + MODULE_NAME + "&" + VAR_ACTION + "=iframe_box&zt_moban=" + zt_moban+"&zt_img="+zt_img+'&mobile_type='+mobile_type+"&type="+type+"&ctl_name="+ctl_name+"&ctl_value="+ctl_value+"&zt_img_pic="+zt_img_pic;
	var query = new Object();
	query.ajax = 1;
	$.ajax({
		url:url,
		type: "POST",
		success:function(html){
				$.weeboxs.open(html, {boxid:"open_zt_box",contentType:'text',showButton:false,title:"图片上传",width:530,onopen:function(){

				},onok:function(){


				}});



		}
	});
}