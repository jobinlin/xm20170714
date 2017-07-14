$(document).on("pageInit", "#dist_user_getpassword", function(e, pageId, $page)  {
    clear_input($('#phonenumer'),$('.j-phone-clear'));
    clear_input($('#sms_verify'),$('.j-verify-clear'));
    clear_input($('#password'),$('.j-password-clear'));

    $("#getpassword").click(function () {
        $("#ph_getpassword").submit();
    });

    $("#ph_getpassword").bind("submit",function(){
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
        if (user_pwd.length < 4) {
            $.toast('密码过短');
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
                if(obj.status) {
                    // 先清理当前页的信息
                    $("input[name='user_mobile']").val('');
                    $("input[name='user_pwd']").val('');
                    $('input[name=sms_verify]').val('');
                    $('#btn').attr('lesstime', 0);

                    // 转弱提示跳转
                    $.toast(obj.info);
                    setTimeout(function() {
                        location.href = obj.jump;
                    }, 1500);
                } else {
                    $.toast(obj.info);
                }
            }
        });

        return false;
    });
});