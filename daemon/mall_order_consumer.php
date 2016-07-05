<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * mall_order_consumer
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     daemon
 */

/**
 * framework initialization
 */
//header('Access-Control-Allow-Origin:*'); //for emberjs
date_default_timezone_set('Asia/Shanghai'); //set timezone

$file_path = dirname(__FILE__);
set_include_path($file_path."/../");
ini_set('default_socket_timeout',-1);

require 'Slim/Slim.php';
\Slim\Slim::registerAutoloader();
$app = new \Slim\Slim();            //初始化Slim对象
$app->Sneaker = new stdClass();     //初始化Sneaker对象

/**
 * public include
 */
require 'conf/include.php';
require 'conf/system.php';          //系统自定义配置
require 'conf/env.php';             //基础环境配置
require 'conf/errcode.php';

/**
 * router middleware
 */
require 'mw/init.php';              //进入API前的初始化

//model类自动加载
function my_autoload($class) {
    $file = 'model/'.cc_format($class).'.php';
    //if(file_exists($file)){
        require($file);
        return;
    //}
}

spl_autoload_register('my_autoload');

init();
$my_model = new Order();
$s_model = new Store();
$c_model = new Customer();
$u_model = new User();
$mall_model = new Mall();
$queue_name = $app->config('mall_order_queue_name');
do{
    try {
        $order_data_queue = $app->kv->blpop($queue_name, 0);
        if(!$order_data_queue){
            error(9904);
        }
        $order_data = json_decode($order_data_queue[1], True);
        if(!$order_data){
            continue;
        }

        //判断是否预取消，如果是则直接跳过
        $pre_delete = $app->kv->get('delete_'.$order_data['orderNo']);
        if($pre_delete == 1){
            continue;
        }

        //判断重试次数，如果重试多次不行就不再进行重试
        $retry_times = get_value($order_data, 'retry_times');
        if(!$retry_times){
            $retry_times = 1;
        }
        else{
            if($retry_times > 3){
                $msg = "下单失败";
                $mall_orderno = $order_data['orderNo'];
                $mall_model->notice_order($mall_orderno, 99, $msg);
                continue;
            }
            $retry_times++;
        }
        $order_data['retry_times'] = $retry_times;
        $order_data_queue[1] = json_encode($order_data);

        $app->Sneaker->cid = $order_data['out_cid'];

        $goods_list = $order_data['goods_list'];
        if(!$goods_list){
            continue;
        }

        $flag = 0;
        //过滤一遍数据，检查是否有数量不为正数或者价格为负数的商品存在
        foreach ($goods_list as $key=>$goods) {
            if ($goods['total'] <= 0 || $goods['amount_price'] < 0) {
                $flag = 1;
                break;
            }
            $order_data['goods_list'][$key]['unit_price'] = $goods['amount_price']/$goods['total'];
        }
        if ($flag) {
            daemon_error(3006, [
                'orderNo' => $order_data['orderNo']
            ]);
        }

        //获取入库公司的第一个仓库，作为默认入库仓库
        $store_res = $s_model->get_first_store($order_data['in_cid']);
        if (!$store_res) {
            daemon_error(5101, [
                'orderNo' => $order_data['orderNo']
            ]);
        }

        //操作员ID和名称
        $order_data['uid'] = get_value($order_data, 'uid', '');
        if($order_data['uid']){
            $order_data['uname'] = $my_model->get_name_by_id('o_user', $order_data['uid']);
        }
        else{
            $order_data['uname'] = '';
        }

        $order_data['in_sid'] = $store_res['id'];
        $order_data['in_sname'] = $store_res['name'];
        $order_data['rank'] = $order_data['delivery'];
        $order_data['discount_amount'] = $order_data['favorable'];
        $order_data['express']=$order_data['express']*100;
        $order_data['in_cname'] = $my_model->get_name_by_id('o_company', $order_data['in_cid']);
        $order_data['out_cname'] = $my_model->get_name_by_id('o_company', $order_data['out_cid']);

        $order_data['checktime'] = date('Y-m-d H:i:s');
        $order_data['status'] = 2;
        $order_data['type'] = 1; //采购订单
        $order_data['mall_orderno'] = $order_data['orderNo'];

        $user_res = $u_model->get_first_user($order_data['in_cid']);
        if (!$user_res) {
            daemon_error(1342, [
                'orderNo' => $order_data['orderNo']
            ]);
        }
        $order_data['buid'] = $user_res['id'];
        $order_data['buname'] = $user_res['name'];

        //设置默认业务员和默认出货仓库

        $c_res = $c_model->read_one([
            'cid' => $order_data['out_cid'],
            'ccid' => $order_data['in_cid']
        ]);
        if ($c_res) {
            $order_data['out_sid'] = $c_res['sid'];
            $order_data['out_sname'] = $my_model->get_name_by_id('o_store', $order_data['out_sid']);

            $suid = get_value($order_data, 'suid');
            if ($suid) {
                $order_data['suname'] = $my_model->get_name_by_id('o_user', $suid);
            } else {
                $order_data['suid'] = $c_res['suid'];
                $order_data['suname'] = $c_res['suname'];
            }
        } else {
            daemon_error(1710, [
                'orderNo' => $order_data['orderNo']
            ]);
        }

        //$goods_list = $order_data['goods_list'];
        usort($order_data['goods_list'], "order_goods_sort");

//        $new_goods_list = [];
//        foreach($goods_list as $goods){
//            $group_gcode = get_value($goods, 'mainGcode');
//            if($group_gcode)
//        }

        $order_data['goods_list'] = json_encode($order_data['goods_list']);

        $order_data['receipt'] = trim($order_data['receipt'], ',');
        $order_data['receipt'] = str_replace(',', ' ', $order_data['receipt']);

        //检测商城订单是否重复下单过
        start_action();
        $res = $my_model->check_mall_orderno($order_data['mall_orderno']);
        if ($res) {
            //如果已经下过单了直接返回成功
            $order_res = $my_model->read_one([
                'mall_orderno'=>$order_data['mall_orderno']
            ]);
            $order_id = $order_res['id'];

            if($order_data['ispaid'] == 1){
                if($order_res['ispaid'] == 0){
                    $order_data['ouid'] = $order_data['suid'];
                    $order_data['ouname'] = $order_data['suname'];
                    $my_model->set_id($order_id);
                    $my_model->update_by_id($order_data);

                    $so_model = new StockOut();

                    //将订单中的供货方公司和仓库信息写入出库单中
                    $data['uid'] = $order_data['uid'];
                    $data['uname'] = $order_data['uname'];
                    $data['suid'] = $order_data['suid'];
                    $data['suname'] = $order_data['suname'];
                    $data['cid'] = $order_data['out_cid'];
                    $data['cname'] = $order_data['out_cname'];
                    $data['in_cid'] = $order_data['in_cid'];
                    $data['in_cname'] = $order_data['in_cname'];

                    $data['mall_orderno'] = $order_data['mall_orderno'];
                    $data['receipt'] = $order_data['receipt'];
                    $data['contacts'] = $order_data['contacts'];
                    $data['phone'] = $order_data['phone'];

                    $in_cid = get_value($order_data, 'in_cid');
                    $mall_orderno = $order_data['mall_orderno'];
                    $data['sid'] = $order_data['out_sid'];

                    $data['discount_amount'] = $order_data['discount_amount'];

                    //补充仓库name
                    $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                    if($data['suid']){
                        $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
                    }
                    else{
                        $data['suname'] = '';
                    }

                    $data['type'] = 1;
                    $data['status'] = 2;
                    $data['rank'] = $order_data['rank'];
                    $data['goods_list'] = $order_data['goods_list'];
                    $data['order_id'] = $order_id;

                    $so_model -> my_create($data);
                }
            }

            $msg = '订单创建成功';
            $mall_model->notice_order($order_data['orderNo'], 1, $msg, $order_id);
            end_action();
            continue;
        }


//        $order_data['uid'] = $order_data['suid'];
//        $order_data['uname'] = $order_data['suname'];
        $order_data['cuid'] = $order_data['uid'];
        $order_data['cuname'] = $order_data['uname'];
        if($order_data['ispaid'] == 1){
            $order_data['ouid'] = $order_data['suid'];
            $order_data['ouname'] = $order_data['suname'];
        }

        $platform = get_value($order_data ,'platform');
        if($platform == 'customer'){
            $order_data['from'] = 2;
        }

        $order_id = $my_model->add($order_data); //创建订单

        //如果已支付，自动进行出库并且结算
        //如果已支付库存不够，自动进行缺货待配并且结算

        if($order_data['ispaid'] == 1){
            $so_model = new StockOut();

            //将订单中的供货方公司和仓库信息写入出库单中
            $data['uid'] = $order_data['uid'];
            $data['uname'] = $order_data['uname'];
            $data['suid'] = $order_data['suid'];
            $data['suname'] = $order_data['suname'];
            $data['cid'] = $order_data['out_cid'];
            $data['cname'] = $order_data['out_cname'];
            $data['in_cid'] = $order_data['in_cid'];
            $data['in_cname'] = $order_data['in_cname'];

            $data['mall_orderno'] = $order_data['mall_orderno'];
            $data['receipt'] = $order_data['receipt'];
            $data['contacts'] = $order_data['contacts'];
            $data['phone'] = $order_data['phone'];

            $in_cid = get_value($order_data, 'in_cid');
            $mall_orderno = $order_data['mall_orderno'];
            $data['sid'] = $order_data['out_sid'];

            //补充仓库name
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
            if($data['suid']){
                $data['suname'] = $my_model->get_name_by_id('o_user', $data['suid']);
            }
            else{
                $data['suname'] = '';
            }

            $data['type'] = 1;
            $data['status'] = 2;
            $data['rank'] = $order_data['rank'];
            $data['goods_list'] = $order_data['goods_list'];
            $data['order_id'] = $order_id;

            $data['discount_amount'] = $order_data['discount_amount'];
            //$status = $so_model -> my_check($data, 'create');
            $so_model -> my_create($data);
            //通知商城订单状态
//            if($mall_orderno){
//                if($status == 3){
//                    $mall_status = 13;
//                }
//                elseif($status == 4){
//                    $mall_status = 4;
//                }
//                $msg = "自动出货";
//                $mall_model->notice_order($mall_orderno, $mall_status, $msg, $order_id);
//            }
        }
        else{
            //通知商城那边订单创建成功
            $msg = '订单创建成功';
            $mall_model->notice_order($order_data['orderNo'], 1, $msg, $order_id);

        }

        end_action();
        unset($order_data_queue);
        echo 'success:'. $order_id;
        //exit;
    }
    catch(Exception $e){
        $errmsg = $e->getMessage();
        echo 'error:'. $errmsg;

        //捕获到异常以后重连mysql 和 redis
        if(strpos($errmsg, '9900') !== False){
            echo 'connect mysql ...';
            try {
                $app->db = new \Slim\medoo($app->Sneaker->cfg_mysql);
                //unset($app->Sneaker->cfg_mysql);
            } catch (Exception $e) {
                sleep(2);
            }
        }

        if(strpos($errmsg, '9904') !== False || strpos($errmsg, 'Redis server') !== False){
            echo 'connect redis ...';
            $app->kv = new Redis();
            if ($app->kv->connect($app->Sneaker->cfg_kv['host'], $app->Sneaker->cfg_kv['port'])){
                //unset($app->Sneaker->cfg_kv);
            } else {
                sleep(2);
            }
        }

        try{
            if(isset($order_data_queue) && $order_data_queue){
                $app->kv->rpush($queue_name, $order_data_queue[1]);
            }
        }
        catch(Exception $e){

        }

        continue;
    }
}while(1);

function order_goods_sort($a, $b){
    $ac = get_value($a, 'mainGcode');
    $bc = get_value($b, 'mainGcode');

    if($ac == $bc){
        $ap = get_value($a, 'unit_price');
        $bp = get_value($b, 'unit_price');
        if($ap == $bp){
            return 0;
        }
        return ($ap > $bp)?-1:1;
    }
    return ($ac < $bc)?-1:1;
}