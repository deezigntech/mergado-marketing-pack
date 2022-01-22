<?php
use Mergado\Glami\GlamiPixelService;
use Mergado\Glami\GlamiTopService;
use Mergado\Tools\Settings;

$glamiPixelClass = new GlamiPixelService();
$glamiTopClass = new GlamiTopService();
?>



<div class="card full">
    <h3><?php _e('Glami piXel', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th><label for="glami-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label></th>
            <td><input type="checkbox" id="glami-form-active" name="glami-form-active" data-mmp-check-main="glami-pixel-active"
                       <?php if ($glamiPixelClass->getActive() == 1){ ?>checked="checked"<?php } ?>>
            </td>
            <td><small class="badge badge_question"><?= _e('You can find your piXel in the Glami Administration at Glami piXel page > Implementing Glami piXel for Developers > Glami piXel Code section for YOUR ESHOP.', 'mergado-marketing-pack') ?></small></td>
            <td></td>
        </tr>
        <tr>
            <th>
                <label for="glami-vat-included"><?php _e('With VAT', 'mergado-marketing-pack') ?></label>
            </th>
            <td colspan="2"><input type="checkbox" id="glami-vat-included" name="glami-vat-included" data-mmp-check-field="glami-pixel-active"
	                   <?php if ($glamiPixelClass->getConversionVatIncluded() == 1){ ?>checked="checked"<?php } ?>>
                <br><small class="badge badge_info"><?= _e('Choose whether the conversion value will be sent with or without VAT.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach (GlamiPixelService::LANGUAGES as $key => $lang):
            $codeName = GlamiPixelService::getCodeName($lang);
            $activeLangName = GlamiPixelService::getActiveLangName($lang);
        ?>
            <tr>
                <th>
                    <label for="<?= $codeName ?>"><?php _e('Pixel code', 'mergado-marketing-pack');
                        echo ' ' . $lang ?></label></th>
                <td class="glami-ECO">
                    <label for="<?= $activeLangName ?>"><?= $lang ?></label>
                    <input type="checkbox" id="<?= $activeLangName ?>" name="<?= $activeLangName ?>" data-mmp-check-field="glami-pixel-active" data-mmp-check-main="glami-pixel-<?=$lang?>"
                           <?php if ($glamiPixelClass->getActiveLang($lang) == 1): ?>checked="checked" <?php endif ?>/>
                </td>
                <td><input type="text" id="<?= $codeName ?>"
                           name="<?= $codeName ?>"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?= $glamiPixelClass->getCode($lang); ?>"
                           data-mmp-check-field="glami-pixel-<?=$lang?>" data-mmp-check-subfield="true">
                </td>
                <td>
                </td>
            </tr>
        <?php endforeach ?>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>

<div class="card full">
    <h3><?php _e('Glami TOP', 'mergado-marketing-pack') ?></h3>

    <ul>
        <li>
            - <?php _e('Your website must have HTTPS protocol on order confirmation page', 'mergado-marketing-pack') ?></li>
        <li>- <?php _e('You have to set your DNS before use', 'mergado-marketing-pack') ?></li>
        <li>-
            <a href="https://www.glami.cz/info/reviews/implementation/"><?php _e('Read more', 'mergado-marketing-pack') ?></a>
        </li>
    </ul>
    <table class="wp-list-table widefat striped">
        <tbody>
            <tr>
                <th><label for="glami-top-form-active"><?php _e('Active', 'mergado-marketing-pack') ?></label></th>
                <td><input type="checkbox" id="glami-top-form-active" name="glami-top-form-active" data-mmp-check-main="glami-top"
                           <?php if ($glamiTopClass->getActive() == 1){ ?>checked="checked"<?php } ?>>
                   </td>
            </tr>

            <tr>
                <th>
                    <label for="<?= GlamiTopService::SELECTION?>"><?php _e('Glami website', 'mergado-marketing-pack');?></label>
                </th>
                <td>
                    <select name="<?= GlamiTopService::SELECTION ?>" id="<?= GlamiTopService::SELECTION ?>"
                            data-mmp-check-field="glami-top">

                    <?php foreach (GlamiTopService::LANGUAGES as $key => $data): ?>
                        <option <?php if ($glamiTopClass->getSelection() && $glamiTopClass->getSelection()['id_option'] == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                    </select>
                </td>
            </tr>
            <tr>
                <th>
                    <label for="<?= GlamiTopService::CODE?>"><?php _e('Glami TOP code', 'mergado-marketing-pack'); ?></label></th>
                <td>
                    <input type="text" id="<?= GlamiTopService::CODE ?>" name="<?= GlamiTopService::CODE ?>"
                           data-mmp-check-field="glami-top"
                           placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                           value="<?= $glamiTopClass->getCode() ?>">
                    <br><small class="badge badge_question"><?= _e('You can find your Glami TOP API key in the Glami Administration at the Glami TOP page > Implementation > Developer Implementation Guide> Javascript Integration section.', 'mergado-marketing-pack') ?></small>
                </td>
            </tr>
        </tbody>
    </table>

    <p>
        <input type="submit" class="button button-primary button-large"
               value="<?php _e('Save', 'mergado-marketing-pack') ?>" name="submit-save">
    </p>
</div>
