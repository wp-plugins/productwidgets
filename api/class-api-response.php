<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */

/**
 * API Response class
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */
class Api_Response implements ArrayAccess, Iterator {
  private $mHttpCode;
  private $mResponse;
  private $mHeaders;
  private $mError = false;

  public function __toString() {
    return "HTTP Code: " . $this->mHttpCode . "<br/>Is Error: " . $this->mError . "<br/>Response: " . $this->mResponse;
  }

  /**
   * Sets the HTTP response code from the API call
   * @param Integer $code
   */
  public function set_http_code($code) {
    $this->mHttpCode = $code;

    // If the response was not a 200 there was an error. Set the error code
    if ('200' != $code) {
      $this->mError = true;
    }
  }

  /**
   * Returns the HTTP response code from the API query
   * @return Integer
   */
  public function get_http_code() {
    return $this->mHttpCode;
  }

  // Must be an array
  public function set_headers($headers) {
    $this->mHeaders = $headers;
  }

  public function get_header($header) {
    return (isset($this->mHeaders[$header])) ?$this->mHeaders[$header]: null;
  }

  public function get_headers() {
    return $this->mHeaders;
  }

  public function is_cachable() {
    // If header is not set then allow it
    // pragma - cache-control(explode)
    if ($this->get_header('pragma') == 'no-cache') {
      // pragma no-cache is set
      return false;
    }

    $arr = explode(',', $this->get_header('cache-control'));
    foreach ($arr AS $k => $v)
      $arr[$k] = trim($v);
    if (in_array('no-cache', $arr)) {
      // cache-control no-cache is set
      return false;
    }

    return true; // By default it is cachable
  }

  public function is_expired() {
    // If header is not set then always expired?
    if (!is_null($this->get_header('expires'))) {
      $expires = strtotime($this->get_header('expires'));
      return (time() > $expires);
    }
    return true; // By default assume expired
  }

  /**
   * Brings in the response from the API call and converts it to a stdClass
   *
   * @param JSON $Response
   */
  public function set_response($Response) {
    $this->mResponse = json_decode(preg_replace('/[\x00-\x1F\x80-\xFF]/', '',$Response), true);
  }

  public function get_response() {
    return $this->mResponse;
  }

  /**
   * Check if there was an error with the API query
   *
   * @return Boolean
   */
  public function is_error() {
    return $this->mError;
  }


  /*
   * ************************************************************************
   *  Below this point are overloaded PHP functions to enable arraylike access
   *  You should not need to change anything beyond this line
   * ************************************************************************
   */

  /*
   * Array Access methods
   * Used directly from PHP docs
   * http://php.net/manual/en/class.arrayaccess.php
   */
  public function offsetSet($offset, $value) {
    if (is_null($offset)) {
      $this->mResponse[] = $value;
    } else {
      $this->mResponse[$offset] = $value;
    }
  }
  public function offsetExists($offset) {
    return isset($this->mResponse[$offset]);
  }
  public function offsetUnset($offset) {
    unset($this->mResponse[$offset]);
  }
  public function offsetGet($offset) {
    return isset($this->mResponse[$offset]) ? $this->mResponse[$offset] : null;
  }

  /*
   * Iterator methods
   * Used directly from PHP docs
   * http://php.net/manual/en/language.oop5.iterations.php
   */
  public function rewind() {
    reset($this->mResponse);
  }

  public function current() {
    return current($this->mResponse);
  }

  public function key() {
    return key($this->mResponse);
  }

  public function next() {
    return next($this->mResponse);
  }

  public function valid() {
    $key = key($this->mResponse);
    return ($key !== NULL && $key !== FALSE);
  }
}
