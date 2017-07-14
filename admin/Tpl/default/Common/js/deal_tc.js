$(document).ready(function(){
	
	$("select[name='tc_mobile_moban']").bind("change",function(){
		load_tc_mobile_html();
	});
	$("select[name='tc_pc_moban']").bind("change",function(){
		load_tc_pc_html();
	});
});

function load_tc_mobile_html()
{
	var tc_file = $("select[name='tc_mobile_moban']").val();
	
	if(tc_file !=0){
		$.ajax({
				url:APP_ROOT + "/mapi/mobile_tc/tc_mobile/"+tc_file,
				dataType:"html",
				success:function(result){
					KE.util.setFullHtml("set_meal",result);
				}
		});
	}else{
		var html =$("#DefaultHtmlMeal").html();
		KE.util.setFullHtml("set_meal",html);
	}
}
function load_tc_pc_html()
{
	var tc_file = $("select[name='tc_pc_moban']").val();
	if(tc_file !=0){
		$.ajax({
				url:APP_ROOT + "/mapi/mobile_tc/tc_pc/"+tc_file,
				dataType:"html",
				success:function(result){
					KE.util.setFullHtml("pc_setmeal",result);
				}
		});
	}else{
		var html =$("#DefaultHtmlPCMeal").html();
		KE.util.setFullHtml("pc_setmeal",html);
		
	}
}