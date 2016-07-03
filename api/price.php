<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * price 调价单管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */



/**
 * @api {post} price/read 查询仓库商品价格
 * @apiName price/read
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库商品价格
 *
 * @apiParam {int} sid *仓库ID
 * @apiParam {int} search 商品检索关键字
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gspec 规格
 * @apiSuccess {string} gunit 计量单位
 * @apiSuccess {json} store_price 仓库级别价格
 * @apiSuccess {json} - store_price字段详情
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 * @apiSuccess {json} company_price 公司级别价格
 * @apiSuccess {json} - company_price字段详情
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 *
 */



/**
 * @api {post} price/create_in 创建进货价调价单
 * @apiName price/create_in
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建进货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/create_out 创建出货价调价单
 * @apiName price/create_out
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建出货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} out_price1 商品新出货价1
 * @apiParam {float} out_price2 商品新出货价2
 * @apiParam {float} out_price3 商品新出货价3
 * @apiParam {float} out_price4 商品新出货价4
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/check_in 创建并审核入货价调价单
 * @apiName price/check_in
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核入货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/check_in/:id 修改并审核入货价调价单
 * @apiName price/check_in/:id
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核入货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} in_price 商品新进价
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/check_out 创建并审核出货价调价单
 * @apiName price/check_out
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核出货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} out_price1 商品新出货价1
 * @apiParam {float} out_price2 商品新出货价2
 * @apiParam {float} out_price3 商品新出货价3
 * @apiParam {float} out_price4 商品新出货价4
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/check_out/:id 修改并审核出货价调价单
 * @apiName price/check_out/:id
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 创建并审核出货价调价单
 *
 * @apiParam {int} sids 仓库ID列表，以逗号分隔(为空时修改公司默认价格)
 * @apiParam {int} isnow *是否立即生效(1-是 2-不是)
 * @apiParam {string} begintime *生效起始时间
 * @apiParam {json} goods_list 商品列表
 * @apiParam {json} - goods_list详情
 * @apiParam {int} gid 商品ID
 * @apiParam {float} out_price1 商品新出货价1
 * @apiParam {float} out_price2 商品新出货价2
 * @apiParam {float} out_price3 商品新出货价3
 * @apiParam {float} out_price4 商品新出货价4
 *
 * @apiSuccess {int} id 调价单号
 *
 */

/**
 * @api {post} price/read_in 读取入货价调价单
 * @apiName price/read_in
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 读取入货价调价单
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} status 状态1-未审核 2-已审核
 * @apiParam {int} gid 商品ID
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货调价 2-出货调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} begintime 生效起始时间
 * @apiSuccess {string} endtime 生效截止时间
 * @apiSuccess {string} memo 备注
 */

/**
 * @api {post} price/read_in/:id 读取入货价调价单详情
 * @apiName price/read_in/:id
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 读取入货价调价单详情
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货调价 2-出货调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} begintime 生效起始时间
 * @apiSuccess {string} endtime 生效截止时间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {list} - goods_list字段详情
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} old_in_price 进货原价
 */

/**
 * @api {post} price/read_out 读取出货价调价单
 * @apiName price/read_out
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 读取出货价调价单
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} status 状态1-未审核 2-已审核
 * @apiParam {int} gid 商品ID
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货调价 2-出货调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} begintime 生效起始时间
 * @apiSuccess {string} endtime 生效截止时间
 * @apiSuccess {string} memo 备注
 */

/**
 * @api {post} price/read_out/:id 读取出货价调价单详情
 * @apiName price/read_out/:id
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 读取出货价调价单详情
 *
 * @apiSuccess {int} id 调价单号
 * @apiSuccess {int} cid 公司ID
 * @apiSuccess {string} cname 公司名称
 * @apiSuccess {int} sids 仓库ID列表，以逗号分隔
 * @apiSuccess {string} snames 仓库名称，以逗号分隔
 * @apiSuccess {int} type 类型 1-进货调价 2-出货调价
 * @apiSuccess {int} status 状态 1-未审核 2-已审核
 * @apiSuccess {int} uid 操作员ID
 * @apiSuccess {string} uname 操作员名称
 * @apiSuccess {int} cuid 审核员ID
 * @apiSuccess {string} cuname 审核员名称
 * @apiSuccess {string} createtime 创建时间
 * @apiSuccess {string} updatetime 最近更新时间
 * @apiSuccess {string} checktime 审核时间
 * @apiSuccess {string} begintime 生效起始时间
 * @apiSuccess {string} endtime 生效截止时间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {list} - goods_list字段详情
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 * @apiSuccess {string} old_out_price1 原出货价1
 * @apiSuccess {string} old_out_price2 原出货价2
 * @apiSuccess {string} old_out_price3 原出货价3
 * @apiSuccess {string} old_out_price4 原出货价4
 */


/**
 * @api {post} price/delete 取消调价单
 * @apiName price/delete
 * @apiGroup Price
 * @apiVersion 0.0.1
 * @apiDescription 取消调价单
 *
 * @apiSuccess {int} id 调价单号
 *
 */

function price($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_model = new Price($id);
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'read':
            //init_log_oper($action, '读取商品仓库价格列表');
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data['in_cid'] = $cid;

            //取出仓库下的商品价格
            $in_sid = get_value($data, 'sid');
            $new_s_res = [];
            if($in_sid){
                $data['in_sid'] = $data['sid'];
                $sg_model = new StoreGoods();
                $s_res = $sg_model -> read_list($data);
                if($s_res['count']) {
                    foreach ($s_res['data'] as $val) {
                        $new_s_res[$val['gid']] = $val;
                    }
                }
            }

            //取出公司级别的默认价格
            $cg_model = new CompanyGoods();
            $c_res = $cg_model -> read_list($data);

            foreach($c_res['data'] as $key=>$val){
                $store_price = get_value($new_s_res, $val['gid']);
                //如果仓库没有记录，则仓库价格全部为默认
                if(!$store_price){
                    $store_price = [
                        'in_price'=>'默认',
                        'out_price1'=>'默认',
                        'out_price2'=>'默认',
                        'out_price3'=>'默认',
                        'out_price4'=>'默认'
                    ];
                }
                else{
                    //如果价格为0，也是自动默认价格
                    foreach($store_price as $key2=>$val2){
                        if($val2 == '0.00'){
                            $store_price[$key2] = '默认';
                        }
                    }
                }
                //组装返回
                $temp = [
                    'gid'=>$val['gid'],
                    'gcode'=>$val['gcode'],
                    'gname'=>$val['gname'],
                    'gbarcode'=>$val['gbarcode'],
                    'gspec'=>$val['gspec'],
                    'gunit'=>$val['gunit'],
                    'company_price'=>[
                        'in_price'=>$val['in_price'],
                        'out_price1'=>$val['out_price1'],
                        'out_price2'=>$val['out_price2'],
                        'out_price3'=>$val['out_price3'],
                        'out_price4'=>$val['out_price4']
                    ],
                    'store_price'=>$store_price
                ];
                $c_res['data'][$key] = $temp;
            }

            success($c_res);
            break;

        case 'create_in':
            init_log_oper($action, '创建进货价调价单');
            param_need($data, ['sids','isnow','begintime','goods_list']); //必选

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

        case 'create_out':
            init_log_oper($action, '创建出货价调价单');
            param_need($data, ['sids','isnow','begintime','goods_list']); //必选

            $sids = get_value($data, 'sids');

            if($sids != -1){
                //获取批量仓库名称，仓库ID做逗号加工处理
                $data['snames'] = $my_model->get_snames($sids);
                $data['sids'] = format_sids_douhao($sids);
            }
            $data['cid'] = $cid;
            $data['cname'] = $app->Sneaker->cname;
            $data['type'] = 2;
            $data['status'] = 1;

            //设置操作员
            Power::set_oper($data);

            $res = $my_model -> my_create($data);

            success(['id'=>$res]);
            break;

        case 'check_in':
            init_log_oper($action, '审核进货价调价单');

            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                //检测是否操作本公司的调价单
                $my_model->my_power($id, 1, 1);
                $sids = get_value($data, 'sids');
                if($sids != -1){
                    //获取批量仓库名称，仓库ID做逗号加工处理
                    $data['snames'] = $my_model->get_snames($sids);
                    $data['sids'] = format_sids_douhao($sids);

                }
                $data['type'] = 1;
                $data['cid'] = $cid;
                $my_model->my_check($data, 'update');
            }
            else{
                param_need($data, ['sids','isnow','begintime','goods_list']); //必选

                $sids = get_value($data, 'sids');
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

                $id = $my_model->my_check($data, 'create');
            }
            success(['id'=>$id]);
            break;

        case 'check_out':
            init_log_oper($action, '审核出货价调价单');
            Power::set_oper($data, 'cuid', 'cuname');
            $data['status'] = 2;
            $data['checktime'] = date('Y-m-d H:i:s');

            if(isset($id)){
                //检测是否操作本公司的调价单
                $my_model->my_power($id, 1, 2);
                $sids = get_value($data, 'sids');
                if($sids != -1){
                    //获取批量仓库名称，仓库ID做逗号加工处理
                    $data['snames'] = $my_model->get_snames($sids);
                    $data['sids'] = format_sids_douhao($sids);
                }
                $data['type'] = 2;
                $data['cid'] = $cid;
                $my_model->my_check($data, 'update');
            }
            else{
                param_need($data, ['sids','isnow','begintime','goods_list']); //必选

                $sids = get_value($data, 'sids');

                if($sids != -1){
                    //获取批量仓库名称，仓库ID做逗号加工处理
                    $data['snames'] = $my_model->get_snames($sids);
                    $data['sids'] = format_sids_douhao($sids);
                }
                $data['cid'] = $cid;
                $data['cname'] = $app->Sneaker->cname;
                $data['type'] = 2;

                //设置操作员
                Power::set_oper($data);

                $id = $my_model->my_check($data, 'create');
            }
            success(['id'=>$id]);
            break;

        case 'read_in':
            if (isset($id)){
                //init_log_oper('read', '查看进价调价单详情');
                if (!is_numeric($id)) error(1100);
                $my_model->my_power($id, 0, 1);
                $res = $my_model->my_read();
            }else{ //获取列表
                //init_log_oper('read', '浏览进价调价单列表');
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

        case 'read_out':
            if (isset($id)){
                //init_log_oper('read', '查看出价调价单详情');
                if (!is_numeric($id)) error(1100);
                $my_model->my_power($id, 0, 2);
                $res = $my_model->my_read();
            }else{ //获取列表
                //init_log_oper('read', '浏览出价调价单列表');
                //只搜当前用户cid和sid的订单
                $data['cid'] = $cid;

                param_check($data, [
                    'page,page_num' => "/^\d+$/",
                ]);
                $data['type'] = 2; //出价调价单

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
