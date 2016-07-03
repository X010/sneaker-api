<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * supplier 供应商管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} supplier/create 添加供应商关系
 * @apiName supplier/create
 * @apiGroup Supplier
 * @apiVersion 0.0.1
 * @apiDescription 添加供应商关系操作（只能为本公司添加供应商）
 *
 * @apiParam {int} scid *供应商公司ID
 * @apiParam {string} scname *供应商公司名称
 *
 * @apiSuccess {int} id 供应商关系ID
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": {
 *             "id":"40",
 *         }
 *     }
 *
 */

/**
 * @api {post} supplier/create_batch 批量添加供应商关系
 * @apiName supplier/create_batch
 * @apiGroup Supplier
 * @apiVersion 0.0.1
 * @apiDescription 批量添加供应商关系操作（只能为本公司添加供应商）
 *
 * @apiParam {string} data *供应商公司信息块（JSON形式，具体JSON内容参照create接口）
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} supplier/update(/:id) 修改供应商关系信息
 * @apiName supplier/update
 * @apiGroup Supplier
 * @apiVersion 0.0.1
 * @apiDescription 修改供应商关系信息操作
 *
 * @apiParam {string} scname *供应商公司名称
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} supplier/delete/:id 删除供应商关系
 * @apiName supplier/delete
 * @apiGroup Supplier
 * @apiVersion 0.0.1
 * @apiDescription 物理删除供应商关系操作
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

function supplier($action, $id = NULL){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new Supplier($id);
    switch($action){
//        case 'create':
//            init_log_oper('create', '添加供应商关系');
//            param_need($data, ['scid','scname','period']); //必选
//            param_check($data, [
//                'scid,period' => "/^\d+$/",
//            ]);
//            if (!$data) error(1101);
//            //判断供应商不能为自己公司
//            if($data['scid'] == $cid){
//                error(1730);
//            }
//            $data['cid'] = $cid;
//            $data['cname'] = $app->Sneaker->cname;
//            //创建供应商关系
//            $id = $my_model->create($data);
//            //返回参数
//            $ret = [
//                'id' => $id,
//            ];
//
//            success($ret);
//            break;

        case 'create_batch':
            init_log_oper('create_batch', '批量添加供应商关系');
            param_need($data, ['data']); //必选
            $params = json_decode($data['data'], True);
            if (!$params) error(1102);

            foreach($params as $val){
                param_need($val, ['scid','scname','period']); //必选
                param_check($val, [
                    'scid,period,auto_delete' => "/^\d+$/",
                ]);
            }

            //批量创建供应商关系
            $ret = $my_model->add_batch($params, $cid, $app->Sneaker->cname);

            success();
            break;

        case 'update':
            init_log_oper('update', '修改供应商关系信息');
            if (!is_numeric($id)) error(1100);
            $data = format_data_ids($data, ['gtids']);

            $res = $my_model->read_by_id();
            if (!$res || $res[0]['cid'] != $cid) error(8110); //数据权限验证
            $data2 = $data;
            $data2['contactor'] = $data['contactor_sup'];
            $data2['contactor_phone'] = $data['contactor_phone_sup'];
            $my_model->update_by_id($data2);

            $scid = $res[0]['scid'];
            $c_model = new Company($scid);
            $sc_res = $c_model->read_by_id($scid);
            if($sc_res[0]['iserp'] == 0 && $sc_res[0]['create_cid'] == $cid){

                //获取公司地域级别
                $data['areatype'] = $c_model->get_area_type($data);
                //经营范围名称
                $gtids = get_value($data, 'gtids');
                if($gtids){
                    $data['gtnames'] = $c_model->get_names_by_ids('o_goods_type', $gtids);
                }
                $c_model->my_update($data);
            }

            success();
            break;

        case 'delete':
            init_log_oper('delete', '删除供应商关系');
            if (!is_numeric($id)) error(1100);

            $res = $my_model->read_by_id();
            if (!$res || $res[0]['cid'] != $cid) error(8110); //数据权限验证

            $my_model->delete_by_id(); //物理删除
            success();
            break;


        case 'register':
            init_log_oper($action, '注册供应商');
            param_need($data, ['name','gtids','type','contactor','contactor_phone'
                ,'my_discount','my_auto_delete','my_period','address']); //必选
            param_check($data, [
                'type' => "/^\d+$/",
            ]);

            $data = format_data_ids($data, ['gtids']);

            $address_list = explode(' ', $data['address']);
            $count = count($address_list);
            if($count > 5){
                for($i=5;$i<$count;$i++){
                    $address_list[4] .= $address_list[$i];
                    unset($address_list[$i]);
                }
            }
            $data['address'] = implode(' ', $address_list);

            $id = $my_model->my_register($data, $cid);
            success(['id'=>$id]);
            break;

        default:
            error(1100);
    }
}


