<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * company_goods_type 公司商品类型管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} company_goods_type/create 新增根级商品类型
 * @apiName company_goods_type/create
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 新增根级商品类型
 *
 * @apiParam {string} name *商品类型名称
 *
 */

/**
 * @api {post} company_goods_type/create/:id 新增子级商品类型
 * @apiName company_goods_type/create/:id
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 新增商品类型实体操作(id:父级类型id)
 *
 * @apiParam {string} name *商品类型名称
 */

/**
 * @api {post} company_goods_type/update/:id 更新商品类型
 * @apiName company_goods_type/update
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 更新商品类型实体操作
 *
 * @apiParam {string} name *商品类型名称
 *
 */

/**
 * @api {post} company_goods_type/delete/:id 删除商品类型
 * @apiName company_goods_type/delete
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 删除商品类型实体操作
 *
 *
 */


/**
 * @api {post} company_goods_type/copy_system 复制系统商品类型
 * @apiName company_goods_type/copy_system
 * @apiGroup CompanyGoodsType
 * @apiVersion 0.0.1
 * @apiDescription 复制系统商品类型
 *
 */

function company_goods_type($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); 
	$app = \Slim\Slim::getInstance();
	$my_model = new CompanyGoodsType($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'create':
			init_log_oper($action, '添加公司商品类型');
			param_need($data, ['name']);
			//补充公司ID参数
			$data['cid'] = $cid;

			$res = $my_model -> my_create($data);
			success([
				'id' => $res
			]);
			break;
			
		case 'update':
			init_log_oper($action, '修改公司商品类型');
			if(!is_numeric($id)){
				error(1100);
			}
			param_need($data, ['name']);
			//检测数据合法性
			$my_model -> my_power();
			$my_model -> my_update($data);
			success();
			break;
			
		case 'delete':
			init_log_oper($action, '删除公司商品类型');
			if(!is_numeric($id)){
				error(1100);
			}
			//检测数据合法性
			$my_model -> my_power();
			$my_model -> my_delete();
			success();
			break;

		case 'copy_system':
			init_log_oper($action, '复制系统商品类型');
			$my_model -> my_copy();
			success();
			break;

		case 'flush':
			init_log_oper($action, '清空公司商品类型');
			$my_model -> my_flush();
			success();
			break;
			
		default:
			error('1100');
	}
	
}
