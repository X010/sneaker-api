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

class Change{
    
    /**
     * 转换商品ID到Name
     * 
     * @param int   大包装商品id
     * @param int   基本包装商品id
     * @param int   倍率
     */
    static public function go($data, $id_name, $name_name, $tb_name, $field='name'){
        $app = \Slim\Slim::getInstance();
        $bid_list = [];
        $res_kv = [];
        foreach($data as $val){
            if(strpos($val[$id_name], ',') !== false){
                $temp_list = explode(',', $val[$id_name]);
                foreach($temp_list as $temp){
                    if($temp && !in_array($temp, $bid_list)){
                        $bid_list[] = $temp;
                    }
                }
            }
            else{
                if($val[$id_name] && !in_array($val[$id_name], $bid_list)){
                    $bid_list[] = $val[$id_name];
                }
            }
        }
        $res_brand = $app->db->select($tb_name, '*', ['id'=>$bid_list]);
        if($res_brand){
            foreach($res_brand as $val){
                $res_kv[$val['id']] = $val[$field];
            }
        }

        foreach($data as $key=>$val){
            if(strpos($val[$id_name], ',') !== false){
                $id_list = explode(',', $val[$id_name]);
                $name_str = '';
                foreach($id_list as $id){

                    if($id && isset($res_kv[$id])){
                        $name_str .= $res_kv[$id].',';
                    }
                }
                if($name_str){
                    $name_str = substr($name_str, 0, -1);
                }
                $data[$key][$name_name] = $name_str;
            }
            else{
                if($val[$id_name]){
                    if(isset($res_kv[$val[$id_name]])){
                        $data[$key][$name_name] = $res_kv[$val[$id_name]];
                    }
                    else{
                        $data[$key][$name_name] = '不存在';
                    }
                }
                else{
                    $data[$key][$name_name] = '无';
                }
            }

        }
        return $data;
    }   
    
    
    
}

