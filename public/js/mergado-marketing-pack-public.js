(function ($) {
    'use strict';

    $(window).on('load', function () {
        var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

        if (typeof window.xoo_wsc_params !== 'undefined') {
            mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
        }

        $(mmpSelector).on('click', function () {
            var $_currency = $('#mergadoSetup').attr('data-currency');
            var $_id = $(this).closest('li.product').find('[data-product_id]').attr('data-product_id');
            var $_name = $(this).closest('li.product').find('.woocommerce-loop-product__title').text();
            var $_priceClone = $(this).closest('li.product').clone();
            $_priceClone.find('del').remove();
            $_priceClone.find('.woocommerce-Price-currencySymbol').remove();
            var $_price = $_priceClone.find('.woocommerce-Price-amount.amount').text();

            glami('track', 'AddToCart', {
                item_ids: [$_id],
                product_names: [$_name],
                value: $_price,
                currency: $_currency
            });
        });
    });
})(jQuery);
