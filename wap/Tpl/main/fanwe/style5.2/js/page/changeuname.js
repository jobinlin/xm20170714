/**
 * Created by Administrator on 2016/11/28.
 */
$(document).on("pageInit", "#changeuname", function(e, pageId, $page) {
    $(".userBtn-yellow").click(function () {
        $("#ph_getuname").submit();
    });


    $("#ph_getuname").bind("submit",function(){
        var user_name = $.trim($(this).find("input[name='user_name']").val());
        if(user_name=="")
        {
            $.toast("请输入昵称");
            return false;
        }
        
        //获取字符长度（包括中文）
        var name_len = getByteLen(user_name);
        if (name_len < 4) {
            $.toast('昵称过短');
            return false;
        }
        if(/\_/.test(user_name) == true){
        	$.toast('用户名不能使用下划线');
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
    
    
    function getByteLen(val) { 
    	var len = 0; 
    	for (var i = 0; i < val.length; i++) { 
	    	if (val[i].match(/[^\x00-\xff]/ig) != null){
		    	len += 2; 
	    	} //全角 
	    	else{
		    	len += 1; 
	    	} 
    	} 
    	return len; 
    } 
    
});