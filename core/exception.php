<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * exception
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     core
 */

/**
 * 程序异常
 */
class SneakerException Extends Exception{
    public function __construct($message, $code = 0) {
        parent::__construct($message, $code);
    }
}

