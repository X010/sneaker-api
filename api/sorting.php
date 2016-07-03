<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * sorting 派车拣货单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} sorting/create 新建拣货派车单
 * @apiName sorting/create
 * @apiGroup Sorting
 * @apiVersion 0.0.1
 * @apiDescription 新建拣货派车单
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {int} car_id *车辆ID
 * @apiParam {int} duid *司机ID
 * @apiParam {string} areapro 省
 * @apiParam {string} areacity 市
 * @apiParam {string} areazone 区
 * @apiParam {string} stock_list *单据号ID集合，用逗号分隔
 *
 */

/**
 * @api {post} sorting/delete/:id 作废拣货派车单
 * @apiName sorting/delete/:id
 * @apiGroup Sorting
 * @apiVersion 0.0.1
 * @apiDescription 作废拣货派车单
 *
 *
 */

/**
 * @api {post} sorting/read 浏览拣货派车单列表
 * @apiName sorting/read
 * @apiGroup Sorting
 * @apiVersion 0.0.1
 * @apiDescription 浏览拣货派车单列表
 *
 * @apiParam {int} status 单据状态检索
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} duid 司机ID
 * @apiParam {string} begin_date 开始时间
 * @apiParam {string} end_date 截止时间
 * @apiParam {string} search 按单据号ID检索
 *
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} status 单据状态
 * @apiSuccess {int} car_id 车辆ID
 * @apiSuccess {string} car_license 车牌号
 * @apiSuccess {float} car_ton 车辆载重
 * @apiSuccess {int} duid 司机ID
 * @apiSuccess {string} duname 司机名称
 * @apiSuccess {string} craetetime 创建时间
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {string} stock_list *单据号ID集合，用逗号分隔
 */

/**
 * @api {post} sorting/read/:id 查看拣货派车单详情
 * @apiName sorting/read/:id
 * @apiGroup Sorting
 * @apiVersion 0.0.1
 * @apiDescription 查看拣货派车单详情
 *
 * @apiSuccess {int} sid 仓库ID
 * @apiSuccess {string} sname 仓库名称
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} status 单据状态
 * @apiSuccess {int} car_id 车辆ID
 * @apiSuccess {string} car_license 车牌号
 * @apiSuccess {float} car_ton 车辆载重
 * @apiSuccess {int} duid 司机ID
 * @apiSuccess {string} duname 司机名称
 * @apiSuccess {string} craetetime 创建时间
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {string} stock_list *单据号ID集合，用逗号分隔
 * @apiSuccess {string} sorting_glist 派车单商品列表
 * @apiSuccess {json} - 详情字段
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {string} stock_id 单据号
 * @apiSuccess {string} ccname 客户名称
 * @apiSuccess {int} total 商品数量
 * @apiSuccess {string} weight 商品箱重
 * @apiSuccess {int} group_total 单品总数
 * @apiSuccess {int} group_box_total 单品总箱数
 * @apiSuccess {string} group_weight 单品总重
 */

function sorting($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $my_model = new Sorting($id);
    switch($action){
        case 'create':
            init_log_oper($action, '创建拣货派车单');

            param_need($data, ['sid','car_id','duid','stock_list']); //必选
            param_check($data, ['sid,car_id,duid' => "/^\d+$/"]);

            $data = format_data_ids($data, ['stock_list']);

            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['sname'] = $my_model->get_name_by_id('o_store', $data['sid']);
            $data['duname'] = $my_model->get_name_by_id('o_user', $data['duid']);
            Power::set_oper($data);
            $data['status'] = 1;

            $ccid = get_value($data, 'ccid');
            if($ccid){
                $data['ccname'] = $my_model->get_name_by_id('o_company', $ccid);
            }

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'read':
            if(isset($id)) {
                //init_log_oper($action, '读取拣货派车单详情');

                //检测是否有权限，状态等
                $res = $my_model->read_by_id();
                if(!$res){
                    error(3000);
                }
                if($res[0]['cid'] != $cid){
                    error(8100);
                }
                $res = $my_model -> my_read();
                success($res);
            }
            else{
                //init_log_oper($action, '读取拣货派车单列表');
                $data['cid'] = $cid;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'delete':
            init_log_oper($action, '取消拣货派车单');
            if (!is_numeric($id)) error(1100);

            //检测是否有权限，状态等
            $res = $my_model->read_by_id();
            if(!$res){
                error(3000);
            }
            if($res[0]['cid'] != $cid){
                error(8100);
            }

            $my_model->my_delete();

            success(['id'=>$id]);
            break;

        default:
            error(1100);
    }

}
