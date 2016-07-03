<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Hook for test
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     hook
 */

/**
 * set the hook name
 *
 * e.g.
 * $app->applyHook('hook.test');
 */ 
$app->hook('hook.test', 'hook_test'); 

function hook_test(){
    $app = \Slim\Slim::getInstance();
    //TODO
}
