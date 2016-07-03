<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * user_group 员工组管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} user_group/create 新增根级员工组
 * @apiName user_group/create
 * @apiGroup UserGroup
 * @apiVersion 0.0.1
 * @apiDescription 新增根级员工组
 *
 * @apiParam {string} name *员工组名称
 *
 */

/**
 * @api {post} user_group/create/:id 新增子级员工组
 * @apiName user_group/create/:id
 * @apiGroup UserGroup
 * @apiVersion 0.0.1
 * @apiDescription 新增员工组实体操作(id:父级类型id)
 *
 * @apiParam {string} name *员工组名称
 */

/**
 * @api {post} user_group/update/:id 更新员工组
 * @apiName user_group/update
 * @apiGroup UserGroup
 * @apiVersion 0.0.1
 * @apiDescription 更新员工组实体操作
 *
 * @apiParam {string} name *员工组名称
 *
 */

/**
 * @api {post} user_group/delete/:id 删除员工组
 * @apiName user_group/delete
 * @apiGroup UserGroup
 * @apiVersion 0.0.1
 * @apiDescription 删除员工组
 *
 *
 */

function user_group($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); 
	$app = \Slim\Slim::getInstance();
	$my_model = new UserGroup($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'create':
			init_log_oper($action, '添加员工组');
			param_need($data, ['name']);
			//补充公司ID参数
			$data['cid'] = $cid;

			$res = $my_model -> my_create($data);
			success([
				'id' => $res
			]);
			break;
			
		case 'update':
			init_log_oper($action, '修改员工组');
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
			init_log_oper($action, '删除员工组');
			if(!is_numeric($id)){
				error(1100);
			}
			//检测数据合法性
			$my_model -> my_power();
			$my_model -> my_delete();
			success();
			break;

		case 'flush':
			init_log_oper($action, '清空员工组');
			$my_model -> my_flush();
			success();
			break;
			
		default:
			error('1100');
	}
	
}
