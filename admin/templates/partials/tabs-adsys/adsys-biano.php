<?php use Mergado\Tools\Settings; ?>

<div class="card full">
    <h3><?php _e('Biano', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
            <tr>
                <th>
                    <label for="<?= Settings::BIANO['ACTIVE'] ?>"><?php _e('Active', 'mergado-marketing-pack') ?></label>
                </th>
                <td>
                    <input type="checkbox"
                           id="<?= Settings::BIANO['ACTIVE'] ?>"
                           name="<?= Settings::BIANO['ACTIVE'] ?>"
                           data-mmp-check-main="biano-active"
                           <?php if (get_option(Settings::BIANO['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
                </td>
            </tr>

            <?php foreach(Settings::BIANO['LANG_OPTIONS'] as $langCode): ?>
                <tr>
                    <th>
                        <label for="<?= Settings::BIANO['FORM_ACTIVE'] . '-' . $langCode ?>"><?php _e('Biano pixel', 'mergado-marketing-pack') ?> - <?= $langCode ?></label>
                    </th>
                    <td>
                        <input type="checkbox"
                               id="<?= Settings::BIANO['FORM_ACTIVE'] . '-' . $langCode ?>"
                               name="<?= Settings::BIANO['FORM_ACTIVE'] . '-' . $langCode ?>"
                               data-mmp-check-main="biano-active-<?= $langCode ?>" data-mmp-check-field="biano-active"
                               <?php if (get_option(Settings::BIANO['FORM_ACTIVE'] . '-' . $langCode, 0) == 1){ ?>checked="checked"<?php } ?>>

                        <input type="text"
                               id="<?= Settings::BIANO['MERCHANT_ID'] . '-' . $langCode ?>"
                               name="<?= Settings::BIANO['MERCHANT_ID'] . '-' . $langCode ?>"
                               data-mmp-check-field="biano-active-<?= $langCode ?>"
                               placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                               value="<?php echo get_option(Settings::BIANO['MERCHANT_ID'] . '-' . $langCode, ''); ?>">
                    </td>
                </tr>
            <?php endforeach ?>

            <tr>
                <th>
                    <label for="biano-vat-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="biano-vat-included" name="biano-vat-included" data-mmp-check-field="biano-active"
	                       <?php if (get_option(Settings::BIANO['CONVERSION_VAT_INCL'], 0) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit"
               class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>