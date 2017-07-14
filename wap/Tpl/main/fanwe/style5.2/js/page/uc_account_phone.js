$(document).on("pageInit", "#uc_account_phone", function(e, pageId, $page) {
	$('.userBtn').on('click', function(){

		$("form").submit( function () {
			return false;
		});
		
		var mobile = $('input[name=mobile]').val();
		var sms_verify = $('input[name=sms_verify]').val();

		if ($.trim(mobile) == '') {
			$.toast('请输入要绑定的手机号码');
			return false;
		}
		if ($.trim(sms_verify) == '') {
			$.toast('请输入收到的验证码');
			return false;
		}

		if (!/^[0-9]{6}$/.test(sms_verify)) {
			$.toast('验证码格式有误');
			return false;
		} else if (!/^1[34578][0-9]{9}/.test(mobile)) {
			$.toast('手机格式有误');
			return false;
		}

		var step = $('input[name=step]').val();
		if (!/^\d$/.test(step)) {
			$.alert('网络异常请刷新重试', function() {
				window.location.reload();
				return false;
			})
			
		}
		var is_luck = $('input[name=is_luck]').val();
		var query = new Object();
		query.mobile = mobile;
		query.sms_verify = sms_verify;
		var is_fx = $('input[name=is_fx]').val();
		query.is_fx = is_fx;
		query.step = step;
		if(step==2)
			query.is_luck = is_luck;
		query.act = 'bindPhone';
		$.ajax({
			url: bind_url,
			data: query,
			dataType: "json",
			type: "post",
			success: function(obj){
				if(obj.user_login_status==0){
					$.alert(obj.info,function(){
						$.router.load(obj.jump);
					});
				} else if(obj.status == 0) {
					$.toast(obj.info);
				} else {
					// 处理页面跳转逻辑
					if (step == 2) { // 绑定成功跳掉用户中心
						// $('.sendBtn').attr('lesstime', 0);
						// $.alert(obj.info, function() {
						// 	window.location.href = obj.jump;
						// });
						$.toast(obj.info);
						setTimeout(function() {
			                // $.router.load(obj.jump, true);
			                // 跳转回上一页
			                if (obj.jump) {
			                	$.router.load(obj.jump, true);
			                } else if (referer_url) {
			                	$.router.load(referer_url, true);
			                } else {
			                	$.router.back();
			                }
			            }, 2000);
					} else { // 绑定新的手机号码
						$('input[name=mobile]').attr('type','tel');
						$('input[name=mobile]').attr('value', '');
						$('input[name=format_mobile]').attr('type','hidden');
						
						$('input[name=mobile]').val('');
						$('input[name=mobile]').attr('placeholder', '请输入新的手机号码');
						$('input[name=sms_verify]').val('');
						$('.title').text(obj.page_title);
						$('input[name=step]').attr('value', 2);
						$('.sendBtn').removeClass("isUseful");
						$('.sendBtn').addClass("noUseful");
						$('.sendBtn').attr('lesstime', 0);
						$('input[name=is_luck]').attr('value', obj.is_luck);
						$('.sendBtn').attr('unique',4);

						$('.userBtn').val('确定');
					}
				}
				
			},
			error:function(ajaxobj) {
				$.toast('网络异常');
			}
		});
	})
})
