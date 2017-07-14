$(document).on("pageInit", ".page", function(e, pageId, $page) {

	
	$('#search').keyup(function(){
		search_city();
	}).focus(function(){
		$('#search_result').show();
	});
	$("#search").blur(function(){
		setTimeout(function(){
	 		$('#search_result').hide();
		},"500");
	});
	$(".searchbar .searchbar-cancel").click(function(){
		$('.searchbar #search').val('');
	});
	$("#city .city_change").unbind('click').bind('click',function(){
		$.ajax({
			url: $(this).attr('url'),
			data: {},
			dataType: "json",
			type: "post",
			success: function(obj){
				$.router.load(obj.jump, true);
			}
		});
	});
	
});
function search_city(){
	var query = new Object();
	query.act = "searchcity";
	var kw=$.trim($('#search').val());
	query.kw=kw;
	//if(kw){
		$.ajax({
					url: CITY_URL,
					data: query,
					dataType: "json",
					type: "post",
					success: function(data){
							
						$('#search_result').remove();
						$('.searchbar').append(data.city.html);
						$('#search_result').show();
						
					}
		});
	//}


}