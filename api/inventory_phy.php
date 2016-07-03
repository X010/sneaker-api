<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * inventory_phy 实盘管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */



/**
 * @api {post} inventory_phy/create 新建实盘（已停用）
 * @apiName inventory_phy/create
 * @apiGroup InventoryPhy
 * @apiVersion 0.0.1
 * @apiDescription 新建实盘
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {json} goods_list 商品清单
 * @apiParam {json} - 商品清单详情
 * @apiParam {int} gid 商品ID
 * @apiParam {int} total 实盘数量
 *
 * @apiSuccess {int} id 实盘ID
 *
 */


/**
 * @api {post} inventory_phy/check 创建并审核实盘
 * @apiName inventory_phy/check
 * @apiGroup InventoryPhy
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核实盘
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {json} goods_list 商品清单
 * @apiParam {json} - 商品清单详情
 * @apiParam {int} gid 商品ID
 * @apiParam {int} total 实盘数量
 *
 * @apiSuccess {int} id 实盘ID
 *
 */


/**
 * @api {post} inventory_phy/check/:id 审核实盘（已停用）
 * @apiName inventory_phy/check/:id
 * @apiGroup InventoryPhy
 * @apiVersion 0.0.1
 * @apiDescription 审核实盘
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {json} goods_list 商品清单
 * @apiParam {json} - 商品清单详情
 * @apiParam {int} gid 商品ID
 * @apiParam {int} total 实盘数量
 *
 * @apiSuccess {int} id 实盘ID
 *
 */

/**
 * @api {post} inventory_phy/read/:id 读取实盘详情
 * @apiName inventory_phy/read/:id
 * @apiGroup InventoryPhy
 * @apiVersion 0.0.1
 * @apiDescription 读取实盘详情
 *
 * @apiSuccess {int} id 实盘ID
 * @apiSuccess {int} sys_id 帐盘ID
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {int} uid 盘点人ID
 * @apiSuccess {string} uname 盘点人姓名
 * @apiSuccess {int} cuid 审核人ID
 * @apiSuccess {string} cuname 审核人姓名
 * @apiSuccess {string} createtime 盘点日期
 * @apiSuccess {string} checktime 审核日期
 * @apiSuccess {list} goods_list 商品清单
 * @apiSuccess {list} - 商品清单详情字段
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {int} total 数目
 *
 */

/**
 * @api {post} inventory_phy/read 读取实盘列表
 * @apiName inventory_phy/read
 * @apiGroup InventoryPhy
 * @apiVersion 0.0.1
 * @apiDescription 读取实盘列表
 *
 * @apiParam {int} sid *仓库
 * @apiParam {int} status 状态1-未审核 2-已审核
 *
 * @apiSuccess {int} id 实盘ID
 * @apiSuccess {int} sys_id 帐盘ID
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} status 实盘状态
 * @apiSuccess {int} uid 盘点人ID
 * @apiSuccess {string} uname 盘点人姓名
 * @apiSuccess {int} cuid 审核人ID
 * @apiSuccess {string} cuname 审核人姓名
 * @apiSuccess {string} createtime 盘点日期
 * @apiSuccess {string} checktime 审核日期
 *
 */

function inventory_phy($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new InventoryPhy($id);

    switch($action){
//        case 'create':
//            init_log_oper($action, '创建实盘');
//
//            param_need($data, ['sid','goods_list']); //必选
//            param_check($data, ['sid' => "/^\d+$/"]);
//
//            //判断仓库是否开启库存，没开启直接报错
//            $s_model = new Store();
//            $is_reserve = $s_model -> is_reserve($data['sid']);
//            if(!$is_reserve){
//                error(1610);
//            }
//
//            $data['cid'] = $cid;
//            $data['cname'] = $app->Sneaker->cname;
//            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
//
//            Power::set_oper($data);
//            $data['status'] = 1;
//
//            $phy_id = $my_model -> my_create($data);
//            success(['id'=>$phy_id]);
//            break;
        case 'check':
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                init_log_oper($action, '修改并审核实盘');
                param_need($data, ['goods_list']); //必选

                //帐盘检测，是否有权限，状态等
                $res = $my_model->my_power($id, 1);
                $data['cid'] = $res['cid'];
                $data['sid'] = $res['sid'];

                $my_model -> my_check($data, 'update');
            }
            else{
                init_log_oper($action, '创建并审核实盘');
                param_need($data, ['sid','goods_list']); //必选
                param_check($data, ['sid' => "/^\d+$/"]);

                //判断仓库是否开启库存，没开启直接报错
                $s_model = new Store();
                $is_reserve = $s_model -> is_reserve($data['sid']);
                if(!$is_reserve){
                    error(1610);
                }

                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
                Power::set_oper($data);

                $id = $my_model -> my_check($data, 'create');
            }
            success(['id'=>$id]);

            break;
        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取实盘详情');

                //帐盘检测，是否有权限，状态等
                $my_model->my_power($id, 0);

                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取实盘列表');

                param_need($data, ['sid','status']); //必选
                param_check($data, ['sid,status' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;
        default:
            error(1100);
    }

}
