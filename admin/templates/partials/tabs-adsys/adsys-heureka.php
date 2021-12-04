<?php use Mergado\Tools\Settings; ?>

<!----------------------   CZ   ---------------------->

<div class="card full">
    <h3><?php _e('Heureka.cz : Verified by customers', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>

            <!--   Verified by customers - ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-verified-cz-form-active"><?php _e('Enable verified by users', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-verified-cz-form-active" data-mmp-check-main="heureka-verified-cz"
                           name="heureka-verified-cz-form-active"
                           <?php if (get_option(Settings::HEUREKA['ACTIVE_CZ'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>


            <!--   Verified by customers - CODE   -->
            <tr>
                <th>
                    <label for="heureka-verified-cz-form-code"><?php _e('Verified by users code', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-verified-cz-form-code" name="heureka-verified-cz-form-code" data-mmp-check-field="heureka-verified-cz"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['VERIFIED_CZ'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('You can find your store key in the Heureka account administration under Verified customers > Settings and questionnaire data > Secret Key for verified customers.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-form-active"><?php _e('Enable CZ widget', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-widget-cz-form-active" name="heureka-widget-cz-form-active" data-mmp-check-main="heureka-verified-widget-cz"
                           <?php if (get_option(Settings::HEUREKA['WIDGET_CZ'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - ID   -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-id"><?php _e('Widget ID', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-cz-id" name="heureka-widget-cz-id" data-mmp-check-field="heureka-verified-widget-cz"
                           placeholder="<?php _e('Insert Widget Id', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_CZ_ID'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('The ID is the same as the Public Key for conversion tracking.
Or you can find the key of your widget in the Heureka account administration under the tab Verified customers > Settings and questionnaire data > Certificate icons Verified customers. The numeric code is in the embed code. It takes the form "... setKey\',\'330BD_YOUR_WIDGET_KEY_2A80\']); _ hwq.push\' ..."', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - POSITION  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-position"><?php _e('Widget position', 'mergado-marketing-pack') ?></label>
                </th>

                <td>
                    <select name="heureka-widget-cz-position" id="heureka-widget-cz-position" data-mmp-check-field="heureka-verified-widget-cz">
                        <option <?php if (get_option(Settings::HEUREKA['WIDGET_CZ_POSITION'], 0) == 21){ ?>selected="selected"<?php } ?> value="21"><?php _e('Left', 'mergado-marketing-pack') ?></option>
                        <option <?php if (get_option(Settings::HEUREKA['WIDGET_CZ_POSITION'], 0) == 22){ ?>selected="selected"<?php } ?> value="22"><?php _e('Right', 'mergado-marketing-pack') ?></option>
                    </select>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - MARGIN TOP  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-margin"><?php _e('Widget top margin', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-cz-margin" name="heureka-widget-cz-margin" data-mmp-check-field="heureka-verified-widget-cz"
                           placeholder="<?php _e('Top margin', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_CZ_MARGIN'], '60'); ?>"> px
                </td>
            </tr>


            <?php /*
            <!--   Verified by customers - WIDGET - SHOW ON MOBILE  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-show-mobile"><?php _e('Show widget on mobile', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-widget-cz-show-mobile" name="heureka-widget-cz-show-mobile" data-mmp-check-field="heureka-verified-widget-cz"
                           <?php if (get_option(Settings::HEUREKA['WIDGET_CZ_SHOW_MOBILE'], 0) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('If this option is enabled, the widget will appear on mobile devices regardless of the width setting for hiding the widget.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - HIDE ON SMALLER SCREEN THAN  -->
            <tr>
                <th>
                    <label for="heureka-widget-cz-hide-width"><?php _e('Hide on screens smaller than', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-cz-hide-width" name="heureka-widget-cz-hide-width" data-mmp-check-field="heureka-verified-widget-cz"
                           placeholder="<?php _e('Min. width to show', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_CZ_HIDE_WIDTH'], ''); ?>"> px
                    <br><small class="badge badge_info"><?= _e('The setting to hide the widget below a certain screen width (in px) is only valid for desktops. On mobile devices, this setting is ignored.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

 */ ?>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?php _e('Heureka.cz : Conversions tracking', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>

            <!--   Order tracking - ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-track-cz-form-active"><?php _e('Track conversions', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-track-cz-form-active" name="heureka-track-cz-form-active" data-mmp-check-main="heureka-track-cz"
                           <?php if (get_option(Settings::HEUREKA['ACTIVE_TRACK_CZ'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>

            <!--   Order tracking - CODE   -->
            <tr>
                <th>
                    <label for="heureka-track-cz-form-code"><?php _e('Conversions code', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-track-cz-form-code" name="heureka-track-cz-form-code" data-mmp-check-field="heureka-track-cz"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['TRACK_CODE_CZ'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('You can find your store conversion tracking key in the Heureka account administration under the Statistics and Reports > Conversion Tracking > Public Key for Conversion Tracking Code.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="heureka-vat-cz-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-vat-cz-included" name="heureka-vat-cz-included" data-mmp-check-field="heureka-track-cz"
	                       <?php if (get_option(Settings::HEUREKA['CONVERSION_VAT_INCL_CZ'], 1) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Heureka recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>

<!----------------------   SK   ---------------------->

<div class="card full">
    <h3><?php _e('Heureka.sk : Verified by customers', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
            <!--   Verified by customers - ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-verified-sk-form-active"><?php _e('Enable verified by users', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-verified-sk-form-active" data-mmp-check-main="heureka-verified-sk"
                           name="heureka-verified-sk-form-active"
                           <?php if (get_option(Settings::HEUREKA['ACTIVE_SK'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>

            <!--   Verified by customers - CODE   -->
            <tr>
                <th>
                    <label for="heureka-verified-sk-form-code"><?php _e('Verified by users code', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-verified-sk-form-code" name="heureka-verified-sk-form-code" data-mmp-check-field="heureka-verified-sk"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['VERIFIED_SK'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('You can find your store key in the Heureka account administration under Verified customers > Settings and questionnaire data > Secret Key for verified customers.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

            <!--   Verified by customers - WIDGET ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-form-active"><?php _e('Enable sk widget', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-widget-sk-form-active" name="heureka-widget-sk-form-active" data-mmp-check-main="heureka-verified-widget-sk"
                           <?php if (get_option(Settings::HEUREKA['WIDGET_SK'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - ID   -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-id"><?php _e('Widget ID', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-sk-id" name="heureka-widget-sk-id" data-mmp-check-field="heureka-verified-widget-sk"
                           placeholder="<?php _e('Insert Widget Id', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_SK_ID'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('The ID is the same as the Public Key for conversion tracking.
Or you can find the key of your widget in the Heureka account administration under the tab Verified customers > Settings and questionnaire data > Certificate icons Verified customers. The numeric code is in the embed code. It takes the form "... setKey\',\'330BD_YOUR_WIDGET_KEY_2A80\']); _ hwq.push\' ..."', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>


            <!--   Verified by customers - WIDGET - POSITION  -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-position"><?php _e('Widget position', 'mergado-marketing-pack') ?></label>
                </th>
                <td>
                    <select name="heureka-widget-sk-position" id="heureka-widget-sk-position"
                            data-mmp-check-field="heureka-verified-widget-sk">
                        <option <?php if (get_option(Settings::HEUREKA['WIDGET_SK_POSITION'], 0) == 21){ ?>selected="selected"<?php } ?> value="21"><?php _e('Left', 'mergado-marketing-pack') ?></option>
                        <option <?php if (get_option(Settings::HEUREKA['WIDGET_SK_POSITION'], 0) == 22){ ?>selected="selected"<?php } ?> value="22"><?php _e('Right', 'mergado-marketing-pack') ?></option>
                    </select>

                </td>
            </tr>


            <!--   Verified by customers - WIDGET - MARGIN TOP  -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-margin"><?php _e('Widget top margin', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-sk-margin" name="heureka-widget-sk-margin" data-mmp-check-field="heureka-verified-widget-sk"
                           placeholder="<?php _e('Top margin', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_SK_MARGIN'], '60'); ?>"> px
                </td>
            </tr>

            <?php

/*
            <!--   Verified by customers - WIDGET - SHOW ON MOBILE  -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-show-mobile"><?php _e('Show widget on mobile', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-widget-sk-show-mobile" name="heureka-widget-sk-show-mobile" data-mmp-check-field="heureka-verified-widget-sk"
                           <?php if (get_option(Settings::HEUREKA['WIDGET_SK_SHOW_MOBILE'], 0) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('If this option is enabled, the widget will appear on mobile devices regardless of the width setting for hiding the widget.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

            <!--   Verified by customers - WIDGET - HIDE ON SMALLER SCREEN THAN  -->
            <tr>
                <th>
                    <label for="heureka-widget-sk-hide-width"><?php _e('Hide on screens smaller than', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-widget-sk-hide-width" name="heureka-widget-sk-hide-width" data-mmp-check-field="heureka-verified-widget-sk"
                           placeholder="<?php _e('Min. width to show', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['WIDGET_SK_HIDE_WIDTH'], ''); ?>"> px
                    <br><small class="badge badge_info"><?= _e('The setting to hide the widget below a certain screen width (in px) is only valid for desktops. On mobile devices, this setting is ignored.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

*/ ?>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>


<div class="card full">
    <h3><?php _e('Heureka.sk : Conversions tracking', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
            <!--   Order tracking - ENABLER   -->
            <tr>
                <th>
                    <label for="heureka-track-sk-form-active"><?php _e('Track conversions', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-track-sk-form-active" name="heureka-track-sk-form-active" data-mmp-check-main="heureka-conversion-sk"
                           <?php if (get_option(Settings::HEUREKA['ACTIVE_TRACK_SK'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>

            <!--   Order tracking - CODE   -->
            <tr>
                <th>
                    <label for="heureka-track-sk-form-code"><?php _e('Conversions code', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="text" id="heureka-track-sk-form-code" name="heureka-track-sk-form-code" data-mmp-check-field="heureka-conversion-sk"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::HEUREKA['TRACK_CODE_SK'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('You can find your store conversion tracking key in the Heureka account administration under the Statistics and Reports > Conversion Tracking > Public Key for Conversion Tracking Code.', 'mergado-marketing-pack') ?></small></td>
            </tr>

            <tr>
                <th>
                    <label for="heureka-vat-sk-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="heureka-vat-sk-included" name="heureka-vat-sk-included" data-mmp-check-field="heureka-conversion-sk"
	                       <?php if (get_option(Settings::HEUREKA['CONVERSION_VAT_INCL_SK'], 1) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Heureka recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>

<!----------------------   OTHER   ---------------------->

<div class="card full">
    <h3><?php _e('Heureka : Other settings', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>

        <tr>
            <th>
                <label for="heureka-stock-feed-form-active"><?php _e('Heureka Stock feed', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="heureka-stock-feed-form-active" name="heureka-stock-feed-form-active"
	                   <?php if (get_option(Settings::HEUREKA['STOCK_FEED'], 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('After activation, the Heureka availability feed will be available in the XML feed tab.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th><strong><?= _e('Edit text of consent', 'mergado-marketing-pack') ?></strong></th>
            <td>
            <small class="badge badge_question">
                <?= _e('Here you can edit the text of the sentence of consent to the sending of the questionnaire, displayed in the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

            <tr>
                <th>
                    <label for="heureka-verify-opt-out-text-en_US"><?php _e('en_US', 'mergado-marketing-pack') ?></label>
                </th>

                <?php
                    $enUsValue = stripslashes(get_option('heureka-verify-opt-out-text-en_US'));
                    $defaultValue = Mergado\Heureka\HeurekaClass::DEFAULT_OPT;

                    if (trim($enUsValue) == '') {
                        $enUsValue = $defaultValue;
                    }
                ?>

                <td colspan="2"><textarea id="heureka-verify-opt-out-text-en_US" name="heureka-verify-opt-out-text-en_US"
                                          placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"><?= $enUsValue ?></textarea>
                    <br><small class="badge badge_info"><?= _e('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>

            <?php foreach(get_available_languages() as $lang): ?>
                <tr>
                    <th>
                        <label for="heureka-verify-opt-out-text-<?= $lang ?>"><?php _e($lang, 'mergado-marketing-pack') ?></label>
                    </th>
                    <td colspan="2"><textarea id="heureka-verify-opt-out-text-<?= $lang ?>" name="heureka-verify-opt-out-text-<?= $lang ?>"
                               placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"><?= stripslashes(get_option('heureka-verify-opt-out-text-'. $lang)) ?></textarea></td>
                </tr>
            <?php endforeach ?>

        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>
