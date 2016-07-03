<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * a_salesman 业务员APP接口
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

function a_salesman($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'debit':
            //业务员－查询没收到的钱
            //客户 ／ 应收金额 ／ 已延期天数
            $so_model = new StockOut();
            $data['cid'] = $cid;
            $data['suid'] = $app->Sneaker->uid;

            $result = $so_model->form_debit_salesman($data);

            success($result);
            break;

//        case 'price_list':
//            //init_log_oper($action, '读取商品价格列表');
//            $my_model = new Price($id);
//            param_need($data, ['cid','ccid','goods_list']);
//            $g_model = new Goods();
//            $c_model = new Customer();
//            $goods_list = json_decode($data['goods_list'], True);
//            $result = [];
//
//            $c_res = $c_model->read_one([
//                'cid' => $data['scid'],
//                'ccid' => $data['cid']
//            ]);
//            if(!$c_res){
//                $price_name = False;
//            }
//            else{
//                //根据客户关系决定到底使用哪个级别的价格
//                $price_name = 'out_price'.$c_res['cctype'];
//            }
//
//            $gcode_list = [];
//            foreach($goods_list as $val){
//                param_need($val, ['gcode']);
//                $gcode_list[] = $val['gcode'];
//            }
//
//            $g_res = $g_model->read_list_nopage([
//                'code'=>$gcode_list
//            ]);
//            $gid_list = [];
//            $code2id = [];
//            foreach($g_res as $val){
//                $gid_list[] = $val['id'];
//                $code2id[$val['code']] = $val['id'];
//            }
//
//            if($price_name){
//                $price_res = $my_model->get_prices($gid_list, $data['scid'], $c_res['sid'], $price_name);
//                foreach($goods_list as $val){
//                    $id = $code2id[$val['gcode']];
//                    $result[$val['gcode']] = get_value($price_res, $id, -2);
//                }
//            }
//            else{
//                foreach($goods_list as $val){
//                    $result[$val['gcode']] = -1;
//                }
//            }
//
//            success($result);
//            break;

        default:
            error(1100);
    }

}
