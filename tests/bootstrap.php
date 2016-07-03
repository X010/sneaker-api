<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * bootstrap for Unit-test
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     tests
 */

// Set default timezone
date_default_timezone_set('Asia/Shanghai');

require_once '../Slim/Slim.php';

// Register Slim's autoloader
\Slim\Slim::registerAutoloader();

//Register non-Slim autoloader
function customAutoLoader( $class )
{
    $file = rtrim(dirname(__FILE__), '/') . '/' . $class . '.php';
    if ( file_exists($file) ) {
        require $file;
    } else {
        return;
    }
}
spl_autoload_register('customAutoLoader');
