<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * f_base 基本资料报表
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} f_base/goods 查看商品报表
 * @apiName f_base/goods
 * @apiGroup FBase
 * @apiVersion 0.0.1
 * @apiDescription 查看商品报表
 *
 * @apiParam {string} tids 商品类型ID列表，以逗号分隔
 * @apiParam {string} scids 供应商ID列表，以逗号分隔
 * @apiParam {string} price_min 最小金额，为空时写0
 * @apiParam {string} price_max 最大金额，无上限时为空
 * @apiParam {string} search 关键字检索，匹配商品名称、code、barcode等字段
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {int} gbid 商品品牌ID
 * @apiSuccess {string} gbname 商品品牌名称
 * @apiSuccess {string} gtax_rate 商品税率
 * @apiSuccess {int} limit_buy 是否限制购买 1-不限制 2-限制
 * @apiSuccess {string} in_price 公司默认进货价
 * @apiSuccess {string} out_price1 公司默认出货价1
 * @apiSuccess {string} out_price2 公司默认出货价2
 * @apiSuccess {string} out_price3 公司默认出货价3
 * @apiSuccess {string} out_price4 公司默认出货价4
 * @apiSuccess {json} goods 商品其它基础信息
 * @apiSuccess {json} - goods字段
 * @apiSuccess {string} factory 厂商
 * @apiSuccess {string} isbind 是否捆绑 0-不是 1-是
 * @apiSuccess {string} place 产地
 * @apiSuccess {string} valid_period 保质期
 * @apiSuccess {json} goods_supplier 供应商信息
 * @apiSuccess {json} - goods_supplier字段
 * @apiSuccess {int} scid 供应商ID
 * @apiSuccess {string} scname 供应商名称
 * @apiSuccess {string} createtime 供应商创建时间
 *
 */

/**
 * @api {post} f_base/supplier 查看供应商报表
 * @apiName f_base/supplier
 * @apiGroup FBase
 * @apiVersion 0.0.1
 * @apiDescription 查看供应商报表
 *
 * @apiParam {string} types 公司类型，逗号分隔
 * @apiParam {string} gtids 经营范围，商品类型ID列表，以逗号分隔
 * @apiParam {string} areapro 省
 * @apiParam {string} areacity 市
 * @apiParam {string} areazone 区
 * @apiParam {string} search 关键字检索，匹配商品名称、code、barcode等字段
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} id 供应商ID
 * @apiSuccess {string} code 供应商编码
 * @apiSuccess {string} name 供应商名称
 * @apiSuccess {int} period 账期
 * @apiSuccess {string} account_no 账号
 * @apiSuccess {string} address 地址
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {int} basedate 基准日
 * @apiSuccess {string} contactor 联系人
 * @apiSuccess {string} contactor_phone 联系人电话
 * @apiSuccess {string} gtnames 经营范围名称
 * @apiSuccess {string} gtids 经营范围ID
 * @apiSuccess {int} iserp 是否ERP公司
 * @apiSuccess {string} lawrep 企业法人
 * @apiSuccess {string} license 营业执照
 * @apiSuccess {string} phone 电话
 * @apiSuccess {string} tax_no 税号
 * @apiSuccess {int} type 公司类型
 */

/**
 * @api {post} f_base/customer 查看客户报表
 * @apiName f_base/customer
 * @apiGroup FBase
 * @apiVersion 0.0.1
 * @apiDescription 查看客户报表
 *
 * @apiParam {string} types 公司类型，逗号分隔
 * @apiParam {string} gtids 经营范围，商品类型ID列表，以逗号分隔
 * @apiParam {string} areapro 省
 * @apiParam {string} areacity 市
 * @apiParam {string} areazone 区
 * @apiParam {string} search 关键字检索，匹配商品名称、code、barcode等字段
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} id 客户ID
 * @apiSuccess {string} code 客户编码
 * @apiSuccess {string} name 客户名称
 * @apiSuccess {int} period 账期
 * @apiSuccess {string} account_no 账号
 * @apiSuccess {string} address 地址
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {int} basedate 基准日
 * @apiSuccess {string} contactor 联系人
 * @apiSuccess {string} contactor_phone 联系人电话
 * @apiSuccess {string} gtnames 经营范围名称
 * @apiSuccess {string} gtids 经营范围ID
 * @apiSuccess {int} iserp 是否ERP公司
 * @apiSuccess {string} lawrep 企业法人
 * @apiSuccess {string} license 营业执照
 * @apiSuccess {string} phone 电话
 * @apiSuccess {string} tax_no 税号
 * @apiSuccess {int} type 公司类型
 */

/**
 * @api {post} f_base/salesman 查看业务员档案
 * @apiName f_base/salesman
 * @apiGroup FBase
 * @apiVersion 0.0.1
 * @apiDescription 查看业务员档案
 *
 * @apiParam {string} begin_date 起始日期
 * @apiParam {string} end_date 截止日期
 * @apiParam {int} suid 业务员ID
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} suid 业务员ID
 * @apiSuccess {string} suname 业务员名称
 * @apiSuccess {string} count 客户数量
 *
 */

/**
 * @api {post} f_base/goods_price_in(out) 查看商品价格
 * @apiName f_base/goods_price_in(out)
 * @apiGroup FBase
 * @apiVersion 0.0.1
 * @apiDescription 查看商品价格
 *
 * @apiParam {int} sid 仓库ID
 * @apiParam {string} search 商品关键字检索
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品计量单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {dict} store_price 仓库价格
 * @apiSuccess {dict} - store_price字段详情
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 *
 */

function f_base($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    switch($action){
        case 'goods':
            //查看商品档案
            $data = format_data_ids($data, ['tids','scids']);
            $cg_model = new CompanyGoods($id);
            $data['cid'] = $cid;
            $res = $cg_model->form_goods($data);
            success($res);
            break;
        case 'supplier':
            //查看供应商档案
            $data = format_data_ids($data, ['types','gtids']);
            $c_model = new Company($id);
            $data['cid'] = $cid;
            $res = $c_model->form_supplier($data);
            success($res);
            break;
        case 'customer':
            //查看客户档案
            $data = format_data_ids($data, ['types','gtids','suids']);
            $c_model = new Company($id);
            $data['cid'] = $cid;
            $res = $c_model->form_customer($data);
            success($res);
            break;

        case 'salesman':
            //查看业务员档案
            $c_model = new Customer($id);
            $data['cid'] = $cid;
            $res = $c_model->form_salesman($data);
            success($res);
            break;

        case 'goods_price_in':
        case 'goods_price_out':
            //查看商品价格
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data['in_cid'] = $cid;

            //取出公司级别的默认价格
            $cg_model = new CompanyGoods();
            $c_res = $cg_model -> read_list($data);
            $gid_list = [];
            foreach($c_res['data'] as $val){
                $gid_list[] = $val['gid'];
            }


            //取出仓库下的商品价格
            $in_sid = get_value($data, 'sid');
            $new_s_res = [];
            if($in_sid){
                $data['in_sid'] = $data['sid'];
                $data['gid'] = $gid_list;
                unset($data['search']);
                $sg_model = new StoreGoods();
                $s_res = $sg_model -> read_list($data);
                if($s_res['count']) {
                    foreach ($s_res['data'] as $val) {
                        $new_s_res[$val['gid']] = $val;
                    }
                }
            }

            //组装返回，判断权限
            $power = $app->Sneaker->user_info['power'];
            $admin = $app->Sneaker->user_info['admin'];
            $in_power = False;
            $out_power = False;

            //判断是否有入库价格权限和出库价格权限
            if($admin || in_array('/f_base/goods_price_in', $power)){
                $in_power = True;
            }
            if($admin || in_array('/f_base/goods_price_out', $power)){
                $out_power = True;
            }

            foreach($c_res['data'] as $key=>$val){
                $store_price = get_value($new_s_res, $val['gid']);
                //如果仓库没有记录，则仓库价格全部为默认
                if(!$store_price){
                    $store_price = [
                        'in_price'=>$val['in_price'],
                        'out_price1'=>$val['out_price1'],
                        'out_price2'=>$val['out_price2'],
                        'out_price3'=>$val['out_price3'],
                        'out_price4'=>$val['out_price4']
                    ];
                }
                else{
//                    //如果价格为0，也是自动默认价格
//                    foreach($store_price as $key2=>$val2){
//                        if($val2 == '0.00'){
//                            $store_price[$key2] = '默认';
//                        }
//                    }
                }

                $company_price = [
                    'in_price'=>$val['in_price'],
                    'out_price1'=>$val['out_price1'],
                    'out_price2'=>$val['out_price2'],
                    'out_price3'=>$val['out_price3'],
                    'out_price4'=>$val['out_price4']
                ];

                if(!$in_power){
                    unset($company_price['in_price']);
                    unset($store_price['in_price']);
                }
                if(!$out_power){
                    unset($company_price['out_price1']);
                    unset($company_price['out_price2']);
                    unset($company_price['out_price3']);
                    unset($company_price['out_price4']);
                    unset($store_price['out_price1']);
                    unset($store_price['out_price2']);
                    unset($store_price['out_price3']);
                    unset($store_price['out_price4']);
                }


                $temp = [
                    'gid'=>$val['gid'],
                    'gcode'=>$val['gcode'],
                    'gname'=>$val['gname'],
                    'gbarcode'=>$val['gbarcode'],
                    'gspec'=>$val['gspec'],
                    'gunit'=>$val['gunit'],
                    'company_price'=>$company_price,
                    'store_price'=>$store_price
                ];
                $c_res['data'][$key] = $temp;
            }

            success($c_res);
            break;

        default:
            error(1100);
    }

}
