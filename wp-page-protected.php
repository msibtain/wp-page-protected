<?php
/*
Plugin Name: WP Page Protected
Plugin URI: https://github.com/msibtain/wp-page-protected
Description: Adds an option to protect page content from non-logged-in users
Version: 1.0.0
Author: Muhammad Sibtain
Author URI: https://github.com/msibtain
License: GPL v2 or later
License URI: https://www.gnu.org/licenses/gpl-2.0.html
*/

// Exit if accessed directly
if (!defined('ABSPATH')) {
    exit;
}

require_once __DIR__ . '/src/clsWPProtected.php';