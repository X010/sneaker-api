<?php
/**
 * Created by IntelliJ IDEA.
 * User: b3st9u
 * Date: 16/7/3
 * Time: 16:55
 */

function wx_express($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch($action){
        case 'express_price_get':
            //$sending_province_id=get_value($data,'sending_province_id');//发货人省份
            $province=get_value($data,'province');//收货省份
            $express_id =get_value($data,'express_id');//快递公司id
            $good_id=get_value($data,'good_id');//货物id
            $good_count=get_value($data,'good_count');//数量
            //$weight=get_value($data,'weight');//箱重
            $weight=$app->db->get('o_company_goods',['weight'],['gid'=>$good_id]);//查询货物箱重

            //查询快递价格
//            $express_price=$app->db2->select('db_province_express',['first_price','continue_price'],['AND'=>['province'=>$province,'express_id'=>$express_id,'status'=>1]]);
            $express_price=$app->db2->get('db_province_express',['first_price','continue_price'],['AND' =>['province'=>$province,'express_id'=>$express_id,'status'=>1]]);
            //var_dump($app->db2->last_query());
            $data=0;
            if($express_price){
                $first_price=$express_price['first_price'];
                $continue_price=$express_price['continue_price'];
                $continue_weight=(int)$weight * (int)$good_count - 1;//续重重量 箱重＊数量－首重1kg
                $price=$first_price + $continue_weight * $continue_price;//计算运费 首重价格＋(续重重量＊续重价格)
                $data=$price;
            }
            respCustomer($data);
            break;
        default:
            error(1100);
    }

}