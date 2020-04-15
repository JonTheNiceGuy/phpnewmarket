<?php

class Object_Deck extends Abstract_GenericObject
{
  protected $arrDBItems = array(
    'strDeckName' => array('type' => 'varchar', 'length' => 255),
    'jsonDeck'    => array('type' => 'text', 'array' => 1),
    'lastChange'  => array('type' => 'datetime')
  );
  protected $arrTranslations = array(
    'label_strDeckName' => array('en' => 'Name of this Deck')
  );
  protected $strDBTable      = "deck";
  protected $strDBKeyCol     = "intDeckID";
  protected $reqCreatorToMod = true;
  // Local Object Requirements
  protected $intDeckID       = null;
  protected $strDeckName     = 'Deck Name';
  protected $jsonDeck        = null;
  protected $lastChange      = null;
}

class Object_Deck_Demo extends Object_Deck
{
    protected $arrDemoData = array(
        array('intDeckID' => 1, 'strDeckName' => 'Play Deck, Game 1', 'jsonDeck' => "[101,102,103,104,105,106,107,108,109,110,111,112,113,201,202,203,204,205,206,207,208,209,210,211,212,213,301,302,303,304,305,306,307,308,309,310,311,312,313,401,402,403,404,405,406,407,408,409,410,411,412,413]"),
        array('intDeckID' => 2, 'strDeckName' => 'Bid Deck, Game 1', 'jsonDeck' => "[102,103,104,105,106,107,108,109,110,111,113,201,202,203,204,205,206,207,208,209,210,211,212,301,302,303,304,305,306,307,308,309,310,312,313,402,403,404,405,406,407,408,409,410,411,412,413]"
        )
    );
}