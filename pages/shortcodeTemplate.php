<?php

$id = $formData['id'];

$question_name = $formData['question_name'];

$choices = $formData['choices'];

$deserialized_choices = unserialize($choices, ['class_names' => []]);

?>


<div id="poll-container" class="poll-chart">
    <h3><?php echo esc_html($question_name); ?></h3>
    <form id="<?php echo 'pollChart' . esc_html($id); ?>" action="" method="post" class="poll-chart">
        <div id="ag-poll-choices-shortcode">
            <?php
            $idx = 0;
            foreach ($deserialized_choices as $choices) {
            ?>
                <input type="radio" name="choices" id="<?php echo esc_html($choices); ?>" value="<?php echo esc_html($choices); ?>">
                <label for="<?php echo esc_html($choices); ?>"><?php echo esc_html($choices); ?></label><br />

            <?php
                $idx++;
            } ?>
        </div>
        <div class="mb1">
            <button id="<?php echo 'voteBtnShortcode' . esc_html($id); ?>" data-vote="<?php echo esc_html($id); ?>" class="ag-vote-btn" type="button">Vote!</button>
        </div>
    </form>

    <div id="<?php echo 'ag-poll-res' . esc_html($id); ?>">
    </div>
    <div id="<?php echo 'ag-poll-form-error' . esc_html($id); ?>"> </div>
</div>