<?php

use Mergado\Kelkoo\KelkooService;

$kelkooService = new KelkooService();

?>

<div class="card full">
    <h3><?php _e('Kelkoo', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
            <tr>
                <th><label for="<?php echo $kelkooService::ACTIVE ?>"><?php _e('Active', 'mergado-marketing-pack') ?></label>
                </th>
                <td>
                    <input type="checkbox"
                           id="<?php echo $kelkooService::ACTIVE ?>"
                           name="<?php echo $kelkooService::ACTIVE ?>"
                           data-mmp-check-main="kelkoo-active"
                           <?php if ($kelkooService->getActive() == 1){ ?>checked="checked"<?php } ?> />
                </td>
            </tr>

            <tr>
                <th>
                    <label for="<?php echo $kelkooService::COUNTRY?>"><?php _e('Kelkoo country', 'mergado-marketing-pack');?></label>
                </th>
                <td>
                    <select name="<?php echo $kelkooService::COUNTRY ?>" id="<?php echo $kelkooService::COUNTRY ?>"
                            data-mmp-check-field="kelkoo-active"
                        <?php foreach ($kelkooService::COUNTRIES as $key => $data): ?>
                            <option <?php if ($kelkooService->getCountry() && $kelkooService->getCountry()['id_option'] == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?php echo $data['id_option'] ?>"><?php echo $data['name'] ?></option>
                        <?php endforeach ?>
                    </select>
                </td>
            </tr>

            <tr>
                <th>
                    <label for="<?php echo $kelkooService::ID; ?>"><?php _e('Kelkoo merchant ID', 'mergado-marketing-pack'); ?></label></th>
                <td>
                    <input type="text" id="<?php echo $kelkooService::ID; ?>" name="<?php echo $kelkooService::ID; ?>"
                           data-mmp-check-field="kelkoo-active"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?php echo $kelkooService->getId() ?>">
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
                </th>
                <td><input type="checkbox" id="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>" name="<?php echo $kelkooService::CONVERSION_VAT_INCL ?>" data-mmp-check-field="kelkoo-active"
	                       <?php if ($kelkooService->getConversionVatIncluded() == 1){ ?>checked="checked"<?php } ?>>
                    <br><small class="badge badge_info"><?php _e('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>