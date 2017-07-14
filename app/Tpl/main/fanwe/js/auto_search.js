$(document).ready(function(){
	
	
	var prev_text 			 = '';
	var search_form 		 = $("form[name='search_form']");
	var input_search_keyword = $("input[name='search_keyword']");
	var search_key_words_ul  = $('.search_key_words_ul');
	
	// 输入框内容改变事件，请求数据库查询关键字
	input_search_keyword.bind('input propertychange', function() {
		var keyWords 	= $(this).val();
		var query 		= $("input[name='key_words_url']").val();
		var search_type = $("select[name='search_type']").val();
		var lis='';
		if($.trim(keyWords) == '' || $.trim(keyWords) == prev_text){
			if($.trim(keyWords) == ''){
				search_key_words_ul.html('');
				$('.key_words_display').css("display", "none");
			}
			return false;
		}else{
			prev_text = keyWords;
		}
		
		$.post(query, { "keyWords": keyWords, 'search_type':search_type },function(data){
			if(data.status == 1){
				search_key_words_ul.html(data.info);
				$('.key_words_display').css("display", "block");
			}else{
				search_key_words_ul.html('');
				$('.key_words_display').css("display", "none");
			}
			
		}, "json");
	});
	
	//键盘操作
	$(document).keydown(function (event) {
		if($(':focus').attr('name') == 'search_keyword'){
			var li = search_key_words_ul.children("li");
			
			var bg_obj = $('.set_search_bg');
			 
			// 上38
			if(event.keyCode == 38){
				// 先获取是否有已经设置了背景, 如果有, 则在设置上一个背景颜色
				var hc = li.hasClass('set_search_bg');
				var num = li.length - 1;
				 
				if(hc){
					// 第一个
					if(bg_obj.prevAll().length == 0){
						li.eq(num).addClass('set_search_bg');
						li.eq(num).siblings().removeClass('set_search_bg');
						
					}else{
						bg_obj.prev().addClass('set_search_bg');
						bg_obj.prev().siblings().removeClass('set_search_bg');
					}
				}else{
					// 如果没有，则设置第一个
					li.eq(num).addClass('set_search_bg');
				}
				input_search_keyword.val(($('.set_search_bg').attr('key_words')));
				 
			}
			// 下40
			if(event.keyCode == 40){
				// 先获取是否有已经设置了背景, 如果有, 则在设置下一个背景颜色
				var hc = li.hasClass('set_search_bg');
				if(hc){
					// 最后一个
					if(bg_obj.nextAll().length == 0){
						li.eq(0).addClass('set_search_bg');
						li.eq(0).siblings().removeClass('set_search_bg');
					}else{
						bg_obj.next().addClass('set_search_bg');
						bg_obj.next().siblings().removeClass('set_search_bg');
					}
					
				}else{
					// 如果没有，则设置第一个
					li.eq(0).addClass('set_search_bg');
				}
				input_search_keyword.val(($('.set_search_bg').attr('key_words')));
			}
			
		}
		
	});
	
	
	// 单击 下拉的关键字，直接提交
	search_key_words_ul.find("li").live("click",function(){
		input_search_keyword.val( $(this).attr('key_words') );
		search_form.submit();
		
	});
	
	// 鼠标经过的时候改变颜色
	search_key_words_ul.find("li").live("hover",function(){
		 $(this).addClass('set_search_bg');
		 $(this).siblings().removeClass('set_search_bg');
		 
	});
	
	// 除了点击当前form等的元素外，隐藏下拉关键字
	$(document.body).click(function(e) {	
		 
		if( !$(e.target).hasClass('key_words_search_display') )
    	{
			$(".key_words_display").fadeOut("fast", function(){
				search_key_words_ul.html('');
			});
			
    	}
	});
	
 
});
	
