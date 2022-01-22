<?php

use Mergado\Google\GoogleAnalyticsRefundService;
use Mergado\Google\GoogleAdsService;
use Mergado\Google\GoogleReviewsService;
use Mergado\Google\GoogleTagManagerService;
use Mergado\Tools\Settings;

$googleAds = new GoogleAdsService();
$googleTagManager = new GoogleTagManagerService();

global $wpdb;

$query = 'SELECT DISTINCT postmeta.meta_key FROM ';
$query .= $wpdb->prefix . 'posts AS posts';
$query .= ' LEFT JOIN ' . $wpdb->prefix . 'postmeta AS postmeta ON posts.id = postmeta.post_id';
$query .= ' WHERE (posts.post_type="product" OR posts.post_type="product_variation") AND postmeta.meta_key NOT LIKE "\_%"';

$result = $wpdb->get_results($query, ARRAY_A);
$fields = [0 => '_sku'];

foreach ($result as $item) {
    $fields[] = $item['meta_key'];
}
?>

<div class="card full">
    <h3><?= __('Google Ads', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="adwords-form-conversion-active"><?= __('Ads conversion active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="adwords-form-conversion-active" name="adwords-form-conversion-active"
                       data-mmp-check-main="adwords-conversion"
                       <?php if ($googleAds->getConversionActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-remarketing-active"><?= __('Ads remarketing active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="adwords-form-remarketing-active"
                       name="adwords-form-remarketing-active"
                       <?php if ($googleAds->getRemarketingActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-conversion-code"><?= __('Ads code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="adwords-form-conversion-code" name="adwords-form-conversion-code"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $googleAds->getConversionCode() ?>">
                <br><small
                        class="badge badge_question"><?= __('Get the Conversion code in your Google Ads Account Administration > Tools & Settings > MEASUREMENT - Conversions > Add Conversion > Website. Create a new conversion, then click Install the tag yourself. The code is located in the “Global Site Tag” section and takes the form of AW-123456789.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="adwords-form-conversion-label"><?= __('Ads conversion label', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="adwords-form-conversion-label" name="adwords-form-conversion-label"
                       data-mmp-check-field="adwords-conversion"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $googleAds->getConversionLabel() ?>">
                <br><small
                        class="badge badge_question"><?= __('You can find the Conversion Label on the same page as the conversion code. The label is located in the “Event fragment” section of the send_to element, after the slash. For example, it has the form of /SqrGHAdS-MerfQC.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?= __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?= __('Google analytics - gtag.js', 'mergado-marketing-pack') ?></h3>
    <p>Only Google Tag Manager or gtag.js should be active at a time</p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= Settings::GOOGLE_GTAGJS['ACTIVE'] ?>"><?= __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= Settings::GOOGLE_GTAGJS['ACTIVE'] ?>"
                       name="<?= Settings::GOOGLE_GTAGJS['ACTIVE'] ?>"
                       data-mmp-check-main="gtagjs-active"
                       <?php if (get_option(Settings::GOOGLE_GTAGJS['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= Settings::GOOGLE_GTAGJS['CODE'] ?>"><?= __('Google Analytics tracking ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= Settings::GOOGLE_GTAGJS['CODE'] ?>"
                       name="<?= Settings::GOOGLE_GTAGJS['CODE'] ?>" data-mmp-check-field="gtagjs-active"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= get_option(Settings::GOOGLE_GTAGJS['CODE'], ''); ?>">
                <br><small
                        class="badge badge_question"><?= __('You can find your tracking ID in Google Analytics property > Admin > Property Settings, formatted as "UA-XXXXXXXXX-X', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= Settings::GOOGLE_GTAGJS['TRACKING'] ?>"><?= __('Add Global Site Tracking Code \'gtag.js\'', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?= Settings::GOOGLE_GTAGJS['TRACKING'] ?>"
                       data-mmp-check-main="gtagjs-tracking"
                       data-mmp-check-field="gtagjs-active"
                       name="<?= Settings::GOOGLE_GTAGJS['TRACKING'] ?>"
                       <?php if (get_option(Settings::GOOGLE_GTAGJS['TRACKING'], 0) == 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?= __('Basic tracking code for page view tracking (necessary for Ecommerce and Enhanced Ecommerce tracking)', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE'] ?>"><?= __('Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE'] ?>"
                       data-mmp-check-field="gtagjs-tracking"
                       data-mmp-check-subfield="gtagjs-active"
                       name="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE'] ?>"
                       <?php if (get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE'], 0) == 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?= __('Measurement of ecommerce transactions/purchases.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED'] ?>"><?= __('Enhanced Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED'] ?>"
                       data-mmp-check-field="gtagjs-tracking"
                       data-mmp-check-subfield="gtagjs-active"
                       name="<?= Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED'] ?>"
                       <?php if (get_option(Settings::GOOGLE_GTAGJS['ECOMMERCE_ENHANCED'], 0) == 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?= __('Enhanced complex tracking of customer actions.', 'mergado-marketing-pack') ?></small>

            </td>
        </tr>
        <tr>
            <th>
                <label for="gtagjs-vat-included"><?= __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="gtagjs-vat-included" name="gtagjs-vat-included"
                                   data-mmp-check-field="gtagjs-pixel-active"
                                   <?php if (get_option(Settings::GOOGLE_GTAGJS['CONVERSION_VAT_INCL'], 1) == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= __('Choose whether the price of the products will be sent with or without VAT.
This setting does not affect total revenue. The total revenue of the transaction is calculated including taxes and shipping costs according to the Google Analytics specification.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?= __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?= __('Google Tag Manager', 'mergado-marketing-pack') ?></h3>
    <p>Only Google Tag Manager or gtag.js should be active at a time</p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= $googleTagManager::ACTIVE ?>"><?= __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= $googleTagManager::ACTIVE ?>"
                       name="<?= $googleTagManager::ACTIVE ?>"
                       data-mmp-check-main="gtm-active"
                       <?php if ($googleTagManager->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= $googleTagManager::CODE ?>"><?= __('Google Tag Manager container ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= $googleTagManager::CODE ?>"
                       name="<?= $googleTagManager::CODE ?>" data-mmp-check-field="gtm-active"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $googleTagManager->getCode() ?>">
                <br><small
                        class="badge badge_question"><?= __('You can find your container ID in Tag Manager > Workspace. Near the top of the window, find your container ID, formatted as "GTM-XXXXXX".', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= $googleTagManager::ECOMMERCE_ACTIVE ?>"><?= __('Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?= $googleTagManager::ECOMMERCE_ACTIVE ?>"
                       data-mmp-check-main="gtm-ecommerce"
                       data-mmp-check-field="gtm-active"
                       name="<?= $googleTagManager::ECOMMERCE_ACTIVE ?>"
                       <?php if ($googleTagManager->getEcommerceActive() == 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?= __('Measurement of ecommerce transactions/purchases.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"><?= __('Enhanced Ecommerce tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox"
                       id="<?= $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"
                       data-mmp-check-main="gtm-ecommerce-enhanced"
                       data-mmp-check-field="gtm-ecommerce"
                       data-mmp-check-subfiled="gtm-active"
                       name="<?= $googleTagManager::ECOMMERCE_ENHANCED_ACTIVE ?>"
                       <?php if ($googleTagManager->getEnhancedEcommerceActive() == 1){ ?>checked="checked"<?php } ?>>
                <br>
                <small class="badge badge_info"><?= __('Enhanced complex tracking of customer actions.', 'mergado-marketing-pack') ?></small>

            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"><?= __('Max view_list_items', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="number" id="<?= $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"
                       name="<?= $googleTagManager::VIEW_LIST_ITEMS_COUNT ?>"
                       data-mmp-check-field="gtm-ecommerce-enhanced"
                       placeholder="<?= __('Insert number here', 'mergado-marketing-pack') ?>"
                       value="<?= $googleTagManager->getViewListItemsCount(); ?>"
                       min="0"
                >
                <br><small
                        class="badge badge_question"><?= __('Set maximum of products sent in view_list_item event. Set 0 if you want to send all products on page.".', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="gtm-vat-included"><?= __('Product prices With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="gtm-vat-included" name="gtm-vat-included"
                                   data-mmp-check-field="gtm-pixel-active"
                                   <?php if ($googleTagManager->getConversionVatIncluded() == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= __('Choose whether the price of the products will be sent with or without VAT.
This setting does not affect total revenue. The total revenue of the transaction is calculated including taxes and shipping costs according to the Google Analytics specification.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?= __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?= __('Google Analytics - Refunds', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= GoogleAnalyticsRefundService::ACTIVE ?>"><?= __('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= GoogleAnalyticsRefundService::ACTIVE ?>"
                       name="<?= GoogleAnalyticsRefundService::ACTIVE ?>"
                       data-mmp-check-main="ga-refund-active"
                       <?php if ( get_option(GoogleAnalyticsRefundService::ACTIVE, 0) == 1){ ?>checked="checked"<?php } ?>>
                <!--                    <br><small class="badge badge_info">-->
                <? //= _e('Whenever you make a refund for entire products or an entire order, the module sends a refund information to Google Analytics. Regardless of the status of the order.', 'mergado-marketing-pack') ?><!--</small>-->
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= GoogleAnalyticsRefundService::CODE ?>"><?= __('Google analytics code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= GoogleAnalyticsRefundService::CODE ?>" name="<?= GoogleAnalyticsRefundService::CODE ?>"
                       data-mmp-check-field="ga-refund-active"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= get_option(GoogleAnalyticsRefundService::CODE, ''); ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?= GoogleAnalyticsRefundService::CODE ?>"><?= __('Order refund status', 'mergado-marketing-pack') ?></label>
            </th>
            <td>
                <small class="badge badge_info"><?= __('Select the order statuses at which the entire order will be refunded. When order status will change to the selected one, refund information will be send to Google Analytics. Note: Woocommerce automatically make Full refund when "Refunded" status is selected.', 'mergado-marketing-pack') ?></small>
                <br>
                <br>

                <table>
                    <tbody>
                    <?php $GaRefundClass = new GoogleAnalyticsRefundService(); ?>
                    <?php foreach (wc_get_order_statuses() as $key => $data): ?>
                        <tr>
                            <th class="px-0 pt-0 pb-5px fw-500"><?= $data ?></th>
                            <td class="px-0 pt-0 pb-5px">
                                <input type="checkbox" id="<?= GoogleAnalyticsRefundService::STATUS . $key ?>"
                                       name="<?= GoogleAnalyticsRefundService::STATUS . $key ?>"
                                       data-mmp-check-field="ga-refund-active"
                                       <?php if ($GaRefundClass->isStatusActive($key) == 1){ ?>checked="checked"<?php } ?>>
                            </td>
                        </tr>
                    <?php endforeach ?>
                    </tbody>
                </table>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?= __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?= __('Google Customer Reviews', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"><?= __('Module active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       name="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       data-mmp-check-main="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       <?php if ( get_option(GoogleReviewsService::OPT_IN_ACTIVE, 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?= __('Show google merchant opt-in on checkout page. To active Customer Reviews log into your Merchant Center > Growth > Manage programs > enable Reviews card.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::MERCHANT_ID ?>"><?= __('Merchant Id', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= GoogleReviewsService::MERCHANT_ID ?>"
                       name="<?= GoogleReviewsService::MERCHANT_ID ?>"
                       data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= get_option(GoogleReviewsService::MERCHANT_ID, ''); ?>">
                <br><small
                        class="badge badge_question"><?= __('You can get this value from the Google Merchant Center. It\'s the same as your Google Merchant ID', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::OPT_IN_GTIN ?>"><?= __('Field for GTIN values', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?= GoogleReviewsService::OPT_IN_GTIN ?>"
                        id="<?= GoogleReviewsService::OPT_IN_GTIN ?>"
                        data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach ($fields as $id => $data): ?>
                        <option
                            <?php if ( get_option(GoogleReviewsService::OPT_IN_GTIN, 0) == $data){ ?>selected="selected"<?php } ?>
                            value="<?= $data ?>"><?= $data ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_question"><?= __('Select what field will be used as GTIN. "_sku" is default SKU value used by woocommerce', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"><?= __('Days to send', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"
                       name="<?= GoogleReviewsService::OPT_IN_DELIVERY_DATE ?>"
                       data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       placeholder="<?= __('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= get_option(GoogleReviewsService::OPT_IN_DELIVERY_DATE, ''); ?>">
                <br><small
                        class="badge badge_question"><?= __('Number of days after ordering, when the email will be send to customers. Only numbers are accepted!', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::OPT_IN_POSITION ?>"><?= __('Opt-in position', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?= GoogleReviewsService::OPT_IN_POSITION ?>"
                        id="<?= GoogleReviewsService::OPT_IN_POSITION ?>"
                        data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach (GoogleReviewsService::OPT_IN_POSITIONS_FOR_SELECT() as $key => $data): ?>
                        <option
                            <?php if ( get_option(GoogleReviewsService::OPT_IN_POSITION, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                            value="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_question"><?= __('Select opt-in position.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::BADGE_ACTIVE ?>"><?= __('Badge active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= GoogleReviewsService::BADGE_ACTIVE ?>"
                       name="<?= GoogleReviewsService::BADGE_ACTIVE ?>"
                       data-mmp-check-main="<?= GoogleReviewsService::BADGE_ACTIVE ?>"
                       data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>"
                       <?php if ( get_option(GoogleReviewsService::BADGE_ACTIVE, 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small
                        class="badge badge_info"><?= __('Show review rating badge on prefered location.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::BADGE_POSITION ?>"><?= __('Badge position', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <div>
                    <select name="<?= GoogleReviewsService::BADGE_POSITION ?>" id="<?= GoogleReviewsService::BADGE_POSITION ?>"
                            data-mmp-check-field="<?= GoogleReviewsService::BADGE_ACTIVE ?>">
                        <?php foreach (GoogleReviewsService::BADGE_POSITIONS_FOR_SELECT() as $key => $data): ?>
                            <option
                                <?php if ( get_option(GoogleReviewsService::BADGE_POSITION, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                                value="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                    <br><small
                        class="badge badge_info"><?= __('Select badge position on page.', 'mergado-marketing-pack') ?></small>
                </div>
                <small
                        class="badge badge_info" style="margin-left: 15px;"><?= __('Paste this line in your HTML at the location on the page where you would like the badge to appear.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= GoogleReviewsService::LANGUAGE ?>"><?= __('Language', 'mergado-marketing-pack'); ?></label>
            </th>
            <td>
                <select name="<?= GoogleReviewsService::LANGUAGE ?>" id="<?= GoogleReviewsService::LANGUAGE ?>"
                        data-mmp-check-field="<?= GoogleReviewsService::OPT_IN_ACTIVE ?>">
                    <?php foreach (GoogleReviewsService::LANGUAGES() as $key => $data): ?>
                        <option
                            <?php if ( get_option(GoogleReviewsService::LANGUAGE, 0) == $data['id']){ ?>selected="selected"<?php } ?>
                            value="<?= $data['id'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
                <br><small
                        class="badge badge_info"><?= __('Select language for opt-in form and badge', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?= __('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
