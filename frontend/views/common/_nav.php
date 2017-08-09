<!-- 左侧导航-->
<nav id="menu" class="mm_menu">
    <ul class="mm_list mb60" id="mmList">
    <!-- 二级分类 -->
        <?php foreach (Cache::cateList() as $v): ?>
        <li class="menu">
            <ul>
                <li class="level0">
                    <a href="/default/index?c_id=<?php echo $v['id'];?>" <?php if ($v['id'] == $b_id):?>class="curSelected"<?php endif;?>>
                        <span><?php echo $v['name'];?></span>
                    </a>
                </li>
                <?php if ($v['child']):?>
                <li class="dropdown" <?php if ($v['id'] == $b_id):?>style="display: list-item;"<?php endif;?>>
                    <ul>
                        <?php foreach ($v['child'] as $vv):?>
                        <?php if ($vv['num']):?>
                        <li>
                            <a href="/default/index?c_id=<?php echo $v['id'].','.$vv['id'];?>" class="level1 <?php if ($c_id == $vv['id']):?>curSelectedNode<?php endif;?>">
                                <span title="" class="cat_num">(<?php echo $vv['num'];?>)</span>
                                <span><?php echo $vv['name'];?></span>
                            </a>
                        </li>
                        <?php endif;?>
                        <?php endforeach;?>
                    </ul>
                </li>
                <?php endif;?>
            </ul>
        </li>
        <?php endforeach;?>
    </ul>
    <ul class="space60"></ul>
</nav>
<!--左侧导航-->
<!--主导航-->
<div id="nav_shade" class="none"></div>
<div class="hd_nav w100" id="hdNav">
    <ul>
        <li class="mm_header fl">
            <span class="mm_title">
              <a href="/default/index">全部商品</a>
            </span>
        </li>
        <li class="hd_nav_fLevel fl">
            <a href="javascript:void(0)" onClick="nav_toggle(this);">
                <?php echo '季节';?>
                <label class="nav_trgl nav_down_trgl"></label>
            </a>
            <ul class="hd_nav_semi home-season">
                <li>
                    <!-- 季节链接 -->
                    <a href="<?php echo $this->urlParams(array('sd'=>0))?>">全部季节</a>
                </li>
                <?php foreach ($this->season as $k=>$v):?>
                <li><a href="<?php echo $this->urlParams(array('sd'=>$k.'_'.$v))?>"><?php echo $v;?></a></li>
                <?php endforeach;?>
            </ul>
        </li>
        <li class="hd_nav_fLevel fl">
            <a href="javascript:void(0)" onClick="nav_toggle(this);">
                <?php echo '波段';?>
                <label class="nav_trgl"></label></a>
            <ul class="hd_nav_semi home-wave">
                <li><a href="<?php echo $this->urlParams(array('wv'=>0))?>">全部波段</a></li>
                <?php foreach ($this->wave as $k=>$v):?>
                    <li><a href="<?php echo $this->urlParams(array('wv'=>$k.'_'.$v))?>"><?php echo $v;?></a></li>
                <?php endforeach;?>
            </ul>
        </li>
        <li class="hd_nav_fLevel fl">
            <a  href="javascript:void(0)" onClick="nav_toggle(this);">
                <?php echo '等级';?>
                <label class="nav_trgl"></label></a>
            <ul class="hd_nav_semi home-level">
                <li><a href="<?php echo $this->urlParams(array('lv'=>0))?>">全部等级</a></li>
                <?php foreach ($this->level as $k=>$v):?>
                    <li><a href="<?php echo $this->urlParams(array('lv'=>$k.'_'.$v))?>"><?php echo $v;?></a></li>
                <?php endforeach;?>
            </ul>
        </li>
        <li class="hd_nav_fLevel fl">
            <a  href="javascript:void(0)" onClick="nav_toggle(this);">
                <?php echo '价格带';?>
                <label class="nav_trgl"></label></a>
            <ul class="hd_nav_semi home-price-level">
                <li><a href="<?php echo $this->urlParams(array('plv'=>0))?>">全部价格带</a></li>
                <?php foreach ($this->parice_level as $k=>$v):?>
                    <li><a href="<?php echo $this->urlParams(array('plv'=>$k.'_'.$v))?>"><?php echo $v;?></a></li>
                <?php endforeach;?>
            </ul>
        </li>
        <li class="hd_nav_fLevel fl">
            <a  href="javascript:void(0)" onClick="nav_toggle(this);">
                <?php if ($model['or'] == 1):?>
                    已订
                <?php elseif ($model['or'] == 2):?>
                    未订
                <?php else:?>
                已订/未订
                <?php endif;?>
                <label class="nav_trgl"></label>
            </a>
            <ul class="hd_nav_semi">
                <li><a href="<?php echo $this->urlParams(array('or'=>0))?>">全部</a></li>
                <li><a href="<?php echo $this->urlParams(array('or'=>1))?>">已订</a></li>
                <li><a href="<?php echo $this->urlParams(array('or'=>2))?>">未订</a></li>
            </ul>
        </li>
        <li class="nav_search fl">
            <form action="?" method="get" class="form-search">
                <input type="number" name="serial_num" placeholder="按流水号/款号搜索" id="keyWords">
                <span class="icon_magnifier_area fr">
                  <i class="icon_magnifier kw-search"></i>
                </span>
            </form>
        </li>
        <li class="hd_nav_fLevel_bt fl <?php if (!$model['hits'] && !$model['price']):?>selected<?php endif;?>">
            <a href="<?php echo $this->urlParams(array('price'=>0,'hits'=>0))?>">默认</a>
        </li>
        <li class="hd_nav_fLevel_bt fl <?php if ($model['hits']):?>selected<?php endif;?>">
            <a href="<?php echo $this->urlParams(array('hits'=>$model['hits_f'],'price'=>0))?>">人气
                <?php if ($model['hits']):?>
                    <label class="sorting_label <?php if ($model['hits'] == 2):?>sort_a<?php endif;?>"></label>
                <?php endif;?>
            </a>
        </li>
        <li class="hd_nav_fLevel_bt fl <?php if ($model['price']):?>selected<?php endif;?>">
            <a href="<?php echo $this->urlParams(array('price'=>$model['price_f'],'hits'=>0))?>">价格
                <?php if ($model['price']):?>
                <label class="sorting_label <?php if ($model['price'] == 2):?>sort_a<?php endif;?>"></label>
                <?php endif;?>
            </a>
        </li>
    </ul>
</div>
<script>
    $(document).ready(function () {
       $("#keyWords").change(function () {
         var getKyw=$("#keyWords").val();
           if(getKyw<=0){
               $("#keyWords").val("0");
           }
       });
    });
</script>
<!--主导航