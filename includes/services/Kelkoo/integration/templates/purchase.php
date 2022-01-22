<script type='text/javascript'>
    _kkstrack = {
        merchantInfo: [{ country:'<?php echo $country; ?>', merchantId:<?php echo $id; ?> }],
        orderValue: '<?php echo $orderPrice; ?>',
        orderId: '<?php echo $orderId; ?>',
        basket: '<?php echo $basket; ?>'
    };

    (function() {
        var s = document.createElement('script');
        s.type = 'text/javascript';
        s.async = true;
        s.src = 'https://s.kk-resources.com/ks.js';
        var x = document.getElementsByTagName('script')[0];
        x.parentNode.insertBefore(s, x);
    })();
</script>