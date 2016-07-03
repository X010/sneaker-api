<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * read 查询信息（无需权限管理）
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} read/company/:id 查询公司详情
 * @apiName read/company/:id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询公司详情（当id为0时，读取当前登录用户所属的公司）
 *
 * @apiSuccess {int} id 公司ID
 * @apiSuccess {string} name 公司名称
 * @apiSuccess {string} simple_name 公司简称
 * @apiSuccess {string} address 公司地址
 * @apiSuccess {string} areapro 省名称
 * @apiSuccess {string} areacity 市名称
 * @apiSuccess {string} areazone 区名称
 * @apiSuccess {string} gtids 经营范围，商品ID用逗号分隔
 * @apiSuccess {int} type 公司类型：1-厂商 2-一级经销商 3-二级经销商 4-零售商
 * @apiSuccess {string} tax_no *税号
 * @apiSuccess {string} account_no *帐号
 * @apiSuccess {string} license 营业执照
 * @apiSuccess {string} fax 传真
 * @apiSuccess {string} phone 电话
 * @apiSuccess {string} lawrep 企业法人
 * @apiSuccess {string} contactor 联系人姓名
 * @apiSuccess {string} contactor_phone 联系人电话
 * @apiSuccess {string} email Email地址
 * @apiSuccess {string} basedate 基准日，1到28之间
 * @apiSuccess {string} memo 备注
 * @apiSuccess {int} status 状态：1-正常 0-停用
 * @apiSuccess {int} iserp 是否开通ERP 1-已开通 0-未开通
 * @apiSuccess {int} ismall 是否开通下单商城 1-已开通 0-未开通
 * @apiSuccess {string} updatetime 最后更新时间
 * @apiSuccess {string} createtime 创建时间
 *
 */

/**
 * @api {post} read/company 浏览公司列表
 * @apiName read/company
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览公司列表，列表字段详情参照“查询公司详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} search 模糊查询条件
 * @apiParam {int} type 公司类型
 *
 */

/**
 * @api {post} read/company_goods/:id 查询公司商品详情
 * @apiName read/company_goods/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询公司商品详情
 *
 * @apiSuccess {int} id 商品价格表ID
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {string} gcode 商品CODE
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gunit 商品单位
 * @apiSuccess {string} gspec 商品规格
 * @apiSuccess {int} gbid 商品品牌ID
 * @apiSuccess {string} gbname 商品品牌名称
 * @apiSuccess {int} gtid 商品类型ID
 * @apiSuccess {string} gtname 商品类型名称
 * @apiSuccess {int} gisbind 是否捆绑商品 0-不是 1-是
 * @apiSuccess {int} limit_buy 是否限制采购 0-不限制 1-限制
 * @apiSuccess {string} in_price 进货价格
 * @apiSuccess {string} out_price1 出货价格1
 * @apiSuccess {string} out_price2 出货价格2
 * @apiSuccess {string} out_price3 出货价格3
 * @apiSuccess {string} out_price4 出货价格4
 * @apiSuccess {array} goods 商品基本信息详情
 * @apiSuccess {array} goods_supplier 商品供应商列表
 * @apiSuccess {array} gtids 商品类型ID树形，从父节点到子节点列出
 */

/**
 * @api {post} read/company_goods 浏览公司商品详情
 * @apiName read/company_goods
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 公司商品详情
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} limit_buy 是否限制采购 0-不限制 1-限制
 * @apiParam {string} search 模糊检索字段
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 */

/**
 * @api {post} read/company_goods_type_tree 读取树形商品类型
 * @apiName read/company_goods_type_tree
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 读取树形商品类型
 *
 * @apiSuccess {int} id 商品类型ID
 * @apiSuccess {int} pId 商品类型父节点ID
 * @apiSuccess {string} name 商品类型名称
 */

/**
 * @api {post} read/company_goods_type/:id 查询子商品类型
 * @apiName read/company_goods_type/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询指定父级商品类型所有子类型
 *
 * @apiSuccess {int} id 商品类型ID
 * @apiSuccess {string} code 商品类型编码
 * @apiSuccess {string} name 商品类型名称
 * @apiSuccess {string} py_name 名称拼音首字母
 * @apiSuccess {string} createtime 商品类型创建时间
 * @apiSuccess {string} updatetime 商品类型上次更新时间
 *
 */

/**
 * @api {post} read/company_goods_type 查询根商品类型
 * @apiName read/company_goods_type
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 浏览所有根级商品类型，列表字段详情参照“查询子商品类型”接口
 *
 */

/**
 * @api {post} read/customer_recommend 推荐客户列表
 * @apiName read/customer_recommend
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 推荐客户列表
 *
 * @apiSuccess {int} id 客户公司ID
 * @apiSuccess {string} name 客户公司名称
 * @apiSuccess {int} type 客户类型
 * @apiSuccess {int} areatype 地域类型
 * @apiSuccess {string} areapro 省
 * @apiSuccess {string} areacity 市
 * @apiSuccess {string} areazone 区
 * @apiSuccess {string} gtnames 经营范围
 * @apiSuccess {string} license 营业执照
 * @apiSuccess {string} contactor 联系人
 * @apiSuccess {string} contactor_phone 联系人号码
 *
 */

/**
 * @api {post} read/customer 浏览客户关系列表
 * @apiName read/customer
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览客户关系列表，列表字段详情参照“添加客户关系”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {int} cctype 客户公司类型
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 */

/**
 * @api {post} read/goods/:id 查询商品详情
 * @apiName read/goods/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询商品详情
 *
 * @apiSuccess {int} id 商品ID
 * @apiSuccess {string} code 商品编码
 * @apiSuccess {string} name 商品名称
 * @apiSuccess {string} py_name 名称拼音首字母
 * @apiSuccess {string} spec 商品规格
 * @apiSuccess {int} bid 商品品牌ID
 * @apiSuccess {int} tid 商品类型ID
 * @apiSuccess {int} ispkg 是否大小包装  1-是 0-不是
 * @apiSuccess {int} isbind 是否捆绑商品 1-是 0-不是
 * @apiSuccess {string} trademark 商标
 * @apiSuccess {int} valid_period 商品有效期，单位天
 * @apiSuccess {int} price_type 是否可调价 1-是 0-不是
 * @apiSuccess {double} shipping_price 出货价格
 * @apiSuccess {double} tax_rate 税率
 * @apiSuccess {int} pkgspec 包装规格
 * @apiSuccess {int} marteting 营销方式
 * @apiSuccess {int} factory 厂家
 * @apiSuccess {int} salerate 联销率
 * @apiSuccess {string} autoadd 是否自动补货
 * @apiSuccess {int} place 产地
 * @apiSuccess {int} output_tax 销项税率
 * @apiSuccess {double} distribution 配货方式
 * @apiSuccess {double} distribution_units 配货单位
 * @apiSuccess {double} barcode 条形码
 * @apiSuccess {string} createtime 商品创建时间
 * @apiSuccess {string} updatetime 商品上次更新时间
 *
 *
 */

/**
 * @api {post} read/goods 浏览商品列表
 * @apiName read/goods
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览商品列表，列表字段详情参照“查询商品详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} code 按照code来进行精确查询
 * @apiParam {string} name 按照name来进行模糊匹配
 *
 *
 */

/**
 * @api {post} read/goods_type/:id 查询子商品类型
 * @apiName read/goods_type/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询指定父级商品类型所有子类型
 *
 * @apiSuccess {int} id 商品类型ID
 * @apiSuccess {string} code 商品类型编码
 * @apiSuccess {string} name 商品类型名称
 * @apiSuccess {string} py_name 名称拼音首字母
 * @apiSuccess {string} createtime 商品类型创建时间
 * @apiSuccess {string} updatetime 商品类型上次更新时间
 *
 */

/**
 * @api {post} read/goods_type 查询根商品类型
 * @apiName read/goods_type
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览所有根级商品类型，列表字段详情参照“查询子商品类型”接口
 *
 */

/**
 * @api {post} read/inventory_sys 检测仓库帐盘
 * @apiName read/inventory_sys
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 检测仓库下是否有未审核的帐盘
 *
 * @apiParam {int} sid 仓库ID
 *
 * @apiSuccess {int} id 帐盘ID
 *
 */

/**
 * @api {post} read/price_all 查询所有仓库商品价格
 * @apiName read/price_all
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询所有仓库商品价格
 *
 * @apiParam {int} gid 商品ID
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 *
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {string} gcode 商品编码
 * @apiSuccess {string} gbarcode 商品条码
 * @apiSuccess {string} gname 商品名称
 * @apiSuccess {list} price 价格列表
 * @apiSuccess {list} - *price字段详情
 * @apiSuccess {int} type 价格类型 1-公司 2-仓库
 * @apiSuccess {int} id 公司ID，或仓库ID
 * @apiSuccess {string} name 公司名称，或仓库名称
 * @apiSuccess {string} in_price 进货价
 * @apiSuccess {string} out_price1 出货价1
 * @apiSuccess {string} out_price2 出货价2
 * @apiSuccess {string} out_price3 出货价3
 * @apiSuccess {string} out_price4 出货价4
 *
 */

/**
 * @api {post} read/role/:id 查询角色详情
 * @apiName read/role/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询角色详情
 *
 * @apiSuccess {int} id 角色ID
 * @apiSuccess {string} name 角色名称
 * @apiSuccess {int} level 角色等级
 * @apiSuccess {string} createtime 角色创建时间
 * @apiSuccess {string} updatetime 角色上次更新时间
 *
 */

/**
 * @api {post} read/role 浏览角色列表
 * @apiName read/role
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览角色列表，列表字段详情参照“查询角色详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} level 按照level来进行精确查询
 * @apiParam {string} name 按照name来进行模糊匹配
 *
 */

/**
 * @api {post} read/store/:id 查询仓库详情
 * @apiName read/store/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询仓库详情
 *
 * @apiSuccess {int} id 仓库ID
 * @apiSuccess {string} code 仓库编码
 * @apiSuccess {string} name 仓库名称
 * @apiSuccess {string} address 仓库地址
 * @apiSuccess {string} phone 仓库电话号码
 * @apiSuccess {string} contactor 仓库联系人
 * @apiSuccess {int} type 仓库类型 1-单店 2-连锁店 3-加盟店 4-配货中心 9-总店
 * @apiSuccess {string} area 仓库区域编码
 * @apiSuccess {string} license 仓库营业执照
 * @apiSuccess {string} memo 仓库备注
 * @apiSuccess {string} createtime 仓库创建时间
 * @apiSuccess {string} updatetime 仓库上次更新时间
 *
 */

/**
 * @api {post} read/store 浏览仓库列表
 * @apiName read/store
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览仓库列表，列表字段详情参照“查询仓库详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} code 按照code来进行精确查询
 * @apiParam {string} name 按照name来进行模糊匹配
 * @apiParam {int} status 状态：1-正常 / 0-停用
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 */

/**
 * @api {post} read/store_mine 浏览权限内的仓库列表
 * @apiName read/store_mine
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览权限内的仓库列表，列表字段详情参照“查询仓库详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} code 按照code来进行精确查询
 * @apiParam {string} name 按照name来进行模糊匹配
 *
 */

/**
 * @api {post} read/goods_in 查询进货仓库商品
 * @apiName read/goods_in
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询进货仓库商品
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} in_sid 进货仓库ID
 * @apiParam {int} out_sid 出货仓库ID
 * @apiParam {string} search 搜索字段
 *
 * @apiSuccess {int} id 商品价格表ID
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {int} gname 商品名称
 * @apiSuccess {int} gcode 商品CODE
 * @apiSuccess {string} price 价格
 * @apiSuccess {int} bid 品牌ID
 * @apiSuccess {string} bname 品牌名称
 * @apiSuccess {int} tid 类型ID
 * @apiSuccess {string} tname 类型名称
 * @apiSuccess {string} spec 规格
 * @apiSuccess {string} trademark 商标
 * @apiSuccess {int} ispkg 是否大包装
 *
 */

/**
 * @api {post} read/goods_out 查询出货仓库商品
 * @apiName read/goods_out
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询进货仓库商品
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {int} in_sid 进货仓库ID
 * @apiParam {int} out_sid 出货仓库ID
 * @apiParam {string} search 搜索字段
 *
 * @apiSuccess {int} id 商品价格表ID
 * @apiSuccess {int} in_cid 进货公司ID
 * @apiSuccess {int} in_sid 进货仓库ID
 * @apiSuccess {int} out_cid 出货公司ID
 * @apiSuccess {int} out_sid 出货仓库ID
 * @apiSuccess {int} gid 商品ID
 * @apiSuccess {int} gname 商品名称
 * @apiSuccess {int} gcode 商品CODE
 * @apiSuccess {string} price 价格
 * @apiSuccess {int} bid 品牌ID
 * @apiSuccess {string} bname 品牌名称
 * @apiSuccess {int} tid 类型ID
 * @apiSuccess {string} tname 类型名称
 * @apiSuccess {string} spec 规格
 * @apiSuccess {string} trademark 商标
 * @apiSuccess {int} ispkg 是否大包装
 *
 */

/**
 * @api {post} read/supplier_recommend 推荐供应商列表
 * @apiName read/supplier_recommend
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 推荐供应商列表
 *
 * @apiSuccess {int} id 供应商公司ID
 * @apiSuccess {string} name 供应商公司名称
 * @apiSuccess {string} gtnames 经营范围
 * @apiSuccess {string} license 营业执照
 * @apiSuccess {string} contactor 联系人
 * @apiSuccess {string} contactor_phone 联系人号码
 *
 */

/**
 * @api {post} read/supplier 浏览供应商关系列表
 * @apiName read/supplier
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览供应商关系列表，列表字段详情参照“添加供应商关系”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 *
 */

/**
 * @api {post} read/user/:id 查询员工个人资料
 * @apiName read/user/id
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 查询员工详情
 *
 * @apiSuccess {int} id 员工ID
 * @apiSuccess {string} code 员工编码
 * @apiSuccess {string} username 登陆账号
 * @apiSuccess {string} truename 员工真实姓名
 * @apiSuccess {string} idcard 员工身份证
 * @apiSuccess {int} sid 员工所属门店id
 * @apiSuccess {string} rids 员工角色id集合，用逗号分隔，如"1,2,3"
 * @apiSuccess {string} worktype 工种
 * @apiSuccess {string} email 电子邮箱
 * @apiSuccess {string} phone 员工手机号码
 * @apiSuccess {string} memo 员工备注
 * @apiSuccess {int} admin 是否管理员 1-是 0-不是
 * @apiSuccess {string} createtime 员工创建时间
 * @apiSuccess {string} updatetime 员工上次更新时间
 *
 */

/**
 * @api {post} read/user 浏览员工列表
 * @apiName read/user
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 浏览员工列表，列表字段详情参照“查询员工详情”接口
 *
 * @apiParam {int} page 当前第几页
 * @apiParam {int} page_num 每页几条
 * @apiParam {string} begin_time 查询起始时间
 * @apiParam {string} end_time 查询截止时间
 * @apiParam {string} code 按照code来进行精确查询
 * @apiParam {string} name 按照name来进行模糊匹配
 * @apiParam {string} orderby 排序字段：如 code^desc 按code倒序 id^asc 按ID正序
 *
 *
 */

/**
 * @api {post} read/chg_password 修改密码
 * @apiName read/chg_password
 * @apiGroup Read
 * @apiVersion 0.0.1
 * @apiDescription 修改密码
 *
 * @apiParam {string} old_password *旧密码，用于验证
 * @apiParam {string} password *新密码
 *
 *
 */


    function read($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;

    switch($action){
        case 'supplier':
            //  supplier/read
            if(isset($id)){
                //init_log_oper($action, '查询供应商信息明细');
                $c_model = new Company();
                $res = $c_model->read_by_id($id);

                $s_model = new Supplier();
                $res[0]['supplier'] = $s_model->read_one([
                    'sid'=>$cid,
                    'scid'=>$id
                ]);

                $allow_edit = 0;
                if($res[0]['iserp'] == 0 && $res[0]['create_cid'] == $cid){
                    $allow_edit = 1;
                }
                $res[0]['allow_edit'] = $allow_edit;

                success($res[0]);
            }
            else{
                //init_log_oper($action, '查询供应商信息');
                param_check($data, ['cctype,page,page_num' => "/^\d+$/"]);
                $data['cid'] = $cid;
                $data['orderby'] = 'scname^asc';
                $my_model = new Supplier($id);
                $res = $my_model->read_list($data);
                success($res);
            }
            break;

        case 'customer':
            //   customer/read
            if(isset($id)){
                //init_log_oper($action, '查询客户信息明细');
                $c_model = new Company();
                $res = $c_model->read_by_id($id);
                //当读取非ERP公司时，取默认第一个用户的用户名信息返回
                if(!$res[0]['iserp']){
                    $u_model = new User();
                    $u_res = $u_model->get_first_user($id);
                    if($u_res){
                        $res[0]['username'] = $u_res['username'];
                    }
                }

                $cs_model = new CustomerSalesman();
                $res[0]['salesman_list'] = $cs_model->read_list_nopage([
                    'cid'=>$cid,
                    'ccid'=>$id
                ]);

                $cu_model = new Customer();
                $res[0]['customer'] = $cu_model->read_one([
                    'cid'=>$cid,
                    'ccid'=>$id
                ]);

                $allow_edit = 0;
                if($res[0]['iserp'] == 0 && $res[0]['create_cid'] == $cid){
                    $allow_edit = 1;
                }
                $res[0]['allow_edit'] = $allow_edit;

                success($res[0]);
            }
            else{
                //init_log_oper($action, '查询客户信息');
                param_check($data, ['cctype,page,page_num' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $data['vip_type'] = 1;
                $my_model = new Customer($id);

                $res = $my_model->view_list($data);
                if($res['count']){
                    $res['data'] = Change::go($res['data'], 'sid', 'sname', 'o_store');
                }
                success($res);
            }
            break;

        case 'customer_tmp':
            //   customer/read
            $my_model = new CustomerTmp($id);
            if(isset($id)){
                //init_log_oper($action, '查询未审核客户信息明细');

                $res = $my_model->read_by_id();
                if(!$res){
                    error(1210);
                }
                $o_model = new User();
                $o_res = $o_model->read_by_id($res[0]['suid']);
                $res[0]['suname'] = $o_res[0]['name'];
                $res[0]['belong'] = $o_res[0]['belong'];
                //$res = Change::go($res, 'suid', 'suname', 'o_user');

                $geo_res = $app->db->select('o_geolocation', '*', ['AND'=>['ccid'=>$id,'source'=>2]]);
                if($geo_res){
                    if($geo_res[0]['baidu_address']){
                        $res[0]['formatted_address'] = $geo_res[0]['baidu_address'];
                    }
                    elseif($geo_res[0]['baidu_latitude'] && $geo_res[0]['baidu_longgitude']){
                        $mall_model = new Mall();
                        $address = $mall_model->get_location($geo_res[0]['baidu_latitude'], $geo_res[0]['baidu_longgitude']);
                        $res[0]['formatted_address'] = $address;
                    }
                }

                success($res[0]);
            }
            else{
                //init_log_oper($action, '查询未审核客户信息列表');
                param_check($data, ['cctype,page,page_num' => "/^\d+$/"]);

                $data['cid'] = $cid;
                //$data['status'] = '0';
                $res = $my_model->read_list($data);
                if($res['count']){
                    //$res['data'] = Change::go($res['data'], 'sid', 'sname', 'o_store');
                    $res['data'] = Change::go($res['data'], 'suid', 'suname', 'o_user');
                }

//                foreach($res['data'] as $key=>$val){
//                    $res['data'][$key]['ctype'] += 1;
//                }

                success($res);
            }

            break;

        case 'vip':
            if(isset($id)){
                //init_log_oper($action, '查询会员信息明细');
                $c_model = new Company();
                $res = $c_model->read_by_id($id);
                //当读取非ERP公司时，取默认第一个用户的用户名信息返回
                if(!$res[0]['iserp']){
                    $u_model = new User();
                    $u_res = $u_model->get_first_user($id);
                    if($u_res){
                        $res[0]['username'] = $u_res['username'];
                    }
                }

                $cu_model = new Customer();
                $res[0]['customer'] = $cu_model->read_one([
                    'cid'=>$cid,
                    'ccid'=>$id
                ]);

                $allow_edit = 0;
                if($res[0]['iserp'] == 0 && $res[0]['create_cid'] == $cid){
                    $allow_edit = 1;
                }
                $res[0]['allow_edit'] = $allow_edit;

                success($res[0]);
            }
            else{
                //init_log_oper($action, '查询客户信息');
                param_check($data, ['cctype,page,page_num' => "/^\d+$/"]);

                $data['cid'] = $cid;
                $data['vip_type'] = 2;
                $my_model = new Customer($id);

                $res = $my_model->view_list($data);
                if($res['count']){
                    $res['data'] = Change::go($res['data'], 'sid', 'sname', 'o_store');
                }
                success($res);
            }
            break;

        case 'store':
            //  store/read
            $my_model = new Store($id);
            if(isset($id)){
                //init_log_oper($action, '查询仓库详情');
                if(!is_numeric($id)){
                    error(1100);
                }

                $res = $my_model->my_read();

                success($res[0]);
            }
            else{
                //init_log_oper($action, '浏览仓库列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                if(!isset($data['cid'])){
                    $data['cid'] = $cid;
                }
                //仓库默认使用正序
                if(!isset($data['orderby'])){
                    $data['orderby'] = 'id^asc';
                }

                //$data['status'] = 1;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'store_mine':
            //  store/read_mine
            $my_model = new Store($id);

            //init_log_oper($action, '浏览仓库列表');
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            Power::set_my_sids($data, 'cid', 'id');

            //仓库默认使用正序
            if(!isset($data['orderby'])){
                $data['orderby'] = 'id^asc';
            }
            $data['status'] = 1;
            $res = $my_model -> read_list($data);
            success($res);
            break;

        case 'car':
            $my_model = new Car($id);
            if(isset($id)){
                //init_log_oper($action, '查询车辆详情');
                if(!is_numeric($id)){
                    error(1100);
                }

                $res = $my_model->read_by_id();
                if(!$res){
                    error(1801);
                }
                success($res[0]);
            }
            else{
                //init_log_oper($action, '浏览车辆列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                if(!isset($data['cid'])){
                    $data['cid'] = $cid;
                }

                if(get_value($data, 'search')){
                    $data['search'] = strtoupper($data['search']);
                }

                //$data['status'] = 1;
                $res = $my_model -> read_list($data);
                success($res);
            }
            break;

        case 'user':
            //  user/read
            $my_model = new User($id);
            if($id){
                //init_log_oper($action, '查询员工个人资料');
                if(!is_numeric($id)){
                    error(1100);
                }
                $res = $my_model -> read_by_id();
                if(!isset($res[0])){
                    error(1342);
                }
                //如果不是本公司的员工则报错
                if($res[0]['cid'] != $cid){
                    error(1347);
                }

                //商品类型归溯到根，返回到前段（用于树的展示）
                if($res[0]['group_id']){
                    $ug_model = new UserGroup();
                    $res[0]['gtids'] = $ug_model->read_tree_by_id($res[0]['group_id'], $res[0]['cid']);
                    $res[0]['group_name'] = $ug_model->get_name_by_id('o_user_group', $res[0]['group_id']);
                }
                else{
                    $res[0]['gtids'] = '';
                }

//                $res = Change::go($res, 'rids', 'rnames', 's_role');
//                $res = Change::go($res, 'sids', 'snames', 'o_store');

                unset($res[0]['password']);
                success($res[0]);
            }
            else{
                //init_log_oper($action, '读取员工列表'); //尽早记录操作日志
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                //默认加上用户本公司条件
                $data['status'] = 1;
                $data['cid'] = $cid;
                $data['admin'] = '0';

                //查找父类型下的所有子类型作为条件
                $group_id = get_value($data, 'group_id');
                if($group_id){
                    $ug_model = new UserGroup();
                    $group_ids = $ug_model->get_ids_by_fid($group_id, $cid);
                    $data['group_id'] = $group_ids;
                }

                $res = $my_model -> read_list($data);

//                if($res['count']){
//                    $res['data'] = Change::go($res['data'], 'rids', 'rnames', 's_role');
//                    $res['data'] = Change::go($res['data'], 'sids', 'snames', 'o_store');
//                }
                success($res);
            }
            break;

        case 'goods_in':
            //  store_goods/read_in
            $my_model = new StoreGoods($id);
            //init_log_oper($action, '查询商品进货价信息');
            param_need($data, ['in_sid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data = format_data_ids($data, ['barcodes']);

            $data['in_cid'] = $cid;
            $data['status'] = 1;

            $cg_model = new CompanyGoods();

            //查找供应商下的所有商品
            $gid_list = [];
            if(get_value($data, 'out_cid')){
                $gs_model = new GoodsSupplier();
                $gs_res = $gs_model -> read_list([
                    'cid' => $data['in_cid'],
                    'scid' => $data['out_cid']
                ]);

                if(!$gs_res['count']){
                    error(3011);
                }
                foreach($gs_res['data'] as $val){
                    $gid_list[] = $val['gid'];
                }
            }

            $barcodes = get_value($data, 'barcodes');
            $barcode_list = [];
            $type_list = [];
            if($barcodes){
                $res = $app->db->select('r_input_code','*',[
                    'input_code'=>explode(',',$barcodes)
                ]);
                $gids = [];
                foreach($res as $val){
                    $barcode_list[$val['input_code']] = $val['gid'];
                    $type_list[$val['input_code']] = $val['itype'];
                    $gids[] = $val['gid'];
                }

                if($gid_list){
                    if($gids){
                        $temp = array_intersect($gid_list, $gids);
                        $gid_list = dict2list($temp);
                        if(!$gid_list){
                            $gid_list = 'null';
                        }
                    }
                    else{
                        $gid_list = 'null';
                    }
                }
                else{
                    if($gids){
                        $gid_list = $gids;
                    }
                    else{
                        $gid_list = 'null';
                    }
                }
            }

            if($gid_list){
                $data['gid'] = $gid_list;
            }

            //首先通过公司查找公司商品列表
            $res = $cg_model -> read_list($data);

            $temp_data = [];
            if($res['count']){
                $db_where = [
                    'in_sid' => $data['in_sid']
                ];
                if($gid_list){
                    $db_where['gid'] = $gid_list;
                }
                $sg_res = $my_model -> read_list_nopage($db_where);
                $sg_list = [];
                foreach($sg_res as $val){
                    $sg_list[$val['gid']] = $val;
                }

                //每一条记录查询是否有仓库记录，如果有则覆盖公司记录
                foreach($res['data'] as $key=>$val){

                    $sg_data = get_value($sg_list, $val['gid']);

                    if($sg_data){
                        $res['data'][$key]['in_price'] = $sg_data['in_price'];
                        $res['data'][$key]['out_price1'] = $sg_data['out_price1'];
                        $res['data'][$key]['out_price2'] = $sg_data['out_price2'];
                        $res['data'][$key]['out_price3'] = $sg_data['out_price3'];
                        $res['data'][$key]['out_price4'] = $sg_data['out_price4'];
                    }
                    $temp_data[$val['gid']] = $res['data'][$key];
                }
            }

            if($barcodes){
                $result = [];
                $barcodes2 = explode(',', $barcodes);
                foreach($barcodes2 as $val){
                    $gid = get_value($barcode_list, $val);
                    $temp = get_value($temp_data, $gid);
                    if($temp){
                        $temp['isbig'] = get_value($type_list, $val);
                        $temp['barcode2'] = $val;
                        $result[] = $temp;
                    }
                }
                $res['data'] = $result;
                $res['count'] = count($result);
                $res['page_count'] = 1;
            }

            //是否启用原价
            $old_price = get_value($data, 'old_price');

            //赋予每条记录更多的商品信息
            if($res['count']){
                $res['data'] = $my_model -> read_in($res['data'], $data['in_sid'], $old_price);
                $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
                $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');

            }
            success($res);
            break;

        case 'goods_out':
            //  store_goods/read_out
            $my_model = new StoreGoods($id);
            //init_log_oper($action, '查询商品出货价信息');
            param_need($data, ['out_sid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data = format_data_ids($data, ['barcodes']);

            $cg_model = new CompanyGoods();

            $cg_data = [
                'in_cid' => $cid,
                'status' => 1
            ];
            if(get_value($data, 'search')){
                $cg_data['search'] = $data['search'];
            }
            $cg_data['page'] = get_value($data, 'page');
            $cg_data['page_num'] = get_value($data, 'page_num');

            $barcodes = get_value($data, 'barcodes');
            $barcode_list = [];
            $type_list = [];
            $gid_list = [];
            if($barcodes){
                $res = $app->db->select('r_input_code','*',[
                    'input_code'=>explode(',',$barcodes)
                ]);
                $gids = [];
                foreach($res as $val){
                    $barcode_list[$val['input_code']] = $val['gid'];
                    $type_list[$val['input_code']] = $val['itype'];
                    $gids[] = $val['gid'];
                }

                if($gids){
                    $gid_list = $gids;
                }
                else{
                    $gid_list = 'null';
                }
            }

            if($gid_list){
                $cg_data['gid'] = $gid_list;
            }
            $temp_data = [];
            //首先通过公司查找公司商品列表
            $res = $cg_model -> read_list($cg_data);
            if($res['count']){
                //每一条记录查询是否有仓库记录，如果有则覆盖公司记录
                foreach($res['data'] as $key=>$val){
                    $res2 = $my_model -> read_one([
                        'in_sid' => $data['out_sid'],
                        'gid' => $val['gid']
                    ]);
                    //每个价格单独覆盖
                    if($res2){
                        if($res2['in_price'] && $res2['in_price'] != '0.00'){
                            $res['data'][$key]['in_price'] = $res2['in_price'];
                        }
                        if($res2['out_price1'] && $res2['out_price1'] != '0.00'){
                            $res['data'][$key]['out_price1'] = $res2['out_price1'];
                        }
                        if($res2['out_price2'] && $res2['out_price2'] != '0.00'){
                            $res['data'][$key]['out_price2'] = $res2['out_price2'];
                        }
                        if($res2['out_price3'] && $res2['out_price3'] != '0.00'){
                            $res['data'][$key]['out_price3'] = $res2['out_price3'];
                        }
                        if($res2['out_price4'] && $res2['out_price4'] != '0.00'){
                            $res['data'][$key]['out_price4'] = $res2['out_price4'];
                        }
                    }
                    $temp_data[$val['gid']] = $res['data'][$key];
                }
            }

            if($barcodes){
                $result = [];
                $barcodes2 = explode(',', $barcodes);
                foreach($barcodes2 as $val){
                    $gid = get_value($barcode_list, $val);
                    $temp = get_value($temp_data, $gid);
                    if($temp){
                        $temp['isbig'] = get_value($type_list, $val);
                        $temp['barcode2'] = $val;
                        $result[] = $temp;
                    }
                }
                $res['data'] = $result;
                $res['count'] = count($result);
                $res['page_count'] = 1;
            }


            $in_cid = get_value($data, 'in_cid');
            if(!$in_cid){
                $data['in_cid'] = -1;
            }

            //赋予每条记录更多的商品信息
            if($res['count']){
                $res['data'] = $my_model -> read_out($res['data'], $data['in_cid'], $data['out_sid']);
                $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
                $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');
            }
            success($res);
            break;

        case 'goods_old':
            //init_log_oper($action, '查询商品最老批次价信息');
            param_need($data, ['sid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data = format_data_ids($data, ['barcodes']);

            $cg_model = new CompanyGoods();

            $cg_data = [
                'in_cid' => $cid,
                'sid' => $data['sid'],
                'status' => 1,
            ];
            if(get_value($data, 'search')){
                $cg_data['search'] = $data['search'];
            }
            $cg_data['page'] = get_value($data, 'page');
            $cg_data['page_num'] = get_value($data, 'page_num');

            $barcodes = get_value($data, 'barcodes');
            $barcode_list = [];
            $type_list = [];
            $gid_list = [];
            if($barcodes){
                $res = $app->db->select('r_input_code','*',[
                    'input_code'=>explode(',',$barcodes)
                ]);
                $gids = [];
                foreach($res as $val){
                    $barcode_list[$val['input_code']] = $val['gid'];
                    $type_list[$val['input_code']] = $val['itype'];
                    $gids[] = $val['gid'];
                }

                if($gids){
                    $gid_list = $gids;
                }
                else{
                    $gid_list = 'null';
                }
            }

            if($gid_list){
                $cg_data['gid'] = $gid_list;
            }

            //首先通过公司查找公司商品列表
            $res = $cg_model -> my_read_old($cg_data);

            $temp_data = [];
            if($res['count']){
                foreach($res['data'] as $key=>$val){
                    $temp_data[$val['gid']] = $res['data'][$key];
                }
            }

            if($barcodes){
                $result = [];
                $barcodes2 = explode(',', $barcodes);
                foreach($barcodes2 as $val){
                    $gid = get_value($barcode_list, $val);
                    $temp = get_value($temp_data, $gid);
                    if($temp){
                        $temp['isbig'] = get_value($type_list, $val);
                        $temp['barcode2'] = $val;
                        $result[] = $temp;
                    }
                }
                $res['data'] = $result;
                $res['count'] = count($result);
                $res['page_count'] = 1;
            }


            //赋予每条记录更多的商品信息
            if($res['count']){
                $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
                $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');
            }
            success($res);
            break;

        case 'goods_inventory':
            //init_log_oper($action, '检索实盘录入商品列表');
            param_need($data, ['sid']);
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

            $data = format_data_ids($data, ['barcodes']);

            $cg_model = new CompanyGoods();

            $cg_data = [
                'in_cid' => $cid,
                'sid' => $data['sid'],
                'status' => 1,
            ];
            if(get_value($data, 'search')){
                $cg_data['search'] = $data['search'];
            }

            //判断当前帐盘单有没有商品分类限制
            $is_model = new InventorySys();
            $is_res = $is_model->read_one([
                'status'=>1,
                'sid'=>$data['sid']
            ]);
            if(!$is_res){
                error(3301);
            }
            $is_res = format_data_ids($is_res, ['tids']);
            if($is_res['tids']){
                $cgt_model = new CompanyGoodsType();
                $cg_data['gtid'] = $cgt_model->get_ids_by_fids($is_res['tids']);
                //$cg_data['gtid'] = explode(',', $is_res['tids']);
            }

            $cg_data['page'] = get_value($data, 'page');
            $cg_data['page_num'] = get_value($data, 'page_num');

            $barcodes = get_value($data, 'barcodes');
            $barcode_list = [];
            $type_list = [];
            $gid_list = [];
            if($barcodes){
                $res = $app->db->select('r_input_code','*',[
                    'input_code'=>explode(',',$barcodes)
                ]);
                $gids = [];
                foreach($res as $val){
                    $barcode_list[$val['input_code']] = $val['gid'];
                    $type_list[$val['input_code']] = $val['itype'];
                    $gids[] = $val['gid'];
                }

                if($gids){
                    $gid_list = $gids;
                }
                else{
                    $gid_list = 'null';
                }
            }

            if($gid_list){
                $cg_data['gid'] = $gid_list;
            }

            //首先通过公司查找公司商品列表
            $res = $cg_model -> my_read_noprice($cg_data);

            $temp_data = [];
            if($res['count']){
                foreach($res['data'] as $key=>$val){
                    $temp_data[$val['gid']] = $res['data'][$key];
                }
            }

            if($barcodes){
                $result = [];
                $barcodes2 = explode(',', $barcodes);
                foreach($barcodes2 as $val){
                    $gid = get_value($barcode_list, $val);
                    $temp = get_value($temp_data, $gid);
                    if($temp){
                        $temp['isbig'] = get_value($type_list, $val);
                        $temp['barcode2'] = $val;
                        $result[] = $temp;
                    }
                }
                $res['data'] = $result;
                $res['count'] = count($result);
                $res['page_count'] = 1;
            }


            //赋予每条记录更多的商品信息
            if($res['count']){
                $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
                $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');
            }
            success($res);
            break;

        case 'company_goods':
            //  company_goods/read
            $my_model = new CompanyGoods($id);
            //init_log_oper($action, '查询公司商品信息');
            if(isset($id)){
                //init_log_oper($action, '查询公司商品档案详情');
                if(!is_numeric($id)){
                    error(1100);
                }
                $my_model -> my_power($id);
                $res = $my_model -> my_read();
                success($res[0]);
            }
            else{
                //init_log_oper($action, '查询公司商品档案列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                $data['in_cid'] = $cid;
                $res = $my_model -> my_read_list($data);
                if($res['count']){
                    $res['data'] = Change::go($res['data'], 'gbid', 'gbname', 'o_goods_brand');
                    $res['data'] = Change::go($res['data'], 'gtid', 'gtname', 'o_company_goods_type');
                }
                success($res);
            }
            break;

        case 'goods':
            //  goods/read
            $my_model = new Goods($id);
            //init_log_oper($action, '查询系统商品信息');
            if($id){
                //init_log_oper($action, '查询商品明细');
                if(!is_numeric($id)) error(1100);

                $res = $my_model -> read_by_id();
                if(!isset($res[0])){
                    error(1401);
                }
                success($res[0]);
            }
            else{
                //init_log_oper($action, '浏览商品列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                $res = $my_model->my_read($data);

                //商品类型和品牌增加返回name
//                if($res['count']){
//                    $res['data'] = Change::go($res['data'], 'bid', 'bname', 'o_goods_brand');
//                    $res['data'] = Change::go($res['data'], 'tid', 'tname', 'o_goods_type');
//                }
                success($res);
            }
            break;

        case 'goods_type':
            //  goods_type/read
            $my_model = new GoodsType($id);
            //init_log_oper($action, '查询系统商品分类');
            if($id){
                //init_log_oper($action, '查询子商品类型');
                if(!is_numeric($id)){
                    error(1100);
                }
                $res = $my_model -> read_tree($id);
                success($res);
            }
            else{
                //init_log_oper($action, '查询根商品类型');
                $res = $my_model -> read_tree();
                success($res);
            }
            break;

        case 'goods_type_tree':
            $my_model = new GoodsType($id);
            //init_log_oper($action, '查询系统树形商品类型');
            $res = $my_model -> my_read_tree();
            success($res);
            break;

        case 'goods_brand':
            $my_model = new GoodsBrand($id);
            $res = $my_model->read_list($data);
            success($res);
            break;

        case 'company_goods_type':
            //  company_goods_type/read
            $my_model = new CompanyGoodsType($id);
            if($id){
                //init_log_oper($action, '查询公司子商品类型');
                if(!is_numeric($id)){
                    error(1100);
                }
                $res = $my_model -> read_tree($id);
                success($res);
            }
            else{
                //init_log_oper($action, '查询公司根商品类型');
                $res = $my_model -> read_tree();
                success($res);
            }
            success();
            break;

        case 'company_goods_type_tree':
            //  company_goods_type/read_tree
            $my_model = new CompanyGoodsType($id);
            //init_log_oper($action, '查询公司树形商品类型');
            $res = $my_model -> my_read_tree($cid);
            success($res);
            break;

        case 'user_group':
            $my_model = new UserGroup($id);
            if($id){
                //init_log_oper($action, '查询公司子商品类型');
                if(!is_numeric($id)){
                    error(1100);
                }
                $res = $my_model -> read_tree($id);
                success($res);
            }
            else{
                //init_log_oper($action, '查询公司根商品类型');
                $res = $my_model -> read_tree();
                success($res);
            }
            success();
            break;

        case 'user_group_tree':
            $my_model = new UserGroup($id);
            //init_log_oper($action, '查询公司树形商品类型');
            $res = $my_model -> my_read_tree($cid);
            success($res);
            break;

        case 'company':
            //  company/read
            $my_model = new Company($id);
            if (isset($id)){
                //init_log_oper('read', '查看公司资料');
                if (!is_numeric($id)) error(1100);
                if ($cid != -1){
                    if ($id == 0){
                        //当用0时读取用户身份公司信息
                        $my_model->set_id($cid);
                    }
                    $res = $my_model->read_by_id($id);
                    //当读取非ERP公司时，取默认第一个用户的用户名信息返回
                    if(!$res[0]['iserp']){
                        $u_model = new User();
                        $u_res = $u_model->get_first_user($id);
                        if($u_res){
                            $res[0]['username'] = $u_res['username'];
                        }
                    }
                } else {
                    $res = [];
                }
                $res = $res ? $res[0] : $res;
            }else{
                //init_log_oper('read', '浏览公司列表');
                param_check($data, ['page,page_num' => "/^\d+$/"]);

                $res = $my_model->read_list($data);

                $new_data = [];
                $cids = [];
                foreach($res['data'] as $key=>$val){
                    $new_data[] = [
                        'id' => $val['id'],
                        'code' => $val['code'],
                        'name' => $val['name']
                    ];
                    $cids[] = $val['id'];
                }

                if(get_value($data, 'search_type') == 'customer'){
                    $c_model = new Customer();
                    $c_res = $c_model->read_list_nopage([
                        'cid'=>$cid,
                        'ccid'=>$cids
                    ]);
                    $c_list = [];
                    foreach($c_res as $val){
                        $c_list[$val['ccid']] = $val;
                    }

                    foreach($new_data as $key=>$val){
                        if(get_value($c_list, $val['id'])){
                            $new_data[$key]['customer'] = 1;
                        }
                        else{
                            $new_data[$key]['customer'] = 0;
                        }
                    }
                }

                if(get_value($data, 'search_type') == 'supplier'){
                    $s_model = new Supplier();
                    $s_res = $s_model->read_list_nopage([
                        'cid'=>$cid,
                        'scid'=>$cids
                    ]);
                    $s_list = [];
                    foreach($s_res as $val){
                        $s_list[$val['scid']] = $val;
                    }

                    foreach($new_data as $key=>$val){
                        if(get_value($s_list, $val['id'])){
                            $new_data[$key]['supplier'] = 1;
                        }
                        else{
                            $new_data[$key]['supplier'] = 0;
                        }
                    }
                }

                $res['data'] = $new_data;
            }
            success($res);
            break;

        case 'role':
            //  role/read
            $my_model = new Role($id);
            if($id){
                //init_log_oper($action, '查询角色权限');
                $res = $my_model -> read_power();
                success($res);
            }
            else{
                //init_log_oper($action, '浏览角色列表');
                param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);

                //通过inc字段标识，是否读取包含系统预设的角色名称
                if(isset($data['inc']) && $data['inc']=='true' && $cid!=-1){
                    $data['cid']=-1;
                }
                else{
                    $data['cid']=$cid; //填写cid
                }
                $data['orderby'] = 'name^asc';
                $res = $my_model -> read_list($data);

                success($res);
            }
            break;

        case 'chg_self':
            //  user/update(只有修改自己的时候才使用这个接口，修改其它人的接口不变)
            $my_model = new User($app->Sneaker->uid);
            init_log_oper($action, '修改个人资料');
            param_check($data, [
                'phone' => "/^[0-9]{11}$/"
            ]);
            if(isset($data['password'])) unset($data['password']);
            if(isset($data['sids'])) unset($data['sids']);
            if(isset($data['rids'])) unset($data['rids']);

            //本公司下不能有重名的员工
            if(get_value($data, 'name')){
                $res = $my_model->has([
                    'cid'=>$cid,
                    'name'=>$data['name'],
                    'id[!]'=>$id
                ]);
                if($res){
                    error(1349);
                }
            }

            $my_model -> my_update($data);
            success();
            break;

        case 'chg_password':
            //  user/chgpwd
            $my_model = new User();
            init_log_oper($action, '修改密码');
            param_need($data, ['password','old_password']);
            param_check($data, [
                'password,old_password' => "/^\w+$/",
            ]);
            $uid = $app->Sneaker->uid;
            $my_model -> change_password($uid, $data['password'], $data['old_password']);
            success();
            break;

        case 'reserve':
            $my_model = new Reserve();
            //init_log_oper($action, '查看仓库商品库存');
            param_need($data, ['sid','gids']);
            $data = format_data_ids($data, ['gids']);
            $data['gids'] = explode(',', $data['gids']);
            $result = $my_model->get_reserve($cid, $data['sid'], $data['gids']);
            success($result);
            break;

        case 'menu':
            $menu = $app->config('menu');
            $user_info = $app->Sneaker->user_info;
            $power = $user_info['power'];
            $result = [];
            foreach($menu as $key=>$val){
                if($val === 1 || $val === 0){
                    $result[$key] = $val;
                }
                else{
                    if(in_array($val, $power)){
                        $result[$key] = 1;
                    }
                    else{
                        $result[$key] = 0;
                    }
                }
            }
            success($result);
            break;

        case 'price_all':
            //init_log_oper($action, '读取所有商品仓库价格列表');
            param_check($data, ['page' => "/^\d+$/",'page_num' => "/^\d+$/"]);
            $cg_model = new CompanyGoods();
            $sg_model = new StoreGoods();
            $s_model = new Store();

            $data['in_cid'] = $cid;

            $cg_res = $cg_model -> read_list($data);

            //先找到公司下所有的仓库
            $s_res = $s_model->read_list_nopage([
                'cid'=>$cid,
                'status'=>1,
            ]);
            $sids = [];
            $snames = [];
            $result = [];
            foreach($s_res as $val){
                $sids[] = $val['id'];
                $snames[] = $val['name'];
            }
            //遍历公司级别商品列表，将价格替换成仓库价格（如果有）
            foreach($cg_res['data'] as $key=>$val){
                $price = [];
                $price[] = [
                    'type' => 1,
                    'id' => $cid,
                    'name' => $app->Sneaker->cname,
                    'in_price' => $val['in_price'],
                    'out_price1' => $val['out_price1'],
                    'out_price2' => $val['out_price2'],
                    'out_price3' => $val['out_price3'],
                    'out_price4' => $val['out_price4']
                ];
                $sg_res = $sg_model -> read_list([
                    'in_cid'=>$data['in_cid'],
                    'gid'=>$val['gid']
                ]);
                foreach($sids as $key2=>$sid){
                    $flag = 0;
                    foreach($sg_res['data'] as $sg_res_temp){
                        if($sid == $sg_res_temp['in_sid']){
                            $price[] = [
                                'type' => 2,
                                'id' => $sid,
                                'name' => $snames[$key2],
                                'in_price' => $sg_res_temp['in_price']=='0.00'?$val['in_price']:$sg_res_temp['in_price'],
                                'out_price1' => $sg_res_temp['out_price1']=='0.00'?$val['out_price1']:$sg_res_temp['out_price1'],
                                'out_price2' => $sg_res_temp['out_price2']=='0.00'?$val['out_price2']:$sg_res_temp['out_price2'],
                                'out_price3' => $sg_res_temp['out_price3']=='0.00'?$val['out_price3']:$sg_res_temp['out_price3'],
                                'out_price4' => $sg_res_temp['out_price4']=='0.00'?$val['out_price4']:$sg_res_temp['out_price4'],
                            ];
                            $flag = 1;
                            break;
                        }
                    }
                    if(!$flag){
                        $price[] = [
                            'type' => 2,
                            'id' => $sid,
                            'name' => $snames[$key2],
                            'in_price' => $val['in_price'],
                            'out_price1' => $val['out_price1'],
                            'out_price2' => $val['out_price2'],
                            'out_price3' => $val['out_price3'],
                            'out_price4' => $val['out_price4']
                        ];
                    }
                }
                $result[] = [
                    'gid'=>$val['gid'],
                    'gcode'=>$val['gcode'],
                    'gname'=>$val['gname'],
                    'gbarcode'=>$val['gbarcode'],
                    'price'=>$price
                ];
            }
            $cg_res['data'] = $result;
            success($cg_res);
            break;

        case 'customer_recommend':
            //init_log_oper($action, '推荐客户');
            $my_model = new Customer();
            $res = $my_model->my_recommend($cid);

            $new_data = [];
            foreach($res as $key=>$val){
                $new_data[] = [
                    'id' => $val['id'],
                    'code' => $val['code'],
                    'name' => $val['name']
                ];
            }
            success($new_data);
            break;

        case 'supplier_recommend':
            //init_log_oper($action, '推荐供应商');
            $my_model = new Supplier();
            $res = $my_model->my_recommend($cid);

            $new_data = [];
            foreach($res as $key=>$val){
                $new_data[] = [
                    'id' => $val['id'],
                    'code' => $val['code'],
                    'name' => $val['name']
                ];
            }
            success($new_data);
            break;

        case 'inventory_sys':
            //init_log_oper($action, '获取当前帐盘');
            $my_model = new InventorySys();

            param_need($data, ['sid']); //必选
            param_check($data, ['sid' => "/^\d+$/"]);

            //获取该仓库当前正在盘点的帐盘
            $res = $my_model -> get_now_id($data['sid']);
            if(!$res){
                error(3301);
            }
            success(['id' => $res]);
            break;

        case 'get_user_by_name':
            //init_log_oper($action, '通过姓名获取用户ID');

            $my_model = new User();

            param_need($data, ['name']);
            $ret = $my_model -> read_one([
                'cid' => $app->Sneaker->cid,
                'name' => $data['name']
            ]);
            unset($ret['password']);
            success([
                'result' => $ret
            ]);
            break;

        case 'get_customer_by_name':
            //init_log_oper($action, '通过客户名获取客户ID');

            $my_model = new Customer();

            param_need($data, ['name']);
            $ret = $my_model -> read_one([
                'cid' => $app->Sneaker->cid,
                'ccname' => $data['name']
            ]);
            success([
                'result' => $ret
            ]);
            break;

        case 'get_supplier_by_name':
            //init_log_oper($action, '通过供应商名获取供应商ID');

            $my_model = new Supplier();

            param_need($data, ['name']);
            $ret = $my_model -> read_one([
                'cid' => $app->Sneaker->cid,
                'scname' => $data['name']
            ]);
            success([
                'result' => $ret
            ]);
            break;

        case 'sorting_stock':
            //init_log_oper($action, '获取出库单据列表');

            $my_model = new Sorting();

            param_need($data, ['sid']);
            $data['cid'] = $cid;

            $res = $my_model->read_stock($data);
            success($res);
            break;

        case 'message_remind':
            //消息提醒
            $ret = $app->Sneaker->user_info;
            $power = get_value($ret, 'power');
            $admin = get_value($ret, 'admin');
            $result = [
                'customer_count' => 0,
                'sell_order_count' => 0,
                'buy_order_count' => 0,
                'forcheck_stockout_count' => 0,
                'forsettle_stockout_count' => 0
            ];

            //如果有审核客户通过的权限
            if(in_array('/customer/check_pass', $power) || $admin){
                //1、待审客户数
                $data = [];
                $my_model = new CustomerTmp();
                $data['cid'] = $cid;
                $data['status'] = '0';
                $result['customer_count'] = $my_model->count(['AND'=>$data]);
            }

            //2、待处理客户订单
            if(in_array('/order/read_out', $power)  || $admin){
                $data = [];

                $my_model = new Order();
                $data['out_cid'] = $cid;
                $data['status'] = 2;
                $data['ouid'] = Null;
                $data['type'] = 1; //采购订单

                if($admin != 1){
                    //如果不是管理员，判断仓库权限
                    $data['out_sid'] = $app->Sneaker->sids;
                }
                $result['sell_order_count'] = $my_model->count(['AND'=>$data]);
            }

            //3、待处理采购订单
            if(in_array('/order/read_in', $power) || $admin){
                $data = [];
                $my_model = new Order();

                $data['status'] = 2;
                $data['iuid'] = Null;
                $data['type'] = 1; //采购订单
                $data['in_cid'] = $cid;

                if($admin != 1){
                    //如果不是管理员，判断仓库权限
                    $data['in_sid'] = $app->Sneaker->sids;
                }
                $result['buy_order_count'] = $my_model->count(['AND'=>$data]);
            }

            //4、待处理待审出库单
            if(in_array('/stock_out/check', $power) || $admin){
                $data = [];
                $my_model = new StockOut();

                Power::set_my_sids($data);
                $data['status'] = 2;
                $data['type'] = 1;

                $result['forcheck_stockout_count'] = $my_model->count(['AND'=>$data]);
            }

            //5、待处理待结算出库单
            if(in_array('/stock_out/check', $power) || $admin){
                $data = [];
                $my_model = new StockOut();

                Power::set_my_sids($data);
                $data['status'] = [3,4];
                $data['settle_status'] = 0;
                $data['type'] = 1;
                $result['forsettle_stockout_count'] = $my_model->count(['AND'=>$data]);
            }

            success($result);
            break;

        case 'gps':
            param_need($data, ['latitude','longgitude']);
            $mall_model = new Mall();
            $address = $mall_model->get_location($data['latitude'], $data['longgitude']);
            success(['address'=>$address]);
            break;

        default:
            error(1100);
    }

}
