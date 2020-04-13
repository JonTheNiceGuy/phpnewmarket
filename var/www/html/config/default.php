<?php

$RW_TYPE = 'mysql';
$RW_HOST = '127.0.0.1';
$RW_PORT = '3306';
$RW_BASE = 'db';
$RW_USER = 'db';
$RW_PASS = 'db';

date_default_timezone_set("UTC");

if (file_exists(dirname(__FILE__) . "/LOCAL_CONFIG.php")) {
  include dirname(__FILE__) . "/LOCAL_CONFIG.php";
}

if (!isset($RW_DSN)) {
  $RW_DSN = array(
    'string' => "$RW_TYPE:host=$RW_HOST;port=$RW_PORT;dbname=$RW_BASE",
    'user' => $RW_USER,
    'pass' => $RW_PASS
  );
}