<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      http://www.productwidgets.com/publishers/wordpress/
 * @copyright 2014 kraut computing UG (haftungsbeschränkt)
 *
 * @wordpress-plugin
 * Plugin Name: ProductWidgets
 * Plugin URI:  http://www.productwidgets.com/
 * Description: Monetize your website with intelligent affiliate widgets displaying products that perfectly fit your unique content.
 * Text Domain: productwidgets
 * Version:     1.0.1
 * Author:      Kraut Computing
 * Author URI:  http://www.krautcomputing.com
 * License:     GPL-2.0
 * License URI: http://www.gnu.org/licenses/gpl-2.0.txt
 */

// If this file is called directly, abort.
if (!defined('WPINC'))
  die;

define('PW_DEV', get_bloginfo('admin_email') == 'admin@pw-dev.dev');

require_once(plugin_dir_path(__FILE__).'api/class-api-response.php');
require_once(plugin_dir_path(__FILE__).'api/class-api-request.php');
require_once(plugin_dir_path(__FILE__).'api/api-exceptions.php');
require_once(plugin_dir_path(__FILE__).'api-productwidgets.php');
require_once(plugin_dir_path(__FILE__).'class-productwidgets.php');

add_action('plugins_loaded', array('Product_Widgets', 'get_instance'));
