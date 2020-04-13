<?php

function __autoload($className)
{
    if (is_file(dirname(__FILE__) . '/' . $className . '.php')) {
        include_once dirname(__FILE__) . '/' . $className . '.php';
        return true;
    } elseif (is_file(dirname(__FILE__) . '/' . strtolower($className) . '.php')) {
        include_once dirname(__FILE__) . '/' . strtolower($className) . '.php';
        return true;
    }
    return false;
}