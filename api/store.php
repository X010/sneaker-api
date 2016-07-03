<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * store 仓库管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} store/create 新增仓库
 * @apiName store/create
 * @apiGroup Store
 * @apiVersion 0.0.1
 * @apiDescription 新增仓库实体操作
 *
 * @apiParam {string} code *仓库编码
 * @apiParam {string} name *仓库名称
 * @apiParam {string} address *仓库地址
 * @apiParam {string} phone *仓库电话号码
 * @apiParam {string} contactor *仓库联系人
 * @apiParam {string} memo *仓库备注
 * @apiParam {string} areapro 省
 * @apiParam {string} areacity 市
 * @apiParam {string} areazone 区
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
 * @api {post} store/update/:id 更新仓库信息
 * @apiName store/update
 * @apiGroup Store
 * @apiVersion 0.0.1
 * @apiDescription 更新仓库实体操作
 *
 * @apiParam {string} code *仓库编码
 * @apiParam {string} name *仓库名称
 * @apiParam {string} address *仓库地址
 * @apiParam {string} phone *仓库电话号码
 * @apiParam {string} contactor *仓库联系人
 * @apiParam {string} memo *仓库备注
 * @apiParam {string} areapro 省
 * @apiParam {string} areacity 市
 * @apiParam {string} areazone 区
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
 * @api {post} store/delete/:id 删除仓库
 * @apiName store/delete
 * @apiGroup Store
 * @apiVersion 0.0.1
 * @apiDescription 删除仓库实体操作
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

function store($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$my_model = new Store($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'create':
			init_log_oper($action, '添加仓库');
			param_need($data, ['name']);

			//找到用户公司ID，补充到参数里
			$data['cid'] = $cid;

			$sid = $my_model -> my_create($data);

			//自动增加仓库权限到当前用户
			$user_model = new User($app->Sneaker->uid);
			$app->Sneaker->sids[] = $sid;
			$sids = implode(',', $app->Sneaker->sids);
			$user_model -> my_update(['sids' => $sids]);

			success(['store_id' => $sid]);
			break;

		case 'update':
			init_log_oper($action, '修改仓库信息');
			if(!is_numeric($id)){
				error(1100);
			}

			//判断用户身份是否拥有该仓库权限
			Power::check_my_sid($id);
			$my_model -> my_update($data, $cid);
			success();
			break;

		case 'delete':
			init_log_oper($action, '删除仓库');
			if(!is_numeric($id)){
				error(1100);
			}
			Power::check_my_sid($id);
			$my_model -> my_delete();
			success();
			break;

		default:
			error(1100);
	}

}
