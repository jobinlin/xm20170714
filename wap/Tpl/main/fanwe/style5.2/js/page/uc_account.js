$(document).on("pageInit", "#uc_account", function(e, pageId, $page) {
	$(".progress-bar-inner").each(function() {
		var progress_width = $(this).attr('data-width');
		$(this).css('width', progress_width);
	});

	// 修改密码前先验证用户是否已绑定手机号
	$('.bindphone').bind('click', function() {
		if (($('input[name=phone]').val() != 1)) {
			$.toast('请先绑定手机号');
			$.router.load($(this).attr('phone-href'));
		} else {
			$.router.load($(this).attr('href-data'));
		}
	});
	//请求绑定微信，获得微信授权
	if(is_weixin_bind){
		js_weixin_login("",1);
	}
	//解绑微信
	$('.wx_unbind').bind('click', function () {
		
		var ajax_url = $(this).attr("action");
		var query = '';
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$.toast(obj.info);
					if(obj.jump){
						$.loadPage(REFRESH_URL);
						//location.href=REFRESH_URL;
						//$.router.load(obj.jump,true);
					}		
				}
				else
				{
					$.toast(obj.info);
					if(obj.jump){
						$.router.load(obj.jump,true);
					}
							
				}
			}
		});
		
		return false;
	});


	// 修改头像。
	$('#up_avatar').bind('change', function() {
		lrz(this.files[0], {width: 200})
			.then(function(rst) {
				// 处理上传到后端的逻辑
				rst.formData.append('fileLen', rst.fileLen);

				$.ajax({
					url: UPLOAD_URL,
					data: rst.formData,
					processData: false,
					contentType: false,
					type: 'POST',
					success: function(obj) {
						var data = JSON.parse(obj);
						if (data.error == 1000) {
							$.router.load(LOGIN_URL, true);
						} else if (data.error == 2000) {
							$.toast('图片上传发生错误,跟换浏览器重试');
						} else if (data.error > 0) {
							$.toast('图片上传发生错误');
						} else {
							$('#user_avatar').attr('src', rst.base64);
							$.toast('头像已修改');
						}
					},
					error: function(msg) {
						$.toast('网络被风吹走了～');
					}
				})
			})
			.catch(function(err) {
				// 捕获错误
				$.toast('数据异常,请重试');
			})
			.always(function() {
				// 总是会发生。要发生什么
			});
	});
	$("#app_up_avatar").on("click",function () {
		App.CutPhoto();
	});

});




