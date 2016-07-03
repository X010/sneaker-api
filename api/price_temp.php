<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * price_temp 促销调价单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */



/**
 * @api {post} price_temp/read 查询商品促销价格
 * @apiName price_temp/read
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库商品价格
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {int} gid 商品ID
 * @apiParam {int} status 状态 1-生效中 2-未生效 3-已过期
 *
 * @apiSuccess {int} id 促销记录ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} begintime 生效起始日期
 * @apiSuccess {string} endtime 生效截止日期
 * @apiSuccess {string} createtime 记录生成时间
 * @apiSuccess {string} in_price 促销价格
 * @apiSuccess {string} range 生效范围
 *
 */


/**
 * @api {post} price_temp/create_in 创建进货价促销调价单
 * @apiName price_temp/create_in
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 创建进货价促销调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 * @apiParam {string} begintime 生效起始时间
 * @apiParam {string} endtime 生效截止时间
 *
 * @apiSuccess {int} id 调价单号
 *
 */


/**
 * @api {post} price_temp/check_in 创建并审核入货价促销调价单
 * @apiName price_temp/check_in
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核入货价促销调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 * @apiParam {string} begintime 生效起始时间
 * @apiParam {string} endtime 生效截止时间
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price_temp/check_in/:id 修改并审核入货价促销调价单
 * @apiName price_temp/check_in/:id
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核入货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 * @apiParam {string} begintime 生效起始时间
 * @apiParam {string} endtime 生效截止时间
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price_temp/read_in 读取入货价促销调价单
 * @apiName price_temp/read_in
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 读取入货价促销调价单
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} status 状态1-未审核 2-已审核
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货临时调价 2-出货临时调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} memo 备注
 */

/**
 * @api {post} price_temp/read_in/:id 读取入货价促销调价单详情
 * @apiName price_temp/read_in/:id
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 读取入货价促销调价单详情
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货临时调价 2-出货临时调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {list} - goods_list字段详情
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} old_in_price 进货原价
 * @apiSuccess {string} begintime 生效起始时间
 * @apiSuccess {string} endtime 生效截止时间
 *
 */

/**
 * @api {post} price_temp/delete 取消调价单
 * @apiName price_temp/delete
 * @apiGroup PriceTemp
 * @apiVersion 0.0.1
 * @apiDescription 取消调价单
 *
 * @apiSuccess {int} id 调价单号
 *
 */

function price_temp($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new PriceTemp($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'read':
            //init_log_oper($action, '读取商品促销价格');
            param_need($data, ['sid','gid','status']); //必选

            $tp_model = new TempPrice();
            $res = $tp_model -> get_temp_list($data['status'], $data['gid'], $cid, $data['sid']);
            success($res);
            break;

        case 'create_in':
            init_log_oper($action, '创建进货价临时调价单');
            param_need($data, ['goods_list']); //必选

            $sids = get_value($data, 'sids');

            if($sids != -1){
                //获取批量仓库名称，仓库ID做逗号加工处理
                $data['snames'] = $my_model->get_snames($sids);
                $data['sids'] = format_sids_douhao($sids);
            }
            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['type'] = 1;
            $data['status'] = 1;

            //设置操作员
            Power::set_oper($data);

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check_in':
            init_log_oper($action, '审核进货价临时调价单');

            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                //检测是否操作本公司的调价单
                $my_model->my_power($id, 1, 1);

                $sids = get_value($data, 'sids');
                //如果传了sid，判断是否有sid权限
                if($sids != -1){
                    //获取批量仓库名称，仓库ID做逗号加工处理
                    $data['snames'] = $my_model->get_snames($sids);
                    $data['sids'] = format_sids_douhao($sids);
                }

                $data['type'] = 1;
                $data['cid'] = $cid;

                $my_model->my_check_temp($data, 'update');
            }
            else{
                param_need($data, ['goods_list']); //必选

                $sids = get_value($data, 'sids');

                //如果传了sid，判断是否有sid权限
                if($sids != -1){
                    //获取批量仓库名称，仓库ID做逗号加工处理
                    $data['snames'] = $my_model->get_snames($sids);
                    $data['sids'] = format_sids_douhao($sids);
                }
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['type'] = 1;

                //设置操作员
                Power::set_oper($data);

                $id = $my_model->my_check_temp($data, 'create');
            }
            success(['id'=>$id]);
            break;

        case 'read_in':
            if (isset($id)){
                //init_log_oper('read', '查看进价促销调价单详情');
                if (!is_numeric($id)) error(1100);
                $my_model->my_power($id, 0, 1);
                $res = $my_model->my_read();
            }else{ //获取列表
                //init_log_oper('read', '浏览进价促销调价单列表');
                //只搜当前用户cid和sid的订单
                $data['cid'] = $cid;

                param_check($data, [
                    'page,page_num' => "/^\d+$/",
                ]);
                $data['type'] = 1; //进价调价单

                $res = $my_model->my_read_list($data);
            }
            success($res);
            break;

        case 'delete':
            init_log_oper($action, '取消调价单');
            if (!is_numeric($id)) error(1100);

            $my_model->my_power($id, 1, 0);

            $data = ['status'=>9];
            Power::set_oper($data, 'cuid', 'cuname');

            $my_model->update_by_id($data); //修改订单

            success(['id'=>$id]);
            break;

        default:
            error(1100);
    }

}
