$(function(){
	
	$("select[name='is_store_payment']").bind("change",function(){
		if( $(this).val() == 1 ){
			$(this).parent().parent().nextAll().css('display', '');
			if( $("#selected_promote").find("div").html() == null ){
				$("#selected_promote").parent().css("display", "none");
			}else{
				$("#selected_promote").parent().css("display", "");
			}
		}else{
			$(this).parent().parent().nextAll().not( $(this).parent().parent().nextAll().last().prev() ).css('display', 'none');
		}
		is_store_payment_fx_hide_show();
	});

	$('select[name="open_xn_talk"]').bind('change', function() {
		if ($(this).val() == 1) {
			$('.xn_talk_opt').show();
		} else {
			$('.xn_talk_opt').hide();
		}
	})
	is_store_payment_fx_hide_show();
	$('input[name="is_store_payment_fx"]').bind('change', function() {
		is_store_payment_fx_hide_show();
	})
	function is_store_payment_fx_hide_show(){
		if($('input[name="is_store_payment_fx"]').is(':checked')){
			$(".is_store_payment_fx").show();
		}else{
			$(".is_store_payment_fx").hide();
		}
	}
});

function addRow(obj)
{
	var promote_name = $("#promote_name option:selected").val();
	var url = ROOT+"?"+VAR_MODULE+"=Supplier&"+VAR_ACTION+"=get_promote_html";
	if($("#selected_promote").children().hasClass(promote_name) || promote_name <= 0){
		return false;
	}
	
	$.post(url, { "promote_name": promote_name }, function(data){   
		if(data.status == 1){
			$("#selected_promote").parent().css("display", "");
			$("#checkbox_supplier").parent().css("display", "");
			$("#selected_promote").append(data.info);
		}
	}, "json");
	
	
}
function delRow(obj)
{
	var promote_id = $(obj).attr("promote_id");
	var url = ROOT+"?"+VAR_MODULE+"=Supplier&"+VAR_ACTION+"=delete_promote";
	var status = confirm('请确认是否删除！');
	if( promote_id >=  1 && status){
		$.post(url, { "promote_id": promote_id }, function(data){   
			if(data.status == 1){
				$(obj.parentNode).remove();
			}else{
				alert('删除失败');
			}
		}, "json");
	}else if(status){
		$(obj.parentNode).remove();
	}
}