$(document).on("pageInit", "#dist_undeliver_scope", function (e, pageId, $page) {
    // 百度地图API功能
    var map = new BMap.Map("allmap");
    var xpoint = $('input[name="xpoint"]').val();
    var ypoint = $('input[name="ypoint"]').val();
    var xpoints = $('input[name="xpoints"]').val().split(",");
    var ypoints = $('input[name="ypoints"]').val().split(",");
    map.centerAndZoom(new BMap.Point(xpoint, ypoint), 11);
    map.enableScrollWheelZoom(true);
    //鼠标绘制完成回调方法   获取各个点的经纬度
    var styleOptions = {
        strokeColor: "red",    //边线颜色。
        fillColor: "red",      //填充颜色。当参数为空时，圆形将没有填充效果。
        strokeWeight: 3,       //边线的宽度，以像素为单位。
        strokeOpacity: 0.8,       //边线透明度，取值范围0 - 1。
        fillOpacity: 0.6,      //填充的透明度，取值范围0 - 1。
        strokeStyle: 'solid' //边线的样式，solid或dashed。
    };
    var points = [];
    for (var i in xpoints) {
        points.push(new BMap.Point(xpoints[i], ypoints[i]));
    }
    var marker = new BMap.Marker(new BMap.Point(xpoint, ypoint));
    map.addOverlay(marker);
    var polygon = new BMap.Polygon(points, styleOptions);
    map.addOverlay(polygon);
});