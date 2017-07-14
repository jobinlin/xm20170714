$(document).ready(function(){
	$(".invoice_type").bind("click",function(){
		var display = 'none';
		if ($(this).val() == 1) {
			$('.content-box').removeClass('hide');
		} else {
			$('.content-box').addClass('hide');
		}
	});

	var orig_ct = '';
	$('.add-content').bind('click', function() {
		var content = $.trim($('.pre-content').val());
		if (content == '') {
			return false;
		}
		if (content.length > 6) {
			alert('内容应在6个字以内');
			return false;
		}
		if (content == orig_ct) {
			return false;
		}
		orig_ct = content;

		var html = '<div class="content-item"><span>'+content+'</span>';
		html += '<input type="hidden" name="invoice_content[]" value="'+content+'"><span class="del">X</span>';
		html += '<input type="button" value="删除" onclick="delRow(this)"></div>';
		$(html).appendTo('.content-list');
	});

	
	$("form[name='invoice']").submit(function(){
		
		var form = $("form[name='invoice']");
		var query = $(form).serialize();
		var url = $(form).attr("action");
		$.ajax({
			url:url,
			data:query,
			type:"post",
			dataType:"json",
			success:function(data){
				if(data.status == 0){
					$.showErr(data.info);
				}else if(data.status==1){
					$.showSuccess(data.info);
				}
				return false;
			}
		});
		
		return false;
	});
});


function delRow(obj) {
	var parentDom = $(obj).parent('div');
	$(parentDom).remove();
}
