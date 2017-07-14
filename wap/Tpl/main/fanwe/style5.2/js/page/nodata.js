$(document).on("pageInit", "#nodata", function(e, pageId, $page) {

    if (typeof suijump === 'function') {
        suijump();
    }
});