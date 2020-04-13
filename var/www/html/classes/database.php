<?php

class database {
  protected static $handler = null;
  protected $rw_db = null;

  private static function getHandler()
  {
      if (self::$handler == null) {
          self::$handler = new self();
      }
      return self::$handler;
  }

  public static function getConnection()
  {
    $self = self::getHandler();
    if ($self->rw_db != null) {
      return $self->rw_db;
    } else {
      include dirname(__FILE__) . '/../config/default.php';
      try {
        $self->rw_db = new PDO(
          $RW_DSN['string'], $RW_DSN['user'], $RW_DSN['pass'], array(
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8"
          )
        );
        return $self->rw_db;
      } catch (Exception $e) {
        echo "Error connecting: " . $e->getMessage();
        die();
      }
    }
  }
}