<?php
    use Mergado\Arukereso\ArukeresoClass;

    $arukeresoClass = new ArukeresoClass();
?>

<div class="card full">
    <h3><?php _e('Árukereső Trusted Shop', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= ArukeresoClass::ACTIVE ?>"><?php _e('Enable Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= ArukeresoClass::ACTIVE ?>" name="<?= ArukeresoClass::ACTIVE ?>" data-mmp-check-main="<?= ArukeresoClass::ACTIVE ?>"
                       <?php if ($arukeresoClass->isActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WEB_API_KEY ?>"><?php _e('WebAPI', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= ArukeresoClass::WEB_API_KEY ?>" name="<?= ArukeresoClass::WEB_API_KEY ?>" data-mmp-check-field="<?= ArukeresoClass::ACTIVE ?>"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $arukeresoClass->getWebApiKey() ?>">
                <br><small class="badge badge_question"><?= _e('You will find the WebAPI key in the Arukereso portal under Megbízható Bolt Program > Csatlakozás > Árukereső WebAPI kulcs', 'mergado-marketing-pack') ?></small></td>
        </tr>

        <tr>
            <th><strong><?= _e('Edit consent to the questionnaire', 'mergado-marketing-pack') ?></strong></th>
            <td>
                <small class="badge badge_question">
                    <?= _e('Here you can edit the sentence of the consent to the sending of the questionnaire, displayed on the checkout page. This is an opt-out consent, ie the customer must confirm that he does not want to be involved in the program.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>


        <tr>
            <?php
            $enUsValue = $arukeresoClass->getOptOut('en_US');
            $defaultValue = ArukeresoClass::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <th>
                <label for="<?= ArukeresoClass::OPT_OUT . 'en_US' ?>"><?php _e('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <td colspan="2">
                <textarea
                        id="<?= ArukeresoClass::OPT_OUT . 'en_US' ?>"
                        name="<?= ArukeresoClass::OPT_OUT . 'en_US' ?>"
                        placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="<?= ArukeresoClass::ACTIVE ?>"
                ><?= $enUsValue ?></textarea>
                <br><small class="badge badge_info"><?= _e('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach(get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?= ArukeresoClass::OPT_OUT . $lang ?>"><?php _e($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?= ArukeresoClass::OPT_OUT . $lang ?>"
                            name="<?= ArukeresoClass::OPT_OUT . $lang ?>"
                            placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="<?= ArukeresoClass::ACTIVE ?>"
                    ><?= stripslashes($arukeresoClass->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WIDGET_ACTIVE ?>"><?php _e('Enable widget Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= ArukeresoClass::WIDGET_ACTIVE ?>" name="<?= ArukeresoClass::WIDGET_ACTIVE ?>" data-mmp-check-main="<?= ArukeresoClass::WIDGET_ACTIVE ?>" data-mmp-check-field="<?= ArukeresoClass::ACTIVE ?>"
                       <?php if ($arukeresoClass->isWidgetActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WIDGET_DESKTOP_POSITION ?>"><?php _e('Widget position on desktop', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoClass::WIDGET_DESKTOP_POSITION ?>" id="<?= ArukeresoClass::WIDGET_DESKTOP_POSITION ?>"
                        data-mmp-check-field="<?= ArukeresoClass::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoClass::DESKTOP_POSITIONS() as $key => $data): ?>
                        <option <?php if ($arukeresoClass->getWidgetDesktopPosition() == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WIDGET_APPEARANCE_TYPE ?>"><?php _e('Appearance type on desktop', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoClass::WIDGET_APPEARANCE_TYPE ?>" id="<?= ArukeresoClass::WIDGET_APPEARANCE_TYPE ?>"
                        data-mmp-check-field="<?= ArukeresoClass::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoClass::APPEARANCE_TYPES() as $key => $data): ?>
                        <option <?php if ($arukeresoClass->getWidgetAppearanceType() == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WIDGET_MOBILE_POSITION ?>"><?php _e('Widget position on mobile', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoClass::WIDGET_MOBILE_POSITION ?>" id="<?= ArukeresoClass::WIDGET_MOBILE_POSITION ?>"
                        data-mmp-check-field="<?= ArukeresoClass::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoClass::getMobilePositionsConstant() as $key => $data): ?>
                        <option <?php if ($arukeresoClass->getWidgetMobilePosition() == $data['id_option']){ ?>selected="selected"<?php } ?> value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoClass::WIDGET_MOBILE_WIDTH ?>"><?php _e('Width on the mobile', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= ArukeresoClass::WIDGET_MOBILE_WIDTH ?>" name="<?= ArukeresoClass::WIDGET_MOBILE_WIDTH ?>" data-mmp-check-field="<?= ArukeresoClass::WIDGET_ACTIVE ?>"
                       value="<?= $arukeresoClass->getWidgetMobileWidth() ?>"> px
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
