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
    $url = $this->build_url();
    try {
      $this->head($url)->get_response();
    } catch (HttpRequestFailedException $e) {
      return $e->getMessage();
    } catch (Exception $e) {}
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
}
