<?php
function __autoload($className)
{
    $arrClass = explode('_', $className);
    $class_path  = dirname(__FILE__);
    if ($arrClass[count($arrClass) - 1] == 'Testable') {
        $class_path .= '/../Tests/classes';
    }
    foreach ($arrClass as $class_point) {
        if ($class_point != 'Demo' && $class_point != 'Testable') {
            $class_path .= '/' . $class_point;
        }
    }
    if ($arrClass[count($arrClass) - 1] == 'Testable') {
        $class_path .= 'Test';
    }
    if (is_file($class_path . '.php')) {
        include_once $class_path . '.php';
        return true;
    }
    return false;
}

spl_autoload_register('__autoload');