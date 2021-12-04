<?php use Mergado\Tools\Settings; ?>

<div class="card full">
    <h3><?php _e('Kelkoo', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
            <tr>
                <th><label for="<?= Settings::KELKOO['ACTIVE'] ?>"><?php _e('Active', 'mergado-marketing-pack') ?></label>
                </th>
                <td>
                    <input type="checkbox"
                           id="<?= Settings::KELKOO['ACTIVE'] ?>"
                           name="<?= Settings::KELKOO['ACTIVE'] ?>"
                           data-mmp-check-main="kelkoo-active"
                           <?php if (get_option(Settings::KELKOO['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?> />
                </td>
            </tr>

            <tr>
                <th>
                    <label for="<?= Settings::KELKOO['COUNTRY']?>"><?php _e('Kelkoo country', 'mergado-marketing-pack');?></label>
                </th>
                <td>
                    <select name="<?= Settings::KELKOO['COUNTRY'] ?>" id="<?= Settings::KELKOO['COUNTRY'] ?>"
                            data-mmp-check-field="kelkoo-active"
                        <?php foreach (Settings::KELKOO_COUNTRIES as $key => $data): ?>
                            <option <?php if (get_option(Settings::KELKOO['COUNTRY'], 0) == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="<?= Settings::KELKOO['COM_ID']?>"><?php _e('Kelkoo merchant ID', 'mergado-marketing-pack'); ?></label></th>
                <td>
                    <input type="text" id="<?= Settings::KELKOO['COM_ID'] ?>" name="<?= Settings::KELKOO['COM_ID'] ?>"
                           data-mmp-check-field="kelkoo-active"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo get_option(Settings::KELKOO['COM_ID'], ''); ?>">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="kelkoo-vat-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="kelkoo-vat-included" name="kelkoo-vat-included" data-mmp-check-field="kelkoo-active"
	                       <?php if (get_option(Settings::KELKOO['CONVERSION_VAT_INCL'], 0) == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>