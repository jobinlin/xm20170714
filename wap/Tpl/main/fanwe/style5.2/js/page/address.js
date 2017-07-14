/**
 * Created by Administrator on 2016/9/8.
 */




$(document).on("pageInit", "#uc_address_index", function(e, pageId, $page){
	
	
	
	
    $("#uc_address_index").on('click','.confirm-address', function () {
        var _this=$(this);
        $.confirm('确定要删除该地址吗？', function () {
        	$.ajax({
				url: _this.attr('del_url'),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status == 1){
						_this.parents("li").remove();
					}else{
						$.alert(obj.info);
					}
				},
        	});
        });
    });


    $("#uc_address_index").on("change",".j-address-set input[type=radio]",function () {
		

        if($(this).prop('checked')==true){

			var vobj=$(this);
        	$.ajax({
				url: $(this).attr('dfurl'),
				data: {},
				dataType: "json",
				type: "post",
				success: function(obj){
					if(obj.status == 1){
						vobj.parents(".j-address-set").find(".u-set-default").addClass("j-address-color");
						vobj.parents("li").siblings("li").find(".u-set-default").removeClass("j-address-color");
					}else{
						$.toast("失败");
					}
				},
        	});
            
        }
    });
    

});