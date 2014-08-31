<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      http://www.productwidgets.com/publishers/wordpress/
 * @copyright 2014 kraut computing UG (haftungsbeschränkt)
 */

/**
 * API class
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */
class Api extends Api_Request {
  public function __construct($config) {
    add_filter("http_request_timeout", function($timeout) { return 30; });

    parent::__construct($config);
  }

  public function test_connection() {
    $url = $this->build_url();
    try {
      $this->head($url)->get_response();
    } catch (HttpRequestFailedException $e) {
      return $e->getMessage();
    } catch (Exception $e) {}
    return "";
  }

  public function signup() {
    $url = $this->build_url("account");
    $args = array("product_source_name" => "amazon");
    $response = $this->post($url, $args)->get_response();
    return $response["results"];
  }

  // API Key must be passed into function because it's not yet saved as option.
  public function get_account($api_key) {
    $url = $this->build_url("account", array("api_key" => $api_key));
    $response = $this->get($url)->get_response();
    return $response["results"];
  }

  public function get_tracking_ids() {
    $api_key = get_option("api_key");
    $url = $this->build_url("product_source_associations/amazon", array("api_key" => $api_key));
    $response = $this->get($url)->get_response();
    return $response["results"]["configuration"]["tracking_ids"];
  }

  public function update_tracking_ids($tracking_ids) {
    $api_key = get_option("api_key");
    $url = $this->build_url("product_source_associations/amazon");
    $args = array("api_key" => $api_key, "configuration" => array("tracking_ids" => $tracking_ids));
    $response = $this->put($url, $args)->get_response();
    return $response["results"]["configuration"]["tracking_ids"];
  }

  public function get_widget_layouts() {
    $api_key = get_option("api_key");
    $url = $this->build_url("widget_layouts", array("api_key" => $api_key));
    $response = $this->get($url)->get_response();
    return $response["results"];
  }
}
