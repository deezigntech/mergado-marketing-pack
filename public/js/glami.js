(function ($) {
    'use strict';

    document.addEventListener("DOMContentLoaded", function() {
        if (typeof (__glamiActive) !== 'undefined' && __glamiActive) {
            (function (f, a, s, h, i, o, n) {
                f['GlamiTrackerObject'] = i;
                f[i] = f[i] || function () {
                    (f[i].q = f[i].q || []).push(arguments)
                };
                o = a.createElement(s),
                    n = a.getElementsByTagName(s)[0];
                o.async = 1;
                o.src = h;
                n.parentNode.insertBefore(o, n)
            })(window, document, 'script', '//www.glami.cz/js/compiled/pt.js', 'glami');

            glami('create', __glamiCode, __lang);
            glami('track', 'PageView');
        }
    });

    $(window).on('load', function () {
        if (typeof (__glamiActive) !== 'undefined' && __glamiActive) {
            var mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button';

            if (typeof window.xoo_wsc_params !== 'undefined') {
                mmpSelector = 'body.woodmart-ajax-shop-on .single_add_to_cart_button, .add_to_cart_button, body.single-product .single_add_to_cart_button';
            }

            $(mmpSelector).on('click', function () {
                if(!$(this).hasClass('product_type_variable')) {
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
                }
            });
        }
    });
})(jQuery);
