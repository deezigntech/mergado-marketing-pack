<?php
    use Mergado\Facebook\FacebookService;
    $facebookClass = new FacebookService();
?>

<div class="card full">
    <h3><?php _e('Facebook Pixel', 'mergado-marketing-pack') ?></h3>
    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="facebook-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="facebook-form-active" name="facebook-form-active" data-mmp-check-main="facebook-active"
                       <?php if ($facebookClass->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="facebook-form-pixel"><?php _e('Facebook pixel ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="facebook-form-pixel" name="facebook-form-pixel" data-mmp-check-field="facebook-active"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $facebookClass->getCode() ?>">
                <br><small class="badge badge_question"><?= _e('Pixel ID can be found in your Facebook Business Manager. Go to Events Manager > Add new data feed > Facebook pixel. Pixel ID is displayed below the title on the Overview page at the top left.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>
        <tr>
            <th>
                <label for="facebook-vat-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="facebook-vat-included" name="facebook-vat-included" data-mmp-check-field="facebook-active"
	                   <?php if ($facebookClass->getConversionVatIncluded() == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
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
