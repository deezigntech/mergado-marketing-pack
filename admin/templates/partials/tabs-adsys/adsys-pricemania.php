<?php
    use Mergado\Pricemania\PricemaniaService;

    $pricemaniaService = new PricemaniaService();
?>

<div class="card full">
    <h3><?php _e('Pricemania', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo $pricemaniaService::ACTIVE; ?>"><?php _e('Active', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?php echo $pricemaniaService::ACTIVE; ?>" name="<?php echo $pricemaniaService::ACTIVE; ?>" data-mmp-check-main="pricemania"
                       <?php if ($pricemaniaService->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo $pricemaniaService::ID; ?>"><?php _e('Pricemania shop ID', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo $pricemaniaService::ID; ?>" name="<?php echo $pricemaniaService::ID; ?>" data-mmp-check-field="pricemania"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?php echo $pricemaniaService->getId(); ?>">
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
