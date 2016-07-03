<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * role 角色管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


/**
 * @api {post} role/create 新增角色
 * @apiName role/create
 * @apiGroup Role
 * @apiVersion 0.0.1
 * @apiDescription 新增角色实体操作
 *
 * @apiParam {string} name *角色名称
 * @apiParam {int} level *角色等级
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} role/update/:id 更新角色
 * @apiName role/update
 * @apiGroup Role
 * @apiVersion 0.0.1
 * @apiDescription 更新角色实体操作
 *
 * @apiParam {string} name *角色名称
 * @apiParam {int} level *角色等级
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

/**
 * @api {post} role/delete/:id 删除角色
 * @apiName role/delete
 * @apiGroup Role
 * @apiVersion 0.0.1
 * @apiDescription 删除角色实体操作
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */



/**
 * @api {post} role/setpower/:id 设置角色权限
 * @apiName role/setpower
 * @apiGroup Role
 * @apiVersion 0.0.1
 * @apiDescription 设置角色所拥有的操作权限
 *
 * @apiParam {string} mids *模块ID集合，以逗号分隔，如"1,2,3"
 *
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,
 *         "status": "0000",
 *         "msg": "success"
 *     }
 *
 */

function role($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$my_model = new Role($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;

	switch($action){
		case 'create':
			init_log_oper($action, '添加角色');
			param_need($data, ['name','level']);
			param_check($data, [
				'level' => "/^\d+$/",
			]);
			//添加的角色必须是创建者公司旗下的
			$data['cid'] = $cid;

			$res = $my_model -> create($data);
			success(['role_id' => $res]);
			break;

		case 'update':
			init_log_oper($action, '更新角色');
			if(!is_numeric($id)){
				error(1100);
			}
			param_check($data, [
				'level' => "/^\d+$/",
			]);

			$res = $my_model -> read_by_id();
			if(!isset($res[0])){
				error(1331);
			}
			if($res[0]['cid'] != $cid){
				error(1346);
			}

			$my_model -> update_by_id($data);
			success();
			break;

		case 'delete':
			init_log_oper($action, '删除角色');
			if(!is_numeric($id)){
				error(1100);
			}

			$res = $my_model -> read_by_id();
			if(!isset($res[0])){
				error(1331);
			}
			if($res[0]['cid'] != $cid){
				error(1346);
			}

			$my_model -> my_delete();
			success();
			break;

		default:
			error(1100);
	}

}
