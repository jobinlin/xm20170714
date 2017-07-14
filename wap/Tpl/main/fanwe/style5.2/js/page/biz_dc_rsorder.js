$(document).on("pageInit", "#biz_dc_rsorder", function(e, pageId, $page) {
	init_listscroll(".j-ajaxlist-"+sort_1,".j-ajaxadd-"+sort_1);
	
	$(document).on('click', '.j-submit', function() {
		var url = $(this).attr('data_url');
		var query = new Object();
		$.ajax({
      	  url:url,
      	  type:'post',
      	  dataType:'json',
      	  success:function(data){
      		  if(data.status == 1){
       			 $.toast(data.info); 
       			 setTimeout(function () {
  				  location.reload(); 
			      }, 2000);
      		  }else{
      			$.toast(data.info);
      			 setTimeout(function () {
     				  location.reload(); 
   			      }, 2000);
      		  }
      	  }
        });
	});
	
	
	function tab_line() {
		var init_width=$(".j-list-choose.active span").width();
		var init_left=$(".j-list-choose.active span").offset().left;
		$(".list-nav-line").css({
			width: init_width,
			left: init_left
		});
	}
	tab_line();
	
	//分类加载内容
	$(".j-list-choose").on('click', function() {
		$(document).off('infinite', '.infinite-scroll-bottom');
		var sort=$(this).attr("sort");
		//alert(sort);
		$(".j-list-choose").removeClass('active');
		$(this).addClass('active');
		$(".biz-order-list").hide();
		tab_line();
		var url=$(this).attr("data-href");
		$(".j-ajaxlist-"+sort).show();
		$(".content").scrollTop(1); 
		if($(".j-ajaxlist-"+sort).html()==null){
			  $.ajax({
			    url:url,
			    type:"POST",
			    success:function(html)
			    {
			      //console.log("成功");
			      
			      $(".content").append($(html).find(".content").html());
			      init_listscroll(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			    },
			    error:function()
			    {
			    	
			    	$(".j-ajaxlist-"+sort).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
			      //console.log("加载失败");
			    }
			  });
		}
		else{
			if( $(".content").scrollTop()>0 ){
				infinite(".j-ajaxlist-"+sort,".j-ajaxadd-"+sort);
			}
        }

	});


	
});