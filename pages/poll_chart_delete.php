<?php

if (current_user_can('manage_options')) {
    // delete current member
    global $wpdb;

    // prepare get statement protect against SQL inject
    $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "ag_poll_chart WHERE id = %d", $id);

    $row = $wpdb->query($sql);

    $pollOptionName = self::RESULTS . $id;

    // also delete voting results record in wp_options table
    if (get_option($pollOptionName)) {
        delete_option($pollOptionName);
    }
} else {
    echo 'You are not authorized to perform this action.';
}
