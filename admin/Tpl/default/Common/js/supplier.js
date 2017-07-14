$(document).ready(function(){
	$("select[name='city_id']").bind("change",function(){
		set_area();
	});
	set_area();
	$("input[name='supplier_key_btn']").bind("click",function(){
		search_supplier();
	});
	
	$("input[name='location_key_btn']").bind("click",function(){
		search_supplier_location();
	});

});

function set_area()
{
	var city_id =$("select[name='city_id']").val();
	var id = $("input[name='id']").val();
	//alert(ACTION_NAME);
	if(ACTION_NAME=="biz_apply_edit")
		var edit_type=2;
	else
		var edit_type=1;
	$.ajax({ 
		url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=area_list&city_id="+city_id+"&id="+id+"&edit_type="+edit_type, 
		data: "ajax=1",
		success: function(obj){
			$("#area_list").html(obj);
			if($("#purpose_list").length > 0){
				get_purpose();
			}
			if($("#big_cate_box").length > 0){
				init_load_cate();
			}
		}
	});	
}