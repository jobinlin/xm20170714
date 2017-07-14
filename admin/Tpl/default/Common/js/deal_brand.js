$(document).ready(function(){
	$("select[name='brand_promote']").bind("change",function(){
		init_brand_promote();
	});
	init_brand_promote();
	
	//根据分类获取品牌
	$("select[name='shop_cate_id']").change(function(){
		var cate_id=$(this).val();
		var query = new Object();
		query.cate_id = cate_id;
		$.ajax({ 
			url: ROOT+"?"+VAR_MODULE+"="+MODULE_NAME+"&"+VAR_ACTION+"=brand_list", 
			data: query,
			dataType:"json",
			success: function(obj){
				$("select[name='brand_id']").empty();
				$option=$("<option value='0'>==未选择==</option>");
				$option.appendTo($("select[name='brand_id']"));
				var data=eval(obj);
				for(var i=0;i<data.length;i++){
					$option=$("<option value='"+data[i]['id']+"'>"+data[i]['name']+"</option>");
					$option.appendTo($("select[name='brand_id']"));
				}
			}
		});
	});
});

function init_brand_promote()
{
	var is_brand_promote = $("select[name='brand_promote']").val();
	if(is_brand_promote==0)
	{
		$(".brand_promote").show();
	}
	else
	{
		$(".brand_promote").hide();
	}
}