
	$(".j-submit-input").each(function(){
		var data = $(this).attr("data");
		var txt = data.split(",");
		var lab = $(this).attr("data-lab");
		$(this).picker({
		toolbarTemplate: '<header class="bar bar-nav">'+
							'<button class="button button-link pull-right close-picker">确定</button>'+
							'<h1 class="title">请选择' + lab + '</h1>'+
						  '</header>',
		cols: [
			{
				textAlign: 'center',
				values: txt
			}
			]
		});
	});
	
	/*
	 *打开下拉导航窗口
	 *触发源.j-opendropdowm
	*/
	/*$("#event_submit").find(".j-opendropdowm").click(function(){
		alert(1111);
		$(".j-flippedout-close").attr("rel","dropdowm");
		$(".flippedout").addClass("showflipped").addClass("dropdowm-open");
		$(".m-nav-dropdown").addClass("showdropdown");
		$(".m-nav-dropdown .nav-dropdown-con").addClass("dropdown-open");
		$(".j-flippedout-close").children(".iconfont").addClass("jump");
	});*/
	
	/*$(".btn-con").click(function(){
		$("form[name='event_submit_form']").unbind("submit");
		init_event_form();
	});*/
	init_event_form();
	
	function init_event_form()
	{
		
		$("form[name='event_submit_form']").bind("submit",function(){
			
			var is_err = 0;
			$(".submit-box input").each(function(){
				if($(this).val()==''){
					is_err++;
				}
			});
			if(is_err>0){
				$.toast("请正确填写报名项");
				return false;
			}
			var url = $(this).attr("action");
			var query = $(this).serialize();
			$.ajax({
				url:url,
				data:query,
				dataType:"json",
				type:"POST",
				success:function(obj){
					$.toast(obj.info);
					if(obj.status==1){
						setTimeout(function(){
							location.href = obj.jump;
						},1000);
					}
				}
			});
			
			return false;
		});
	}
