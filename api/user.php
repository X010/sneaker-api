<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * user 员工管理
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */


/**
 * @api {post} user/create 新增员工
 * @apiName user/create
 * @apiGroup User
 * @apiVersion 0.0.1
 * @apiDescription 新增员工实体操作
 *
 * @apiParam {string} code *员工编码
 * @apiParam {string} username *登陆账号
 * @apiParam {string} password *登陆密码
 * @apiParam {string} truename *员工真实姓名
 * @apiParam {int} sid *员工所属门店id
 * @apiParam {string} rids *员工角色id集合，用逗号分隔，如"1,2,3"
 * @apiParam {string} worktype *工种
 * @apiParam {string} idcard 员工身份证
 * @apiParam {string} email 电子邮箱
 * @apiParam {string} phone 员工手机号码
 * @apiParam {string} memo 员工备注
 * @apiParam {int} admin 是否管理员 1-是 0-不是
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
 * @api {post} user/update/:id 更新员工个人资料
 * @apiName user/update
 * @apiGroup User
 * @apiVersion 0.0.1
 * @apiDescription 更新员工实体操作
 *
 * @apiParam {string} code *员工编码
 * @apiParam {string} username *登陆账号
 * @apiParam {string} truename *员工真实姓名
 * @apiParam {int} sid *员工所属门店id
 * @apiParam {string} rids *员工角色id集合，用逗号分隔，如"1,2,3"
 * @apiParam {string} worktype *工种
 * @apiParam {string} idcard 员工身份证
 * @apiParam {string} email 电子邮箱
 * @apiParam {string} phone 员工手机号码
 * @apiParam {string} memo 员工备注
 * @apiParam {int} admin 是否管理员 1-是 0-不是
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
 * @api {post} user/delete/:id 删除员工
 * @apiName user/delete
 * @apiGroup User
 * @apiVersion 0.0.1
 * @apiDescription 删除员工实体操作
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

function user($action, $id = Null){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$my_model = new User($id);
	$data = $app->params;
	$cid = $app->Sneaker->cid;
	switch($action){
		case 'create':
			init_log_oper($action, '添加员工'); //操作日志初始化
			param_need($data, ['username','password','name','sids','rids',
				'worktype','phone']);	//判断参数是否必填
			param_check($data, [
				//'idcard' => "/^\w+$/",
				'sids,rids' => "/^[0-9,]+$/",
			]);	//判断所传参数是否符合规范，正则

//			//创建者必须是管理员，被创建者一定不是管理员
//			if($app->Sneaker->user_info['admin'] != 1){
//				error(1344);
//			}

			$data = format_data_ids($data, ['sids','rids']);
			$data['rids'] = ','.$data['rids'].',';

			$data['admin'] = 0;

			//找到用户公司ID，补充到参数里
			$data['cname'] = $app->Sneaker->cname;
			$data['cid'] = $cid;

			//找一个sids里存在并且所属公司不在cid的仓库，如果找到就报错
			$store_data = [
				'id' => explode(',', $data['sids']),
				'cid[!]' => $cid,
				'status' => 1
			];
			$res = $app->db->has('o_store', $store_data);
			if($res){
				error(1343);
			}
			//员工姓名不能重复
			$res = $my_model->has([
				'cid'=>$cid,
				'name'=>$data['name']
			]);
			if($res){
				error(1349);
			}

			$res = $my_model -> my_create($data);
			success(['user_id' => $res]);
			break;

		case 'update':
			init_log_oper($action, '修改员工个人资料');
			if(!is_numeric($id)){
				error(1100);
			}
			param_check($data, [
				//'password,idcard' => "/^\w+$/",
				'sids,rids' => "/^[0-9,]+$/",
				'phone' => "/^[0-9]{11}$/"
			]);	//判断所传参数是否符合规范，正则

			//unset($data['username']);

			$data = format_data_ids($data, ['sids','rids']);
			$data['rids'] = ','.$data['rids'].',';

			$res = $my_model -> read_by_id();
			if(!isset($res[0])){
				error(1342);
			}
			if($res[0]['cid'] != $cid){
				error(1347);
			}

			//找一个sids里存在并且所属公司不在cid的仓库，如果找到就报错
			if(isset($data['sids'])){
				$store_data = [
					'id' => explode(',', $data['sids']),
					'cid[!]' => $cid,
					'status' => 1
				];
				$res = $app->db->has('o_store', $store_data);
				if($res){
					error(1343);
				}
			}

			if(get_value($data, 'username')){
				$res = $my_model->has([
					'username'=>$data['username'],
					'id[!]'=>$id
				]);
				if($res){
					error(1345);
				}
			}

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

		case 'read':
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
					if($res[0]['gtids']){
						$res[0]['group_name'] = $ug_model->get_name_by_id('o_user_group', $res[0]['group_id']);
					}
				}
				else{
					$res[0]['gtids'] = '';
				}

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

				if(get_value($data, 'rid')){
					$data['rids[~]'] = '%,'.$data['rid'].',%';
				}

				//查找父类型下的所有子类型作为条件
				$group_id = get_value($data, 'group_id');
				if($group_id){
					$ug_model = new UserGroup();
					$group_ids = $ug_model->get_ids_by_fid($group_id, $cid);
					$data['group_id'] = $group_ids;
				}

				$res = $my_model -> read_list($data);

				if($res['count']){
                    $res['data'] = Change::go($res['data'], 'group_id', 'group_name', 'o_user_group');
                }

				success($res);
			}
			break;

		case 'delete':
			init_log_oper($action, '删除员工');
			if(!is_numeric($id)){
				error(1100);
			}

			$res = $my_model -> read_by_id();
			if(!isset($res[0])){
				error(1342);
			}
			//不能删除非本公司员工
			if($res[0]['cid'] != $cid){
				error(1347);
			}
			//不能删除自己
			if($res[0]['id'] == $app->Sneaker->uid){
				error(1348);
			}

			$c_model = new Customer();
			$c_res = $c_model->has([
				'suid'=>$id
			]);
			if($c_res){
				error(1350);
			}
			$my_model -> my_delete($data);
			success();
			break;

		case 'move_customer':
			init_log_oper($action, '移交员工客户');

			param_need($data, ['from_uid','to_uid','ccids']);

			$data = format_data_ids($data, ['ccids']);

			if($data['from_uid'] == $data['to_uid']){
				error(1756);
			}

			$from_res = $my_model -> read_by_id($data['from_uid']);
			if(!isset($from_res[0])){
				error(1342);
			}
			if($from_res[0]['cid'] != $cid){
				error(1347);
			}

			$to_res = $my_model -> read_by_id($data['to_uid']);
			if(!isset($to_res[0])){
				error(1342);
			}
			if($to_res[0]['cid'] != $cid){
				error(1347);
			}
			$to_uname = $to_res[0]['name'];
			$c_model = new Customer();
			$cs_model = new CustomerSalesman();

			if($from_res[0]['belong'] == 1 && $to_res[0]['belong'] == 1){
				//都是自有

			}
			elseif($from_res[0]['belong'] == 2 && $to_res[0]['belong'] == 2){
				//都是外借

			}
			else{
				error(1754);
			}

			$ccid_list = explode(',', $data['ccids']);
			$c_model->update([
				'suid'=>$data['to_uid'],
				'suname'=>$to_uname
			],[
				'AND' => [
					'suid'=>$data['from_uid'],
					'cid'=>$cid,
					'ccid'=>$ccid_list
				]
			]);
			$cs_model->update([
				'suid'=>$data['to_uid'],
				'suname'=>$to_uname
			],[
				'AND' => [
					'suid'=>$data['from_uid'],
					'cid'=>$cid,
					'ccid'=>$ccid_list
				]
			]);

			success();
			break;

		default:
			error(1100);
	}

}
