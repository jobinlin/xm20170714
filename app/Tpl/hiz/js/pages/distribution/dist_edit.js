$(document).ready(function(){

	$('input.form-submit').bind('click', function() {
		var param = $('form[name="dist_edit"]').serialize();
		$.ajax({
			url: ajax_url,
			data: param,
			type: "POST",
			dataType: "json",
			success: function(obj) {
				if (obj.status) {
					$.showSuccess(obj.info, reload);
				} else {
					$.showErr(obj.info);
				}
			},
			error: function() {
				$.showErr("网络异常", reload);
			}
		});
		return;
	});

	function reload() {
		window.location.reload();
	}
})