<?php

namespace AG\PollChart\Shortcodes;

defined('\ABSPATH') or die();

use AG\PollChart\Log\Logger as Logger;

use AG\PollChart\DB\WPDBHandle as WPDBHandle;

use AG\PollChart\Crud\{EmptyDBTableException as EmptyDBTableException, DBQueryException as DBQueryException};


/**
 * Shortcode functionality class
 * Note: uses a global constant called 'AG_COMPANY_TEAM_PLUGIN_DIR',
 * but has no other dependencies
 */
class ShortCodes extends WPDBHandle
{
    use Logger;

    private const DEBUG = 0;
    private const LOGGING = 1;

    private const TABLE_NAME = 'ag_poll_chart';

    public function __construct()
    {
    }
    public function __destruct()
    {
    }


    /**
     * Get all members from the database argument passed by reference
     * @param reference $formData
     * @return bool
     */
    // public function getAllMembersFromDB(&$formData): bool
    // {
    //     $this->logger(self::DEBUG, self::LOGGING);

    //     try {
    //         $valid = true;

    //         // db abstraction layer
    //         $formData = $this->dbHandle->list(self::TABLE_NAME);

    //         if (!$formData) {
    //             $valid = false;
    //             throw new EmptyDBTableException('Warning: Data table does not contain any records yet.');
    //         }
    //     } catch (EmptyDBTableException $ex) {
    //         echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
    //         $this->exceptionLogger(self::LOGGING, $ex);
    //     } catch (DBQueryException $ex) {
    //         echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
    //         $this->exceptionLogger(self::LOGGING, $ex);
    //     } catch (\Exception $ex) {
    //         echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
    //         $this->exceptionLogger(self::LOGGING, $ex);
    //     } finally {
    //         // always executes
    //         return $valid;
    //     }
    // }



    /**
     * List all members as shortcode
     * table and list views
     * extract shortcode arguments
     * @param array $atts shortcode arguments as key-value pairs
     * @return string
     * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
     */
    public function getMyPoll(array $atts, string $content = null): string
    {
        $this->logger(self::DEBUG, self::LOGGING);

        global $post;

        /**
         * extract shortcode arguments
         * @see https://developer.wordpress.org/reference/functions/shortcode_atts/
         * id: ID of the poll you want to be shown
         */
        extract(shortcode_atts(array(
            'id' => null
        ), $atts));


        $id = filter_var(esc_html($id), FILTER_SANITIZE_NUMBER_INT);
        $id = intval($id, 10);

        try {
            $formData = $this->getItem($id, self::TABLE_NAME);

            // print_r($formData);
            if (!$formData) {
                $valid = false;
                throw new EmptyDBTableException('Warning: Data table does not contain any records yet.');
            }
        } catch (EmptyDBTableException $ex) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (DBQueryException $ex) {
            echo '<div class="notice notice-warning is-dismissible"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        }

        ob_start();
        
        require AG_POLL_CHART_PLUGIN_DIR . '/pages/shortcodeTemplate.php';
       
        $content = ob_get_clean();

        return $content;
    }
}
