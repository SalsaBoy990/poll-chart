<?php

namespace AG\PollChart\Input;

use AG\PollChart\Log\Logger as Logger;

trait FormInput
{
    use Logger;

    /**
     * Get form input, sanitize values
     * @return array associative
     */
    public function getFormInputValues(): array
    {
        $this->logger(AG_POLL_CHART_DEBUG, AG_POLL_CHART_LOGGING);

        // store escaped user input field values
        $formValues = array();

        if ($_POST['pollid'] ?? 0) {
            $id = $this->sanitizeInput($_POST['pollid']);
            $id = intval($id, 10);
            $formValues['id'] = absint($id);
        }

        if ($_POST['question_name'] ?? 0) {
            $question_name = $this->sanitizeInput($_POST['question_name']);
            $formValues['question_name'] = $question_name;
        } else {
            $formValues['question_name'] = '';
        }


        // Serialized array, use FILTERED unserialization when unserializing it!
        // Against PHP Object Injection attack
        // unserialize($serialized, [ 'allowed_classes' => [] ]);
        if ($_POST['choices'] ?? 0) {
            $choices = $_POST['choices'];

            // sanitize user input before serializing
            $sanitized_choices = [];
            foreach ($choices as $key => $value) {
                $sanitized_choices[$key] = $this->sanitizeInput($value);
            }
            $formValues['choices'] = serialize($sanitized_choices);
        } else {
            $formValues['choices'] = '';
        }


        if ($_POST['create_time'] ?? 0) {
            $create_time = $this->sanitizeInput($_POST['create_time']);
            $formValues['create_time'] = $create_time;
        } else {
            $formValues['create_time'] = time();
        }


        return $formValues;
    }

    /**
     * Sanitizes input values
     * strips tags, more sanitization needed!
     * @return string
     */
    public function sanitizeInput(string $input): string
    {
        // debug log and log to file
        $this->logger(AG_POLL_CHART_DEBUG, AG_POLL_CHART_LOGGING);

        $input = wp_strip_all_tags(trim($input));
        return esc_html($input);
    }
}
