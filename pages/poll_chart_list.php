<?php
if (current_user_can('manage_options')) {
    // display member list in a admin table
    global $wpdb;
    $valid = true;

    $sql = "SELECT * FROM " . $wpdb->prefix . 'ag_poll_chart';

    $formData = $wpdb->get_results($sql);

    // print_r($formData);

    if (!$formData) {
        $valid = false;
        // echo $sql . '- This form is invalid.';
    }
} else {
    $valid = false;
    echo 'You are not authorized to perform this action.';
}


// $current = get_current_screen(  );
// print_r($current);

?>
<h1 class="mt1 mb1"><?php echo __('Manage Polls'); ?></h1>

<div class="poll-chart-wrapper">
    <table class="poll-chart widefat table table-striped">
        <thead>
            <tr>
                <!-- <th scope="col">#</th> -->
                <th scope="col"><?php _e('ID'); ?></th>
                <th scope="col"><?php _e('Question name'); ?></th>
                <!-- <th scope="col"><?php // _e('Choices (serial.)'); ?></th> -->
                <th scope="col"><?php _e('Create time'); ?></th>
                <th scope="col"><?php _e('Action'); ?></th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($valid) :
                foreach ($formData as $row) :
                    $id             = $row->id;
                    $question_name  = $row->question_name;
                    $choices        = $row->choices;
                    $updated        = $row->updated;
            ?>
                    <tr>
                        <form action="" method="post">
                            <input type="hidden" name="listaction" value="edit">
                            <input type="hidden" name="pollid" value="<?php echo esc_html($id) ?>">
                            <!-- <td><?php echo esc_html($id); ?></td> -->
                            <td class="small-col"><?php echo esc_html($id); ?></td>
                            <td class="small-col"><?php echo esc_html($question_name); ?></td>
                            <!-- <td class="medium-col"><?php // echo esc_html($choices); ?></td> -->
                            <td class="medium-col"><?php echo esc_html($updated); ?></td>
                            <td>
                                <div class="btn-group" role="group">
                                    <button type="submit" class="button-secondary"><span class="poll-chart dashicons dashicons-edit"></span><?php _e('Edit'); ?></button>
                                </div>
                            </td>
                        </form>
                    </tr>
            <?php
                endforeach;
            endif;
            ?>
        </tbody>
    </table>
</div>
<form action="" method="post" class="mb1">
    <input type="hidden" name="listaction" value="insert">
    <button type="submit" class="button-primary"><span class="poll-chart dashicons dashicons-plus"></span><?php _e('Add new poll'); ?></button>
</form>