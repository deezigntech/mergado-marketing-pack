<?php use Mergado\Tools\Settings; ?>

<div class="card full">
    <h3><?php _e('Etarget', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="etarget-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="etarget-form-active" name="etarget-form-active" data-mmp-check-main="etarget"
                       <?php if (get_option(Settings::ETARGET['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="etarget-form-hash"><?php _e('ETARGET hash', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="etarget-form-hash" name="etarget-form-hash" data-mmp-check-field="etarget"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::ETARGET['HASH'], ''); ?>"></td>
        </tr>
        <tr>
            <th>
                <label for="etarget-form-id"><?php _e('ETARGET ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="etarget-form-id" name="etarget-form-id" data-mmp-check-field="etarget"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::ETARGET['ID'], ''); ?>"></td>
        </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>"
               name="submit-save">
    </p>
</div>