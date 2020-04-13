<?php

require_once dirname(__FILE__) . '/classes/autoloader.php';
$cards = new Cards();
$cards->remove_jokers();
$bid_cards = [];
list($card_id, $bid_card) = $cards->remove_card("♠A");
$bid_cards[] = $bid_card;
list($card_id, $bid_card) = $cards->remove_card("♦J");
$bid_cards[] = $bid_card;
list($card_id, $bid_card) = $cards->remove_card("♥Q");
$bid_cards[] = $bid_card;
list($card_id, $bid_card) = $cards->remove_card("♣K");
$bid_cards[] = $bid_card;
$cards->shuffle();
list($card_id, $bid_card) = $cards->draw();
$bid_cards[] = $bid_card;
foreach($bid_cards as $card) {
  echo $card->get_name() . " ";
}
echo PHP_EOL;
echo "==========================" . PHP_EOL;
foreach($cards->get_deck() as $card_id=>$card) {
  echo $card->get_name() . " ";
}
echo PHP_EOL;