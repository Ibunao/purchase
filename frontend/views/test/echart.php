<script src="/js/echarts.common.min.js"></script>




<div id="main" style="width: 1000px;height:600px;"></div>
<script type="text/javascript">

var myChart = echarts.init(document.getElementById('main'));
// 指定图表的配置项和数据
var option = {
    title : {
        text: '订货会季节占比',
        subtext: '总钱数占比',
        x:'center'
    },
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        bottom: 1,
        left: 'center',
        data: ['春','夏']
    },
    series : [
        {
            name: '订货会季节占比',
            type: 'pie',
            radius : '55%',
            center: ['50%', '40%'],
            data:[
                {value:335, name:'春'},
                {value:310, name:'夏'}
            ],
            itemStyle: {
                emphasis: {
                    shadowBlur: 10,
                    shadowOffsetX: 0,
                    shadowColor: 'rgba(0, 0, 0, 0.5)'
                }
            }
        }
    ]
};

myChart.setOption(option);
</script>