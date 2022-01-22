<?php
    use Mergado\Arukereso\ArukeresoService;

    $arukeresoService = new ArukeresoService();
?>

<div class="card full">
    <h3><?php _e('Árukereső Trusted Shop', 'mergado-marketing-pack') ?></h3>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?= ArukeresoService::ACTIVE ?>"><?php _e('Enable Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= ArukeresoService::ACTIVE ?>" name="<?= ArukeresoService::ACTIVE ?>" data-mmp-check-main="<?= ArukeresoService::ACTIVE ?>"
                       <?php if ($arukeresoService->isActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WEB_API_KEY ?>"><?php _e('WebAPI', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= ArukeresoService::WEB_API_KEY ?>" name="<?= ArukeresoService::WEB_API_KEY ?>" data-mmp-check-field="<?= ArukeresoService::ACTIVE ?>"
                       placeholder="<?php _e('Insert code here', 'mergado-marketing-pack') ?>"
                       value="<?= $arukeresoService->getWebApiKey() ?>">
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
            $enUsValue = $arukeresoService->getOptOut('en_US');
            $defaultValue = ArukeresoService::DEFAULT_OPT;

            if (trim($enUsValue) == '') {
                $enUsValue = $defaultValue;
            }
            ?>

            <th>
                <label for="<?= ArukeresoService::OPT_OUT . 'en_US' ?>"><?php _e('en_US', 'mergado-marketing-pack') ?></label>
            </th>

            <td colspan="2">
                <textarea
                        id="<?= ArukeresoService::OPT_OUT . 'en_US' ?>"
                        name="<?= ArukeresoService::OPT_OUT . 'en_US' ?>"
                        placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                        data-mmp-check-field="<?= ArukeresoService::ACTIVE ?>"
                ><?= $enUsValue ?></textarea>
                <br><small class="badge badge_info"><?= _e('English text will be used as default value if any other language won\'t be filled.', 'mergado-marketing-pack') ?></small>
            </td>
        </tr>

        <?php foreach(get_available_languages() as $lang): ?>
            <tr>
                <th>
                    <label for="<?= ArukeresoService::OPT_OUT . $lang ?>"><?php _e($lang, 'mergado-marketing-pack') ?></label>
                </th>
                <td colspan="2">
                    <textarea
                            id="<?= ArukeresoService::OPT_OUT . $lang ?>"
                            name="<?= ArukeresoService::OPT_OUT . $lang ?>"
                            placeholder="<?php _e('Insert your text for this language', 'mergado-marketing-pack') ?>"
                            data-mmp-check-field="<?= ArukeresoService::ACTIVE ?>"
                    ><?= stripslashes($arukeresoService->getOptOut($lang)) ?></textarea>
                </td>
            </tr>
        <?php endforeach ?>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WIDGET_ACTIVE ?>"><?php _e('Enable widget Trusted Shop', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="checkbox" id="<?= ArukeresoService::WIDGET_ACTIVE ?>" name="<?= ArukeresoService::WIDGET_ACTIVE ?>" data-mmp-check-main="<?= ArukeresoService::WIDGET_ACTIVE ?>" data-mmp-check-field="<?= ArukeresoService::ACTIVE ?>"
                       <?php if ($arukeresoService->isWidgetActive()){ ?>checked="checked"<?php } ?>>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WIDGET_DESKTOP_POSITION ?>"><?php _e('Widget position on desktop', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoService::WIDGET_DESKTOP_POSITION ?>" id="<?= ArukeresoService::WIDGET_DESKTOP_POSITION ?>"
                        data-mmp-check-field="<?= ArukeresoService::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoService::DESKTOP_POSITIONS() as $key => $data): ?>
                        <option <?php if ( $arukeresoService->getWidgetDesktopPosition() == $data['id_option']){ ?>selected="selected"<?php } ?>value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WIDGET_APPEARANCE_TYPE ?>"><?php _e('Appearance type on desktop', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoService::WIDGET_APPEARANCE_TYPE ?>" id="<?= ArukeresoService::WIDGET_APPEARANCE_TYPE ?>"
                        data-mmp-check-field="<?= ArukeresoService::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoService::APPEARANCE_TYPES() as $key => $data): ?>
                        <option <?php if ( $arukeresoService->getWidgetAppearanceType() == $data['id_option']){ ?>selected="selected"<?php } ?>value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WIDGET_MOBILE_POSITION ?>"><?php _e('Widget position on mobile', 'mergado-marketing-pack');?></label>
            </th>
            <td>
                <select name="<?= ArukeresoService::WIDGET_MOBILE_POSITION ?>" id="<?= ArukeresoService::WIDGET_MOBILE_POSITION ?>"
                        data-mmp-check-field="<?= ArukeresoService::WIDGET_ACTIVE ?>">
                    <?php foreach (ArukeresoService::getMobilePositionsConstant() as $key => $data): ?>
                        <option <?php if ( $arukeresoService->getWidgetMobilePosition() == $data['id_option']){ ?>selected="selected"<?php } ?>value="<?= $data['id_option'] ?>"><?= $data['name'] ?></option>
                    <?php endforeach ?>
                </select>
            </td>
        </tr>

        <tr>
            <th>
                <label for="<?= ArukeresoService::WIDGET_MOBILE_WIDTH ?>"><?php _e('Width on the mobile', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?= ArukeresoService::WIDGET_MOBILE_WIDTH ?>" name="<?= ArukeresoService::WIDGET_MOBILE_WIDTH ?>" data-mmp-check-field="<?= ArukeresoService::WIDGET_ACTIVE ?>"
                       value="<?= $arukeresoService->getWidgetMobileWidth() ?>"> px
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
