<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_supplier
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class GoodsSupplier extends Object{
    /**
     * 入库所需字段（必须）
     */
    protected $format_data = ['cid','scid','gid'];
    
    
    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('r_goods_supplier', $id);
    }

}

