$(document).ready(function(){
	$("input[name='description']").blur(function(){
		var val = $.trim( $(this).val() );
		var val_length = val.length;
		if(val_length != '' && val_length > 6){
			alert('简单描述只要最多6个文字的长度');
			$(this).focus();
		}
	});
});