<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * exists 是否存在判断
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} exists/customer 判断客户是否存在
 * @apiName exists/customer
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断客户是否存在
 *
 * @apiParam {int} ccid *客户公司ID
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 *
 */

/**
 * @api {post} exists/supplier 判断供应商是否存在
 * @apiName exists/supplier
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断供应商是否存在
 *
 * @apiParam {int} scid *供应商公司ID
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 *
 */

/**
 * @api {post} exists/store_goods 判断仓库商品是否存在
 * @apiName exists/store_goods
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断仓库商品是否存在
 *
 * @apiParam {int} gid *商品系统ID
 * @apiParam {int} in_sid *供应商仓库ID
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 *
 */


/**
 * @api {post} exists/company_goods 判断公司商品是否存在
 * @apiName exists/company_goods
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断公司商品是否存在
 *
 * @apiParam {int} gid *商品系统ID
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 *
 */


/**
 * @api {post} exists/company_name 判断公司名称是否存在
 * @apiName exists/company_name
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断公司名称是否存在
 *
 * @apiParam {string} name *公司名称
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 */

/**
 * @api {post} exists/user_name 判断用户名称是否存在
 * @apiName exists/user_name
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断用户名称是否存在
 *
 * @apiParam {string} username *用户名称
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 */

/**
 * @api {post} exists/true_name 判断用户真实姓名是否存在
 * @apiName exists/true_name
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 判断用户真实名称是否存在
 *
 * @apiParam {int} id *用户ID
 * @apiParam {string} name *用户真实名称
 *
 * @apiSuccess {bool} result 已存在-true / 不存在-false
 *
 */

/**
 * @api {post} exists/getuser 判断用户ticket登录状态
 * @apiName exists/getuser
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 获取用户登录信息
 *
 * @apiSuccess {int} id 员工ID
 * @apiSuccess {string} code 员工编码
 * @apiSuccess {string} username 登陆账号
 * @apiSuccess {string} name 员工真实姓名
 * @apiSuccess {int} cid 员工所属公司ID
 * @apiSuccess {string} cname 员工所属公司名称
 * @apiSuccess {string} worktype 工种
 * @apiSuccess {array} rids 员工角色id集合
 * @apiSuccess {array} sids 员工仓库ID集合
 * @apiSuccess {array} power 权限集合，key为前端菜单或按钮ID，value为1（有权限）或0（无权限）
 * @apiSuccess {string} logintime 登录时间
 * @apiSuccess {int} admin 是否管理员 1-是 0-不是
 *
 *
 */

/**
 * @api {post} exists/getuser_admin 判断用户ticket登录状态（商城专用）
 * @apiName exists/getuser_admin
 * @apiGroup Exists
 * @apiVersion 0.0.1
 * @apiDescription 获取用户登录信息（商城专用）
 *
 * @apiSuccess {int} id 员工ID
 * @apiSuccess {string} code 员工编码
 * @apiSuccess {string} username 登陆账号
 * @apiSuccess {string} name 员工真实姓名
 * @apiSuccess {int} cid 员工所属公司ID
 * @apiSuccess {string} cname 员工所属公司名称
 * @apiSuccess {string} worktype 工种
 * @apiSuccess {array} rids 员工角色id集合
 * @apiSuccess {array} sids 员工仓库ID集合
 * @apiSuccess {string} logintime 登录时间
 * @apiSuccess {int} admin 是否管理员 1-是 0-不是
 * @apiSuccess {dict} com 公司信息
 * @apiSuccess {list} ck 仓库信息列表，包括所有仓库
 */

function exists($action, $id = NULL){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'customer':
			$obj = new Customer($id);
			//判断客户是否存在
			param_need($data, ['ccid']); //必选
			param_check($data, [
				'ccid' => "/^\d+$/",
			]);

			$ret = $obj->has([
				'ccid' => $data['ccid'],
				'cid' => $cid
			]);
			success([
				'result' => $ret
			]);
			break;
		case 'supplier':
			$obj = new Supplier($id);
			//判断供应商是否存在
			param_need($data, ['scid']); //必选
			param_check($data, [
				'scid' => "/^\d+$/",
			]);
			$ret = $obj->has([
				'scid' => $data['scid'],
				'cid' => $cid
			]);
			success([
				'result' => $ret
			]);
			break;

		case 'store_goods':
			$obj = new StoreGoods($id);
			//判断仓库商品是否存在
			param_need($data, ['in_sid','gid']); //必选
			param_check($data, [
				'gid,in_sid' => "/^\d+$/",
			]);
			$ret = $obj->has([
				'in_sid' => $data['in_sid'],
				'gid' => $data['gid']
			]);
			success([
				'result' => $ret
			]);
			break;

		case 'company_goods':
			$obj = new CompanyGoods($id);
			//判断公司商品是否存在
			param_need($data, ['gid']); //必选
			param_check($data, [
				'gid' => "/^\d+$/",
			]);
			$ret = $obj->has([
				'gid' => $data['gid'],
				'in_cid' => $cid
			]);
			success([
				'result' => $ret
			]);
			break;

		case 'company_name':
			$obj = new Company($id);
			//判断公司名称是否存在
			param_need($data, ['name']); //必选

			$ret = $obj->has([
				'name' => $data['name']
			]);

			$why = get_value($data, 'why');
			$msg = "公司名已存在";
			//如果需要详细信息，提供重复公司名的业务员
			if($why){
				$res = $obj->read_one([
					'name' => $data['name']
				]);

				$c_model = new Customer();
				$c_res = $c_model->read_one([
					'cid'=>$cid,
					'ccid'=>$res['id']
				]);
				if($c_res){
					$suname = $c_res['suname'];
					$msg = "客户已存在（所属业务员：{$suname}）";
				}
			}

			success([
				'result' => $ret,
				'msg' => $msg
			]);
			break;

		case 'user_name':
			$obj = new User($id);
			//判断用户名称是否存在
			param_need($data, ['username']);
			$ret = $obj -> has([
				'username' => $data['username']
			]);

			$why = get_value($data, 'why');
			$msg = "帐号已存在";
			//如果需要详细信息，提供重复公司名的业务员
			if($why){
				$res = $obj->read_one([
					'username' => $data['username']
				]);

				$cname = $res['cname'];
				$msg = "帐号已存在（所属公司：{$cname}）";

				$c_model = new Customer();
				$c_res = $c_model->read_one([
					'cid'=>$cid,
					'ccid'=>$res['cid']
				]);
				if($c_res){
					$suname = $c_res['suname'];
					$msg = "帐号已存在（所属客户：{$cname}，所属业务员：{$suname}）";
				}

			}

			success([
				'result' => $ret,
				'msg' => $msg
			]);
			break;

		case 'true_name':
			$obj = new User($id);
			//判断用户真实名称是否存在
			param_need($data, ['name','id']);
			$ret = $obj -> has([
				'cid' => $cid,
				'name' => $data['name'],
				'id[!]' => $data['id']
			]);
			success([
				'result' => $ret
			]);
			break;

		case 'getuser':
			//获取登陆缓存中的用户信息
			$ret = $app->Sneaker->user_info;
			if(get_value($data, 'basedate') == 1){
				//如果需要基准日，会增加基准日返回
				$c_model = new Company();
				$res = $c_model->read_by_id($cid);

				$basedate = $res[0]['basedate'];
				$my_date = date('Y-m-'. $basedate);
				$ret['basedate'] = $my_date;
			}
			$power = get_value($ret, 'power');

			//权限菜单和按钮配置
			$result = [];
			$button = $app->config('button');
			$menu = $app->config('menu');

			$button = $button + $menu;

			$array_temp = [];
			//如果为1，肯定有权限，为0肯定无权限，否则从power中判断是否有权限
			foreach($button as $key=>$val){
				if($val === 1 || $val === 0){
					$result[$key] = $val;
				}
				elseif(is_array($val)){
					$array_temp[$key] = $val;
				}
				elseif(in_array($val, $power)){
					$result[$key] = 1;
				}
				else{
					$result[$key] = 0;
				}
			}

			foreach($array_temp as $key=>$val){
				foreach($val as $val2){
					if($result[$val2]){
						$result[$key] = 1;
						break;
					}
				}
				if(!isset($result[$key])){
					$result[$key] = 0;
				}
			}

			$ret['power'] = $result;

			if($cid == $app->config('b2c_id')['pbs']){
				$ret['business'] = 'B2C';
			}
			else{
				$ret['business'] = 'B2B';
			}

			success($ret);
			break;

		case 'getuser_admin':
			//商城后台专用接口
			$ret = $app->Sneaker->user_info;
			$ret['sids'] = implode(',',$ret['sids']);
			$ret['rids'] = implode(',',$ret['rids']);
			$ret['status'] = 1;
			unset($ret['power']);
			$c_model = new Company();
			$ret['com'] = $c_model->read_one(['id'=>$ret['cid']]);
			$s_model = new Store();
			$ret['ck'] = $s_model->read_list_nopage([
				'cid'=>$ret['cid'],
				'status'=>1
			]);

			if($ret['cid'] == $app->config('b2c_id')['pbs']){
				$ret['business'] = 'B2C';
			}
			else{
				$ret['business'] = 'B2B';
			}

			success($ret);
			break;

		default:
			error(1100);
	}
}


