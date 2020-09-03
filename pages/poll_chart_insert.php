<h1><?php _e('Add new poll'); ?></h1>

<?php
print_r(get_current_screen());

?>
<div class="card bg-light">
    <div class="card-header">

        <h3 class="card-title">
            <?php _e('Add poll details'); ?>
        </h3>
    </div>
    <div class="card-body">
        <div>
            <form action="#" method="post">
                <input type="hidden" name="postid" value="">
                <?php wp_nonce_field('poll_chart_insert', 'poll_chart_admin_insert_security'); ?>
                <div class="form-group mbhalf">
                    <label for="question_name"><?php _e('Question name'); ?></label><br />
                    <textarea name="question_name" id="question_name" class="large-text" cols="30" rows="5"></textarea>
                </div>

                <h2>
                    <label for="ag-poll-choices-insert"><?php _e('Add poll choices'); ?></label><br />
                </h2>
                <div id="ag-poll-choices-insert" class="form-group mbhalf">
                    <label for="choices[1]"><?php _e('Choice 1:'); ?></label><br />
                    <input type="text" class="form-control large-text" name="choices[1]" value="" data-number="1" /><br />

                    <label for="choices[2]"><?php _e('Choice 2:'); ?></label><br />
                    <input type="text" class="form-control large-text" name="choices[2]" value="" data-number="2" /><br />
                </div>
                <div>
                    <!-- client click event js -->
                    <button id="ag-poll-add-choice-insert-btn" type="button" name="add-choice" class="button-secondary"><?php _e('+ add'); ?></button>
                </div>

                <div class="mt1" style="margin-top: 20px;">
                    <button type="submit" name="listaction" value="handleinsert" class="button-primary"><?php _e('Create poll'); ?></button>
                    <button type="submit" name="listaction" value="list" class="button-secondary"><?php _e('Cancel'); ?></button>
                </div>
            </form>
        </div>
    </div>
</div>