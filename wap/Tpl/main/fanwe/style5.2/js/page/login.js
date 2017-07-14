/**
 * Created by Administrator on 2016/10/13.
 */
$(document).on("pageInit", "#user_login", function(e, pageId, $page)  {
	clear_input($('#phonenumer'),$('.j-phone-clear'));
	clear_input($('#sms_verify'),$('.j-verify-clear'));
	clear_input($('#user_key'),$('.j-name-clear'));
	clear_input($('#password'),$('.j-password-clear'));
	$(document).on('click','.open-popup', function () {
	var url=$(".open-popup").attr("data-src");
	  $.ajax({
	    url:url,
	    type:"POST",
	    success:function(html)
	    {
	      //console.log("成功");

	      $(".popup-agreement .protocol").html($(html).find(".content").html());
	      $(".popup-agreement .title").html($(html).find(".title").html());
	    },
	    error:function()
	    {

	    	$(".popup-agreement").html("网络被风吹走啦~");
	      //console.log("加载失败");
	    }
	  });
	});
   $(".tab-ways li").click(function () {
       var index=$(this).index();
       $(this).addClass("active").siblings("li").removeClass("active");
       $(this).removeClass("b-line").siblings("li").addClass("b-line");
       $(".phone-login").hide();
       $(".phone-login").eq(index).show();
   });


    
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

    

    
    //var wait=60;
    
    
    
    
    //if($("#btn").attr("lesstime")>0){
    	//wait = $("#btn").attr("lesstime");
    //	time($("#btn"));
    //}
    
    var lock = 0; // 防止频繁提交

    //账号密码登录
    $("#com_login_box").bind("submit",function(){
		var user_key = $.trim($(this).find("input[name='user_key']").val());
		var user_pwd = $.trim($(this).find("input[name='user_pwd']").val());
		if(user_key=="")
		{
			$.toast("请输入登录帐号");
			return false;
		}
		if(user_pwd=="")
		{
			$.toast("请输入密码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		if (!lock) {
			lock = 1;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj) {
					if(obj.status) {
						$("#prohibit").show();
						$.toast(obj.info);
						window.setTimeout(function(){ 
							if(obj.url!="")
								location.href = obj.url;
							else
								location.href = obj.jump;
							},1500); 			
					} else {
						$.toast(obj.info);
					}
					setTimeout(function() {
						lock = 0;
					}, 3000);
				}
			});
		}
		
		return false;
	});
    //手机快捷登录
    
    $("#ph_login_box").bind("submit",function(){
		
		var mobile = $.trim($(this).find("input[name='mobile']").val());
		var sms_verify = $.trim($(this).find("input[name='sms_verify']").val());
		if(mobile=="")
		{
			$.toast("请输入手机号");
			return false;
		}
		if(sms_verify=="")
		{
			$.toast("请输入收到的验证码");
			return false;
		}
		
		var query = $(this).serialize();
		var ajax_url = $(this).attr("action");
		if (!lock) {
			lock = 1;
			$.ajax({
				url:ajax_url,
				data:query,
				type:"POST",
				dataType:"json",
				success:function(obj){
					if(obj.status) {
						$("#prohibit").show();
						$.toast(obj.info);
						window.setTimeout(function(){
							location.href = obj.jump;
							},1500);				
					} else {
						$.toast(obj.info);
					}
					setTimeout(function() {
						lock = 0;
					}, 3000);
				}
			});
		}
		
		return false;
	});
    
});