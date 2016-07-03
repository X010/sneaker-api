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
            //$express_price=$app->db2->select('db_province_express',['AND'=>['province'=>$province,'express_id'=>$express_id]]);
            $express_price=$app->db2->get('db_province_express',['first_price','continue_price'],['province'=>$province,'express_id'=>$express_id,'status'=>1]);
            $data=0;
            if($express_price){
                $first_price=$express_price['first_price'];
                $continue_price=$express_price['continue_price'];
                $price=$first_price+(($weight*$good_count-1)*$continue_price);//计算运费 首重价格＋((重量－首重1公斤)＊续重价格)
                $data=$price;
                //$data2['first']=$express_price['first_price'];
                //$data2['continue']=$express_price['continue_price'];
            }
            respCustomer($data);
            break;
        default:
            error(1100);
    }

}