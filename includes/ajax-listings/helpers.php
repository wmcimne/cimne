<?php

if (!defined('CIMNE_EVENT_DATE_FIELD')) {
    define('CIMNE_EVENT_DATE_FIELD', 'date');
}

if (!defined('CIMNE_EVENT_FINISH_DATE_FIELD')) {
    define('CIMNE_EVENT_FINISH_DATE_FIELD', 'finish_date');
}

if (!defined('CIMNE_EVENT_COMPARE_DATE_FIELD')) {
    define('CIMNE_EVENT_COMPARE_DATE_FIELD', 'event_compare_date');
}

function cimne_get_event_compare_date($post_id) {
    $date        = get_post_meta($post_id, CIMNE_EVENT_DATE_FIELD, true);
    $finish_date = get_post_meta($post_id, CIMNE_EVENT_FINISH_DATE_FIELD, true);

    if (!empty($finish_date)) {
        return $finish_date;
    }

    return $date;
}

/*================================================
 * Helper to parse ACF date fields stored as Ymd.
 ================================================*/
function cimne_parse_event_date($date_string) {
    if (empty($date_string)) {
        return null;
    }

    $date = DateTime::createFromFormat('Ymd', $date_string);

    if (!$date) {
        return null;
    }

    return $date;
}
