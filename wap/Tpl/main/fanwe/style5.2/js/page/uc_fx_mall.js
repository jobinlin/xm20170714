
$(document).on("pageInit", "#uc_fx_mall", function(e, pageId, $page) {
	var rel = $('.mall-tab .active').attr('rel');
	init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);

	$(".mall-tab a").click(function() {
		$(".mall-tab-item").removeClass('active');
		$(this).addClass('active');
		$(document).off('infinite', '.infinite-scroll-bottom');
		var rel = Number($(this).attr("rel"));
		var hidetab = '#tab' + (rel ? 0 : 1);
		var showtab = '#tab' + rel;
		// console.log(hidetab + '' + showtab);
		$(showtab).removeClass('hide');
		$(hidetab).addClass('hide');
		$('.content').scrollTop(1);
		if(!$.trim($("#tab"+rel).html())){
			var param = {
				type: rel,
				act: 'mall'
			};
			$.ajax({
				url:ajax_url,
				data: param,
				type:"GET",
				success:function(html) {
					$('#item-content').append($(html).find('#item-content').html());
					// $('.content').append($(html).find('.content').html());
					init_listscroll(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
				},
				error:function() {
					$(".j_ajaxlist_"+rel).find(".page-load span").removeClass("loading").addClass("loaded").html("网络被风吹走啦~");
				}
			});
		} else{
			if($(".content").scrollTop()>0){
				infinite(".j_ajaxlist_"+rel,".j_ajaxadd_"+rel);
			}
		}
	});
});