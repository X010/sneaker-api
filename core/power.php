<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * power
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

/**
 * 数据权限类
 */
class Power{
    
    /**
     * 检查传入公司ID是否有权限
     *
     * @param int $cid 公司ID
     * @return bool
     */
    static public function check_my_cid($cid){
        $app = \Slim\Slim::getInstance();
        if($cid != $app->Sneaker->cid){
            error(8110);
        }
        return True;
    }

//    /**
//     * 设置公司ID为检索条件
//     *
//     * @param array $data 查询条件列表
//     * @param string $cname 设置的公司字段名称
//     * @return mixed
//     */
//    static public function set_my_cid(&$data, $cname='cid'){
//        $app = \Slim\Slim::getInstance();
//        $data[$cname] = $app->Sneaker->cid;
//        return $data;
//    }

    /**
     * 检查传入仓库ID是否有权限（含公司判断）
     *
     * @param int $sid 仓库ID
     * @return bool
     */
    static public function check_my_sid($sid){
        $app = \Slim\Slim::getInstance();
        
        //如果是管理员，判断公司权限
        $store_res = $app->db->select('o_store','*',['id'=>$sid]);
        if(!$store_res){
            error(1601);
        }
        $store_name = $store_res[0]['name'];

        if($app->Sneaker->user_info['admin'] == 1){   
            if($store_res[0]['cid'] != $app->Sneaker->cid){
                error(8111, $store_name);
            }    
        }
        else{
            //如果不是管理员，判断仓库权限
            if(!in_array($sid, $app->Sneaker->sids)){
                error(8111, $store_name);
            }
        }
        return True;
    }

    /**
     * 设置仓库ID为检索条件（含公司判断）
     *
     * @param array $data 查询条件列表
     * @param string $cname 公司字段名称
     * @param string $sname 仓库字段名称
     * @return mixed
     */
    static public function set_my_sids(&$data, $cname='cid', $sname='sid'){
        $app = \Slim\Slim::getInstance();
        $data[$cname] = $app->Sneaker->cid;
        if($app->Sneaker->user_info['admin'] != 1 && !isset($data[$sname])){
            //如果不是管理员，判断仓库权限
            $data[$sname] = $app->Sneaker->sids;
        }
        return $data;
    }

    /**
     * 设置操作员和操作员姓名
     *
     * @param array $data 要设置的目标
     * @param string $uid 操作员ID字段名称
     * @param string $uname 操作员名称字段名称
     * @return bool
     */
    static public function set_oper(&$data, $uid='uid', $uname='uname'){
        $app = \Slim\Slim::getInstance();
        $data[$uid] = $app->Sneaker->uid;
        $data[$uname] = $app->Sneaker->uname;
        return True;
    }
    
}

