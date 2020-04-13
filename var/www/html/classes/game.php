<?php

class Game {
  protected function new_game_id(PDO $db) {
    var_dump($db);
    $now = microtime(true);
    $id = substr(sha1($now), 0, 4);
    $sql = "SELECT count(ID) as c_ID FROM game WHERE ID = ?";
    $query = $db->prepare($sql);
    $query->execute(array($id));
    if ($query->errorCode() != 0) {
      throw new Exception('SQL Error', 1);
    }
    if($query->fetch(PDO::FETCH_ASSOC) == 0) {
      $sql = "INSERT INTO game (ID) VALUES (?)";
      $query = $db->prepare($sql);
      $query->execute(array($id));
      if ($query->errorCode() != 0) {
        throw new Exception('SQL Error: ' . $query->errorInfo(), 1);
      }
      return $id;
    } else {
      return self::new_game_id($db);
    }
  }
  public static function new() {
    $db = database::getConnection();
    return self::new_game_id($db);
  }

  public function join($id = null) {
    if (isset($id)) {

    } else {
      throw new Exception('No game ID');
    }
  }
}