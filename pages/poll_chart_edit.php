<?php

if (current_user_can('manage_options')) {
    // edit form for simple member
    global $wpdb;
    $valid = true;

    // prepare get statement protect against SQL inject
    $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . "ag_poll_chart WHERE id = %d", $id);

    $row = $wpdb->get_row($sql);

    // get the values for the current member record
    $question_name  = $row->question_name;
    $choices        = $row->choices;

    $deserialized_choices = unserialize($choices, ['allowed_classes' => []]);

    // print_r($formData);

    if (!$row) {
        $valid = false;
        echo $sql . '- This form is invalid.';
    }
} else {
    echo 'You are not authorized to perform this action.';
}
?>
<h1><?php echo __('Edit poll details'); ?></h1>


<div class="card bg-light">
    <div class="card-header">

        <h3 class="card-title">
            <?php _e('Poll details'); ?>
        </h3>
    </div>
    <div class="card-body">
        <div>
            <form action="#" method="post">
                <?php wp_nonce_field('poll_chart_edit', 'poll_chart_admin_edit_security'); ?>
                <input type="hidden" name="pollid" value="<?php echo esc_html($id); ?>">

                <div class="form-group mbhalf">
                    <label for="question_name"><?php _e('Question name'); ?></label><br />
                    <textarea name="question_name" id="question_name" class="large-text" cols="30" rows="5"><?php echo esc_html($question_name) ?></textarea>
                </div>

                <label for="ag-poll-choices-edit"><?php _e('Add poll choices'); ?></label><br />
                <div id="ag-poll-choices-edit" class="form-group mbhalf">
                    <?php
                    $idx = 1;
                    foreach ($deserialized_choices as $choices) {
                    ?>
                        <label for="<?php echo 'choices[' . $idx . ']'; ?>"><?php echo 'Choice: ' . $idx; ?></label><br />
                        <input type="text" class="form-control large-text" name="<?php echo 'choices[' . $idx . ']'; ?>"
                            value="<?php echo esc_html($choices); ?>" data-number="<?php echo $idx; ?>" />
                        <br />
                    <?php
                        $idx++;
                    }

                    ?>
                </div>
                <div>
                    <!-- client click event js -->
                    <button id="ag-poll-add-choice-edit-btn" type="button" name="add-choice" class="button-secondary"><?php _e('+ add'); ?></button>
                </div>

                <div class="mt1">
                    <button type="submit" name="listaction" value="handleupdate" class="button-primary"><?php _e('Update'); ?></button>
                    <button type="submit" name="listaction" value="list" class="button-secondary"><?php _e('Cancel'); ?></button>
                    <button type="submit" name="listaction" value="handledelete" class="poll-chart button-secondary button-danger" onclick="return confirm('Are you sure you want to delete this member?'); "><?php _e('Delete'); ?></button>
                </div>
            </form>


            </form>
        </div>
    </div> 
</div>

<div class="card bg-light">
    <div class="card-header">

        <h3 class="card-title">
            <?php _e('Poll results'); ?>
        </h3>
    </div>
    <div class="card-body">
        <div id="ag-poll-results-edit-page"></div>
    </div>
</div>