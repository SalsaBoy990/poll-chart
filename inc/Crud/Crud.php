<?php

namespace AG\PollChart\Crud;

defined('ABSPATH') or die();

/**
 * CRUD functionality class
 */
class Crud extends \AG\PollChart\DB\WPDBHandle implements CrudInterface
{
    private const DEBUG = 0;
    private const LOGGING = 1;
    private const TABLE_NAME = 'ag_poll_chart';
    private const RESULTS = 'ag_poll_chart_results_';

    public function __construct()
    {
        parent::__construct();
    }
    public function __destruct()
    {
    }


    /**
     * Post actions switcher function
     */
    public function postAction(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        global $id;

        // if (isset($_POST) && !empty($_POST)) {
        if (($_POST ?? 0) && !empty($_POST)) {
            $listaction = $_POST['listaction'];

            if ($_POST['pollid'] ?? 0) {
                $id = absint(intval($_POST['pollid'], 10));
            }

            switch ($listaction) {
                    // add new member
                case 'insert':
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_insert.php';
                    break;

                    // edit member
                case 'edit':
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_edit.php';
                    break;

                    // list elements
                case 'list':
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
                    break;

                    // handler function when updating
                case 'handleupdate':
                    $this->handleUpdate();
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
                    break;

                    // handler function when deleting
                case 'handledelete':
                    $this->handleDelete();
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
                    break;

                    // handler function when inserting new member
                case 'handleinsert':
                    $this->handleInsert();
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
                    break;
                default:
                    require AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
                    break;
            }
        } else {
            include AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
        }
    }



    /**
     * Insert new record, add new team member
     * @return void
     */
    public function handleInsert(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify insert nonce !!!
        if (
            !isset($_POST['poll_chart_admin_insert_security'])
            || !wp_verify_nonce($_POST['poll_chart_admin_insert_security'], 'poll_chart_insert')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                // get sanitized form values from inputs
                $sanitizedData = $this->getFormInputValues();

                // prepare query, update table
                $res = $this->insert(self::TABLE_NAME, $sanitizedData);

                if ($res === false) {
                    throw new InsertRecordException('Database Error: Unable to insert new member into table.');
                } else {
                    echo <<<ADDMEMBER
                        <div class="notice notice-success is-dismissible">
                            <p>Poll chart member successfully added.</p>
                        </div>
ADDMEMBER;
                }
            } catch (InsertRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }



    /**
     * Update current member
     * @return void
     */
    public function handleUpdate(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify edit nonce !!!
        if (
            !isset($_POST['poll_chart_admin_edit_security']) ||
            !wp_verify_nonce($_POST['poll_chart_admin_edit_security'], 'poll_chart_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                // get sanitized form values from inputs
                $sanitizedData = $this->getFormInputValues();

                $res = $this->update(self::TABLE_NAME, $sanitizedData);

                // echo $res . PHP_EOL;
                // echo $res === false;
                if ($res == null) {
                    echo <<<MEMBERNOCHANGE
                        <div class="notice notice-success is-dismissible">
                            <p>Team member data unchanged and saved.</p>
                        </div>
MEMBERNOCHANGE;
                } elseif ($res === false) {
                    throw new UpdateRecordException(
                        'Database Error: Unable to update team member data/record.'
                    );
                } else {
                    echo <<<MEMBERUPDATE
                        <div class="notice notice-success is-dismissible">
                            <p>Team member data successfully updated.</p>
                        </div>
MEMBERUPDATE;
                }
            } catch (UpdateRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }


    /**
     * delete current member
     * @return void
     */
    public function handleDelete(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        // !!! verify edit nonce !!!
        if (
            !isset($_POST['poll_chart_admin_edit_security'])
            || !wp_verify_nonce($_POST['poll_chart_admin_edit_security'], 'poll_chart_edit')
        ) {
            print 'Sorry, your nonce did not verify.';
            exit;
        } else {
            try {
                if ($_POST['pollid'] ?? 0) {
                    $id = $_POST['pollid'];

                    // DELETE item
                    $res = $this->delete($id);

                    if ($res === false) {
                        throw new DeleteRecordException('Database error: Unable to delete team member.');
                    } else {
                        echo <<<DELETEMEMBER
                            <div class="notice notice-success is-dismissible">
                                <p>Team member successfully deleted.</p>
                            </div>
DELETEMEMBER;
                    }
                }
            } catch (DeleteRecordException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (DBQueryException $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            } catch (\Exception $ex) {
                echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
                $this->exceptionLogger(self::LOGGING, $ex);
            }
        }
    }


    /**
     * Add new member
     * @return void
     */
    public function insertRecord(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        try {
            if (!current_user_can('manage_options')) {
                throw new PermissionsException('You do not have sufficent permissions to view this page.');
                wp_die('You do not have sufficent permissions to view this page.');
            }

            if (!empty($_POST)) {
                $this->postAction();
            } else {
                include AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_insert.php';
            }
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        }
    }




    /**
     * Get list of all members
     * @return void
     */
    public function listTable(): void
    {
        // debug log and log to file
        $this->logger(self::DEBUG, self::LOGGING);

        try {
            // note: current_user_can() always returns false if the user is not logged in
            if (!current_user_can('manage_options')) {
                throw new PermissionsException(
                    'You do not have sufficent permissions to view this page.'
                );
                wp_die();
            }

            $this->postAction();
            // include AG_POLL_CHART_PLUGIN_DIR . '/pages/poll_chart_list.php';
        } catch (PermissionsException $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        } catch (\Exception $ex) {
            echo '<div class="notice notice-error"><p>' . $ex->getMessage() . '</p></div>';
            $this->exceptionLogger(self::LOGGING, $ex);
        }
    }
}
