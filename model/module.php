<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * role
 *
 * @author      jeffwu <x010@foxmail.com>
 * @copyright   2015 jeffwu
 * @version     0.0.1
 * @package     model
 */

class Module extends Object
{
    /**
     * 入库必填字段
     */
    protected $format_data = ['name', 'function', 'menu', 'api'];

    protected $order_data = ['menu'];

    public function  __construct($id = NULL)
    {
        parent::__construct('s_module', $id);
    }

}

