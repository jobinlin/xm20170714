$(document).on("pageInit", "#help", function(e, pageId, $page) {
	var nav_num=$(".j-nav-item").length;
	var m_width=$(".m-nav-tab").width();
	if (nav_num>5) {
		$(".m-nav-tab .nav-tab").css('width',m_width*.22*nav_num);
	} else {
		$(".m-nav-tab .nav-tab").css('width', '100%');
	}
	if ($(".m-nav-tab").length!==0) {
		tab_line_init();
		nav_tab();
	}
	$(".j-nav-item").on('click', function() {
		$(".bar-list").removeClass('active');
		$(".bar-list").eq($(this).index()).addClass('active');
		/* Act on the event */
	});

	// 小能
	$('.xnOpenSdk').bind('click', function() {
		if (app_index != 'app') {
			return;
		}
		if(is_login==0){
			App.login_sdk();
			return false;
		}
		var xnOptionsObj = {
			goods_id:'',
			goods_showURL:'',
			goodsTitle: '',
			goodsPrice: '',
			goods_URL: '',
			settingid: settingid,
			appGoods_type: '0',
		};
		xnOptions = JSON.stringify(xnOptionsObj);
		try {
			App.xnOpenSdk(xnOptions);
		} catch (e) {
			$.toast(e);
		}
	})
});