/**
 * Created by Administrator on 2016/11/16.
 */
$(document).on("pageInit", "#uc_order_dp", function(e, pageId, $page) {
   $(".j-stars .iconfont").click(function () {
       var _index=$(this).index();
       var val=parseInt(_index)+1;
       $(this).parent().parent().find(".star").val(val);
       var $icon=$(this).parent().find(".iconfont");
        $icon.each(function (i) {
           if(i<=_index){
               $icon.eq(i).addClass("active");
           }else {
               $icon.eq(i).removeClass("active");
           }
        });

   });
   
   $(".send_dp").click(function(){
	   $("form[name='dp_submit_form']").unbind("submit");
	   do_dp_form();
	});
   function do_dp_form()
   {
		$("form[name='dp_submit_form']").bind("submit",function(){
			
			var evaluate=$(this).find(".evaluate li");
			
			for(var i=0;i<evaluate.length;i++){
				if(evaluate.eq(i).find(".dp_centent").val()==""){
					$.toast("请填写评价内容");
					return false;
				}else if(evaluate.eq(i).find(".star").val()==""){
					$.toast("请选择评分");
					return false;
				}
			}
			
			/*var i=0;
			$(this).find(".dp_centent").each(function(){
				if($.trim($(this).val())==""){
					$.toast("请填写评价内容");
					return;
				}
				i++;
			});
			$(this).off;
			var k=0;
			$(this).find(".star").each(function(){
				if($.trim($(this).val())==""){
					$.toast("请选择评分");
					return;
				}
				k++;
			});*/
			
//			if(i>=$(this).find(".dp_centent").length  && k>=$(this).find(".star").length){
				var url = $(this).attr("action");
				var query = $(this).serialize();
				$.ajax({
					url:url,
					data:query,
					dataType:"json",
					type:"POST",
					success:function(obj){
						$.toast(obj.info);
						$("form[name='dp_submit_form']").unbind("submit");
						if(obj.jump){
							setTimeout(function(){
								location.href = obj.jump;
							},1000);
						}
					}
				});
//			}

			return false;
		});
	}
});