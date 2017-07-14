$(document).on("pageInit", "#dc_resview", function(e, pageId, $page) {
	//通用方法（接单，取消，确认）	
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

});