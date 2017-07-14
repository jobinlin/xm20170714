$(document).on("pageInit", "#uc_home_show", function(e, pageId, $page) {

  _initform('', '');

    /*赞和评论弹出事件*/
    $(".reply-btn").click(function(){
    var act_item_box=$(this).parent().find(".act-item-box");

    var act_item=$(".act-box .act-item-box");

    act_item.each(function(){
            if(act_item.hasClass("trans_late")){
              act_item.removeClass("trans_late");
            }
        });

        if(act_item_box.hasClass("trans_late")){
          act_item_box.removeClass("trans_late");
        }else{
          act_item_box.addClass("trans_late");
        }
    });

    /*其他区域点击时，如果评论出现，则关闭*/
    $(document).click(function(e){
        if($(e.target).parents(".reply-btn").length==0){
            var act_item=$(".act-box .act-item-box");
            act_item.each(function(){
                if(act_item.hasClass("trans_late")){
                    act_item.removeClass("trans_late");
                }
            });
        };
        if($(e.target).parents(".reply-input-box").length==0){
            var reply_input_box=$(".reply-input-box");
            if(reply_input_box.hasClass("trans_reply")){
                reply_input_box.removeClass("trans_reply");
            }
        };
        if($(e.target).parents(".reply-act-box").length==0){
            var reply_act_box=$(".reply-act-box");
            if(reply_act_box.hasClass("trans_act")){
                reply_act_box.removeClass("trans_act");
            }
        };
        // _initform('', '');
    });


    /*点击回复事件*/
    $(".act-item-box .act-table .act-dp").click(function(e){
        e.stopPropagation();
        $(".reply-act-box").removeClass("trans_act");
        $(".reply-input-box").addClass("trans_reply");
        $(".act-box .act-item-box").removeClass("trans_late");

        var tid = $(this).parents('.item_box').attr('data_id');
        var rid = '';
        _initform(tid, rid);
    });

    /*点击赞事件*/
    $(".act-item-box .act-table .act-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_fav_topic(tid);
    });

    /* 取消点赞 */
    $(".act-item-box .act-table .cancel-zan").click(function(){
        var tid = $(this).parents('.item_box').attr('data_id');
        do_cancel_fav(tid);
    });


    /*点击取消事件*/
    $(".r-input-btn-box .c_btn").click(function(){
        $(".reply-input-box").removeClass("trans_reply");

    });

    /*评论列表点击事件*/
    $(".reply-list .r-con").click(function(e){
        e.stopPropagation();
        // 回复对象名称
        var reply_name=$(this).parent().find(".name_link").text();
        var reply_act_box=$(".reply-act-box");
        // 主题ID
        var tid = $(this).parents('.item_box').attr('data_id');
        // 评论ID
        var rid = $(this).parent().attr('data-id');

        $(".act-box .act-item-box").removeClass("trans_late");

        if(reply_name == user_name){
            $(".reply-input-box").removeClass("trans_reply");
            reply_act_box.addClass("trans_act");
            reply_act_box.find('a').on('click', function() {
              del_reply(tid, rid);
            });
        }else{
            reply_act_box.removeClass("trans_act");
            $(".reply-input-box").addClass("trans_reply");
            $("input[name='reply_txt']").attr('placeholder', "回复@"+reply_name+":");
            _initform(tid, rid);
        }
    });

    /*取消按钮点击事件*/
    $(".r-act-item .cancel_act").click(function(){
        $(".reply-act-box").removeClass("trans_act");
    });

    // 回复留言
    $("form[name='reply_form']").bind("submit",function(){
        var tid = $("input[name='reply_tid']").val();
        var rid = $("input[name='reply_rid']").val();
        var r_txt = $.trim($("input[name='reply_txt']").val());
        if(r_txt != ''){
            if (rid != '') {
                $("input[name='reply_txt']").val($("input[name='reply_txt']").attr('placeholder') + r_txt);
            }
            var url = $("form[name='reply_form']").attr('action');
            var query = $("form[name='reply_form']").serialize();
            $.ajax({
                url:url,
                data:query,
                type:"POST",
                dataType:"json",
                success:function(obj){
                    if(obj.status) {
                        $(".r_data_"+tid).append(obj.reply_html);
                        if($(".r_data_"+tid).find(".r-item").length>0) {
                            $(".r_data_"+tid).parent().show();
                        }
                        $(window).scrollTop($(".r_sub_data_id_"+obj.reply_data.reply_id).offset().top-($(window).height()/2));
                    } else {
                        $.toast(obj.info);
                    }
                }
            });
        }
        $(".reply-input-box").removeClass("trans_reply");
        $("input[name=reply-txt]").val('');
        _initform('', '');
        return false;
    });

    var imglight2 = new Swiper ('.img-light', {
        onSlideChangeStart: function(swiper){
            var index = $(".img-light-box .swiper-slide-active").attr("rel");
            $(".light-index .now-index").html(index);
        }
    });

  /*
   *评论图点击显示当前评论所有图片集
  */
  $(".j_open_img").click(function(){
      imglight2.removeAllSlides();
    $(".flippedout").addClass("z-open-black");
    $(".flippedout").addClass("showflipped");
    $(".light-txt").addClass("z-open");
    $(".j-flippedout-close").attr("rel","light_firend");
    $(".totop").addClass("vhide");//隐藏回到头部按钮
    var index = 0;
    $(this).parents(".images_box").find(".j_open_img").each(function(index){//动态为查看器添加内容
    console.log(0);
      var url = $(this).children("img").attr("data-lingtsrc");
      index = parseInt(index) + 1;
      imglight2.appendSlide('<div class="swiper-slide" rel="'+ index +'"><img class="j-slide-img2" src="'+ url +'" width="100%"></div>');
    });
    var index = parseInt($(this).attr("data"))-1;//获取点击的是第几张图片
    imglight2.slideTo(index,0);//设置查看器图片为点击的图片
    $(".light-index .light-count").html($(this).parent().children(".j_open_img").length); //设置图片索引总数
    $(".light-index .now-index").html($(this).attr("data"));//设置当前图片索引
  });

    $(".swiper-wrapper").on("click",".j-slide-img2",function(){
        $(".flippedout").removeClass("z-open-black").removeClass("showflipped");
        $(".light-txt").removeClass("z-open");
        $(".img-light-box .j-flippedout-close").removeClass("showflipped");
        imglight2.removeAllSlides();
        $(".totop").removeClass("vhide");
      });

    $(document).on("click",".j-flippedout-close",function(){
        var rel = $(this).attr("rel");
        $(".flippedout").removeClass("showflipped").removeClass("dropdowm-open").removeClass("z-open");
            $(".cancel-shoucan").removeClass("z-open");
            if(rel == "light_firend"){
              //关闭图片查看器
              $(".flippedout").removeClass("z-open-black");
              $(".light-txt").removeClass("z-open");
              $(".img-light-box .j-flippedout-close").removeClass("showflipped");
              imglight2.removeAllSlides();
        }
    });

    /*加载更多操作*/
    var load_page = 2;
    $(".load-move").bind("click",function(){
      var id = $(this).attr("data-id");
      var query = new Object();
      query.id = id;
      query.page = load_page;
      query.act = "load_move_reply";
      $.ajax({
        url:ajax_url,
        data:query,
        type:"POST",
        dataType:"json",
        success:function(obj){
          if(obj.status==1) {
            $(".r_data_"+id).append(obj.reply_html);
            if($(".r_data_"+id).find(".r-item").length>0)
              $(".r_data_"+id).parent().show();
            
            if(obj.is_lock==1){
              $(".load-move").unbind();
              $(".load-move").css("background-color","#A6A6A6");
            }
            load_page++;
          } else if(obj.status==-1) {
            $.toast(obj.info);
            setTimeout(function() {
              $.router.load(obj.jump, true);
            }, 2000);
          }
        }
      });
    });
});



