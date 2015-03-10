<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
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
   * URL for displaying widgets.
   *
   * @since    1.0.0
   *
   * @var      string
   */
  protected $display_url = null;

  /**
   * URL to the plugin folder
   *
   * @since    1.0.1
   *
   * @var      string
   */
  public $plugin_url = null;

  /**
   * API
   *
   * @since    1.0.0
   *
   * @var      Api
   */
  public $api = null;

  public $countries = array('Germany', 'France', 'Italy', 'Spain', 'Netherlands', 'Belgium', 'Austria', 'Switzerland', 'Sweden', 'Norway', 'Denmark', 'Finland', 'Portugal', 'Poland', 'Czech Republic', 'Brazil');

  /**
   * Initialize the plugin by setting localization, filters, and administration functions.
   *
   * @since     1.0.0
   */
  private function __construct() {
    $this->display_url = '//d.productwidgets'.(PW_DEV ? '.dev' : '.com');
    $this->plugin_url = trailingslashit(trailingslashit(plugins_url()).$this->plugin_slug);

    // Add admin menu
    add_action('admin_menu', array($this, 'add_admin_menu'));

    // Load admin styles and scripts
    add_action('admin_enqueue_scripts', array($this, 'enqueue_admin_styles_and_scripts'));

    // Add an action link to entry in plugin list
    $plugin_basename = plugin_basename(plugin_dir_path(__FILE__).$this->plugin_slug.'.php');
    add_filter('plugin_action_links_'.$plugin_basename, array($this, 'add_action_links'));

    // Replace widget shortcodes in the page content
    add_shortcode('productwidget', array($this, 'replace_widget_shortcode'));

    // Enable shortcodes in widgets
    add_filter('widget_text', 'do_shortcode');

    // Respond to Ajax calls from admin pages
    add_action('wp_ajax_get_amazon_tracking_ids', array($this, 'get_amazon_tracking_ids_callback'));
    add_action('wp_ajax_get_widgets',             array($this, 'get_widgets_callback'));
    add_action('wp_ajax_get_widget_layouts',      array($this, 'get_widget_layouts_callback'));
    add_action('wp_ajax_parse_widget_shortcode',  array($this, 'parse_widget_shortcode_callback'));

    // Initialize the API
    $this->api = new Api(array(
      'url'     => PW_DEV ? 'http://api.productwidgets.dev' : 'https://api.productwidgets.com',
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
    if (preg_match("/".$this->plugin_slug.'/i', $screen->id)) {
      wp_enqueue_style($this->plugin_slug.'-admin-styles', $this->plugin_url.'css/admin.css', array(), self::VERSION);
      wp_enqueue_script($this->plugin_slug.'-jquery-lazyload-script', $this->plugin_url.'js/jquery.lazyload.js', array('jquery'), self::VERSION, true);
      wp_enqueue_script($this->plugin_slug.'-admin-script', $this->plugin_url.'js/admin.js', array('jquery'), self::VERSION, true);
    }
  }

  /**
   * Register admin menu
   *
   * @since     1.0.0
   */
  public function add_admin_menu() {
    $api_key = get_option('api_key');
    $account_activated_at = get_option('account_activated_at');
    if (empty($api_key) || empty($account_activated_at)) {
      // Add top-level menu
      add_menu_page(
        $this->plugin_name,
        $this->plugin_name,
        'manage_options',
        $this->plugin_slug.'/signup.php',
        array($this, 'display_signup_page')
      );
    } else {
      // Add top-level menu
      add_menu_page(
        $this->plugin_name,
        $this->plugin_name,
        'manage_options',
        $this->plugin_slug.'/widgets.php',
        array($this, 'display_widgets_page')
      );

      // Add sub-level menu "Widgets"
      add_submenu_page(
        $this->plugin_slug.'/widgets.php',
        'Widgets',
        'Widgets',
        'manage_options',
        $this->plugin_slug.'/widgets.php',
        array($this, 'display_widgets_page')
      );

      // Add sub-level menu "Add Widget"
      add_submenu_page(
        $this->plugin_slug.'/widgets.php',
        'Add Widget',
        'Add Widget',
        'manage_options',
        $this->plugin_slug.'/add-widget.php',
        array($this, 'display_add_widget_page')
      );

      // Add sub-level menu "Settings"
      add_submenu_page(
        $this->plugin_slug.'/widgets.php',
        'Settings',
        'Settings',
        'manage_options',
        $this->plugin_slug.'/settings.php',
        array($this, 'display_settings_page')
      );

      // Add sub-level menu "Signup"
      // Make it a child of another submenu page,
      // so it's not displayed in the menu.
      add_submenu_page(
        $this->plugin_slug.'/add-widget.php',
        'Signup',
        'Signup',
        'manage_options',
        $this->plugin_slug.'/signup.php',
        array($this, 'display_signup_page')
      );
    }
  }

  /**
   * Render the signup page
   *
   * @since    1.0.0
   */
  public function display_signup_page() {
    if ($this->perform_checks())
      include_once('views/signup.php');
  }

  /**
   * Render the widgets page
   *
   * @since    1.0.0
   */
  public function display_widgets_page() {
    if ($this->perform_checks())
      include_once('views/widgets.php');
  }

  /**
   * Render the add widget page
   *
   * @since    1.0.0
   */
  public function display_add_widget_page() {
    if ($this->perform_checks())
      include_once('views/add-widget.php');
  }

  /**
   * Render the settings page
   *
   * @since    1.0.0
   */
  public function display_settings_page() {
    if ($this->perform_checks())
      include_once('views/settings.php');
  }

  /**
   * Perform checks
   *
   * @since    1.0.0
   */
  function perform_checks() {
    # Check if the user has sufficient permissions
    if (!current_user_can('manage_options'))
      wp_die(__('You do not have sufficient permissions to access this page.'));

    # Check if communication with the API works
    $api_error = $this->api->test_connection();
    if (!empty($api_error)) {
      include('views/api-error.php');
      return false;
    }

    # Check if account was deactivated
    $api_key = get_option('api_key');
    $account_activated_at = get_option('account_activated_at');
    if (!empty($api_key) && !empty($account_activated_at)) {
      $account = $this->api->get_account();
      if (!empty($account['deactivated_at'])) {
        include('views/account-deactivated.php');
        return false;
      }
    }

    return true;
  }

  /**
   * Add action link to the plugins page.
   *
   * @since    1.0.0
   */
  public function add_action_links($links) {
    $api_key = get_option('api_key');
    $account_activated_at = get_option('account_activated_at');
    if (empty($api_key) || empty($account_activated_at)) {
      $link = array(
        'signup' => '<a href="'.admin_url('admin.php?page='.$this->plugin_slug.'/signup.php').'">Signup</a>'
      );
    } else {
      $link = array(
        'widgets' => '<a href="'.admin_url('admin.php?page='.$this->plugin_slug.'/widgets.php').'">Widgets</a>'
      );
    }
    return array_merge(
      $link,
      $links
    );
  }

  /**
   * Fetches the tracking IDs from the API and send them as JSON.
   *
   * @since    1.0.0
   */
  function get_amazon_tracking_ids_callback() {
    try {
      $amazon_tracking_ids = $this->api->get_amazon_tracking_ids();
      wp_send_json($amazon_tracking_ids);
    } catch (Exception $e) {
      $error_message = "Could not load Amazon tracking IDs.";
      include("views/partials/_exception.php");
      die();
    }
  }

  /**
   * Renders the widgets partial.
   *
   * @since    1.0.0
   */
  function get_widgets_callback() {
    include("views/partials/widgets/_widgets.php");
    die();
  }

  /**
   * Fetches the widget layouts from the API and send them as JSON.
   *
   * @since    1.0.0
   */
  function get_widget_layouts_callback() {
    try {
      $widget_layouts = $this->api->get_widget_layouts();
      wp_send_json($widget_layouts);
    } catch (Exception $e) {
      $error_message = "Could not load widget layouts.";
      include("views/partials/_exception.php");
      die();
    }
  }

  function parse_widget_shortcode_callback() {
    $shortcode = stripslashes($_GET['shortcode']);
    echo do_shortcode($shortcode);
    die();
  }

  /**
   * Convert a widget shortcode to a Javascript tag.
   *
   * @since    1.0.0
   */
  public function replace_widget_shortcode($attributes) {
    extract(shortcode_atts(array(
      'layout'   => '',
      'keywords' => ''
    ), $attributes));
    return $this->generate_javascript_tag($layout, $keywords);
  }

  /**
   * Generate a Javascript tag from a layout and keywords.
   *
   * @since    1.0.0
   */
  public function generate_javascript_tag($layout, $keywords) {
    $api_key = get_option('api_key');
    $url = $this->display_url.'/'.$api_key.'/'.$layout.'/widget.js';
    return '<script src="'.$url.'" data-keywords="'.$keywords.'" type="text/javascript"></script>'."\n";
  }
}
