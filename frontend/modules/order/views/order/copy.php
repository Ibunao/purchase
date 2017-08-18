<?php

$this->params['breadcrumbs'] = [
    ['label' => '订单'],
    ['label' => '订单复制'],
];

?>
<form action="" method="get">
    <input type="hidden" name="r" value="order/order/doCopy">
    被复制客户编号：<input type="text" name="from" id="from" value="">
    复制到客户编号：<input type="text" name="to" id="to" value="">
    <input type="button" class="btn btn-sm btn-danger copy" data-status="confirm" value="订单复制">
</form>
<script>
    $(function ($) {
        $(".copy").click(function () {
            if (confirm('确认订单复制操作吗？')) {
                var from = $('#from').val();
                var to = $('#to').val();
                $.post('/order/order/docopy', {'from': from, 'to': to}, function (data) {
                    if (data.code !== 400) {
                        alert(data.msg);
                        location.replace(location.href);
                    } else {
                        alert(data.msg);

                    }

                }, 'json');
            }

        });
    });

</script>
