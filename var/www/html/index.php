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
 * This file defines the autoloader for the classes mentioned elsewhere.
 */
require_once dirname(__FILE__) . '/classes/autoloader.php';
Base_Response::setGenerationTime();
Container_Config::LoadConfig();

$objRequest = Container_Request::getRequest();
$arrMediaType = explode('/', $objRequest->get_strPrefAcceptType());

if (Base_GeneralFunctions::getValue($objRequest->get_arrRqstParameters(), 'logout', false, false) != false) {
    Object_User::logout();
    Base_Response::redirectTo('welcome?logout');
}

// What type of request is this
$rest = false;
$media = false;

if (is_array($objRequest->get_arrPathItems()) && count($objRequest->get_arrPathItems()) > 0) {
    $arrPathItems = $objRequest->get_arrPathItems();
    if ($arrPathItems[0] == 'SETUP' && $arrPathItems[1] == 'install') {
        Base_Response::sendHttpResponse(200, 'Done', $objRequest->get_strPrefAcceptType());
    } elseif ($arrPathItems[0] == 'media') {
        unset($arrPathItems[0]);
        $tmpPathItems = array();
        foreach ($arrPathItems as $data) {
            $tmpPathItems[] = $data;
        }
        $arrPathItems = $tmpPathItems;
        $media = $objRequest->hasMediaType('media');
        if (! $media) {
            Base_Response::sendHttpResponse(404, null, $objRequest->get_strPrefAcceptType());
        }
        $file = Container_Config::brokerByID('strMediaPath', dirname(__FILE__) . '/Media')->getKey('value');
        foreach ($arrPathItems as $key => $pathItem) {
            if ($pathItem == '..') {
                Base_Response::sendHttpResponse(403, null, $objRequest->get_strPrefAcceptType());
            }
            $file .= '/' . $pathItem;
        }
        if ($objRequest->get_strPathFormat() != '') {
            $file .= '.' . $objRequest->get_strPathFormat();
        }
        if (is_file($file)) {
            Base_Response::sendResumableFile($file, TRUE, $objRequest->get_strPrefAcceptType());
        } else {
            Base_Response::sendHttpResponse(404, null, $objRequest->get_strPrefAcceptType());
        }
    }
    if ($arrPathItems[0] == 'rest' && $objRequest->hasMediaType('rest')) {
        unset($arrPathItems[0]);
        $tmpPathItems = array();
        foreach ($arrPathItems as $data) {
            $tmpPathItems[] = $data;
        }
        $arrPathItems = $tmpPathItems;
        $rest = true;
    }
}

// What type of objects can we request
$arrValidObjects = array('config' => 'Base_Config');

foreach (new DirectoryIterator(dirname(__FILE__) . '/classes/Object') as $file) {
    if ($file->isDir() || $file->isDot()) continue;
    if ($file->isFile() && ($file->getBasename('.php') != $file->getBasename())) {
        $arrValidObjects[strtolower($file->getBasename('.php'))] = 'Object_' . $file->getBasename('.php');
    }
}

foreach (new DirectoryIterator(dirname(__FILE__) . '/classes/Collection') as $file) {
    if ($file->isDir() || $file->isDot()) continue;
    if ($file->isFile() && ($file->getBasename('.php') != $file->getBasename())) {
        $arrValidObjects[strtolower($file->getBasename('.php'))] = 'Collection_' . $file->getBasename('.php');
    }
}

/**
 * A value which stores the last processed object type
 * @var string
 */
$lastObject = null;
/**
 * An array of objects requested
 * @var array
 */
$useObjects = array();
/**
 * An array of the processed requested objects
 * @var array
 */
$arrObjects = array();
/**
 * An array containing the values from the requested objects
 * @var array
 */
$arrObjectsData = array();
/**
 * Load the template of the last object type requested. This value stores what
 * that was.
 * @var string
 */
$renderPage = null;

$objRequest = Container_Request::getRequest();
$arrObjectsData['SiteConfig']['baseurl'] = $objRequest->get_strBasePath();
$arrObjectsData['SiteConfig']['thisurl'] = $objRequest->get_requestUrlExParams();
try {
    $arrObjects['Object_User']['current'] = Object_User::brokerCurrent();
} catch (Exception_AuthenticationFailed $e) {
    $_SESSION['authentication_failure'] = $e->getMessage();
} catch (Exception $e) {
    error_log("Unable to authenticate due to error: " . $e->getMessage());
    Base_Response::sendHttpResponse(500);
}

if (is_array($arrPathItems) && count($arrPathItems) > 0 && $arrPathItems[0] != '') {
    foreach ($arrPathItems as $pathItem) {
        if (isset($arrValidObjects[$pathItem])) {
            if ($renderPage == null) {
                $useObjects[$arrValidObjects[$pathItem]] = null;
                $lastObject = $pathItem;
                $renderPage = $arrValidObjects[$pathItem];
            }
        } elseif ($lastObject != null) {
            $useObjects[$arrValidObjects[$lastObject]] = $pathItem;
            $lastObject = null;
        } else {
            $lastObject = null;
        }
    }

    foreach ($useObjects as $object => $item) {
        if ($item == null || $item == 'new' || $item == 'me') {
            switch ($objRequest->get_strRequestMethod()) {
            case 'head':
            case 'get':
                if ($item == null) {
                    $arrObjects[$object] = $object::brokerAll();
                } elseif ($item == 'me') {
                    if ($arrObjects['Object_User']['current'] != false
                        && $arrObjects['Object_User']['current'] != null
                    ) {
                        $arrObjects[$object] = $object::brokerByColumnSearch('intUserID', $arrObjects['Object_User']['current']->getKey('intUserID'));
                        if ($object == 'Object_User') {
                            $arrObjects['Object_User']['current'] = Object_User::brokerCurrent();
                        }
                    } else {
                        if ($rest) {
                            Base_Response::sendHttpResponse(404);
                        } else {
                            Base_Response::redirectTo('welcome?me');
                        }
                    }
                } else {
                    if ($arrObjects['Object_User']['current'] != false
                        && $arrObjects['Object_User']['current'] != null
                    ) {
                        $arrObjects['renderPage'][] = $object::dataForNewPage();
                        $renderPage = 'new_' . $object;
                    } else {
                        if ($rest) {
                            Base_Response::sendHttpResponse(404);
                        } else {
                            Base_Response::redirectTo('welcome?get');
                        }
                    }
                }
                break;
            case 'post':
            case 'put':
                if ($arrObjects['Object_User']['current'] == false
                    || $arrObjects['Object_User']['current'] == null
                ) {
                    if ($rest) {
                        Base_Response::sendHttpResponse(404);
                    } else {
                        Base_Response::redirectTo('welcome?put');
                    }
                }
                $newobject = new $object();
                foreach ($objRequest->get_arrRqstParameters() as $key => $value) {
                    if (is_array($value)) {
                        foreach ($value as $newvalue) {
                            $arrNewValue = explode(':', $newvalue, 2);
                            if (substr($newvalue, 0, 7) == 'http://'
                                || substr($newvalue, 0, 8) == 'https://'
                            ) {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), null, $newvalue));
                            } elseif (substr($newvalue, 0, 7) == 'mailto:') {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), 'EMail', $newvalue));
                            } elseif (substr($newvalue, 0, 5) == 'xmpp:'
                                || substr($newvalue, 0, 6) == 'gtalk:'
                            ) {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), 'XMPP', $newvalue));
                            } elseif (substr($newvalue, 0, 4) == 'sip:') {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), 'SIP', $newvalue));
                            } elseif (count($arrNewValue) > 1) {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), $arrNewValue[0], $arrNewValue[1]));
                            } else {
                                $newobject->setKey($key, Base_GeneralFunctions::addJson($newobject->getKey($key), null, $newvalue));
                            }
                        }
                    } else {
                        $newobject->setKey($key, $value);
                    }
                }
                try {
                    $newobject->create();
                    $key = $newobject->getPrimaryKeyValue();
                    if ($key == '') {
                        throw new Exception("Although the object was created, we didn't receive a primary key for it. Values are: " . print_r($newobject->getSelf(), true));
                    } else {
                        $arrType = explode('_', $object);
                        $object_type = strtolower($arrType[1]);
                        if ($rest) {
                            $object_type = 'rest/' . $object_type;
                        }
                        Base_Response::redirectTo($object_type . '/' . $key);
                    }
                } catch (Exception $e) {
                    error_log("Unable to create new object of type $object due to error " . $e->getMessage());
                    Base_Response::sendHttpResponse(406);
                }
                break;
            case 'delete':
                Base_Response::sendHttpResponse(405);
            }
        } else {
            $requestedobject = $object::brokerByID($item);
            switch ($objRequest->get_strRequestMethod()) {
            case 'head':
            case 'get':
                $arrObjects[$object][$item] = $requestedobject;
                break;
            case 'post':
            case 'put':
                if ($requestedobject == false) {
                    Base_Response::sendHttpResponse(404);
                } else {
                    foreach ($objRequest->get_arrRqstParameters() as $key => $value) {
                        if (is_array($value)) {
                            foreach ($value as $newvalue) {
                                if ($newvalue == '') {
                                    continue;
                                }
                                if (substr($newvalue, 0, 4) == 'del_') {
                                    $newvalue = substr($newvalue, 4);
                                    $arrNewValue = explode(':', $newvalue, 2);
                                    if (substr($newvalue, 0, 7) == 'http://'
                                        || substr($newvalue, 0, 8) == 'https://'
                                        || substr($newvalue, 0, 7) == 'mailto:'
                                        || substr($newvalue, 0, 4) == 'sip:'
                                    ) {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::delJson($requestedobject->getKey($key), $newvalue));
                                    } elseif (count($arrNewValue) > 1) {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::delJson($requestedobject->getKey($key), $arrNewValue[1]));
                                    } elseif ($newvalue == "0" || 0 + $newvalue > 0) {
                                        foreach (Base_GeneralFunctions::getJson($requestedobject->getKey($key)) as $newkey => $value) {
                                            if ($newkey == $newvalue) {
                                                $newvalue = $value;
                                            }
                                        }
                                        $requestedobject->setKey($key, Base_GeneralFunctions::delJson($requestedobject->getKey($key), $newvalue));
                                    } else {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::delJson($requestedobject->getKey($key), $newvalue));
                                    }
                                } else {
                                    $arrNewValue = explode(':', $newvalue, 2);
                                    if (substr($newvalue, 0, 7) == 'http://'
                                        || substr($newvalue, 0, 8) == 'https://'
                                        || substr($newvalue, 0, 7) == 'mailto:'
                                        || substr($newvalue, 0, 4) == 'sip:'
                                    ) {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::addJson($requestedobject->getKey($key), null, $newvalue));
                                    } elseif (count($arrNewValue) > 1) {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::addJson($requestedobject->getKey($key), $arrNewValue[0], $arrNewValue[1]));
                                    } else {
                                        $requestedobject->setKey($key, Base_GeneralFunctions::addJson($requestedobject->getKey($key), null, $newvalue));
                                    }
                                }
                            }
                        } else {
                            $requestedobject->setKey($key, $value);
                        }
                    }
                    try {
                        $requestedobject->write();
                        $arrType = explode('_', $object);
                        $object_type = strtolower($arrType[1]);
                        if ($rest) {
                            $object_type = 'rest/' . $object_type;
                        }
                        Base_Response::redirectTo($object_type . '/' . $item);
                    } catch (Exception $e) {
                        error_log("Unable to update object of type $object, item code $item due to error " . $e->getMessage());
                        Base_Response::sendHttpResponse(406);
                    }
                }
                break;
            case 'delete':
                if ($requestedobject == false) {
                    Base_Response::sendHttpResponse(404);
                } else {
                    try {
                        $requestedobject->delete();
                        if ($rest) {
                            Base_Response::sendHttpResponse(204);
                        } else {
                            Base_Response::redirectTo('welcome?delete');
                        }
                    } catch (Exception $e) {
                        error_log("Unable to update object of type $object, item code $item due to error " . $e->getMessage());
                        Base_Response::sendHttpResponse(406);
                    }
                }
            }
        }
    }
} else {
    if ($rest) {
        Base_Response::sendHttpResponse(404);
    } else {
        Base_Response::redirectTo('welcome?lost');
    }
}

foreach ($arrObjects as $object_group => $data) {
    foreach ($data as $key => $object) {
        if (is_object($object)) {
            $object->setFull(true);
            if ($object_group == $renderPage) {
                $arrObjectsData['renderPage'][$key] = $object->getSelf();
            }
            $arrObjectsData[$object_group][$key] = $object->getSelf();
        } elseif (is_array($object)) {
            if ($object_group == $renderPage) {
                $arrObjectsData['renderPage'][$key] = $object;
            }
            $arrObjectsData[$object_group][$key] = $object;
        } else {
            if ($object_group == $renderPage) {
                $arrObjectsData['renderPage'][$key] = null;
            }
            $arrObjectsData[$object_group][$key] = null;
        }
    }
}

$arrObjectsData['PageGenerationTime'] = Base_Response::getGenerationTime();
foreach (Container_Config::brokerAll() as $key=>$object) {
    switch ($key) {
    case 'RW_DSN':
    case 'RW_User':
    case 'RW_Pass':
    case 'RO_DSN':
    case 'RO_User':
    case 'RO_Pass':
    case 'DatabaseType':
        break;
    default:
        $arrObjectsData['SiteConfig'][$key] = $object->getKey('value');
    }
}

if (isset($_SESSION['authentication_failure'])) {
    $arrObjectsData['Object_User']['Failure'] = $_SESSION['authentication_failure'];
    unset($_SESSION['authentication_failure']);
} elseif (isset($_SESSION['login'])) {
    $arrObjectsData['Object_User']['Success'] = $_SESSION['login'];
    unset($_SESSION['login']);
}

if ($rest) {
    switch ($objRequest->get_strPrefAcceptType()) {
    case 'application/json':
        Base_Response::sendHttpResponse(200, Base_GeneralFunctions::utf8json($arrObjectsData), $objRequest->get_strPrefAcceptType());
        break;
    case 'text/xml':
        Base_Response::sendHttpResponse(200, Base_GeneralFunctions::utf8xml($arrObjectsData), $objRequest->get_strPrefAcceptType());
        break;
    case 'text/html':
        Base_Response::sendHttpResponse(200, Base_GeneralFunctions::utf8html($arrObjectsData), $objRequest->get_strPrefAcceptType());
        break;
    // I'd like to add RDFa or TTL files here, but I need to work out how to set the data up for that.
    }
} else {
    Base_Response::render($renderPage, $arrObjectsData);
}
