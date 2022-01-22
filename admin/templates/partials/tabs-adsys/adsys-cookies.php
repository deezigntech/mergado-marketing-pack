<?php use Mergado\Tools\CookieClass; ?>

<div class="card full mmp_cookiepage">
    <h2><?php _e('Cookie consent settings', 'mergado-marketing-pack') ?></h2>
    <p><?php _e('<strong>When you activate this feature, advertising scripts that use cookies will not automatically run unless consent is granted.</strong><br> Using this feature is at your own risk. The creator of module, the company Mergado technologies, LLC, is not liable for any losses or damages in any form.', 'mergado-marketing-pack') ?></p>

    <div style="display: flex; align-items: center; margin-top: 20px;">
        <input type="checkbox" id="<?php echo CookieClass::FIELD_COOKIES_ENABLE ?>" name="<?php echo CookieClass::FIELD_COOKIES_ENABLE ?>"
               <?php if (CookieClass::isCookieBlockingEnabled()){ ?>checked="checked"<?php } ?>>
        <label style="height: 23px; font-weight: 700;" for="<?php echo CookieClass::FIELD_COOKIES_ENABLE ?>"><?php _e('Activate cookie consent settings', 'mergado-marketing-pack') ?></label>
    </div>

    <h3 style="margin-top: 30px"><?php _e('CookieYes plugin support', 'mergado-marketing-pack') ?></h3>

    <p><?php _e('If you have <strong>activated CookieYes plugin</strong>, there is <strong>no need to set up anything.</strong>', 'mergado-marketing-pack') ?></p>

    <div style="background-color: #f2f2f2; padding: 1px 15px 16px; border-radius: 4px; margin-bottom: 30px; border: 1px solid #c3c4c7; box-shadow: 0 1px 1px rgb(0 0 0 / 4%);">
        <p><?php _e('The functions are divided by consent type as follows:') ?></p>
        <div>
            <strong><?php _e('Advertisement:', 'mergado-marketing-pack') ?></strong> <?php _e('Google Ads, Facebook Pixel, Heureka conversion tracking, Glami piXel, Sklik retargeting, Sklik conversion tracking, Zboží conversion tracking, Etarget, Najnakup.sk, Pricemania, Kelkoo conversion tracking, Biano Pixel', 'mergado-marketing-pack') ?>
        </div>
        <div>
            <strong><?php _e('Analytics:', 'mergado-marketing-pack') ?></strong> <?php _e('Google Analytics', 'mergado-marketing-pack') ?>
        </div>
        <div>
            <strong><?php _e('Functional:', 'mergado-marketing-pack') ?></strong> <?php _e('Google Customer Reviews, Heureka Verified by Customer', 'mergado-marketing-pack') ?>
        </div>

        <hr style="margin-top: 16px;">

        <p style="margin-bottom: 0;"><i>Google Tag Manager and other unlisted features are not dependent on consent.</i></p>

    </div>

    <h3><?php _e('Set cookie values manually', 'mergado-marketing-pack') ?></h3>

    <p><?php _e('Manually type name of the cookie that corresponds to selected category.', 'mergado-marketing-pack') ?></p>
    <p><?php _e('To activate scripts after change of user consent call javascript code <code>window.mmp.cookies.functions.checkAndSetCookies()</code> or reload the page.', 'mergado-marketing-pack') ?></p>

    <table class="wp-list-table widefat striped">
        <tbody>
        <tr>
            <th>
                <label for="<?php echo CookieClass::FIELD_ANALYTICAL_USER ?>"><?php _e('Analytics cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieClass::FIELD_ANALYTICAL_USER ?>" name="<?php echo CookieClass::FIELD_ANALYTICAL_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php _e('Insert name of analytics cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo CookieClass::getAnalyticalCustomName() ?>">
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo CookieClass::FIELD_ADVERTISEMENT_USER ?>"><?php _e('Advertisement cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieClass::FIELD_ADVERTISEMENT_USER ?>" name="<?php echo CookieClass::FIELD_ADVERTISEMENT_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php _e('Insert name of advertisement cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo CookieClass::getAdvertisementCustomName() ?>">
                <br>
            </td>
        </tr>
        <tr>
            <th>
                <label for="<?php echo CookieClass::FIELD_FUNCTIONAL_USER ?>"><?php _e('Functional cookies', 'mergado-marketing-pack') ?></label>
            </th>
            <td><input type="text" id="<?php echo CookieClass::FIELD_FUNCTIONAL_USER ?>" name="<?php echo CookieClass::FIELD_FUNCTIONAL_USER ?>"
                       style="width: 250px;"
                       placeholder="<?php _e('Insert name of functional cookie', 'mergado-marketing-pack') ?>"
                       value="<?php echo CookieClass::getFunctionalCustomName() ?>">
                <br>
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
