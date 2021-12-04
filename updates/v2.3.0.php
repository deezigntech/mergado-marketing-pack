<?php

if(is_multisite()) {
    $sites = get_sites();

    foreach($sites as $site) {
        switch_to_blog($site->blog_id);

        removeOldGlamiOptions();;

        restore_current_blog();
    }
} else {
    removeOldGlamiOptions();
}

function removeOldGlamiOptions() {
    //Copy item to new name
    $cz_active = get_option('top_glami-form-active-lang-CZ', false);
    $cz_code = get_option('glami_top_code-CZ', false);

    if($cz_active) {
        add_option('glami-selection-top', 1);
    }

    if($cz_code) {
        add_option('glami-form-top', $cz_code);
    }

    //Remove old item
    delete_option('top_glami-form-active-lang-CZ');
    delete_option('glami_top_code-CZ');
}