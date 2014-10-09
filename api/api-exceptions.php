<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      http://www.productwidgets.com/publishers/wordpress/
 * @copyright 2014 kraut computing UG (haftungsbeschränkt)
 */

/**
 * API Exceptions
 *
 * @package ProductWidgets
 * @author  Kraut Computing <info@krautcomputing.com>
 */

// 400 Bad Request
class BadRequestException extends Exception {
  public function __construct($error) {
    parent::__construct($error, E_USER_ERROR, null);
  }
}

// 401 Unauthorized
class UnauthorizedRequestException extends Exception {
  public function __construct($error) {
    parent::__construct($error, E_USER_ERROR, null);
  }
}

// 404 Not Found
class NotFoundException extends Exception {
  public function __construct($error) {
    parent::__construct($error, E_USER_ERROR, null);
  }
}

// 422 Unprocessable Entity
class UnprocessableEntityException extends Exception {
  public function __construct($error) {
    parent::__construct($error, E_USER_ERROR, null);
  }
}

// 500 Internal Server Errpr
class InternalServerErrorException extends Exception {
  public function __construct($error) {
    parent::__construct($error, E_USER_ERROR, null);
  }
}

class UnknownErrorException extends Exception {
  public function __construct($response) {
    if (is_wp_error($response)) {
      $message = "";
      $errors = $response->errors;
      foreach ($errors as $k => $v) {
        $message .= $k.": ".implode(",", $v).", ";
      }
      $message = rtrim($message, ", ");
    } else {
      $message = "Unknown error";
    }
    parent::__construct($message, E_USER_ERROR, null);
  }
}

class HttpRequestFailedException extends Exception {
  public function __construct($response, $url) {
    $message = "Could not connect to ";
    if (empty($url)) {
      $message .= "unknown URL";
    } else {
      $message .= $url;
    }
    $message .= ".";
    if (is_wp_error($response)) {
      $errors = $response->errors;
      foreach ($errors as $k => $v) {
        $message .= " ".$k.": ".implode(",", $v).",";
      }
      $message = rtrim($message, ",");
    }
    parent::__construct($message, E_USER_ERROR, null);
  }
}
