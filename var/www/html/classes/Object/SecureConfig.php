<?php
/**
 * CampFire Manager is a scheduling tool predominently used at BarCamps to 
 * schedule talks based, mainly, on the number of people attending each talk
 * receives.
 *
 * PHP version 5
 *
 * @category Default
 * @package  CampFireManager2
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     https://github.com/CampFireManager/cfm2 Version Control Service
 */
/**
 * This class obtains manipulates all the configuration data for the service. It
 * handles local configuration (per-server), global configuration (per-site) and
 * secure configuration (api keys, password salts etc.)
 *
 * @category Object_SecureConfig
 * @package  CampFireManager2
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     https://github.com/CampFireManager/cfm2 Version Control Service
 */

class Object_SecureConfig extends Abstract_GenericObject
{
    // Generic Object Requirements
    protected $arrDBItems = array(
        'key' => array('type' => 'varchar', 'length' => 255),
        'value' => array('type' => 'text'),
        'lastChange' => array('type' => 'datetime')
    );
    protected $strDBTable = "secureconfig";
    protected $reqAdminToMod = true;
    // Local Object Requirements
    protected $key = null;
    protected $value = null;
    protected $isLocal = false;
    protected $lastChange = null;
    
    public static function brokerByID($key = 0)
    {
        $search = self::brokerByColumnSearch('key', $key);
        if (!is_array($search) || count($search) == 0) {
            return null;
        } else {
            return end($search);
        }
    }
    
    /**
     * This function overloads the parent write function - ensuring the value
     * is not locally stored first before performing the write action.
     * 
     * @return void
     */
    public function write()
    {
        if (! $this->isLocal) {
            parent::write();
        }
    }
    
    /**
     * This function overloads the parent create function - ensuring the value
     * is not locally stored first before performing the create action.
     * 
     * @return void
     */
    public function create()
    {
        if (! $this->isLocal) {
            parent::create();
        }
    }
    
    /**
     * This constructor permits the insertion of data into the object, for
     * locally stored (i.e. configuration rather than database) values.
     *
     * @param array $values The optional values to insert into the object
     * 
     * @return Object_SecureConfig
     */
    public function __construct($values = null)
    {
        if ($values != null && is_array($values) && count($values) > 0) {
            $this->isLocal = true;
            foreach ($values as $key => $value) {
                $this->setKey($key, $value);
            }
        } else {
            parent::__construct();
        }
    }
    
    /**
     * We do not want to return all the SecureConfig values!
     *
     * @return array 
     */
    public function getSelf()
    {
        return array();
    }

    public function writeChangeLog() {
        if ($this->change['value']['old'] != '') {
            $this->change['value']['old'] = 'A Non Null Value';
        } else {
            $this->change['value']['old'] = 'A Null Value';
        }
        if ($this->change['value']['new'] != '') {
            $this->change['value']['new'] = 'A Non Null Value';
        } else {
            $this->change['value']['new'] = 'A Null Value';
        }
        parent::writeChangeLog();
    }
}

/**
 * This class defines some default and demo data for the use in demos.
 * 
 * @category Object_SecureConfig
 * @package  CampFireManager2
 * @author   Jon Spriggs <jon@sprig.gs>
 * @license  http://www.gnu.org/licenses/agpl.html AGPLv3
 * @link     https://github.com/CampFireManager/cfm2 Version Control Service
 */
class Object_SecureConfig_Demo extends Object_SecureConfig
{
    protected $arrDemoData = array(
        array('key' => 'Dummy Key', 'value' => 'Set')
    );
    
    /**
     * This overriden function does nothing - a ChangeLog can't be changed - 
     * only read, and even then only at the Database Level.
     * 
     * @return void
     */
    public function writeChangeLog() {
        // Do Nothing!
    }
}
