<?php

namespace AG\PollChart;

defined('ABSPATH') or die();

/*
Plugin Name: Poll Chart
Plugin URI: https://github.com/SalsaBoy990/poll-chart
Description: Create a poll and view results in a Google chart after voting
Version: 1.0
Author: András Gulácsi
Author URI: https://github.com/SalsaBoy990
License: GPLv2 or later
Text Domain: ag-poll-chart
Domain Path: /languages
*/

// require all requires once
require_once 'requires.php';

use AG\PollChart\PollChart as PollChart;

use AG\PollChart\Log\KLogger as Klogger;


$ag_poll_chart_log_file_path = plugin_dir_path(__FILE__) . '/log';

$ag_poll_chart_log = new KLogger($ag_poll_chart_log_file_path, KLogger::INFO);

// main class
PollChart::getInstance();

// we don't need to do anything when deactivation
// register_deactivation_hook(__FILE__, function () {});

register_activation_hook(__FILE__, '\AG\PollChart\PollChart::activatePlugin');

// delete options when uninstalling the plugin
register_uninstall_hook(__FILE__, 'AG\PollChart\PollChart::uninstallPlugin');
