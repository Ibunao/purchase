<!--主导航-->
<div id="nav_shade" class="none"></div>
<?= $this->render('/layouts/_nav', array('sel' => 'bycount')); ?>
<!--主导航-->
<!--右侧主区域-->
<div class="order_dt_table_all">
<!--表头-->
<ul class="normal_line">
    <!--第一行 first_l 第一列 first_v-->
    <li class="div10 fl first_v first_l">大类</li>
    <li class="div12 fl first_l">小类</li>
    <li class="div10 fl first_l">款数</li>
    <li class="div10 fl first_l"><?= Yii::$app->params['season_one_name']; ?>季</li>
    <li class="div10 fl first_l"><?= Yii::$app->params['season_two_name']; ?>季</li>
    <li class="div14 fl first_l">总订货数量</li>
    <li class="div10 fl first_l">总数量占比</li>
    <li class="div14 fl first_l">总订货金额</li>
    <li class="div10 fl first_l">总金额占比</li>
</ul>
<!--表头-->

<?php foreach ($list as $v):?>
<!--大类1-->
<ul class="normal_line">
    <li class="div10 fl first_v"><?= $v['b_name'];?></li>
    <li class="div12 fl">
        <!--小类-->
        <ul class="semi_level">
            <!--二级的首行first_l-->
            <?php foreach ($v['small'] as $kk=>$vv):?>
            <li><?= $vv['name'];?></li>
            <?php endforeach;?>
        </ul>
        <!--小类-->
    </li>
    <li class="div10 fl">
        <!--款数-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= count(array_unique($vv['model']));?></li>
            <?php endforeach;?>
        </ul>
        <!--款数-->
    </li>
    <li class="div10 fl">
        <!--秋季-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['season_id_1'];?></li>
            <?php endforeach;?>
        </ul>
        <!--秋季-->
    </li>
    <li class="div10 fl">
        <!--冬季-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['season_id_2'];?></li>
            <?php endforeach;?>
        </ul>
        <!--冬季-->
    </li>
    <li class="div14 fl">
        <!--订单数量-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['nums'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订单数量-->
    </li>
    <li class="div10 fl">
        <!--数量占比-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= round(($vv['nums']/$result['total_nums'])*100,1).'%';?></li>
            <?php endforeach;?>
        </ul>
        <!--数量占比-->
    </li>
    <li class="div14 fl">
        <!--订货金额-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= $vv['amount'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订货金额-->
    </li>
    <li class="div10 fl last_v">
        <!--金额占比-->
        <ul class="semi_level">
            <?php foreach ($v['small'] as $kk=>$vv):?>
                <li><?= round(($vv['amount']/$result['amount'])*100,1).'%';?></li>
            <?php endforeach;?>
        </ul>
        <!--金额占比-->
    </li>
</ul>
<ul class="statistics_line">
    <li class="div22 fl first_v"><?= $v['b_name'];?></li>
    <li class="div10 fl word_red"><?= count(array_unique($v['model']))?></li>
    <li class="div10 fl word_red"><?= $v['season_id_1'];?></li>
    <li class="div10 fl word_red"><?= $v['season_id_2'];?></li>
    <li class="div14 fl word_red"><?= $v['nums'];?></li>
    <li class="div10 fl word_red"><?= round(($v['nums']/$result['total_nums'])*100,1).'%';?></li>
    <li class="div14 fl word_red"><?= $v['amount'];?></li>
    <li class="div10 fl word_red last_v"><?= round(($v['amount']/$result['amount'])*100,1).'%';?></li>
</ul>
<!--大类1-->
<?php endforeach;?>

<!--总计-->
<ul class="statistics_line">
    <li class="div22 fl first_v">订货总计</li>
    <li class="div10 fl word_red last_l"><?= count(array_unique($result['model']));?></li>
    <li class="div10 fl word_red last_l"><?= $result['season_1'];?></li>
    <li class="div10 fl word_red last_l"><?= $result['season_2'];?></li>
    <li class="div14 fl word_red last_l"><?= $result['total_nums'];?></li>
    <li class="div10 fl word_red last_l"><?= $result['total_nums']?'100%':'0%';?></li>
    <li class="div14 fl word_red last_l"><?= $result['amount'];?></li>
    <li class="div10 fl word_red last_v last_l"><?= $result['total_nums']?'100%':'0%';?></li>
</ul>
<!--总计-->
</div>


<div id="main" style="width: 400px;height:600px; float: left;"></div>
<div id="one" style="width: 300px;height:400px; float: left;"></div>
<div id="two" style="width: 300px;height:400px;float: left;"></div>
<script type="text/javascript">

//饼状图
var myChart = echarts.init(document.getElementById('main'));
var data = JSON.parse('<?=$bing ;?>');

// 指定图表的配置项和数据
var option = {
    title : {
        text: '订货会季节占比',
        subtext: '数量占比',
        x:'center'
    },
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        bottom: 100,
        left: 'center',
        data: ['<?=Yii::$app->params['season_one_name'].'\',\''.Yii::$app->params['season_two_name'] ?>']
    },
    series : [
        {
            name: '订货会季节数量占比',
            type: 'pie',
            radius : '55%',
            center: ['50%', '40%'],
            data:data,
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

//第一个季节图
var myChart = echarts.init(document.getElementById('one'));

option = {
    title : {
        text: '<?=Yii::$app->params['season_one_name'] ;?>季各大类占比',
        subtext: '数量占比',
        x:'center'
    },
    color: ['#3398DB'],
    tooltip : {
        trigger: 'axis',
        axisPointer : {            
            type : 'shadow'  
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis : [
        {
            type : 'category',
            data : <?=$onecat ;?>,
            axisTick: {
                alignWithLabel: true
            }
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'数量',
            type:'bar',
            barWidth: '60%',
            data:<?=$onevalue ;?>
        }
    ],
    itemStyle: { 
        normal: { 
            color: function(params) { 
            　//首先定义一个数组 
            var colorList = [ 
                '#C33531', '#61a080', '#334b5c', '#c23531', '#2f4554', '#d48235', '#9fdabf'
            ]; 
            return colorList[params.dataIndex] 
            }, 
        } 
    } 
};
myChart.setOption(option);
//第二个季节图
var myChart = echarts.init(document.getElementById('two'));

option = {
    title : {
        text: '<?=Yii::$app->params['season_two_name'] ;?>季各大类占比',
        subtext: '数量占比',
        x:'center'
    },
    color: ['#3398DB'],
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        }
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '3%',
        containLabel: true
    },
    xAxis : [
        {
            type : 'category',
            data : <?=$twocat ;?>,
            axisTick: {
                alignWithLabel: true
            }
        }
    ],
    yAxis : [
        {
            type : 'value'
        }
    ],
    series : [
        {
            name:'数量',
            type:'bar',
            barWidth: '60%',
            data:<?=$twovalue ;?>
        }
    ],
    itemStyle: { 
        normal: { 
            color: function(params) { 
            　//首先定义一个数组 
            var colorList = [ 
                '#C33531', '#61a080', '#334b5c', '#c23531', '#2f4554', '#d48235', '#9fdabf'
            ]; 
            return colorList[params.dataIndex] 
            }, 
        } 
    } 
};
myChart.setOption(option);
</script>
<?= $this->render('/common/_footer_order',array());?>