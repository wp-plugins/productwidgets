<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */

/**
 * API class
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */
class Api extends Api_Request {
  public function __construct($config) {
    function _timeout($timeout) { return 30; }
    add_filter("http_request_timeout", "_timeout");

    parent::__construct($config);
  }

  public function test_connection() {
    $url = $this->build_url("", null, false);
    try {
      $this->get($url)->get_response();
    } catch (Exception $e) {
      $message = get_class($e);
      $error_message = $e->getMessage();
      if (!empty($error_message)) {
        $message .= ": ".$error_message;
      }
      return $message;
    }
    return "";
  }

  public function create_account($args) {
    $url = $this->build_url("account");
    $response = $this->post($url, $args)->get_response();
    return $response["results"];
  }

  public function get_account() {
    $api_key = get_option("api_key");
    $url = $this->build_url("account", array("api_key" => $api_key));
    $response = $this->get($url)->get_response();
    return $response["results"];
  }

  public function get_widget_layouts() {
    $api_key = get_option("api_key");
    $url = $this->build_url("widget_layouts", array("api_key" => $api_key));
    $response = $this->get($url)->get_response();
    return $response["results"];
  }

  public function get_amazon_tracking_ids() {
    $url = $this->amazon_tracking_ids_url();
    $response = $this->get($url)->get_response();
    return $response["results"]["configuration"]["tracking_ids"];
  }

  public function update_amazon_tracking_ids($amazon_tracking_ids) {
    $api_key = get_option("api_key");
    $url = $this->build_url("product_source_associations/amazon");
    $args = array("api_key" => $api_key, "configuration" => array("tracking_ids" => $amazon_tracking_ids));
    $response = $this->put($url, $args)->get_response();

    // Expire cached URL for tracking IDs
    $amazon_amazon_tracking_ids_url = $this->amazon_tracking_ids_url();
    $this->expire_cache($amazon_amazon_tracking_ids_url);

    return $response["results"]["configuration"]["tracking_ids"];
  }

  private function amazon_tracking_ids_url() {
    $api_key = get_option("api_key");
    return $this->build_url("product_source_associations/amazon", array("api_key" => $api_key));
  }
}
