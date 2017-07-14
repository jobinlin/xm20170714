$(document).on("pageInit", ".page", function(e, pageId, $page) {
	/*
	 *打开规格选择窗口
	 *触发源.j-open-choose
	*/

	$(document).on('click',".j-open-choose",function(){
		console.log(01);
		var page_id= $(".page").attr("id");
		if(page_id !="dealgroup"){
			$(".j-flippedout-close").attr("rel","spec");
			$(".j-spec-choose-close").attr("rel","spec");
			$(".flippedout-spec").addClass("showflipped").addClass("z-open");
			$(".spec-choose").addClass("z-open");
			$(".totop").addClass("vhide");//隐藏回到头部按钮
		}
	});

	/*
	 *打开下拉导航窗口
	 *触发源.j-opendropdowm
	*/
	$(document).on('click',".j-opendropdowm",function(){
		$(".j-flippedout-close").attr("rel","dropdowm");
		$(".flippedout").addClass("showflipped").addClass("dropdowm-open");
		$(".m-nav-dropdown").addClass("showdropdown");
		$(".m-nav-dropdown .nav-dropdown-con").addClass("dropdown-open");
		$(".j-flippedout-close").children(".iconfont").addClass("jump");
	});

	$(document).on('click',".j-opendropdowm-default",function(){
		console.log(0);
		$(".j-flippedout-close").attr("rel","dropdowm");
		$(".flippedout-default").addClass("showflipped").addClass("dropdowm-open");
		$(".m-nav-dropdown").addClass("showdropdown");
		$(".m-nav-dropdown .nav-dropdown-con").addClass("dropdown-open");
		$(".flippedout-default .j-flippedout-close").children(".iconfont").addClass("jump");
	});

	/*
	 *打开分享弹出窗口
	 *触发源为.j-openshare
	*/
	$(document).on('click',".j-openshare",function(){
		$(".j-flippedout-close").attr("rel","share");
		$("#boxclose_share").attr("rel","share");
		$(".flippedout").addClass("z-open").addClass("showflipped");
		$(".box_share").addClass("z-open");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
	});



	/*
	 *下拉导航点击分享操作
	 *触发源为.j-dropdown-share
	*/
	$(document).on('click',".j-dropdown-share",function(){
		$(".j-flippedout-close").attr("rel","share");
		$("#boxclose_share").attr("rel","share");
		$(".m-nav-dropdown .nav-dropdown-con").removeClass("dropdown-open");//下拉导航还原
		$(".j-flippedout-close .iconfont").removeClass("jump");
		$(".box_share").addClass("z-open");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
	});

	var imglight = new Swiper ('.img-light', {
		onSlideChangeStart: function(swiper){
			var index = $(".img-light-box .swiper-slide-active").attr("rel");
			$(".light-index .now-index").html(index);
		}
	});

	/*
	 *评论图点击显示当前评论所有图片集
	*/
	$('.page').on('click',".j-review-item,.j-comment-item",function(){
		var flag = $(this).parent(".comment-imglist").attr("rel");
		if(flag == "review"){
			var obj = "j-review-item";
		}else{
			var obj = "j-comment-item";
		}
		$(".pop-light-img").addClass("z-open-black");
		$(".light-txt").addClass("z-open");
		$(".img-light-box").addClass("z-open");
		$(".j-flippedout-close").attr("rel","light");
		$(".totop").addClass("vhide");//隐藏回到头部按钮
		var index = 0;

		$(this).parent(".comment-imglist").children("." + obj).each(function(index){//动态为查看器添加内容
			var url = $(this).children("img").attr("data-lingtsrc");;
			index = parseInt(index) + 1;
			imglight.appendSlide('<div class="swiper-slide j-slide-img" rel="'+ index +'"><img class="j-slide-img" src="'+ url +'" width="100%"></div>');
		});
		var index = parseInt($(this).attr("data"))-1;//获取点击的是第几张图片
		imglight.slideTo(index,0);//设置查看器图片为点击的图片
		if(flag == "review"){
			var txt = $(this).parent().siblings().children(".comment-txt").html();//获取评论内容
		}else{
			var txt = $(this).parent().siblings(".comment-txt").html();//获取评论内容
		}
		var name = $(this).parent().siblings(".commenter").children().children(".username").html();//获取用户名
		console.log(txt);
		console.log(name);
		//$(".light-txt .light-con").html(txt);//设置评论内容
		//$(".light-txt .light-name .name").html(name);//设置用户名
		$(".light-index .light-count").html($(this).parent().children("." + obj).length); //设置图片索引总数
		$(".light-index .now-index").html($(this).attr("data"));//设置当前图片索引

	});

	/*
	 *为新添加的查看器图片添加点击关闭事件
	*/
	$(".swiper-wrapper").on("click",".j-slide-img",function(){
		$(".pop-light-img").removeClass("z-open-black").removeClass("showflipped");
		$(".light-txt").removeClass("z-open");
		$(".img-light-box").removeClass("z-open");
		imglight.removeAllSlides();
		$(".totop").removeClass("vhide");
	});


	/*
	 *关闭弹出层
	*/
	$(document).on("click","#boxclose_share,.j-spec-choose-close,.j-flippedout-close",function(){
		var rel = $(this).attr("rel");
		$(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
		$(".cancel-shoucan").removeClass("z-open");
		if(rel == "light"){
			//关闭图片查看器
			$(".pop-light-img").removeClass("z-open-black");
			$(".light-txt").removeClass("z-open");
			$(".img-light-box .j-flippedout-close").removeClass("showflipped");
			imglight.removeAllSlides();

		}else if (rel == "spec") {
			//关闭图片规格选择器
			$(".flippedout-spec").removeClass("showflipped").removeClass("z-open");
			$(".spec-choose").removeClass("z-open");
			$(".spec-btn-list").removeClass("isAddCart");
			$(".img-light-box").removeClass("z-open");

		}else if (rel == "dropdowm") {
			//关闭下拉导航
			$(".flippedout-default").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
			$(".m-nav-dropdown").removeClass("showdropdown");
			$(".nav-dropdown-con").removeClass("dropdown-open");
			$(".j-flippedout-close .iconfont").removeClass("jump");

		}else if (rel == "share") {
			//关闭分享
			$(".box_share").removeClass("z-open");
			$("#jiathis_weixin_share").remove();
		}

		$(".totop").removeClass("vhide");
	});


});

function fun_add_miuns(e) {
	var operate = e.attr("data-operate");//获取按钮的操作 判断是执行加还是减
	var txt = e.siblings(".numplusminus");//获取当前文本框
	var txt_val = parseInt(txt.val());//获取文本框中的值，并转化为Int类型
	var max = parseInt(txt.attr("data-max"));//获取可购买的最大值
	var min = parseInt(txt.attr("data-min"));//获取可购买的最小值
	var new_val;
	if(operate == "+"){
		if (txt_val < max) {
			new_val = txt_val + 1;//当前文本框中的值小于最大可购买数，则进行+1操作
			txt.val(new_val);
			$("input[name='num']").val(new_val);

			//以下是判断加减按钮是否可用
			if (new_val == max) {
				$(".j-add").addClass("isUse");
			}else if(new_val == min){
				$(".j-miuns").addClass("isUse");
			}else{
				$(".j-add-miuns").removeClass("isUse");
			}
		}

	}else if (operate == "-") {
		if (txt_val > min) {//当前文本框中的值大于最小可购买数，则进行-1操作
			new_val = txt_val - 1;
			txt.val(new_val);
			$("input[name='num']").val(new_val);
			//以下是判断加减按钮是否可用
			if (new_val == max) {
				$(".j-add").addClass("isUse");
			}else if(new_val == min){
				$(".j-miuns").addClass("isUse");
			}else{
				$(".j-add-miuns").removeClass("isUse");
			}
		}
	}

}