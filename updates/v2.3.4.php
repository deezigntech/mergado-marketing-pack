<?php

if(is_multisite()) {
    $sites = get_sites();

    foreach($sites as $site) {
        switch_to_blog($site->blog_id);

        changeCountryIdToCountryCode();;

        restore_current_blog();
    }
} else {
    changeCountryIdToCountryCode();;
}

function changeCountryIdToCountryCode()
{
    $value = get_option('m_feed_vat_option');

    if ($value) {
        $countryCode = getTaxCodeById($value);

        if(!$countryCode) {
            update_option('m_feed_vat_option', '');
        } else {
            update_option('m_feed_vat_option', $countryCode);
        }
    }
}

function getTaxCodeById($id)
{
    global $wpdb;
    $prepare = $wpdb->prepare(
        "SELECT * 
                    FROM {$wpdb->prefix}woocommerce_tax_rates 
                    WHERE tax_rate_id = %s
                    ORDER BY tax_rate_country", $id
    );

    return $wpdb->get_row($prepare)->tax_rate_country;
}