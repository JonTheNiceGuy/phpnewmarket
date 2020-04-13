<?php

require_once dirname(__FILE__) . '/classes/autoloader.php';
$arrUri = UI::getUri();
try {
  if (is_array($arrUri)
      and isset($arrUri['path_items'])
      and is_array($arrUri['path_items'])
      and count($arrUri['path_items']) > 0
      and strlen($arrUri['router_path']) > 0
  ) {
    switch($arrUri['path_items'][0]) {
    case 'join_game':
      if (isset($arrUri['path_items'][1])) {

      }
      break;
    case 'new_game':
      $id = Game::new();
      var_dump($id);
    }
  } else {
    UI::sendHttpResponseNote(400, "You specified no valid action. Please cease!");
  }
} catch(Exception $e) {
  error_log($e);
  UI::sendHttpResponseNote(500, "An error occurred - we are looking into it.");
}