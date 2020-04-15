<?php

class Card {
  protected $suit = null;
  protected $face = null;
  protected $card_def = [
    # JOKERS
      1 => ["J", "R"],  2 => ["J", "B"],
    # HEARTS
    101 => ["♥", "A"],102 => ["♥", "2"],103 => ["♥", "3"],104 => ["♥", "4"],105 => ["♥", "5"],106 => ["♥", "6"],107 => ["♥", "7"],108 => ["♥", "8"],109 => ["♥", "9"],110 => ["♥", "10"],111 => ["♥", "J"],112 => ["♥", "Q"],113 => ["♥", "K"],
    # CLUBS
    201 => ["♣", "A"],202 => ["♣", "2"],203 => ["♣", "3"],204 => ["♣", "4"],205 => ["♣", "5"],206 => ["♣", "6"],207 => ["♣", "7"],208 => ["♣", "8"],209 => ["♣", "9"],210 => ["♣", "10"],211 => ["♣", "J"],212 => ["♣", "Q"],213 => ["♣", "K"],
    # DIAMONDS
    301 => ["♦", "A"],302 => ["♦", "2"],303 => ["♦", "3"],304 => ["♦", "4"],305 => ["♦", "5"],306 => ["♦", "6"],307 => ["♦", "7"],308 => ["♦", "8"],309 => ["♦", "9"],310 => ["♦", "10"],311 => ["♦", "J"],312 => ["♦", "Q"],313 => ["♦", "K"],
    # SPADES
    401 => ["♠", "A"],402 => ["♠", "2"],403 => ["♠", "3"],404 => ["♠", "4"],405 => ["♠", "5"],406 => ["♠", "6"],407 => ["♠", "7"],408 => ["♠", "8"],409 => ["♠", "9"],410 => ["♠", "10"],411 => ["♠", "J"],412 => ["♠", "Q"],413 => ["♠", "K"]
  ];

  public function __construct($cardid) {
    if (isset($this->card_def[$cardid])) {
      list($suit, $face) = $this->card_def[$cardid];
      $this->suit = $suit;
      $this->face = $face;
    } else {
      throw new Exception("Missing CardID");
    }
  }

  public function __toString() {
    return $this->get_long_name();
  }

  public function get_suit() {
    return $this->suit;
  }

  public function get_colour() {
    switch ($this->suit) {
      case '♣':
      case '♠':
        return "Black";
      break;
      case '♦':
      case '♥':
        return "Red";
      break;
      case 'J':
        return $this->get_long_face();
      break;
    }
  }

  public function get_long_suit() {
    switch ($this->suit) {
      case '♣':
        return "Clubs";
      break;
      case '♦':
        return "Diamonds";
      break;
      case '♠':
        return "Spades";
      break;
      case '♥':
        return "Hearts";
      break;
      case 'J':
        return "Joker";
      break;
    }
  }

  public function get_face() {
    return $this->face;
  }

  public function get_long_face() {
    switch ($this->face) {
      case 'A':
        return "Ace";
      break;
      case 'K':
        return "King";
      break;
      case 'Q':
        return "Queen";
      break;
      case 'J':
        return "Jack";
      break;
      case 'R':
        return "Red";
      break;
      case 'B':
        return "Black";
      break;
      case '10':
        return "Ten";
      break;
      case '9':
        return "Nine";
      break;
      case '8':
        return "Eight";
      break;
      case '7':
        return "Seven";
      break;
      case '6':
        return "Six";
      break;
      case '5':
        return "Five";
      break;
      case '4':
        return "Four";
      break;
      case '3':
        return "Three";
      break;
      case '2':
        return "Two";
      break;
      default:
        return "" . $this->face;
    }
  }

  public function get_name() {
    return $this->suit . $this->face;
  }

  public function get_long_name() {
    if($this->suit == 'J') {
      return $this->get_long_face() . " " . $this->get_long_suit();
    } else {
      return $this->get_long_face() . " of " . $this->get_long_suit();
    }
  }
}
