$(document).ready(function() {
    var myChart = echarts.init(document.getElementById('index-canvas'));
    var colors = ['#00c0ef', '#dd4b39', '#00a65a','#f39c12'];
    option = {
        color: colors,

        tooltip: {
            trigger: 'axis',
            axisPointer: {
                type: 'line'
            }
        },
        axisPointer:{
            label:{
                show:false
            }
        },
        legend: {
            data:['会员数量','网宝数量'],
            top:0
        },
        grid: {
            top:'40px',
            left: '3%',
            right: '5%',
            bottom: '30px',
            containLabel: true
        },
        xAxis : [
        {
            type : 'category',
            boundaryGap : false,
            data : [day0[0],day1[0],day2[0],day3[0],day4[0],day5[0],day6[0]]
        }
        ],
        yAxis: [
            {
                type: 'value'
            }
        ],
        series : [
        {
            name:'网宝数量',
            type:'line',
            smooth:true,
            data:[day0[2],day1[2],day2[2],day3[2],day4[2],day5[2],day6[2]]
        },
        {
            name:'会员数量',
            type:'line',
            smooth:true,
            data:[day0[1],day1[1],day2[1],day3[1],day4[1],day5[1],day6[1]]
        }
    ]
    };
    myChart.setOption(option);
    window.onresize = myChart.resize;
});
