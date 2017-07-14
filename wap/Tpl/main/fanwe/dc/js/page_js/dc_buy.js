$(document).ready(function(){

/*
	$('.del_one').live('click',function(){
		var num=-1;
		dc_change_num($(this),num);
	});
	
	$('.add_one').live('click',function(){
		var num=1;

		dc_change_num($(this),num);
	});
*/	
	

	$('#dc_cart_clear').live('click',function(){
		dc_cart_clear();
		
	});
	
	$('#dc_total ,#dc_cart_close').live('click',function(){

		$('#dc_cart').slideToggle();
		
	});
	
	
		$(".rsorder_view_close_wind").click(function(){
		
			 $(".show_wind").hide();
	   });
		
});


function dc_change_num(id,count,num){
	/*
		var menu_o=$(".menu_right[menu-id='"+id+"']");
		var menu_id=parseInt(menu_o.attr('menu-id'));
		var number=parseInt(num);
		var number_total=parseInt(menu_o.attr('menu-count'))+num;
*/
	//var menu_o=$(".menu_right[menu-id='"+id+"']");
	var menu_id=parseInt(id);
	var number=parseInt(num);
	var number_total=parseInt(count)+num;
	
		var ajaxurl=DC_AJAX_URL;
		var query=new Object();
		query.menu_id=menu_id;
		query.number=number;
		query.number_total=number_total;
		query.tid=tid;
		query.location_id=location_id;
		query.supplier_id=supplier_id;
		query.distance=distance;
		query.act='dc_add_cart';
		$.ajax({
				url:ajaxurl,
				data:query,
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.status==1){
					$('#dc_cartsection').html(data.html);
					
					$('.total_price').html(data.total_price);
					
					$(".menu_right[menu-id='"+menu_id+"']").html(data.cart_add);
	
					$('#dc_cart').html(data.dc_cart);

					/*
					$('.del_one').bind('click',function(){
						var num=-1;
						dc_change_num($(this),num);
					});
					
					$('.add_one').bind('click',function(){
						var num=1;
						dc_change_num($(this),num);
					});
	*/
					}else{
						$.showErr(data.info);
					}
				}
		});
		
}



function dc_blur_num(o,num){
		var menu_o=o.parent();
		var menu_id=parseInt(menu_o.attr('menu-id'));
		var number=parseInt(num);
		if(!number){
			
			var number=0;	
		}
		var number_total=0;
		var ajaxurl=DC_AJAX_URL;
		var query=new Object();
		query.menu_id=menu_id;
		query.number=number;
		query.number_total=number_total;
		query.tid=tid;
		query.inp_stat=1;
		query.location_id=location_id;
		query.supplier_id=supplier_id;
		query.distance=distance;
		query.act='dc_add_cart';
		$.ajax({
				url:ajaxurl,
				data:query,
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.status==1){
					$('#dc_cartsection').html(data.html);
					
					$('.total_price').html(data.total_price);
					
					$(".menu_right[menu-id='"+menu_id+"']").html(data.cart_add);
	
					$('#dc_cart').html(data.dc_cart);


					}else{
						$.showErr(data.info);
					}
				}
		});
		
}




function dc_cart_clear(){
		var query=new Object();
		var ajaxurl=DC_AJAX_URL;
		query.location_id=location_id;
		query.act='dc_cart_clear';
		$.ajax({
				url:ajaxurl,
				data:query,
				type:'post',
				dataType:'json',
				success:function(data){
					if(data.status==1){

						location.href=location.href;
					}
				}
		});
		
}

function show_window(obj){
	
	 var show_tr=document.getElementById("show_"+obj);
	 		
		 show_tr.style.display="block";
}