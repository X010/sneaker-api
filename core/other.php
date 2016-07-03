<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * goods_packing
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Other{

    static public function get_weixin_data($state){
        $app = \Slim\Slim::getInstance();
        $res = $app->db2->select('db_mall','*',[
            'AND' => [
                'state' => $state,
                'bind' => 1
            ]
        ]);
        if(!$res){
            return False;
        }
        return $res[0];
    }   
    
    
    
}

