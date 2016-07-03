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

require 'model/breturn.php';
require 'model/car.php';
require 'model/company.php';
require 'model/company_goods.php';
require 'model/company_goods_type.php';
require 'model/customer.php';
require 'model/debit_note.php';
require 'model/debit_note_detail.php';
require 'model/f_adjust.php';
require 'model/f_reserve.php';
require 'model/f_reserve_detail.php';
require 'model/f_sell_customer.php';
//require 'model/f_sell_goods.php';
require 'model/f_sell_supplier.php';
require 'model/f_stock_in.php';
require 'model/f_stock_out.php';
require 'model/goods.php';
require 'model/goods_brand.php';
require 'model/goods_packing.php';
require 'model/goods_supplier.php';
require 'model/goods_type.php';
require 'model/inventory_phy.php';
require 'model/inventory_phy_glist.php';
require 'model/inventory_sys.php';
require 'model/inventory_sys_glist.php';
require 'model/login.php';
require 'model/mall.php';
require 'model/module.php';
require 'model/operation_log.php';
require 'model/order.php';
require 'model/payment_note.php';
require 'model/payment_note_detail.php';
require 'model/price.php';
require 'model/price_glist.php';
require 'model/price_temp.php';
require 'model/price_temp_glist.php';
require 'model/role.php';
require 'model/settle_glist.php';
require 'model/settle_customer.php';
require 'model/settle_supplier.php';
require 'model/sorting.php';
require 'model/sorting_glist.php';
require 'model/stock_in.php';
require 'model/stock_out.php';
require 'model/store.php';
require 'model/store_area.php';
require 'model/store_goods.php';
require 'model/supplier.php';
require 'model/system_config.php';
require 'model/system_config_detail.php';
require 'model/temp_price.php';
require 'model/transfer.php';
require 'model/user.php';
require 'model/settle_proxy_supplier.php';
require 'model/settle_proxy_glist.php';
require 'model/customer_tmp.php';
require 'model/customer_salesman.php';
require 'model/commission.php';
require 'model/commission_glist.php';
require 'model/task.php';
require 'model/task_glist.php';
require 'model/user_group.php';
require 'model/visit.php';


init();
$fr_model = new FReserve();
$frd_model = new FReserveDetail();
$fsi_model = new FStockIn();
$fso_model = new FStockOut();
$fa_model = new FAdjust();

if(isset($argv[1])){
    $my_date = $argv[1];
    $last_date = date('Y-m-d', strtotime($my_date)-24*3600);
}
else{
    die('请输入日期参数');
    //$my_date = date('Y-m-d');
    //$last_date = date('Y-m-d', time()-24*3600);
}

$s_res = $app->db->select('o_store','*',[
    'AND'=>[
        'status'=>1,
        'isreserve'=>1
    ]
]);
foreach($s_res as $store){
    //每个仓库一次事务
    start_action();
    //先做防重，一个仓库一天只能有一条总记录
//    $has_res = $fr_model->has([
//        'date'=>$my_date,
//        'sid'=>$store['id']
//    ]);
//    //如果已存在，可能今天该仓库已经跑过脚本了，不可重复跑
//    if($has_res){
//        echo '重复（'.$store['id'].'）～';
//        continue;
//    }

    $last_fr_res = $fr_model->read_one([
        'date'=>$last_date,
        'sid'=>$store['id']
    ]);
    if(!$last_fr_res){
        echo '没有昨天记录（'.$store['id'].'）～';
        continue;
    }

    $fsi_res = $fsi_model->read_list_nopage([
        'date'=>$last_date,
        'sid'=>$store['id']
    ]);
    $fsi_data = [];
    foreach($fsi_res as $val){
        $fsi_data[$val['gid']] = $val;
    }

    $fso_res = $fso_model->read_list_nopage([
        'date'=>$last_date,
        'sid'=>$store['id']
    ]);
    $fso_data = [];
    foreach($fso_res as $val){
        $fso_data[$val['gid']] = $val;
    }

    $fa_res = $fa_model->read_list_nopage([
        'date'=>$last_date,
        'sid'=>$store['id']
    ]);
    $fa_data = [];
    foreach($fa_res as $val){
        $fa_data[$val['gid']] = $val;
    }

    $last_gres = $frd_model->read_list_nopage([
        'date'=>$last_date,
        'sid'=>$store['id']
    ]);

    $all_total_end = 0;
    $all_amount_end = 0;
    foreach($last_gres as $val){
        $gid = $val['gid'];
        $amount_begin = $val['amount_begin'];
        $total_begin = $val['total_begin'];
        $fsi_temp = get_value($fsi_data, $gid, []);
        $fso_temp = get_value($fso_data, $gid, []);
        $fa_temp = get_value($fa_data, $gid, []);

        $total_end = $total_begin+get_value($fsi_temp, 'buy_total')+get_value($fsi_temp, 'return_total')-get_value($fso_temp, 'sell_total')
            -get_value($fso_temp, 'return_total')+get_value($fsi_temp, 'transfer_total')-get_value($fso_temp, 'transfer_total')
            +get_value($fa_temp, 'overloss_total')+get_value($fa_temp, 'inventory_total');
        $amount_end = $amount_begin+get_value($fsi_temp, 'buy_amount')+get_value($fsi_temp, 'return_amount')-get_value($fso_temp, 'sell_amount')
            -get_value($fso_temp, 'return_amount')+get_value($fsi_temp, 'transfer_amount')-get_value($fso_temp, 'transfer_amount')
            +get_value($fa_temp, 'overloss_amount')+get_value($fa_temp, 'inventory_amount')+get_value($fa_temp, 'return_amount')
            +get_value($fa_temp, 'flush_amount')+get_value($fa_temp, 'transfer_amount');

        $all_total_end += $total_end;
        $all_amount_end += $amount_end;

        //修正昨日的记录
        $db_set = [
            'amount_end'=>$amount_end,
            'total_end'=>$total_end
        ];
        $db_where = [
            'AND'=>[
                'sid'=>$store['id'],
                'gid'=>$gid,
                'date'=>$last_date
            ]
        ];
        $frdu_res = $frd_model->update($db_set, $db_where);
        //创建或更新今天记录
        $temp_res = $frd_model->has([
            'sid'=>$store['id'],
            'gid'=>$gid,
            'date'=>$my_date
        ]);
        if($temp_res){
            //更新
            $db_set = [
                'amount_begin'=>$amount_end,
                'total_begin'=>$total_end
            ];
            $db_where = [
                'AND'=>[
                    'sid'=>$store['id'],
                    'gid'=>$gid,
                    'date'=>$my_date
                ]
            ];
            $frdu_res = $frd_model->update($db_set, $db_where);
        }
        else{
            //创建今天记录
            $temp_data = $val;
            $temp_data['date'] = $my_date;
            $temp_data['amount_begin'] = $amount_end;
            $temp_data['amount_end'] = 0;
            $temp_data['total_begin'] = $total_end;
            $temp_data['total_end'] = 0;
            $frd_model->create($temp_data);
        }

    }
    //更新上期期末
    $db_set = [
        'amount_end'=>$all_amount_end,
        'total_end'=>$all_total_end
    ];
    $db_where = [
        'AND'=>[
            'sid'=>$store['id'],
            'date'=>$last_date
        ]
    ];
    $fru_res = $fr_model->update($db_set, $db_where);

    //创建或更新今天记录
    $temp_res = $fr_model->has([
        'sid'=>$store['id'],
        'date'=>$my_date
    ]);
    if($temp_res){
        //更新
        $db_set = [
            'amount_begin'=>$all_amount_end,
            'total_begin'=>$all_total_end
        ];
        $db_where = [
            'AND'=>[
                'sid'=>$store['id'],
                'date'=>$my_date
            ]
        ];
        $frdu_res = $fr_model->update($db_set, $db_where);
    }
    else{
        //创建今天记录
        $temp_data = $last_fr_res;
        $temp_data['date'] = $my_date;
        $temp_data['amount_begin'] = $all_amount_end;
        $temp_data['amount_end'] = 0;
        $temp_data['total_begin'] = $all_total_end;
        $temp_data['total_end'] = 0;
        $fr_model->create($temp_data);
    }
    end_action();
}
echo 'success';
exit;
