<?php

namespace AG\PollChart\DB;

class WPDBHandle
{
    use \AG\PollChart\Input\FormInput;

    public function __construct()
    {
    }
    public function __destruct()
    {
    }

    protected function list(string $tableName): array
    {
        // display list in a admin table
        // GET request
        global $wpdb;

        $sql = "SELECT * FROM " . $wpdb->prefix . $tableName;
        $res = $wpdb->get_results($sql);
    
        return $res;
    }

    public function getItem(int $id, string $tableName): array
    {
        // display list in a admin table
        // GET request
        global $wpdb;

        $sql = $wpdb->prepare("SELECT * FROM " . $wpdb->prefix . $tableName . " WHERE id=%d", $id);
        $res = $wpdb->get_row($sql, \ARRAY_A);
    
        return $res;
    }

    protected function insert(string $tableName, array $sanitizedData): bool
    {
        global $wpdb;

        date_default_timezone_set('Europe/Budapest');

        // prepare query, update table
        $res = $wpdb->insert(
            $wpdb->prefix . $tableName,
            array(
                'question_name' => $sanitizedData['question_name'],
                'choices'       => $sanitizedData['choices'],
                'updated'       => date('Y-m-d H:i:s')
            ),
            array('%s', '%s', '%s') // data format
        );

        return $res;
    }
    protected function update(string $tableName, array $sanitizedData, bool $profilePhoto = false): bool
    {
        global $wpdb;

        date_default_timezone_set('Europe/Budapest');
        
        // prepare query, update table
        $res = $wpdb->update(
            $wpdb->prefix . $tableName,
            array(
                'question_name' => $sanitizedData['question_name'],
                'choices'       => $sanitizedData['choices'],
                'updated'       => date('Y-m-d H:i:s')
            ),
            // where clause
            array('id'  => $sanitizedData['id']),
            // data format
            array('%s', '%s', '%s'),
            // where format
            array('%d')
        );

        return $res;
    }

    protected function delete(int $id): bool
    {
        global $wpdb;
        // prepare get statement protect against SQL inject attacks!
        $sql = $wpdb->prepare("DELETE FROM " . $wpdb->prefix . "ag_poll_chart WHERE id = %d", $id);

        // perform query
        $res = $wpdb->query($sql);

        return $res;
    }
}
