$(document).ready(function(){
	$("input[name='invoice_type']").bind("click",function(){
		var display = 'none';
		if ($(this).val() == 1) {
			display = '';
		}
		$('.content-box').css('display', display);
	});

	var orig = '';
	$('.add-content').bind('click', function() {
		var content = $.trim($('.pre_content').val());
		if (content == '') {
			return false;
		}
		if (content.length > 6) {
			alert('内容应在6个字以内');
			return false;
		}
		if (content == orig) {
			return false;
		}
		orig = content;
		var html = '<div class="content-item"><span>'+content+'</span>';
		html += '<input type="hidden" name="invoice_content[]" value="'+content+'">';
		html += '<a class="del" href="javascript:void(0);"  onclick="delRow(this)">X</a></div>';
		$(html).appendTo('.content-list');
	});
});


function delRow(obj)
{
	var parentDom = $(obj).parent('div');
	$(parentDom).remove();
}