<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 *
 * @wordpress-plugin
 * Plugin Name: ProductWidgets
 * Plugin URI:  https://www.productwidgets.com/
 * Description: The Smart Alternative to Banner Ads - Monetise your website by promoting relevant products to your visitors.
 * Text Domain: productwidgets
 * Version:     2.0.3
 * Author:      Kraut Computing
 * Author URI:  https://www.krautcomputing.com
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
