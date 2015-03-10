<?php
/**
 * @package   ProductWidgets
 * @author    Kraut Computing <info@krautcomputing.com>
 * @license   GPL-2.0
 * @link      https://www.productwidgets.com/
 * @copyright 2015 kraut computing UG (haftungsbeschränkt)
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
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($error, E_USER_ERROR, null);
    } else {
      parent::__construct($error, E_USER_ERROR);
    }
  }
}

// 401 Unauthorized
class UnauthorizedRequestException extends Exception {
  public function __construct($error) {
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($error, E_USER_ERROR, null);
    } else {
      parent::__construct($error, E_USER_ERROR);
    }
  }
}

// 404 Not Found
class NotFoundException extends Exception {
  public function __construct($error) {
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($error, E_USER_ERROR, null);
    } else {
      parent::__construct($error, E_USER_ERROR);
    }
  }
}

// 422 Unprocessable Entity
class UnprocessableEntityException extends Exception {
  public function __construct($error) {
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($error, E_USER_ERROR, null);
    } else {
      parent::__construct($error, E_USER_ERROR);
    }
  }
}

// 500 Internal Server Errpr
class InternalServerErrorException extends Exception {
  public function __construct($error) {
    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($error, E_USER_ERROR, null);
    } else {
      parent::__construct($error, E_USER_ERROR);
    }
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

    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($message, E_USER_ERROR, null);
    } else {
      parent::__construct($message, E_USER_ERROR);
    }
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

    if (version_compare(PHP_VERSION, '5.3.0') >= 0) {
      parent::__construct($message, E_USER_ERROR, null);
    } else {
      parent::__construct($message, E_USER_ERROR);
    }
  }
}
