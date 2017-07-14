/**
 * Created by Administrator on 2016/11/14.
 */

    var lock=false;
	$(".submitBtn").click(function(){
		if(lock){
			return;
		}
		lock=true;
    	$("#order_msg").submit();
    });

    $('textarea[name=content]').on('input propertychange', function() {
        var that = $(this),
            _val = that.val();
        if (_val.length > 100) {
            that.val(_val.substring(0, 100));
        }
    });
	

	$("#order_msg").bind("submit",function(){
		
		var len = $.trim($('textarea[name=content]').attr('value')).length;
		if (len < 1) {
			$.toast('请输入退款理由');
			return false;
		}
		if (len > 100) {
			$.toast('退款理由应不大于100字');
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

//					$("#uc_order_refund").remove();
//					$("#uc_order_message").remove();
//					$(".page").addClass("page-current");
//					return false;
				}
				else
				{
					$.toast(obj.info);
					lock=false;
					return false;
				}
				return false;
			}
		});
		lock=false;
		return false;
	});

