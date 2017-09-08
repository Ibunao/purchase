<!--主导航-->
<div id="nav_shade" class="none"></div>
<?= $this->render('/layouts/_nav', array('sel' => 'byprice')); ?>
<!--主导航-->
<!--右侧主区域-->
<div class="order_dt_table_all">
<!--表头-->
<ul class="normal_line">
    <!--第一行 first_l 第一列 first_v-->
    <li class="div10 fl first_v first_l">大类</li>
    <li class="div12 fl first_l">价格带</li>
    <li class="div12 fl first_l">款式数量</li>
    <li class="div14 fl first_l">款式占比</li>
    <li class="div14 fl first_l">订货数量</li>
    <li class="div14 fl first_l">数量占比</li>
    <li class="div14 fl first_l">订货金额</li>
    <li class="div10 fl first_l">金额占比</li>
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
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
            <li><?= $vv['name'];?></li>
            <?php endforeach;?>
        </ul>
        <!--小类-->
    </li>
    <li class="div12 fl">
        <!--款数-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?= count(array_unique($vv['model']));?></li>
            <?php endforeach;?>
        </ul>
        <!--款数-->
    </li>

    <li class="div14 fl">
        <!--占比-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?php if($result['all']==0){echo '0%';}else{  echo round(count(array_unique($vv['model']))/$result['all']*100,1)."%"; }?></li>
            <?php endforeach;?>
        </ul>
        <!--占比-->
    </li>

    <li class="div14 fl">
        <!--订单数量-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?= $vv['nums'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订单数量-->
    </li>
    <li class="div14 fl">
        <!--数量占比-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?php if($result['total_nums']==0){echo '0%';}else{  echo round(($vv['nums']/$result['total_nums'])*100,1).'%';}?></li>
            <?php endforeach;?>
        </ul>
        <!--数量占比-->
    </li>
    <li class="div14 fl">
        <!--订货金额-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?= $vv['amount'];?></li>
            <?php endforeach;?>
        </ul>
        <!--订货金额-->
    </li>
    <li class="div10 fl last_v">
        <!--金额占比-->
        <ul class="semi_level">
            <?php foreach ($v['dpj'] as $kk=>$vv):?>
                <li><?php if($result['amount']==0){echo '0%';}else{  echo round(($vv['amount']/$result['amount'])*100,1).'%';}?></li>
            <?php endforeach;?>
        </ul>
        <!--金额占比-->
    </li>
</ul>
<ul class="statistics_line">
    <li class="div22 fl first_v"><?= $v['b_name'];?></li>
    <li class="div12 fl word_red"><?= count(array_unique($v['model']))?></li>
    <li class="div14 fl word_red"><?php if($result['all']==0){echo '0%';}else{  echo round(count(array_unique($v['model']))/$result['all'],2)*100.."%";} ?></li><!--占比 -->
    <li class="div14 fl word_red"><?= $v['nums'];?></li>
    <li class="div14 fl word_red"><?php if($result['total_nums']==0){echo '0%';}else{ echo round(($v['nums']/$result['total_nums'])*100,1).'%';}?></li>
    <li class="div14 fl word_red"><?= $v['amount'];?></li>
    <li class="div10 fl word_red last_v"><?php  if($result['amount']==0){echo '0%';}else{ echo round(($v['amount']/$result['amount'])*100,1).'%';}?></li>
</ul>
<!--大类1-->
<?php endforeach;?>

<!--总计-->
<ul class="statistics_line">
    <li class="div22 fl first_v">订货总计</li>
    <li class="div12 fl word_red last_l"><?= $result['all']?></li>
    <li class="div14 fl word_red last_l"><?= count(array_unique($result['model']))?'100%':'0%'; ?></li>
    <li class="div14 fl word_red last_l"><?= $result['total_nums']?></li>
    <li class="div14 fl word_red last_l"><?= $result['total_nums']?'100%':'0%';?></li>
    <li class="div14 fl word_red last_l"><?= $result['amount'];?></li>
    <li class="div10 fl word_red last_v last_l"><?= $result['total_nums']?'100%':'0%';?></li>
</ul>
<!--总计-->
</div>

<div id="main" style="width: 400px;height:600px; float: left;"></div>
<div id="one" style="width: 600px;height:600px; float: left;"></div>

<script type="text/javascript">

//饼状图
var myChart = echarts.init(document.getElementById('main'));

// 指定图表的配置项和数据
var option = {
    title : {
        text: '订货会种类总额占比',
        subtext: '总额占比',
        x:'center'
    },
    tooltip : {
        trigger: 'item',
        formatter: "{a} <br/>{b} : {c} ({d}%)"
    },
    legend: {
        bottom: 100,
        left: 'center',
        data: <?=$bingname ;?>
    },
    series : [
        {
            name: '大类总额占比',
            type: 'pie',
            radius : '55%',
            center: ['50%', '40%'],
            data: <?=$bingdata ;?>,
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

//饼状图
var myChart = echarts.init(document.getElementById('one'));
option = {
    title : {
        text: '各价格带数量占比',
        subtext: '数量占比',
        x:'center'
    },
    tooltip : {
        trigger: 'axis',
        axisPointer : {            // 坐标轴指示器，坐标轴触发有效
            type : 'shadow'        // 默认为直线，可选为：'line' | 'shadow'
        },
        formatter: function(data) 
        {
            console.log(data);
            str = '';
            for (var i = 0; i < data.length; i++) {
                if (data[i].value == '') {continue;}
                str += data[i].seriesName +  '：' + data[i].value + '个' + '<br/>';
            }
            return str;
        }
    },

    legend: {
        data: ['0-99','100-199','200-299','300-399','400-499','500-999','1000-1499','1500-2000','2000以上',]
    },
    grid: {
        left: '3%',
        right: '4%',
        bottom: '10%',
        containLabel: true
    },
    xAxis:  {
        type: 'category',
        data: <?=$bingname ;?>
    },
    yAxis: {
        type: 'value'
    },
    series: [
        {
            name: '0-99元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$a ;?>
        },
        {
            name: '100-199元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$b ;?>
        },
        {
            name: '200-299元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$c ;?>
        },
        {
            name: '300-399元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$d ;?>
        },
        {
            name: '400-499元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$e ;?>
        },
        {
            name: '500-999元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$f ;?>
        },
        {
            name: '1000-1499元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$g ;?>
        },
        {
            name: '1500-2000元',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$h ;?>
        },
        {
            name: '2000元以上',
            type: 'bar',
            stack: '总量',
            label: {
                normal: {
                    show: true,
                    position: 'insideRight'
                }
            },
            data: <?=$l ;?>
        }
    ]
};
myChart.setOption(option);
</script>
<?= $this->render('/common/_footer_order',array());?>