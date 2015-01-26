<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
 */

/**
 * API Request class
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */
class Api_Request {
  private $config = array();

  /**
   * Default constructor
   */
  public function __construct($config) {
    $required_config = array('url');
    $default_config = array(
      'response_type'    => 'json',
      'cache_enabled'    => !PW_DEV,
      'cache_lifetime'   => 60 * 60,
      'cache_prefix'     => 'pw_', # Cache key should not be more than 40 chars in total
      'check_for_update' => true
    );
    $config = wp_parse_args($config, $default_config);

    $this->set_config($config);
  }

  /**
   * Performs a GET query against the specified url
   *
   * @todo Add conditional code to fetch header data first
   * @throws UnkownErrorException
   * @throws BadRequestException
   * @throws UnauthorizedRequestException
   * @throws InternalServerErrorException
   * @param String $url - Endpoint with URL paramters (for now)
   * @return Api_Response
   */
  public function get($url) {
    /*
     * Several things to check before we go fetch the data
     *
     * - Is caching enabled?
     * --- Yes? Check to see if the transient is still valid
     * ------ Yes? Set it as the response
     * --------- Is check_for_update? (HTTP HEAD)
     * ------------ Boolean: fetch_url = false
     * ------------ Yes? Check HTTP HEAD
     * --------------- If timestamp newer than transient
     * ------------------ fetch_url = true
     * --------- If fetch_url
     * ------------ Go fetch!, Set transient
     * ------ No? Go fetch!, Set transient
     * --- No? Go fetch!
     * - Return response
     *
     * To make this easier to read some of the conditionals may be broken
     * apart in a not as efficient manner as possible
     */
    if ($this->get_config('cache_enabled')) {
      $transient_name = $this->transient_name($url);

      // Grab the cached data
      $transient = get_transient($transient_name);

      if (!$transient) {
        // Cached data is no longer valid, refresh it
        $response = $this->perform_get($url);
        if ($response->is_cachable())
          set_transient($transient_name, $response, $this->get_config('cache_lifetime'));
      } else {
        // Cached data is valid
        // Do we need to check the HTTP HEAD data?
        if ($this->get_config('check_for_update')) {
          // Need to check HTTP HEAD to see if there are any updates
          // or if the expires header has been exceeded
          $head = $this->perform_head($url);

          if ($head->is_expired()) {
            // Cache is expired. Grab the data again.
            $response = $this->perform_get($url);

            if ($response->is_cachable()) {
              // Response is cachable (HTTP HEAD)
              set_transient($transient_name, $response, $this->get_config('cache_lifetime'));
            }
          } else {
            $response = $transient;
          }
        } else {
          $response = $transient;
        }
      }
    } else {
      // Caching is not enabled. Always perform the GET
      $response = $this->perform_get($url);
    }

    return $response;
  }

  /**
   * Performs a POST query against the specified url
   *
   */
  public function post($url, $args) {
    $response = $this->perform_post($url, $args);
    return $response;
  }

  /**
   * Performs a HEAD query against the specified url
   *
   */
  public function head($url) {
    $response = $this->perform_head($url);
    return $response;
  }

  /**
   * Performs a POST query against the specified url with a parameter "_method" set to "PUT"
   *
   */
  public function put($url, $args) {
    $args["_method"] = "PUT";
    $response = $this->perform_post($url, $args);
    return $response;
  }

  public function expire_cache($url) {
    if ($this->get_config('cache_enabled')) {
      $transient_name = $this->transient_name($url);
      delete_transient($transient_name);
    }
  }

  private function transient_name($name) {
    return $this->get_config('cache_prefix').md5($name);
  }

  private function handle_response_error($response, $url) {
    if (count($response->errors) == 1) {
      $errors = array_keys($response->errors);
      if ($errors[0] == 'http_request_failed')
        throw new HttpRequestFailedException($response, $url);
    }
    throw new UnknownErrorException($response);
  }

  private function api_response($response) {
    $api_response = new Api_Response();
    $api_response->set_http_code(wp_remote_retrieve_response_code($response));
    $api_response->set_headers(wp_remote_retrieve_headers($response));
    $api_response->set_response(wp_remote_retrieve_body($response));

    if ($api_response->get_http_code() != '200') {
      $response_content = $api_response->get_response();
      if (!is_null($response_content) && array_key_exists("error_description", $response_content)) {
        $error = $response_content["error_description"];
      } else {
        $error = $response_content;
      }
      switch ($api_response->get_http_code()) {
        case '400':
          throw new BadRequestException($error);
        case '401':
          throw new UnauthorizedRequestException($error);
        case '404':
          throw new NotFoundException($error);
        case '422':
          throw new UnprocessableEntityException($error);
        case '500':
          throw new InternalServerErrorException($error);
        default:
          throw new UnknownErrorException($response);
      }
    }

    return $api_response;
  }

  private function perform_get($url) {
    $response = wp_remote_get($url);
    if (is_wp_error($response)) {
      $this->handle_response_error($response, $url);
      die();
    } else {
      $api_response = $this->api_response($response);
      return $api_response;
    }
  }

  private function perform_post($url, $args) {
    $response = wp_remote_post($url, array("body" => $args));
    if (is_wp_error($response)) {
      $this->handle_response_error($response, $url);
      die();
    } else {
      $api_response = $this->api_response($response);
      return $api_response;
    }
  }

  private function perform_head($url) {
    $response = wp_remote_head($url);
    if (is_wp_error($response)) {
      $this->handle_response_error($response, $url);
      die();
    } else {
      $api_response = $this->api_response($response);
      return $api_response;
    }
  }

  public function build_url($endpoint = null, $parameters = null, $include_version = true) {
    $url = trailingslashit($this->get_config('url'));
    if ($include_version && !is_null($this->get_config('version')))
      $url .= trailingslashit('v'.$this->get_config('version'));
    $url .= $endpoint;

    if (!is_null($parameters))
      $url = add_query_arg($parameters, $url);

    return $url;
  }

  public function get_config($var) {
    return (isset($this->config[$var])) ? $this->config[$var] : null;
  }

  public function set_config($var, $value = null) {
    if (is_array($var)) {
      foreach($var as $k => $v) {
        $this->set_config($k, $v);
      }
    } else {
      $this->config[$var] = $value;
    }
  }
}
