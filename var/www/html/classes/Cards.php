<?php

class Cards {
  protected $deck = [];

  public function __construct() {
    $this->deck = [];
    $this->deck[1] = new card(1);
    $this->deck[2] = new card(2);
    for ($suit = 100; $suit <= 400; $suit = $suit+100) {
      for ($card = 1; $card <= 13; $card++) {
        $this->deck[$suit+$card] = new card($suit+$card);
      }
    }
  }

  public function show_top_card() {
    $keys = array_keys($this->deck);
    $return_key = null;
    $return_value = null;
    foreach($keys as $key) {
      if ($return_key === null) {
        $return_key = $key;
        $return_value = $this->deck[$key];
      }
    }
    return [$return_key, $return_value];
  }

  public function draw() {
    list($return_key, $return_value) = $this->show_top_card();
    $this->remove_card($return_key);
    return [$return_key, $return_value];
  }

  public function remove_card($card) {
    if (is_integer($card)) {
      $return_value = $this->deck[$card];
      $return_key = $card;
      unset($this->deck[$card]);
      return [$return_key, $return_value];      
    } else {
      foreach($this->deck as $key=>$value) {
        if($value->get_name() == $card) {
          return $this->remove_card($key);
        }
      }
    }
  }

  public function shuffle() {
    $keys = array_keys($this->deck);

    shuffle($keys);

    foreach($keys as $key) {
        $new[$key] = $this->deck[$key];
    }

    $this->deck = $new;
  }

  public function get_deck() {
    return $this->deck;
  }

  public function remove_jokers() {
    $this->remove_card(1);
    $this->remove_card(2);
  }
}