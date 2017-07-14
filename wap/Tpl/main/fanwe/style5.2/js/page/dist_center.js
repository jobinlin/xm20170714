$(document).on("pageInit", "#dist_center", function(e, pageId, $page)  {

    $('.dist_scope').bind('click', function() {
        var url = $(this).attr('url');
        window.location = url;
    })
});