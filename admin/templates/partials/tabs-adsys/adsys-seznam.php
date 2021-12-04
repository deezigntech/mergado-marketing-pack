<?php
    use Mergado\Tools\Settings;
    use Mergado\Zbozi\ZboziClass;
    $ZboziClass = new ZboziClass();
?>

<div class="card full">
    <h3><?php _e('Sklik', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="sklik-form-conversion-active"><?php _e('Sklik conversion tracking', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="sklik-form-conversion-active" name="sklik-form-conversion-active" data-mmp-check-main="sklik-conversion"
                       <?php if (get_option(Settings::SKLIK['CONVERSION_ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-conversion-code"><?php _e('Sklik conversion code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="sklik-form-conversion-code" name="sklik-form-conversion-code"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::SKLIK['CONVERSION_CODE'], ''); ?>"
                       data-mmp-check-field="sklik-conversion"
                >
                <br><small class="badge badge_question"><?= _e('You can find the code in Sklik → Tools → Conversion Tracking → Conversion Detail / Create New Conversion. The code is in the generated HTML conversion code after: src = "// c.imedia.cz/checkConversion?c=CONVERSION CODE', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-conversion-value"><?php _e('Sklik conversion value', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="number" id="sklik-form-conversion-value" name="sklik-form-conversion-value"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::SKLIK['CONVERSION_VALUE'], ''); ?>"
                       data-mmp-check-field="sklik-conversion"
                >
                <br><small class="badge badge_question"><?= _e('Leave blank to fill the order value automatically. Total price excluding VAT and shipping is calculated.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-vat-included"><?php _e('Sklik conversions with VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="sklik-vat-included" name="sklik-vat-included" data-mmp-check-field="sklik-conversion"
	                   <?php if (get_option(Settings::SKLIK['CONVERSION_VAT_INCL'], 0) == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Sklik recommends the conversion value to be excluding VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-retargeting-active"><?php _e('Sklik retargeting', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="sklik-form-retargeting-active" name="sklik-form-retargeting-active" data-mmp-check-main="sklik-retargeting"
                       <?php if (get_option(Settings::SKLIK['RETARGETING_ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="sklik-form-retargeting-id"><?php _e('Sklik retargeting code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="sklik-form-retargeting-id" name="sklik-form-retargeting-id" data-mmp-check-field="sklik-retargeting"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= get_option(Settings::SKLIK['RETARGETING_ID'], ''); ?>">
                <br><small class="badge badge_question"><?= _e('The code can be found in Sklik → Tools → Retargeting → View retargeting code. The code is in the generated script after: var list_retargeting_id = RETARGETING CODE', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php _e('Zboží.cz', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="zbozi-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="zbozi-form-active" name="zbozi-form-active" data-mmp-check-main="zbozi-active"
                       <?php if ($ZboziClass->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-standard-active"><?php _e('Enable standard conversion measuring', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="zbozi-form-standard-active" name="zbozi-form-standard-active" data-mmp-check-field="zbozi-active"
                       <?php if ($ZboziClass->getStandardActive() == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('Unlike limited tracking, Standard Conversion Tracking allows you to keep track of the number and value of conversions, as well as conversion rate, cost per conversion, direct conversions, units sold, etc.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-id"><?php _e('Zbozi shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="zbozi-form-id" name="zbozi-form-id" data-mmp-check-field="zbozi-active"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $ZboziClass->getId() ?>">
                <br><small class="badge badge_question"><?= _e('You can find your unique Secret Key in admin page zbozi.cz > Branches > ESHOP > Conversion Tracking > Your unique Secret Key.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="zbozi-form-id"><?php _e('Secret code', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="zbozi-form-secret-key" name="zbozi-form-secret-key" data-mmp-check-field="zbozi-active"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $ZboziClass->getKey() ?>">
                <br><small class="badge badge_question"><?= _e('You can find your unique Secret Key in admin page zbozi.cz > Branches > ESHOP > Conversion Tracking > Your unique Secret Key.', 'mergado-marketing-pack') ?></small></td>
        </tr>

        <tr>
            <th>
                <label for="zbozi-vat-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="zbozi-vat-included" name="zbozi-vat-included" data-mmp-check-field="zbozi-active"
	                   <?php if ($ZboziClass->getConversionVatIncluded() == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT. Note: In the specification of conversion tracking, Zboží.cz recommends the price of the order and shipping to be including VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th><strong><?= _e('Edit consent to the questionnaire', 'mergado-marketing-pack') ?></strong></th>
            <td>
                <small class="badge badge_question">
                    <?= _e('Here you can edit the sentence of the consent to the sending of the questionnaire, displayed on the checkout page. This is an opt-in consent, ie the customer must agree to participate in the programHere you can edit the text of the sentence of consent to the sending of the questionnaire, displayed in the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ZboziClass::OPT_IN . 'en_US' ?>"><?php _e('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <?php
            $enUsValue = stripslashes($ZboziClass->getOptOut('en_US'));
            $defaultValue = Mergado\Zbozi\ZboziClass::DEFAULT_OPT;


            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <td colspan="2">
                <textarea
                        id="<?= ZboziClass::OPT_IN . 'en_US' ?>"
                        name="<?= ZboziClass::OPT_IN . 'en_US' ?>"
                        placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="zbozi-active"
                ><?= $enUsValue ?></textarea>
                <br><small class="badge badge_info"><?= _e('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach(get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?= ZboziClass::OPT_IN . $lang ?>"><?php _e($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?= ZboziClass::OPT_IN . $lang ?>"
                            name="<?= ZboziClass::OPT_IN . $lang ?>"
                            placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="zbozi-active"
                    ><?= stripslashes($ZboziClass->getOptOut($lang)) ?></textarea>
                </td>
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
