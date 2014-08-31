<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      http://www.productwidgets.com/publishers/wordpress/
 * @copyright 2014 kraut computing UG (haftungsbeschränkt)
 */

/**
 * Plugin class
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */
class Product_Widgets {
  /**
   * Plugin version, used for cache-busting of style and script file references.
   *
   * @since   1.0.0
   *
   * @var     string
   */
  const VERSION = '1.0.0';

  /**
   * Unique identifier for your plugin.
   *
   * Use this value (not the variable name) as the text domain when internationalizing strings of text. It should
   * match the Text Domain file header in the main plugin file.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  public $plugin_slug = 'productwidgets';

  /**
   * Plugin name
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $plugin_name = 'ProductWidgets';

  /**
   * Instance of this class.
   *
   * @since    1.0.0
   *
   * @var      object
   */
  protected static $instance = null;

  /**
   * Base URL for loading widgets.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $base_url = null;

  /**
   * API
   *
   * @since    1.0.0
   *
   * @var      Api
   */
  public $api = null;

  /**
   * Initialize the plugin by setting localization, filters, and administration functions.
   *
   * @since     1.0.0
   */
  private function __construct() {
    $this->base_url = 'http://display.productwidgets'.(PW_DEV ? '.dev' : '.com');

    // Register admin settings
    add_action('admin_init', array($this, 'register_admin_settings'));

    // Add admin menu
    add_action('admin_menu', array($this, 'add_admin_menu'));

    // Load admin styles and scripts
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles_and_scripts'));

    // Add an action link pointing to the options page
    $plugin_basename = plugin_basename(plugin_dir_path(__FILE__).$this->plugin_slug.'.php');
    add_filter('plugin_action_links_'.$plugin_basename, array($this, 'add_action_links'));

    // Replace widget tags in the page content
    add_shortcode('productwidget', array($this, 'replace_widget_tag'));

    // Respond to Ajax calls from admin pages
    add_action('wp_ajax_get_tracking_ids', array($this, 'get_tracking_ids_callback'));
    add_action('wp_ajax_get_widget_layouts', array($this, 'get_widget_layouts_callback'));

    // Initialize the API
    $this->api = new Api(array(
      'url'     => 'http://api.productwidgets'.(PW_DEV ? '.dev' : '.com'),
      'version' => 1
    ));
  }

  /**
   * Return an instance of this class
   *
   * @since     1.0.0
   *
   * @return    object    A single instance of this class.
   */
  public static function get_instance() {
    // If the single instance hasn't been set, set it now.
    if (null == self::$instance)
      self::$instance = new self;
    return self::$instance;
  }

  /**
   * Register and enqueue admin-specific styles and scripts
   *
   * @since     1.0.0
   *
   * @return    null    Return early if no settings page is registered.
   */
  public function enqueue_admin_styles_and_scripts() {
    $screen = get_current_screen();
    if (preg_match("/".$this->plugin_slug."/i", $screen->id)) {
      wp_enqueue_style($this->plugin_slug."-admin-styles", plugins_url("css/admin.css", __FILE__), array(), self::VERSION);
      wp_enqueue_script($this->plugin_slug."-jquery-lazyload-script", plugins_url("js/jquery.lazyload.js", __FILE__), array("jquery"), self::VERSION, true);
      wp_enqueue_script($this->plugin_slug."-admin-script", plugins_url("js/admin.js", __FILE__), array("jquery"), self::VERSION, true);
    }
  }

  /**
   * Register admin settings
   *
   * @since     1.0.0
   */
  public function register_admin_settings() {
    add_settings_section(
      'general_settings_section',                 // ID used to identify this section and with which to register options
      '',                                         // Title to be displayed on the administration page
      '',                                         // Callback used to render the description of the section
      $this->plugin_slug                          // Page on which to add this section of options
    );

    add_settings_field(
      'api_key_field',                            // ID used to identify the field throughout the theme
      'API Key',                                  // The label to the left of the option interface element
      array($this, 'api_key_field'),              // The name of the function responsible for rendering the option interface
      $this->plugin_slug,                         // The page on which this option will be displayed
      'general_settings_section'                  // The name of the section to which this field belongs
    );

    register_setting(
      $this->plugin_slug,
      'api_key'
    );
  }

  /**
   * Render API Key field
   *
   * @since     1.0.0
   */
  public function api_key_field() {
    echo '<input id="api_key" name="api_key" size="40" type="text" value="'.get_option('api_key').'">';
  }

  /**
   * Register admin menu
   *
   * @since     1.0.0
   */
  public function add_admin_menu() {
    // Add top-level menu
    add_menu_page(
      $this->plugin_name,
      $this->plugin_name,
      'manage_options',
      $this->plugin_slug.'/widget-layouts.php',
      array($this, 'display_widget_layouts_page')
    );

    // Add sub-level menu "Widget Layouts"
    add_submenu_page(
      $this->plugin_slug.'/widget-layouts.php',
      'Widget Layouts',
      'Widget Layouts',
      'manage_options',
      $this->plugin_slug.'/widget-layouts.php',
      array($this, 'display_widget_layouts_page')
    );

    // Add sub-level menu "Settings"
    add_submenu_page(
      $this->plugin_slug.'/widget-layouts.php',
      'Settings',
      'Settings',
      'manage_options',
      $this->plugin_slug.'/settings.php',
      array($this, 'display_settings_page')
    );
  }

  /**
   * Render the widgets page
   *
   * @since    1.0.0
   */
  public function display_widget_layouts_page() {
    if (!current_user_can('manage_options'))
      wp_die(__('You do not have sufficient permissions to access this page.'));

    include_once('views/widget-layouts.php');
  }

  /**
   * Render the settings page
   *
   * @since    1.0.0
   */
  public function display_settings_page() {
    if (!current_user_can('manage_options'))
      wp_die(__('You do not have sufficient permissions to access this page.'));

    include_once('views/settings.php');
  }

  /**
   * Add settings action link to the plugins page.
   *
   * @since    1.0.0
   */
  public function add_action_links($links) {
    return array_merge(
      array(
        'settings' => '<a href="'.admin_url('admin.php?page='.$this->plugin_slug.'/settings.php').'">Settings</a>'
      ),
      $links
    );
  }

  /**
   * Fetches the tracking IDs from the API and send them as JSON.
   *
   * @since    1.0.0
   */
  function get_tracking_ids_callback() {
    try {
      $tracking_ids = $this->api->get_tracking_ids();
      wp_send_json($tracking_ids);
    } catch (Exception $e) {
      $error_message = "Could not load tracking IDs.";
      include("views/partials/_exception.php");
      die();
    }
  }

  /**
   * Renders the widgets partial.
   *
   * @since    1.0.0
   */
  function get_widget_layouts_callback() {
    include("views/partials/_widget-layouts.php");
    die();
  }

  /**
   * Convert a widget tag to a Javascript tag.
   *
   * @since    1.0.0
   */
  public function replace_widget_tag($attributes) {
    extract(shortcode_atts(array('layout' => ''), $attributes));
    return $this->generate_javascript_tag($layout);
  }

  /**
   * Generate a Javascript tag based on keywords and layout.
   *
   * @since    1.0.0
   */
  public function generate_javascript_tag($layout) {
    $api_key = get_option('api_key');
    if (empty($api_key)) return 'ProductWidgets: API Key not set.';
    return '<script src="'.$this->base_url.'/'.$api_key.'/'.$layout.'/widget.js" type="text/javascript"></script>'."\n";
  }

  public function locale_to_country($locale) {
    switch ($locale) {
      case "de": return "Germany";
      case "gb": return "United Kingdom";
      case "us": return "United States";
      case "ca": return "Canada";
      case "es": return "Spain";
      case "fr": return "France";
      case "it": return "Italy";
      default: throw new Exception("Locale ".$locale." is invalid.");
    }
  }
}
