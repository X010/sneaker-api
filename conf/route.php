<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Configuration for route
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     conf
 */


/**
 * Configuration for route
 */
$app->config('route', [    
    //系统配置
    'config_module' =>[
        'menu'  => '系统配置#0',
        'module' => '模块权限#1',
        'hook'   => ['init','auth'],
    ],
    'config_balance' => [
        'menu'  => '系统配置#0',
        'module'=> '结算形式#2',
        'hook'   => ['init','auth'],
    ],

    'config_rank' => [
        'menu'  => '系统配置#0',
        'module'=> '订单紧急度#3',
        'hook'   => ['init','auth'],
    ],

    // login
	'login' => [
		'menu'  => '系统登录#1',
		'module'=> '系统登录#1',
        'hook'  => ['init'],
        'method'=> 'any'
	],
    //基本资料
    'company' => [
		'menu'  => '基本资料#2',
        'module'=> '公司信息#1',
    ],
	'store' => [
		'menu'  => '基本资料#2',
		'module'=> '仓库管理#2',
	],
	'user' => [
		'menu'  => '基本资料#2',
		'module'=> '员工管理#3',
	],
    'customer' => [
        'menu'  => '基本资料#2',
        'module'=> '客户管理#4',
    ],
    'supplier' => [
        'menu'  => '基本资料#2',
        'module'=> '供应商管理#5',
    ],
    'company_goods' => [
        'menu'  => '基本资料#2',
        'module'=> '商品管理#7',
    ],
    'company_goods_type' => [
        'menu'  => '基本资料#2',
        'module'=> '商品类型管理#8',
    ],
    'goods_supplier' => [
        'menu'  => '基本资料#2',
        'module'=> '商品供应商关系管理#9',
    ],
    'car' => [
        'menu'  => '基本资料#2',
        'module'=> '车辆管理#10',
    ],
    'user_group' => [
        'menu'  => '基本资料#2',
        'module'=> '员工组管理#11',
    ],
    'goods' => [
        'menu'  => '基本资料#2',
        'module'=> '平台商品管理#12',
    ],
    'goods_warning' => [
        'menu'  => '基本资料#2',
        'module'=> '商品库存预警管理#13',
    ],

    //系统设置
    'operation_log' => [
        'menu'  => '系统设置#3',
        'module'=> '操作日志#1',
    ],
	'role' => [
		'menu'  => '系统设置#3',
		'module'=> '角色管理#2',
	],

    //进销
    'order' => [
        'menu'  => '进销流程#4',
        'module'=> '订单管理#1',
    ],
    'stock_in' => [
        'menu'  => '进销流程#4',
        'module'=> '入库单管理#2',
    ],
    'stock_out' => [
        'menu'  => '进销流程#4',
        'module'=> '出库单管理#3',
    ],
    'task' => [
        'menu'  => '进销流程#4',
        'module'=> '出库单管理#4',
    ],

    //库存管理
    'transfer' => [
        'menu'  => '库存管理#5',
        'module'=> '调拨单管理#1',
    ],
    'loss' => [
        'menu'  => '库存管理#5',
        'module'=> '报损单管理#2',
    ],
    'overflow' => [
        'menu'  => '库存管理#5',
        'module'=> '报溢单管理#3',
    ],

    //退货管理
    'return_out' => [
        'menu'  => '退货管理#6',
        'module'=> '采购退货单管理#1', //退货出库
    ],
    'return_in' => [
        'menu'  => '退货管理#6',
        'module'=> '销售退回单管理#2', //退货入库
    ],

    //价格管理
    'price' => [
        'menu'  => '价格管理#7',
        'module'=> '调价单管理#1',
    ],
    'price_temp' => [
        'menu'  => '价格管理#7',
        'module'=> '促销调价单管理#2',
    ],

    //盘点管理
    'inventory_sys' => [
        'menu'  => '盘点管理#8',
        'module'=> '帐盘单管理#1',
    ],
    'inventory_phy' => [
        'menu'  => '盘点管理#8',
        'module'=> '实盘单管理#2',
    ],

    //财务管理
    'settle_supplier' => [
        'menu'  => '财务管理#9',
        'module'=> '供应商结算管理#1',
    ],
    'settle_customer' => [
        'menu'  => '财务管理#9',
        'module'=> '客户结算管理#2',
    ],
    'debit_note' => [
        'menu'  => '财务管理#9',
        'module'=> '收款单管理#3',
    ],
    'payment_note' => [
        'menu'  => '财务管理#9',
        'module'=> '付款单管理#4',
    ],
    'settle_proxy_supplier' => [
        'menu'  => '财务管理#9',
        'module'=> '供应商代销结算管理#5',
    ],
    'commission' => [
        'menu'  => '财务管理#9',
        'module'=> '业务员提成结算管理#6',
    ],

    //仓储管理
    'sorting' => [
        'menu'  => '仓储管理#10',
        'module'=> '分拣派车单管理#1',
    ],

    //客服管理
    'visit' => [
        'menu'  => '客服管理#11',
        'module'=> '客服回访管理#1',
    ],

    //报表管理
    'f_base' => [
        'menu'  => '报表管理#90',
        'module'=> '基础资料管理#1',
    ],
    'f_reserve' => [
        'menu'  => '报表管理#90',
        'module'=> '库存报表管理#2',
    ],
    'f_sell' => [
        'menu'  => '报表管理#90',
        'module'=> '销售报表管理#3',
    ],
    'f_finance' => [
        'menu'  => '报表管理#90',
        'module'=> '财务报表管理#4',
    ],
    'f_salesman' => [
        'menu'  => '报表管理#90',
        'module'=> '业务员报表管理#5',
    ],

    //通用
    'exists' => [
        'menu'  => '通用#99',
        'module'=> '判断记录是否存在#1',
        'hook'   => ['init','auth'],
    ],
    'read' => [
        'menu'  => '通用#99',
        'module'=> '读取信息#2',
        'hook'   => ['init','auth'],
    ],
    'plan' => [
        'menu'  => '通用#99',
        'module'=> '计划任务#3',
        'method'=> 'get',
        'hook'   => ['init'],
    ],
    'mall' => [
        'menu'  => '通用#99',
        'module'=> '商城接口#4',
        'method'=> 'any',
        'hook'   => ['init'],
    ],
    'notice' => [
        'menu'  => '通用#99',
        'module'=> '通知接口#5',
        'hook'   => ['init'],
    ],
    'a_salesman' => [
        'menu'  => '通用#99',
        'module'=> '业务员APP接口#6',
        'method' => 'get',
        'hook'   => ['init','auth'],
    ],
    'app' => [
        'customer' => [
            'menu'  => '通用#99',
            'module'=> '客户APP接口#7',
            'method' => 'any',
            'hook'   => ['init','auth'],
        ],
        'pbs' => [
            'menu'  => '通用#99',
            'module'=> '鹏博士APP接口#9',
            'method' => 'any',
            'hook'   => ['init','auth'],
        ],
    ],
    'vip' => [
        'menu'  => '基本资料#2',
        'module'=> '会员管理#4',
    ],

    'wx_customer' => [
        'menu'  => '通用#99',
        'module'=> '客户APP接口#8',
        'method' => 'any',
        'hook'   => ['init','auth'],
    ],
    'wx_pay' => [
        'menu'  => '通用#99',
        'module'=> '微信公众号支付相关接口#8',
        'method' => 'any',
        'hook'   => ['init'],
    ],
    'wx_user' => [
        'menu'  => '通用#99',
        'module'=> '微信公众号用户相关接口#8',
        'method' => 'any',
        'hook'   => ['init'],
    ],
    'wx_coupon'=>[
        'menu'  => '通用#99',
        'module'=> '微信公众号用户相关接口#8',
        'method' => 'any',
        'hook'   => ['init'],
    ],
]);
