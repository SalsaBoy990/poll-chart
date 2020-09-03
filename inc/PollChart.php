<?php

namespace AG\PollChart;

use AG\PollChart\Crud\Crud as Crud;

use AG\PollChart\Shortcodes\ShortCodes as Shortcodes;

defined('ABSPATH') or die();

/**
 * Class for making polls
 * Author: András Gulácsi 2020
 */
final class PollChart
{
    private const TEXT_DOMAIN = 'ag-poll-chart';

    private const OPTION_NAME = 'ag_poll_chart_version';

    private const RESULTS = 'ag_poll_chart_results_';

    private const OPTION_VERSION = '1.0';

    private const TABLE_NAME = 'ag_poll_chart';

    private const DB_VERSION = '1.0';

    // class instance
    private static $instance;

    private static $crud;

    private static $shortcode;

    /**
     * Get class instance, if not exists -> instantiate it
     * @return self $instance
     */
    public static function getInstance()
    {
        if (self::$instance == null) {
            self::$instance = new self(
                new Crud(),
                new Shortcodes()
            );
        }
        return self::$instance;
    }


    // CONSTRUCTOR ------------------------------
    // initialize properties, some defaults added
    private function __construct(Crud $crud, Shortcodes $shortcode)
    {
        self::$crud = $crud;
        self::$shortcode = $shortcode;

        add_action('plugins_loaded', array($this, 'loadTextdomain'));

        // add admin menu and page
        add_action('admin_menu', array($this, 'addAdminMenu'));

        add_shortcode('ag_poll_chart', array(self::$shortcode, 'getMyPoll'));

        // put the css into head (only admin page)
        // add_action('admin_head', array($this, 'addCSS'));
        // add script on the backend
        add_action('admin_enqueue_scripts', array($this, 'adminLoadInsertScripts'));
        add_action('admin_enqueue_scripts', array($this, 'adminLoadEditScripts'));

        // put the css before end of </body>
        add_action('wp_enqueue_scripts', array($this, 'addCSS'));

        // add ajax script
        add_action('wp_enqueue_scripts', function () {
            wp_enqueue_script('ag-poll-chart-client', plugin_dir_url(dirname(__FILE__)) . 'js/agPollChartClient.js', array('jquery'));

            // enable ajax on frontend
            wp_localize_script('ag-poll-chart-client', 'AGPollChartAjax', array(
                'ajax_url' => admin_url('admin-ajax.php'),
                'security' => wp_create_nonce('agpollchartajax-ayjku7ye5')
            ));
        });


        // connect AJAX request with PHP hooks
        add_action('wp_ajax_ag_poll_chart_ajax_action', array($this, 'agPollChartAJAXHandler'));
        add_action('wp_ajax_nopriv_ag_poll_chart_ajax_action', array($this, 'agPollChartAJAXHandler'));

        add_action('wp_ajax_nopriv_ag_poll_chart_admin_ajax_action', array($this, 'agPollChartAdminAJAXHandler'));
        add_action('wp_ajax_ag_poll_chart_admin_ajax_action', array($this, 'agPollChartAdminAJAXHandler'));



        // hook for our widget implementation
        // add_action('widgets_init', array($this, 'registerWidgets'));
    }


    // DESCTRUCTOR -------------------------------
    public function __destruct()
    {
    }

    // METHODS
    public static function loadTextdomain(): void
    {
        // modified slightly from https://gist.github.com/grappler/7060277#file-plugin-name-php

        $domain = self::TEXT_DOMAIN;
        $locale = apply_filters('plugin_locale', get_locale(), $domain);

        load_textdomain($domain, trailingslashit(\WP_LANG_DIR) . $domain . '/' . $domain . '-' . $locale . '.mo');
        load_plugin_textdomain($domain, false, basename(dirname(__FILE__, 2)) . '/languages/');
    }

    /**
     * Register admin menu page and submenu page
     * @return void
     */
    public function addAdminMenu(): void
    {
        add_menu_page(
            __('Poll Chart Admin'), // page title
            __('Set up your poll form'), // menu title
            'manage_options', // capability
            'poll_chart_settings', // menu slug
            array(self::$crud, 'listTable'), // callback
            'dashicons-chart-pie' // icon
        );

        add_submenu_page(
            'poll_chart_settings', //parent slug
            __('Add new poll'), // page title
            __('Add new'),  // menu title
            'manage_options', // capability
            'poll_chart_insert', // menu slug
            array(self::$crud, 'insertRecord') // callback
        );
    }


    /**
     * Add some styling to the plugin's admin and shortcode UI
     * @return void
     */
    public function addCSS(): void
    {
        // add in head
        wp_enqueue_script(
            'google-charts',
            'https://www.gstatic.com/charts/loader.js',
            array(),
            '',
            false
        );

        wp_enqueue_style(
            'ag_poll_chart_frontend_css',
            plugins_url() . '/poll-chart/css/poll-chart.css'
        );
    }

    public function adminLoadInsertScripts($hook)
    {
        if ($hook !== 'set-up-your-poll-form_page_poll_chart_insert') {
            return;
        }

        wp_enqueue_script(
            'ag-poll-admin-insert',
            plugins_url() . '/poll-chart/js/agPollChartAdminInsert.js',
            array('jquery'),
            '',
            true
        );

        wp_enqueue_style(
            'ag_poll_chart_admin_css',
            plugins_url() . '/poll-chart/css/poll-chart.css'
        );
    }
    public function adminLoadEditScripts($hook)
    {
        if (
            $hook !== 'toplevel_page_poll_chart_settings'
        ) {
            return;
        }

        // add in head
        wp_enqueue_script(
            'google-charts-admin',
            'https://www.gstatic.com/charts/loader.js',
            array(),
            '',
            false
        );

        wp_enqueue_script(
            'ag-poll-admin-insert',
            plugins_url() . '/poll-chart/js/agPollChartAdminInsert.js',
            array('jquery'),
            '',
            true
        );

        wp_enqueue_script(
            'ag-poll-admin-edit',
            plugins_url() . '/poll-chart/js/agPollChartAdminEdit.js',
            array('jquery'),
            '',
            true
        );

        // enable ajax on backend
        wp_localize_script('ag-poll-admin-edit', 'AGPollChartAdminAjax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('agpollchartadminajax-dg4z78zab')
        ));


        wp_enqueue_style(
            'ag_poll_chart_admin_css',
            plugins_url() . '/poll-chart/css/poll-chart.css'
        );
    }


    /**
     * Add add an option with the version when activated
     */
    public static function activatePlugin(): void
    {
        global $wpdb;

        $table_name = $wpdb->prefix . self::TABLE_NAME;

        $charset_collate = $wpdb->get_charset_collate();

        $sql = "CREATE TABLE IF NOT EXISTS $table_name (
    `id` INT(11) UNSIGNED NOT NULL AUTO_INCREMENT,
    `question_name` TEXT NOT NULL,
    `choices` MEDIUMBLOB NOT NULL,
    `updated` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    PRIMARY KEY (`id`)
  ) $charset_collate;";

        require_once(\ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);


        $option = self::OPTION_NAME;
        // check if option exists, then delete
        if (!get_option($option)) {
            add_option($option, self::OPTION_VERSION);
        }
    }


    // This code will only run when plugin is deleted
    // it will drop the custom database table, delete wp_option record (if exists)
    public static function uninstallPlugin()
    {

        global $wpdb;
        $table_name = $wpdb->prefix . self::TABLE_NAME;
        $wpdb->query("DROP TABLE IF EXISTS $table_name");

        // check if option exists, then delete
        if (get_option(self::OPTION_NAME)) {
            delete_option(self::OPTION_NAME);
        }

        // delete settings option created via Settings API
        if (!get_option('ag_pollchart_results')) {
            delete_option('ag_pollchart_results');
        }
    }


    /**
     * Register the new widget.
     *
     * @see 'widgets_init'
     */
    // public function register_widgets()
    // {
    //     register_widget('\AG\PollChart\Widget\PollChartWidget');
    // }


    public function agPollChartAJAXHandler()
    {
        if (check_ajax_referer('agpollchartajax-ayjku7ye5', 'security')) {

            // print_r($_REQUEST);

            $id = intval($_REQUEST['id'], 10);
            $vote = esc_html($_REQUEST['vote']);


            // get choices from serialized array
            $formData = self::$crud->getItem($id, self::TABLE_NAME);
            $choices = $formData['choices'];
            $question_name = $formData['question_name'];

            $deserialized_choices = unserialize($choices, ['class_names' => []]);


            // get all vote choices, construct assoc array
            $vote_choices = array();
            foreach ($deserialized_choices as $key => $value) {
                if ($deserialized_choices[$key] === $vote) {
                    // value and key switched on purpose!!!
                    // in the deserialized array, keys are numbers
                    // values are the choice strings
                    $vote_choices[$value] = 1;
                } else {
                    $vote_choices[$value] = 0;
                }
            }

            $currentResultsTable = self::RESULTS . $id;

            // for the first time construct option record
            // with the initial values
            if (!get_option($currentResultsTable)) {
                add_option($currentResultsTable, $vote_choices);
            } else {
                $old_results = get_option($currentResultsTable);

                // add new values to the sum of votes for each choices
                // the keys are the choices strings here, $values are the vote counts
                $new_results = array();
                foreach ($old_results as $key => $value) {
                    $new_results[$key] = $old_results[$key] + $vote_choices[$key];
                }

                // update values
                update_option($currentResultsTable, $new_results);


                wp_send_json_success(
                    array(
                        'question'   => $question_name,
                        'chartData'  => $new_results
                    )
                );
            }

            $initial_results = get_option($currentResultsTable, $vote_choices);
            wp_send_json_success(
                array(
                    'question'   => $question_name,
                    'chartData'  => $initial_results
                )
            );
        } else {
            wp_send_json_error();
        }
        wp_die();
    }

    public function agPollChartAdminAJAXHandler()
    {
        if (current_user_can('manage_options')) {
            if (check_ajax_referer('agpollchartadminajax-dg4z78zab', 'security')) {
                // print_r($_REQUEST);

                $id = intval($_REQUEST['pollId'], 10);

                $currentResultsTable = self::RESULTS . $id;

                // for the first time construct option record
                // with the initial values
                if (!get_option($currentResultsTable)) {
                    wp_send_json_error('No data yet to show on the chart.');
                } else {
                    $current_results = get_option($currentResultsTable);
                    wp_send_json_success(
                        array(
                            'chartData'  => $current_results
                        )
                    );
                }
                wp_die();
            }
            wp_die();
        }
        wp_die();
    }
}
