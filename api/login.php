<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * login 登录关系
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} login/in 登录
 * @apiName login/in
 * @apiGroup Login
 * @apiVersion 0.0.1
 * @apiDescription 用户进行登录，获取ticket（此接口无须传入ticket参数）
 *
 * @apiParam {string} username *用户名
 * @apiParam {string} password *密码
 *
 * @apiSuccess {string} ticket 登录获取的ticket凭证，大部分接口都需要ticket才能操作
 * @apiSuccess {int} id 员工ID
 * @apiSuccess {string} code 员工code
 * @apiSuccess {string} name 员工姓名
 * @apiSuccess {string} username 员工登陆账号
 * @apiSuccess {string} worktype 工种
 * @apiSuccess {int} cid 所属公司ID
 * @apiSuccess {string} cname 所属公司名称
 * @apiSuccess {array} sids 拥有仓库权限列表，是一个list类型
 * @apiSuccess {array} rids 拥有角色权限列表，是一个list类型
 * @apiSuccess {array} power 拥有API权限列表，是一个list类型
 * @apiSuccess {string} logintime 本次登陆的时间
 * @apiSuccess {int} admin 是否管理员 0-不是 1-是，如果是管理员，则不用判断power自动拥有所有API权限
 *
 */

/**
 * @api {post} login/out 登出
 * @apiName login/out
 * @apiGroup Login
 * @apiVersion 0.0.1
 * @apiDescription 用户登出
 *
 * @apiParam {string} ticket *登录获取的ticket凭证
 *
 */

/**
 * @api {post} login/verify 获取验证码
 * @apiName login/verify
 * @apiGroup Login
 * @apiVersion 0.0.1
 * @apiDescription 获取验证码
 *
 * @apiParam {string} username *用户名
 * @apiParam {string} password *密码
 *
 * @apiSuccess {string} phone 发送验证码的手机号
 *
 */

/**
 * @api {post} login/check 检测ticket是否有效
 * @apiName login/check
 * @apiGroup Login
 * @apiVersion 0.0.1
 * @apiDescription 检测ticket是否有效，该接口不刷新ticket的有效时间
 *
 * @apiParam {string} ticket TICKET
 *
 */

/**
 * @api {post} login/message_remind 获取消息提醒
 * @apiName login/message_remind
 * @apiGroup Login
 * @apiVersion 0.0.1
 * @apiDescription 获取消息提醒，该接口不刷新ticket的有效时间
 *
 * @apiSuccess {string} customer_count 待审核客户数
 * @apiSuccess {string} sell_order_count 待处理客户订单
 * @apiSuccess {string} buy_order_count 待处理采购订单
 * @apiSuccess {string} forcheck_stockout_count 待处理出库单
 * @apiSuccess {string} forsettle_stockout_count 待结算出库单
 *
 */

function login($action){
	init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
	$app = \Slim\Slim::getInstance();
	$my_model = new Login();
	$data = $app->params;
	switch($action){
		case 'in':
			init_log_oper($action, '登录'); //尽早记录操作日志

			$openid = get_value($data, 'openid');
			$password = get_value($data, 'password');
			if($openid){

				param_need($data, ['openid','platform']);	//判断参数是否必填
				$openid = $data['openid'];
				$platform = $data['platform'];

				$open_data = $app->kv->get('openid_'.$data['openid']);
				$open_data = json_decode($open_data, true);
				$state = $open_data['state'];

				$login_res = $my_model -> login2($openid, $platform, $state);

				success($login_res);

			}
			elseif($password){
				param_need($data, ['username','password','platform']);	//判断参数是否必填

				$username = $data['username'];
				$password = $data['password'];
				$platform = $data['platform'];
				$verify = get_value($data, 'verify');

				$login_res = $my_model -> login($username, $password, $platform, $verify);

				unset($login_res['power']);

				success($login_res);
			}
			else{
				param_need($data, ['username','verify','platform']);	//判断参数是否必填

				$username = $data['username'];
				$platform = $data['platform'];
				$verify = get_value($data, 'verify');

				$login_res = $my_model -> login3($username, $platform, $verify);

				unset($login_res['power']);

				success($login_res);
			}

			break;
			
		case 'out':
			init_log_oper($action, '登出'); //尽早记录操作日志
			//param_need($data, ['ticket']);
			if(!get_value($data, 'ticket')){
				error(8003);
			}
			param_check($data, [
				'ticket' => "/^\w+$/",
			]);	//判断所传参数是否符合规范，正则
			
			$my_model -> logout($data['ticket']);
			success();
			break;

		case 'verify':
			init_log_oper($action, '获取验证码'); //尽早记录操作日志
			$password = get_value($data, 'password');

			if($password){
				//用户名获取验证码模式
				param_need($data, ['username','password','platform']);	//判断参数是否必填
				$phone = $my_model -> create_verify($data['username'], $data['password'], $data['platform']);
			}
			else{
				//用户名获取验证码模式，无密码
				param_need($data, ['username','platform']);	//判断参数是否必填
				$phone = $my_model -> create_verify2($data['username'], $data['platform']);
			}
			success(['phone'=>$phone]);
			break;

		case 'check':
			param_need($data, ['ticket']);
			$ticket = $data['ticket'];
			$ticket_object = new Ticket();
			$res = $ticket_object -> check($ticket);
			if(!$res){
				error(8000);
			}
			//读取ticket信息
			$res = $app->kv->get('T_'.$ticket);
			if(!$res){
				error(8001);
			}
			success('success', False);
			break;

		case 'message_remind':
			//消息提醒
			param_need($data, ['ticket']);
			$ticket = $data['ticket'];
			$ticket_object = new Ticket();
			$res = $ticket_object -> check($ticket);
			if(!$res){
				error(8000);
			}
			//读取ticket信息
			$res = $app->kv->get('T_'.$ticket);
			if(!$res){
				error(8001);
			}
			$user_info = json_decode($res, True);

			//将用户信息写入当前请求中
			$app->Sneaker->uid = $user_info['id'];
			$app->Sneaker->uname = $user_info['name'];
			$app->Sneaker->cid = $user_info['cid'];
			$app->Sneaker->cname = get_value($user_info, 'cname');
			$app->Sneaker->sids = get_value($user_info, 'sids', []); //array
			$app->Sneaker->user_info = $user_info;

			$cid = $app->Sneaker->cid;
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
			if(in_array('/f_finance/stock_out', $power) || $admin){
				$data = [];
				$my_model = new StockOut();

				Power::set_my_sids($data);
				$data['status'] = 4;
				$data['settle_status'] = 0;
				$data['type'] = 1;
				$data['lastdate[<=]'] = date('Y-m-d');
				$result['forsettle_stockout_count'] = $my_model->count(['AND'=>$data]);
			}

			//6. 缺货待配出库单
			if(in_array('/stock_out/check', $power) || $admin){
				$data = [];
				$my_model = new StockOut();

				Power::set_my_sids($data);
				$data['status'] = 3;
				$data['type'] = 1;
				$result['forout_stockout_count'] = $my_model->count(['AND'=>$data]);
			}

			success($result);
			break;

		case 'get_openid':
			$code = get_value($data, 'code');
			$state = get_value($data, 'state', 'ssmd_test');
			$weixin_config = Other::get_weixin_data($state);
			if(!$weixin_config){
				error(1105);
			}
			//$weixin_config = $app->config('weixin_config');
			//$my_weixin_config = get_value($weixin_config, $state);
			$ret = '';
			if($code){
				define('APPID', $weixin_config['app_id']);
				define('APPSECRET', $weixin_config['app_secret']);
				$ret = weixin::getAuthToken($code);//网页授权获取用户的openid
				if(isset($ret['errcode']) && $ret['errcode']){
					error(6200, $ret['errmsg']);
				}
				//$ret_data = json_decode($ret, True);
				//var_dump($ret);
				$openid = $ret['openid'];
				$open_data = $ret;
				$open_data['state'] = $state;
				$app->kv->setex('openid_'.$openid, 1800, json_encode($open_data));
			}
			success($ret);
			break;

		case 'bind_third':
			//绑定第三方帐号
			param_need($data, ['openid','username','platform','verify']);
			$u_model = new User();
			$res = $u_model->read_one(['username'=>$data['username']]);
			//帐号不存在
			if(!$res){
				error(6210);
			}
			//获取uid
			$uid = $res['id'];
			$cid = $res['cid'];
			$ut_model = new UserThird();

			if($app->config('sms_verify')){
				//如果开启验证码验证
				$verify_sys = $app->kv->get('verify_'.$data['platform'].'_'.$uid);
				if(!$verify_sys){
					error(1304);
				}
				if($verify_sys != $data['verify']){
					error(1305);
				}
			}

			$open_data = $app->kv->get('openid_'.$data['openid']);
			$open_data = json_decode($open_data, true);
			$data['state'] = $open_data['state'];
			$data['access_token'] = $open_data['access_token'];
			$data['expires_in'] = $open_data['expires_in'];
			$data['refresh_token'] = $open_data['refresh_token'];

			$weixin_config = Other::get_weixin_data($data['state']);
			if(!$weixin_config){
				error(1105);
			}
			$scid = $weixin_config['company_id'];
			$c_model = new Customer();
			$c_res = $c_model->has([
				'cid' => $scid,
				'ccid' => $cid
			]);
			if(!$c_res){
				error(6213);
			}

			//开启绑定
			$data['type'] = 1;
			$data['uid'] = $uid;
			$data['cid'] = $cid;
			$ut_model->my_create($data);

			$login_res = $my_model -> login2($data['openid'], $data['platform'], $data['state']);
			success($login_res);
			break;

		case 'bind_third2':
			//绑定第三方帐号，注册指定客户类型并且绑定
			param_need($data, ['openid','username','platform','verify','pcode']);

			$ut_model = new UserThird();

			if($app->config('sms_verify')){
				//如果开启验证码验证
				$verify_sys = $app->kv->get('verify_'.$data['platform'].'_'.$data['username']);
				if(!$verify_sys){
					error(1304);
				}
				if($verify_sys != $data['verify']){
					error(1305);
				}
			}

			$open_data = $app->kv->get('openid_'.$data['openid']);
			$open_data = json_decode($open_data, true);
			$data['state'] = $open_data['state'];
			$data['access_token'] = $open_data['access_token'];
			$data['expires_in'] = $open_data['expires_in'];
			$data['refresh_token'] = $open_data['refresh_token'];

			$weixin_config = Other::get_weixin_data($data['state']);
			if(!$weixin_config){
				error(1105);
			}

			$scid = $weixin_config['company_id'];
			$promotion_code = $weixin_config['promotion_code'];
			$promotion_start_time = $weixin_config['promotion_start_time'];
			$promotion_end_time = $weixin_config['promotion_end_time'];
			$default_cctype = $weixin_config['default_cctype'];
			$default_sid = $weixin_config['default_sid'];

			$now = date('Y-m-d H:i:s');

			if(!$promotion_code || $now<$promotion_start_time || $now>$promotion_end_time){
				error(6230);
			}
			if($promotion_code != $data['pcode']){
				error(6231);
			}

			$u_model = new User();
			$res = $u_model->read_one(['username'=>$data['username']]);

			$c_model = new Customer();
			if(!$res){
				//帐号不存在
				//自动注册成指定客户
				$data['name'] = $data['contactor'] = $data['username'];
				$data['type'] = $default_cctype;
				$data['my_sid'] = $default_sid;
				$c_res = $c_model->my_register3($data, $scid);
				$uid = $c_res['uid'];
				$cid = $c_res['cid'];
			}
			else{
				//帐号已存在，绑定
				$uid = $res['id'];
				$cid = $res['cid'];

				$c_res = $c_model->has([
					'cid' => $scid,
					'ccid' => $cid
				]);
				if(!$c_res){
					error(6213);
				}
			}

			//开启绑定
			$data['type'] = 1;
			$data['uid'] = $uid;
			$data['cid'] = $cid;
			$ut_model->my_create($data);

			$login_res = $my_model -> login2($data['openid'], $data['platform'], $data['state']);
			success($login_res);
			break;

		case 'unbind_third':
			//绑定第三方帐号
			param_need($data, ['openid']);

			//解除绑定
			$data['type'] = 1;
			$ut_model = new UserThird();
			$ut_model->my_delete($data);

			success();
			break;

		case 'oauth_weixin':
			define('TOKEN','ms9dweixinmall');
			$wx_model = new weixin();
			$wx_model->run();
			break;

		default:
			error(1100);
	}
	
}
