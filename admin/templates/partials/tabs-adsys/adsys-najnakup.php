<?php use Mergado\Tools\Settings; ?>

<div class="card full">
    <h3><?php _e('NajNakup.sk', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="najnakup-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="najnakup-form-active" name="najnakup-form-active" data-mmp-check-main="najnakup"
                       <?php if (get_option(Settings::NAJNAKUP['ACTIVE'], 0) == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="najnakup-form-id"><?php _e('NajNakup shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="najnakup-form-id" name="najnakup-form-id" data-mmp-check-field="najnakup"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo get_option(Settings::NAJNAKUP['ID'], ''); ?>">
                <br><small class="badge badge_question"><?= _e('Your unique store ID for Najnakup.sk.', 'mergado-marketing-pack') ?></small>
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
