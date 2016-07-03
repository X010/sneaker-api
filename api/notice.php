<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * notice 通知接口（内部API）
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} notice/paid 通知支付结果
 * @apiName notice/paid
 * @apiGroup Notice
 * @apiVersion 0.0.1
 * @apiDescription 通知支付结果
 *
 * @apiParam {int} type 类型1-出库单通知 2-结算单通知 3-订单通知
 * @apiParam {int} pay_type 支付方式
 * @apiParam {string} id 单据号，可能是出库单号，结算单号，订单号，根据type变化
 * @apiParam {string} amount 支付金额，单位元。用于二次核对
 *
 */

function notice($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch($action){
        case 'paid':
            param_need($data, ['type','pay_type','id','amount']);
            param_check($data, [
                'type' => "/^[123]$/",
                'pay_type,id' => "/^\d+$/",
            ]);	//判断所传参数是否符合规范，正则
            switch($data['type']){
                case 1:
                    //出库单通知，模拟货到付款类型
                    //结算出库单，自动创建审核结算单
                    $so_model = new StockOut($data['id']);
                    $so_res = $so_model->read_by_id();
                    if(!$so_res){
                        error(3120);
                    }
                    if($so_res[0]['type'] != 1){
                        error(3122);
                    }
                    if($so_res[0]['status'] != 2 && $so_res[0]['status'] != 3 && $so_res[0]['status'] != 4){
                        error(3123);
                    }
                    //如果已经结算过，是重复通知，直接返回成功
                    if($so_res[0]['settle_status'] == 1){
                        success();
                    }
                    $my_price = yuan2fen($so_res[0]['amount']);
                    $his_price = yuan2fen($data['amount']);
                    if($my_price != $his_price){
                        error(3801);
                    }

                    //操作员ID和名称
                    $data['uid'] = get_value($data, 'uid', '');
                    if($data['uid']){
                        $data['uname'] = $so_model->get_name_by_id('o_user', $data['uid']);
                    }
                    else{
                        $data['uname'] = '';
                    }

//                    $so_model->update_by_id([
//                        'settle_type'=>1,
//                        'pay_type'=>$data['pay_type'],
//                        'small_amount'=>get_value($data,'small_amount',0)
//                    ]);

                    $sc_model = new SettleCustomer();
                    $settle_data['pay_type'] = $data['pay_type'];
                    $settle_data['cid'] = $so_res[0]['cid'];
                    $settle_data['cname'] = $so_res[0]['cname'];
                    $settle_data['ccid'] = $so_res[0]['in_cid'];
                    $settle_data['ccname'] = $so_res[0]['in_cname'];
                    $settle_data['stock_list'] = json_encode([['id'=>$data['id']]]);
                    $settle_data['small_amount'] = get_value($data,'small_amount',0);
                    $settle_data['settle_type'] = 2;
                    $settle_data['uid'] = $settle_data['cuid'] = $data['uid'];
                    $settle_data['uname'] = $settle_data['cuname'] = $data['uname'];

                    $app->Sneaker->cid = $so_res[0]['cid'];

                    $sc_model->my_check($settle_data, 'create', 1);

                    break;
                case 2:
                    //结算单通知，模拟账期付款类型
                    //审核结算单
                    $sc_model = new SettleCustomer($data['id']);
                    $sc_res = $sc_model->read_by_id();
                    if(!$sc_res){
                        error(3402);
                    }

                    //如果已经结算，直接返回成功
                    if($sc_res[0]['status'] == 2){
                        success();
                    }

                    if($sc_res[0]['status'] != 1){
                        error(3404);
                    }

                    $my_price = yuan2fen($sc_res[0]['amount_price']);
                    $his_price = yuan2fen($data['amount']);
                    if($my_price != $his_price){
                        error(3801);
                    }

                    //操作员ID和名称
                    $data['uid'] = get_value($data, 'uid', '');
                    if($data['uid']){
                        $data['uname'] = $sc_model->get_name_by_id('o_user', $data['uid']);
                    }
                    else{
                        $data['uname'] = '';
                    }

                    $stock_list = $sc_res[0]['stock_list'];
                    $stock_list = explode(',', $stock_list);
                    $stocks = [];
                    foreach($stock_list as $val){
                        $stocks[] = [
                            'id'=>$val
                        ];
                    }

                    $app->Sneaker->cid = $sc_res[0]['cid'];

                    $settle_data = [
                        'pay_type' => $data['pay_type'],
                        'settle_type' => 2,
                        'small_amount' => get_value($data,'small_amount',0),
                        'cuid'=>$data['uid'],
                        'cuname'=>$data['uname'],
                        'stock_list' => json_encode($stocks)
                    ];
                    $sc_model->my_check($settle_data, 'update', 2);

                    break;
                case 3:
                    //订单通知，模拟预付款类型
                    //订单置为已支付状态
                    $o_model = new Order($data['id']);
                    $o_res = $o_model->read_by_id();
                    if(!$o_res){
                        error(3100);
                    }
                    if($o_res[0]['type'] != 1){
                        error(3102);
                    }
                    if($o_res[0]['status'] != 2){
                        error(3103);
                    }

                    $my_price = yuan2fen($o_res[0]['amount']);
                    $his_price = yuan2fen($data['amount']);
                    if($my_price != $his_price){
                        error(3801);
                    }

                    //预付款成功，重复通知
                    if($o_res[0]['paid'] == 1){
                        success();
                    }

                    $o_model->update_by_id(['paid'=>1]);

                    $so_model = new StockOut();
                    //将订单中的供货方公司和仓库信息写入出库单中
                    $order_data = $o_res[0];
                    $so_data['order_id'] = $data['id'];
                    $so_data['cid'] = $order_data['out_cid'];
                    $so_data['cname'] = $order_data['out_cname'];
                    $so_data['in_cid'] = $order_data['in_cid'];
                    $so_data['in_cname'] = $order_data['in_cname'];

                    $so_data['mall_orderno'] = $order_data['mall_orderno'];
                    $so_data['receipt'] = $order_data['receipt'];
                    $so_data['contacts'] = $order_data['contacts'];
                    $so_data['phone'] = $order_data['phone'];
                    $so_data['sid'] = $order_data['out_sid'];
                    $so_data['suid'] = $order_data['suid'];

                    //补充仓库name
                    $so_data['sname'] = $so_model->get_name_by_id('o_store', $so_data['sid']);
                    $so_data['suname'] = $so_model->get_name_by_id('o_user', $so_data['suid']);

                    $data['type'] = 1;
                    $so_model -> my_check($so_data, 'create', 3);

                    break;
                default:
                    break;
            }

            success();
            break;

        case 'vip_up':
            //支付网关通知VIP升级
            //param: cid,scid,product_id
            $c_model = new Customer();
            $c_model->vip_up($data);
            success();
            break;

        default:
            error(1100);
    }

}
