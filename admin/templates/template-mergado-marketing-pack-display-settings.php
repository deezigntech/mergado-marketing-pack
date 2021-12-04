<?php

include_once __MERGADO_DIR__ . 'autoload.php';

include_once( 'partials/template-mergado-marketing-pack-header.php' );


use Mergado\Tools\BannersClass;
use Mergado\Tools\ImportPricesClass;
use Mergado\Tools\Settings;
use Mergado\Tools\XML\EanClass;

/**
 * Product feed form settings
 */
if (isset($_POST['import-form-products'])) {
    //Product feed
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['PRODUCT_FEED']]);
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['STOCK_FEED']]);
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['CATEGORY_FEED']]);

    $files = glob(__MERGADO_TMP_DIR__ . Settings::getCurrentBlogId() . '/productFeed/*');

    foreach ($files as $file) {
        if (is_file($file)) {
            unlink($file);
        }
    }

    //Feed VAT options
    Settings::saveOptions($_POST, [], [], [Settings::VAT]);

    //Import settings
    Settings::saveOptions($_POST, [], [Settings::OPTIMIZATION['IMPORT_FEED']]);
    Settings::saveOptions($_POST, [], [ImportPricesClass::IMPORT_URL]);

    //Ean options
    Settings::saveOptions($_POST, [], [], [EanClass::EAN_PLUGIN]);
    Settings::saveOptions($_POST, [], [], [EanClass::EAN_PLUGIN_FIELD]);
}
?>

<div class="wrap">
    <div class="rowmer">
        <div class="col-content">
            <form method="post" id="import-form" action="">
                <h2><?= __('Export settings', 'mergado-marketing-pack') ?></h2>

                <div class="card full">

                    <h3><?php _e('Generate export in batches', 'mergado-marketing-pack') ?></h3>

                    <p><?php _e('Set how many products will be generated per export batch. Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></p>
                    <table class="wp-list-table widefat striped">
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-products"><?php _e('Number of products to be generated in one step of Product feed', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td><input type="text" id="feed-form-products" name="feed-form-products"
                                       placeholder="<?php _e('Insert number of products', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('feed-form-products', ''); ?>">
                                <br>
                                <small class="badge badge_info"><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="feed-form-stock"><?php _e('Number of products to be generated in one step of Heureka stock feed', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td><input type="text" id="feed-form-stock" name="feed-form-stock"
                                       placeholder="<?php _e('Insert number of products', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('feed-form-stock', ''); ?>">
                                <br>
                                <small class="badge badge_info"><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="feed-form-category"><?php _e('Number of categories to be generated in one step of Category feed', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td><input type="text" id="feed-form-category" name="feed-form-category"
                                       placeholder="<?php _e('Insert number of products', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('feed-form-category', ''); ?>">
                                <br>
                                <small class="badge badge_info"><?php _e('Leave blank to generate the entire XML feed at once.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <p>
                        <input type="submit" class="button button-primary button-large" value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-feed-form">
                    </p>
                </div>

                <br/><br/>

                <div class="card full">
                    <h3><?php _e('EAN code used in export', 'mergado-marketing-pack') ?></h3>
                    <p><?php _e('EAN added to product using selected plugin will be used in product feed results.', 'mergado-marketing-pack') ?></p>
                    <p><?php _e('Following selectbox provides supported plugins. In order to use their values, they must be installed and activated.', 'mergado-marketing-pack') ?></p>
                    <!--                    <p>--><?php //_e('Our exports will use the VAT with the highest priority for the selected country.', 'mergado-marketing-pack') ?><!--</p>-->
                    <table class="wp-list-table widefat striped">
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-products"><?php _e('Select plugin', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td>
                                <?php
                                    $eanClass = new EanClass();
                                ?>

                                <select name="<?= EanClass::EAN_PLUGIN ?>" id="select-ean">

                                    <?php
                                        $eanOptions = EanClass::getOptionsForSelect();
                                        $eanSubOptions = EanClass::getSuboptionsForSelect();
                                        $eanSelectedOption = EanClass::getSelectedPlugin();
                                        $eanSubSelectedOption = EanClass::getSelectedPluginField();

                                        if (!$eanSelectedOption || $eanSelectedOption == 0) { ?>
                                            <option value="" data-has-fields="false" selected><?= __('Disabled', 'mergado-marketing-pack') ?></option>
                                        <?php } else { ?>
                                            <option value="" data-has-fields="false"><?= __('Disabled', 'mergado-marketing-pack') ?></option>
                                        <?php }

                                        foreach ($eanOptions as $key => $option): ?>
                                            <option value="<?= $key ?>"
                                                <?php
                                                if($eanSelectedOption === $key) {
                                                    echo 'selected';
                                                }

                                                if (!$option['active']) {
                                                    echo 'disabled';
                                                }
                                                ?>

                                                data-has-fields="<?= $eanOptions[$key]['hasFields'] ?>"
                                            >
                                                <?= $option['name'] ?>
                                            </option>
                                        <?php endforeach ?>
                                </select>
                            </td>
                        </tr>
                        <tr id="eanSubFieldLine" style="display:none;">
                            <th>
                                <label for="feed-form-products"><?php _e('Select plugin field', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td>
                                <select name="<?= EanClass::EAN_PLUGIN_FIELD ?>" id="select-ean-subselect" data-subselect-selected="<?= $eanSubSelectedOption ?>">
                                    <?php
                                    if($eanSelectedOption):
                                        foreach ($eanSubOptions[$eanSelectedOption] as $key => $option): ?>
                                            <option value="<?= $key ?>"
                                                <?php
                                                    if($eanSubSelectedOption === $key) {
                                                        echo 'selected';
                                                    }
                                                ?>
                                            >
                                                <?= $option['name'] ?>
                                            </option>
                                        <?php
                                        endforeach;
                                    endif;
                                    ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <script>
                        document.addEventListener('DOMContentLoaded', function () {
                          var selectbox = document.getElementById('select-ean');
                          var subSelect = document.getElementById('select-ean-subselect');
                          var subSelectSelected = subSelect.getAttribute('data-subselect-selected');
                          var eanSubFieldLine = document.getElementById('eanSubFieldLine');

                          window.eanSelectData = <?= json_encode($eanSubOptions) ?>;

                          selectbox.addEventListener('change', function (e) {
                            changeSubselect(e);
                          });

                          function changeSubselect(e)
                          {
                            var currentValue = e.target.value;
                            var currentData = window.eanSelectData[currentValue];
                            if (currentData && Object.keys(currentData).length > 0) {


                              Object.keys(currentData).forEach(function (key) {
                                var option = document.createElement('option');
                                option.setAttribute('value', key);

                                if (key === subSelectSelected) {
                                  option.setAttribute('selected', 'selected');
                                }

                                option.text = currentData[key]['name'];
                                subSelect.add(option);
                              });

                              eanSubFieldLine.style.display = 'table-row';
                            } else {
                              subSelect.options.length = 0;
                              eanSubFieldLine.style.display = 'none';
                            }
                          }

                          if (subSelect.options.length && subSelect.options.length > 0) {
                            eanSubFieldLine.style.display = 'table-row';
                          }

                        });
                    </script>

                    <p>
                        <input type="submit" class="button button-primary button-large" value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-subselect-ean">
                    </p>
                </div>

                <br/><br/>

                <div class="card full">
                    <h3><?php _e('Country (VAT) used in exports', 'mergado-marketing-pack') ?></h3>
                    <p><?php _e('VAT percentage is taken by priority. If no VAT rate match your country code, then \'*\' is taken. If country code \'*\' is not set, then 0% is used.', 'mergado-marketing-pack') ?></p>
<!--                    <p>--><?php //_e('Our exports will use the VAT with the highest priority for the selected country.', 'mergado-marketing-pack') ?><!--</p>-->
                    <table class="wp-list-table widefat striped">
                        <tbody>
                        <tr>
                            <th>
                                <label for="feed-form-products"><?php _e('Select tax rule', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td>
                                <select name="<?= Settings::VAT ?>" id="feed-form-vat">

                                    <?php $rates = Settings::getTaxRates();
                                        foreach ($rates as $rate):
                                    ?>
                                        <option value="<?= $rate->tax_rate_country ?>"
                                            <?php
                                                if($rate->tax_rate_country === get_option(Settings::VAT)) {
                                                   echo 'selected';
                                                }
                                            ?>
                                        >
                                            <?= $rate->tax_rate_country === '' ? '*' : $rate->tax_rate_country?>
                                        </option>
                                    <?php
                                        endforeach
                                    ?>
                                </select>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <p>
                        <input type="submit" class="button button-primary button-large" value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-feed-form-vat">
                    </p>
                </div>


                <br/><br/>
                <h2><?= __('Import settings', 'mergado-marketing-pack') ?></h2>

                <div class="card full">
                    <h3><?php _e('Import prices from Mergado - optimization', 'mergado-marketing-pack') ?></h3>

                    <p><?php _e('Price import from Mergado XML feed', 'mergado-marketing-pack') ?></p>
                    <table class="wp-list-table widefat striped">
                        <tbody>
                        <tr>
                            <th>
                                <label for="import_product_prices_url"><?php _e('Prices import feed URL', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td><input type="text" id="import_product_prices_url" name="import_product_prices_url"
                                       value="<?php echo get_option('import_product_prices_url', ''); ?>">
                                <br>
                                <small class="badge badge_info"><?php _e('Insert URL of import prices feed from Mergado webpage.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        <tr>
                            <th>
                                <label for="import-form-products"><?php _e('Number of products imported in one cron run', 'mergado-marketing-pack') ?></label>
                            </th>
                            <td><input type="text" id="import-form-products" name="import-form-products"
                                       placeholder="<?php _e('Insert number of steps', 'mergado-marketing-pack') ?>"
                                       value="<?php echo get_option('import-form-products', ''); ?>">
                                <br>
                                <small class="badge badge_info"><?php _e('Leave blank or 0 if you don\'t have problem with importing product prices.', 'mergado-marketing-pack') ?></small>
                            </td>
                        </tr>
                        </tbody>
                    </table>

                    <p>
                        <input type="submit" class="button button-primary button-large" value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-import-form">
                    </p>
                </div>
            </form>
        </div>
        <div class="col-side">
            <?= BannersClass::getSidebar()?>
        </div>
    </div>
    <div class="merwide">
        <?= BannersClass::getWide() ?>
    </div>
</div>
