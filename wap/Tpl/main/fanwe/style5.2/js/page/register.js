/**
 * Created by Administrator on 2016/10/14.
 */
$(document).on("pageInit", "#user_register", function(e, pageId, $page)  {
	clear_input($('#phonenumer'),$('.j-phone-clear'));
	clear_input($('#sms_verify'),$('.j-verify-clear'));
	clear_input($('#password'),$('.j-password-clear'));

    var _cli=0;
    $(".eyes").click(function () {
        _cli++;

        if(_cli==1){
            $(".eyes-no").hide();
            $(".eyes-yes").show();
            $(".password").attr("type","text");
        }
        if(_cli==2){
            $(".eyes-no").show();
            $(".eyes-yes").hide();
            $(".password").attr("type","password");
        }
        if(_cli>=2){
            _cli=0;
        }
    });

    $(".userBtn-yellow").click(function () {
    	$("#ph_register").submit();
    });
    //手机注册
    $("#ph_register").bind("submit",function(){
		
		var mobile = $.trim($(this).find("input[name='user_mobile']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());
		if(mobile=="")
		{
			$.toast("请输入手机号");
			return false;
		}
		if(user_pwd=="")
		{
			$.toast("请输入密码");
			return false;
		}
		if(sms_verify=="")
		{
			$.toast("请输入收到的验证码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		$.ajax({
			url:ajax_url,
			data:query,
			type:"POST",
			dataType:"json",
			success:function(obj){
				if(obj.status)
				{
					$("#prohibit").show();
					$.toast(obj.info);
					window.setTimeout(function(){
						location.href = obj.jump;
						},1500);
				}
				else
				{
					$.toast(obj.info);
				}
			}
		});
		
		return false;
	});


    /*var _input=$("input");
    _input.each(function (e) {
        $(this)[0].addEventListener("blur",function () {
            
            document.querySelector(".third-login").style.display="block";
        },false);

        $(this)[0].addEventListener("focus",function () {
            document.querySelector(".third-login").style.display="none";
        },false);
    });*/

});