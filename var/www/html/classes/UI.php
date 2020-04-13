<?php

class UI
{
  protected static $ui_handler = null;
  protected $arrUri = null;
  protected $arrLibs = null;

  protected static $http_status_codes = Array(
    100 => 'Continue',
    101 => 'Switching Protocols',
    200 => 'OK',
    201 => 'Created',
    202 => 'Accepted',
    203 => 'Non-Authoritative Information',
    204 => 'No Content',
    205 => 'Reset Content',
    206 => 'Partial Content',
    300 => 'Multiple Choices',
    301 => 'Moved Permanently',
    302 => 'Found',
    303 => 'See Other',
    304 => 'Not Modified',
    305 => 'Use Proxy',
    306 => '(Unused)',
    307 => 'Temporary Redirect',
    400 => 'Bad Request',
    401 => 'Unauthorized',
    402 => 'Payment Required',
    403 => 'Forbidden',
    404 => 'Not Found',
    405 => 'Method Not Allowed',
    406 => 'Not Acceptable',
    407 => 'Proxy Authentication Required',
    408 => 'Request Timeout',
    409 => 'Conflict',
    410 => 'Gone',
    411 => 'Length Required',
    412 => 'Precondition Failed',
    413 => 'Request Entity Too Large',
    414 => 'Request-URI Too Long',
    415 => 'Unsupported Media Type',
    416 => 'Requested Range Not Satisfiable',
    417 => 'Expectation Failed',
    500 => 'Internal Server Error',
    501 => 'Not Implemented',
    502 => 'Bad Gateway',
    503 => 'Service Unavailable',
    504 => 'Gateway Timeout',
    505 => 'HTTP Version Not Supported'
  );

  private static function getHandler() {
    if (self::$ui_handler == null) {
      self::$ui_handler = new UI();
    }
    return self::$ui_handler;
  }

  public static function getPath() {
    $handler = self::getHandler();
    if ($handler->arrUri != null) {
      return $handler->arrUri;
    }
    if (! isset($_SERVER['REQUEST_METHOD'])) {
      if (preg_match('/\/(.*)$/', $GLOBALS['argv'][0]) == 0) {
        $filename = trim(`pwd`) . '/' . $GLOBALS['argv'][0];
      } else {
        $filename = $GLOBALS['argv'][0];
      }
      $uri = 'file://' . $filename;
      unset($data[0]);
      $data = $GLOBALS['argv'];
    } else {
      $uri = "http";
      if (isset($_SERVER['HTTPS'])) {
        $uri .= 's';
      }
      $uri .= '://';
      list($username, $password) = self::getAuth();
      if ($username != null) {
        $uri .= "{$username}";
        if ($password != null) {
          $uri .= ":{$password}";
        }
        $uri .= '@';
      }
      $uri .= $_SERVER['SERVER_NAME'];
      if ((isset($_SERVER['HTTPS']) and $_SERVER['SERVER_PORT'] != 443) or ( ! isset($_SERVER['HTTPS']) 
        and $_SERVER['SERVER_PORT'] != 80)
      ) {
        $uri .= ':' . $_SERVER['SERVER_PORT'];
      }
      $uri .= $_SERVER['REQUEST_URI'];
      switch(strtolower($_SERVER['REQUEST_METHOD'])) {
      case 'get':
        $data = $_GET;
        break;
      case 'post':
        $data = $_POST;
        if (isset($_FILES) and is_array($_FILES)) {
          $data['_FILES'] = $_FILES;
        }
        break;
      case 'put':
        parse_str(file_get_contents('php://input'), $_PUT);
        $data = $_PUT;
        break;
      case 'delete':
      case 'head':
        $data = $_REQUEST;
      }
    }
    if (isset($data['__HTTP_AUTHORIZATION'])) {
      unset($data['__HTTP_AUTHORIZATION']);
    }
    $handler->arrUri = array($uri, $data);
    return array($uri, $data);
  }

  public static function getUri() {
    list($uri, $data) = self::getPath();
    $arrUrl = parse_url($uri);
    $arrUrl['full'] = $uri;
    $match = preg_match('/^([^\?]+)/', $arrUrl['full'], $matches);
    if ($match > 0) {
      $arrUrl['no_params'] = $matches[1];
    } else {
      $arrUrl['no_params'] = $arrUrl['full'];
    }
    $arrUrl['parameters'] = $data;
    if (substr($arrUrl['path'], -1) == '/') {
      $arrUrl['path'] = substr($arrUrl['path'], 0, -1);
    }
    $match = preg_match('/\/(.*)/', $arrUrl['path'], $matches);
    if ($match > 0) {
      $arrUrl['path'] = $matches[1];
    }
    $arrUrl['site_path'] = '';
    $arrUrl['router_path'] = $arrUrl['path'];
    if (isset($_SERVER['SCRIPT_NAME']) and isset($_SERVER['REQUEST_METHOD'])) {
      $path_elements = str_split($arrUrl['path']);
      $match = preg_match('%/(.*)$%', $_SERVER['SCRIPT_NAME'], $matches);
      $script_elements = str_split($matches[1]);
      $char = 0;
      while (isset($path_elements[$char]) and isset($script_elements[$char]) and $path_elements[$char] == $script_elements[$char]) {
        $char++;
      }
      $arrUrl['site_path'] = substr($arrUrl['path'], 0, $char);
      $arrUrl['router_path'] = substr($arrUrl['path'], $char);
    }
    if (substr($arrUrl['router_path'], 0, 1) == '/') {
      $arrUrl['router_path'] = substr($arrUrl['router_path'], 1);
    }
    $arrUrl['path_items'] = explode('/', $arrUrl['router_path']);
    $arrLastUrlItem = explode('.', $arrUrl['path_items'][count($arrUrl['path_items'])-1]);
    if (count($arrLastUrlItem) > 1) {
      $arrUrl['path_items'][count($arrUrl['path_items'])-1] = '';
      foreach ($arrLastUrlItem as $key=>$UrlItem) {
        if ($key + 1 == count($arrLastUrlItem)) {
          $arrUrl['format'] = $UrlItem;
        } else {
          if ($arrUrl['path_items'][count($arrUrl['path_items'])-1] != '') {
            $arrUrl['path_items'][count($arrUrl['path_items'])-1] .= '.';
          }
          $arrUrl['path_items'][count($arrUrl['path_items'])-1] .= $UrlItem;
        }
      }
    } else {
      $arrUrl['format'] = '';
    }
    $arrUrl['basePath'] = "{$arrUrl['scheme']}://{$arrUrl['host']}";
    if (isset($arrUrl['port']) and $arrUrl['port'] != '') {
      $arrUrl['basePath'] .= ':' . $arrUrl['port'];
    }
    if (isset($arrUrl['site_path']) and $arrUrl['site_path'] != '') {
      $arrUrl['basePath'] .= '/' . $arrUrl['site_path'];
    }
    $arrUrl['basePath'] .=  '/';
    if (isset($_SERVER['HTTP_USER_AGENT'])) {
      // Remember, this isn't guaranteed to be accurate
      $arrUrl['ua'] = $_SERVER['HTTP_USER_AGENT'];
    }
    var_dump($arrUrl);
    return $arrUrl;
  }

  public static function getAuth() {
    $isApache = function_exists('apache_get_version');
    $username = null;
    $password = null;
    if (isset($_SERVER['HTTP_AUTHORIZATION'])) { // If the server is passing an environment variable
      $auth_params = explode(":", base64_decode(substr($_SERVER['HTTP_AUTHORIZATION'], 6)));
      $username = $auth_params[0];
      unset($auth_params[0]);
      $password = implode('', $auth_params);
    } elseif (getenv('HTTP_AUTHORIZATION')) {
      $auth_params = explode(":", base64_decode(substr(getenv('HTTP_AUTHORIZATION'), 6)));
      $username = $auth_params[0];
      unset($auth_params[0]);
      $password = implode('', $auth_params);
    } elseif ($isApache && apache_getenv('HTTP_AUTHORIZATION')) {
      $auth_params = explode(":", base64_decode(substr(apache_getenv('HTTP_AUTHORIZATION'), 6)));
      $username = $auth_params[0];
      unset($auth_params[0]);
      $password = implode('', $auth_params);
    } elseif (isset($_REQUEST['__HTTP_AUTHORIZATION'])) { 
      // If we're having to pass it as a variable as part of the query.
      $auth_params = explode(":", base64_decode(substr($_REQUEST['__HTTP_AUTHORIZATION'], 6)));
      $username = $auth_params[0];
      unset($auth_params[0]);
      $password = implode('', $auth_params);
    } elseif (isset($_SERVER['PHP_AUTH_USER']) and isset($_SERVER['PHP_AUTH_PW'])) {
      $username = $_SERVER['PHP_AUTH_USER'];
      $password = $_SERVER['PHP_AUTH_PW'];
    }
    return array($username, $password);
  }

  public static function requireAuth() {
    list($username, $password) = self::getAuth();
    if ($username == null) {
      self::sendHttpResponse(401);
    }
  }

  public static function sendHttpResponse($status = 200, $body = null, $content_type = 'text/html', $extra = '') {
    header('HTTP/1.1 ' . $status . ' ' . self::$http_status_codes[$status]);
    header('Content-type: ' . $content_type);

    if ($body != '' and $body != null) {
      echo $body;
      exit;
    } elseif ($content_type != 'text/html') {
      // We can't send anything because it's not a valid response.
    } else {
      $message = '';
      switch($status) {
      case 204:
        $message = '';
        break;
      case 401:
        header('WWW-Authenticate: Basic realm="Authentication Required"');
        $message = 'You must be authorized to view this page.';
        break;
      case 404:
        list($uri, $data) = self::getPath();
        $message = 'The requested URL ' . $uri . ' was not found.';
        break;
      case 500:
        $message = 'The server encountered an error processing your request.';
        break;
      case 501:
        $message = 'The requested method is not implemented.';
        break;
      }

      if ($status != 204) {
        $message_content = "<p>{$message}</p>";
        if ($extra != '') {
          $message_content .= "\r\n  <p>$extra</p>";
        }
        $body = '<!DOCTYPE html><html><head><title>' . $status . ' ' . self::$http_status_codes[$status] . '</title></head><body><h1>' . self::$http_status_codes[$status] . '</h1>' . $message_content . '</body></html>';
        echo $body;
      }
      exit(0);
    }
  }

  function sendHttpResponseNote($status = 200, $extra = '') {
    static::sendHttpResponse($status, null, 'text/html', $extra);
  }

  function returnHttpResponseString($status = 200) {
    if (isset(self::$http_status_codes[$status])) {
      return self::$http_status_codes[$status];
    } else {
      return false;
    }
  }

  public static function Redirect($new_page = '') {
    $arrUri = self::getUri();
    if (substr($new_page, 0, 1) != '/') {
      $new_page = '/' . $new_page;
    }
    if (substr($arrUri['basePath'], -1) == '/') {
      $arrUri['basePath'] = substr($arrUri['basePath'], 0, -1);
    }
    $redirect_url = $arrUri['basePath'] . $new_page;
    header("Location: $redirect_url");
    exit(0);
  }

  public static function start_session() {
    if (session_id()==='') {
      // 604800 is 7 Days in seconds
      $currentCookieParams = session_get_cookie_params();
      session_set_cookie_params(
        604800, $currentCookieParams["path"], $currentCookieParams["domain"], $currentCookieParams["secure"], 
        $currentCookieParams["httponly"]
      );
      session_start();
      setcookie(
        session_name(), session_id(), time() + 604800, 
        $currentCookieParams["path"], $currentCookieParams["domain"]
      );
    }
  }
}