<?php

class Cards {
  protected $deck = [];

  public function __construct() {
    $this->deck = [
      1 => new card("J","R"),  2 => new card("J","B"),                                              # JOKERS
      101 => new card("♥","A"),102 => new card("♥","2"),103 => new card("♥","3"),104 => new card("♥","4"),105 => new card("♥","5"),106 => new card("♥","6"),107 => new card("♥","7"),108 => new card("♥","8"),109 => new card("♥","9"),110 => new card("♥","10"),111 => new card("♥","J"),112 => new card("♥","Q"),113 => new card("♥","K"), # HEARTS
      201 => new card("♣","A"),202 => new card("♣","2"),203 => new card("♣","3"),204 => new card("♣","4"),205 => new card("♣","5"),206 => new card("♣","6"),207 => new card("♣","7"),208 => new card("♣","8"),209 => new card("♣","9"),210 => new card("♣","10"),211 => new card("♣","J"),212 => new card("♣","Q"),213 => new card("♣","K"), # CLUBS
      301 => new card("♦","A"),302 => new card("♦","2"),303 => new card("♦","3"),304 => new card("♦","4"),305 => new card("♦","5"),306 => new card("♦","6"),307 => new card("♦","7"),308 => new card("♦","8"),309 => new card("♦","9"),310 => new card("♦","10"),311 => new card("♦","J"),312 => new card("♦","Q"),313 => new card("♦","K"), # DIAMONDS
      401 => new card("♠","A"),402 => new card("♠","2"),403 => new card("♠","3"),404 => new card("♠","4"),405 => new card("♠","5"),406 => new card("♠","6"),407 => new card("♠","7"),408 => new card("♠","8"),409 => new card("♠","9"),410 => new card("♠","10"),411 => new card("♠","J"),412 => new card("♠","Q"),413 => new card("♠","K") # SPADES
    ];
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