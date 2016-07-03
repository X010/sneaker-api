<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * inventory_sys 账盘管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} inventory_sys/create 新建帐盘
 * @apiName inventory_sys/create
 * @apiGroup InventorySys
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库商品价格
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {int} tids 类型ID,用逗号分隔（不传或为空代表盘点所有类型）
 * @apiParam {int} type *帐盘单类型(1-盘点库存商品 2-盘点档案商品)
 *
 * @apiSuccess {int} id 帐盘ID
 *
 */

/**
 * @api {post} inventory_sys/check/:id 审核帐盘（记账）
 * @apiName inventory_sys/check/:id
 * @apiGroup InventorySys
 * @apiVersion 0.0.1
 * @apiDescription 审核帐盘（记账）
 *
 * @apiSuccess {int} id 帐盘ID
 *
 */

/**
 * @api {post} inventory_sys/read/:id 读取帐盘明细
 * @apiName inventory_sys/read/:id
 * @apiGroup InventorySys
 * @apiVersion 0.0.1
 * @apiDescription 读取帐盘明细
 *
 * @apiSuccess {int} id 帐盘ID
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {int} uid 盘点人ID
 * @apiSuccess {string} uname 盘点人姓名
 * @apiSuccess {int} cuid 记账人ID
 * @apiSuccess {string} cuname 记账人姓名
 * @apiSuccess {string} createtime 盘点日期
 * @apiSuccess {string} checktime 记账日期
 * @apiSuccess {list} goods_list 商品清单
 * @apiSuccess {list} - 商品清单详情字段
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {int} total_sys 帐盘数目
 * @apiSuccess {int} total_phy 实盘数目
 *
 */

/**
 * @api {post} inventory_sys/read 读取帐盘列表
 * @apiName inventory_sys/read
 * @apiGroup InventorySys
 * @apiVersion 0.0.1
 * @apiDescription 读取帐盘列表
 *
 * @apiParam {int} status 状态1-未审核 2-已审核
 *
 * @apiSuccess {int} id 帐盘ID
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库姓名
 * @apiSuccess {int} uid 盘点人ID
 * @apiSuccess {string} uname 盘点人姓名
 * @apiSuccess {int} cuid 记账人ID
 * @apiSuccess {string} cuname 记账人姓名
 * @apiSuccess {string} createtime 盘点日期
 * @apiSuccess {string} checktime 记账日期
 * @apiSuccess {string} amount 金额
 * @apiSuccess {int} status 状态
 *
 */

function inventory_sys($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new InventorySys($id);

    switch($action){
        case 'create':
            init_log_oper($action, '创建帐盘');

            param_need($data, ['sid','type']); //必选
            param_check($data, ['sid,type' => "/^\d+$/"]);

            $data = format_data_ids($data, ['tids']);

            //判断仓库是否开启库存，没开启直接报错
            $s_model = new Store();
            $is_reserve = $s_model -> is_reserve($data['sid']);
            if(!$is_reserve){
                error(1610);
            }

            //清除未审核的帐盘、实盘数据
            $my_model -> clear($data['sid']);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);

            Power::set_oper($data);
            $data['status'] = 1;

            //生成帐盘
            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;
        case 'check':
            init_log_oper($action, '审核帐盘');
            if(!$id){
                error(1100);
            }

            //帐盘检测，是否有权限，状态等
            $c_res = $my_model->my_power($id, 1);

            Power::set_oper($data, 'cuid', 'cuname');

            $data['cid'] = $c_res['cid'];
            $data['cname'] = $c_res['cname'];
            $data['sid'] = $c_res['sid'];
            $data['sname'] = $c_res['sname'];

            $res = $my_model -> my_check($data);

            success(['id'=>$res]);
            break;
        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取帐盘详情');

                //帐盘检测，是否有权限，状态等
                $my_model->my_power($id, 0);

                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取帐盘列表');

                param_need($data, ['status']); //必选
                param_check($data, ['status' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }

            break;
        default:
            error(1100);
    }

}
