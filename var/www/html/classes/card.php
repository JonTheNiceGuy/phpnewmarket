<?php

class Card {
  protected $suit = null;
  protected $face = null;
  public function __construct($suit, $face) {
    $this->suit = $suit;
    $this->face = $face;
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
