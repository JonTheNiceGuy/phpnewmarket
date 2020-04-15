<?php

class Object_Game extends Abstract_GenericObject
{
  protected $arrDBItems = array(
    'strGameName' => array('type' => 'varchar', 'length' => 255),
    'intDeckPlay' => array('type' => 'int', 'length' => 11),
    'intDeckBet'  => array('type' => 'int', 'length' => 11),
    'jsonPlayers' => array('type' => 'text'),
    'lastChange'  => array('type' => 'datetime')
  );
  protected $arrTranslations = array(
    'label_strGameName' => array('en' => 'Name of the Game')
  );
  protected $strDBTable      = "game";
  protected $strDBKeyCol     = "intGameID";
  protected $reqCreatorToMod = true;
  // Local Object Requirements
  protected $intGameID       = null;
  protected $strGameName     = 'Your Game';
  protected $intDeckPlay     = null;
  protected $intDeckBet      = null;
  protected $jsonPlayers     = null;
  protected $lastChange      = null;
}

class Object_Game_Demo extends Object_Game
{
    protected $arrDemoData = array(
        array('intGameID' => 1, 'strGameName' => 'Your Game')
    );
}