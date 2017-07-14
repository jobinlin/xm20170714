
$(document).ready(function(){

    $(".content").on('click','.search-btn',function(){
        var url=$(this).attr('data-href');
        var begin_time = $.trim($("input[name='begin_time']").val());
        var end_time = $.trim($("input[name='end_time']").val());
        url += "&begin_time="+begin_time+"&end_time="+end_time;
        
        $.ajax({
            url:url,
            type:"POST",
            success:function(html)
            {
                $(".table-content").html($(html).find(".table-content").html());
				$(".time_input").datetimepicker();
            },
            error:function()
            {

            }
        });
    });



});