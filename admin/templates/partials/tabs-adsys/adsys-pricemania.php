<?php use Mergado\Tools\Settings; ?>

<div class="card full">
    <h3><?php _e('Pricemania', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="pricemania-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="pricemania-form-active" name="pricemania-form-active" data-mmp-check-main="pricemania"
                       <?php if (get_option(Settings::PRICEMANIA['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="pricemania-form-id"><?php _e('Pricemania shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="pricemania-form-id" name="pricemania-form-id" data-mmp-check-field="pricemania"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::PRICEMANIA['ID'], ''); ?>">
                    <br><small class="badge badge_question"><?= _e('Your unique Store ID from Pricemania.', 'mergado-marketing-pack') ?></small>
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
