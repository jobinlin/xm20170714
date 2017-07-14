$(document).ready(function(){

	//绑定图标选择
	if($(".ui_iconfont").length>0)
	{
		var box = $(".ui_iconfont_box");
		$.ajax({
			url:ICON_FETCH_URL,
			dataType:"json",
			type:"POST",
			success:function(obj){
				
				$(".ui_iconfont").html(obj.html);
				$(".ui_iconfont .pickfont").bind("click",function(){
					var code = $.trim($(this).attr("rel"));
					if(code!=''){
						$(box).find(".diyfont").html(code).css("padding",'3px');
					}else{
						$(box).find(".diyfont").html(code).css("padding",0);
					}
					
					
					$(box).find("input[name='m_iconfont']").val(htmlEncode(code));
				});
				
				if($(".ui_iconfont").height()>200)
				{
					$(".ui_iconfont").css("height",200);
				}
			}
		});
	}

});
