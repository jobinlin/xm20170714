$(document).ready(function() {
	$("input[name='cbo1']").bind('click',function(){
		if($(this).is(':checked')){
			$("label.ui-checkbox[name='cbo2']").removeAttr('init');	
			$("input[name='cbo2']").attr('checked',false);	

		}else{

			$("label.ui-checkbox[name='cbo2']").removeAttr('init');	
			$("input[name='cbo2']").attr('checked',true);	
			
		}
		init_ui_checkbox();	
		
	});
	

	
});