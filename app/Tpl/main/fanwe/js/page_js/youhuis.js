$(document).ready(function(){
	$(".more-cate").click(function() {
		var m_height=$(".cate-list").height();
		$(this).toggleClass('active');
		if ($(".cate-wrap").hasClass('active')) {
			$(".cate-wrap").removeClass('active').css('height', '42px');
		} else {
			$(".cate-wrap").addClass('active').css('height', m_height);
		}
	});
	/*左边列表的hover事件*/
	$(".yh_l .back").hover(function(){
		show_scan_box(this);
		$(this).addClass("current");
	},
	function(){
		hide_scan_box(this);
		$(this).removeClass("current");
	});
	
	/*右边领券动态hover*/
	$(".get_list").each(function(i,get_list){
		$(get_list).find("ul li:eq(0)").addClass("current");
		$(get_list).find("ul li").hover(function(){
			$(this).addClass("current").siblings().removeClass("current");
		});
	});
	
	$(".j-get-youhui").bind("click",function(){
		var data_id=$(this).attr("data-id");
		
		var query = new Object();
		query.act = "download_youhui";
		query.id = data_id;
		$.ajax({
			url:AJAX_URL,
			data:query,
			dataType:"json",
			type:"POST",
			success:function(obj){
				//console.log(obj);
				$.showSuccess(obj.info,function(){
					if(obj.change_status == 1){
						$(".j-get-youhui[data-id='"+data_id+"']").unbind("click");
						if(obj.use_status == 1){
							$(".j-get-youhui[data-id='"+data_id+"']").attr("href",obj.jump);
						}else{
							$("li[data-id='"+data_id+"']").addClass("disable");
						}
						$(".j-get-youhui[data-id='"+data_id+"']").html(obj.button_info);
						$(".j-get-youhui[data-id='"+data_id+"']").removeClass("j-get-youhui");
					}
				});
			}
		});
		
	});

});