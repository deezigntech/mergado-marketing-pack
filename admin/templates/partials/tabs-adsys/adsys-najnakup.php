<?php
    use Mergado\NajNakup\NajNakupService;

    $najNakupService = new NajNakupService();
?>

<div class="card full">
    <h3><?php _e('NajNakup.sk', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $najNakupService::ACTIVE; ?>"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo $najNakupService::ACTIVE; ?>" name="<?php echo $najNakupService::ACTIVE; ?>" data-mmp-check-main="najnakup"
                       <?php if ($najNakupService->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $najNakupService::ID; ?>"><?php _e('NajNakup shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $najNakupService::ID; ?>" name="<?php echo $najNakupService::ID; ?>" data-mmp-check-field="najnakup"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $najNakupService->getId(); ?>">
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
