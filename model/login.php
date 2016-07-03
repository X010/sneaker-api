<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * login
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class Login{
	private $ticket_prefix = 'T_';	//ticket前缀
	private $uid_prefix = 'U_';		//uid前缀
	
	public function __construct(){
		
	}
	
	/**
	 * 登录
	 *
	 * @param string $username 帐号
	 * @param string $password 密码
	 *
	 * @return string $ticket 登录凭证
	 */
	public function login($username, $password, $platform, $verify = ''){
		
		$app = \Slim\Slim::getInstance();
		$platform_list = $app->config('platform');
		if(!in_array($platform, $platform_list)){
			error(1104);
		}

		if(strlen($password) >= 32){
			$password = rsa_decrypt($password);
		}

		//验证用户名是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'username' => $username
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}
		//验证密码是否正确
		if($res[0]['password'] != my_password_hash($password)){
			error(1302);
		}

		$c_res = $app->db->select('o_company', '*', [
			'id' => $res[0]['cid']
		]);

		$print_tpl = $c_res[0]['print_tpl'];

		$user_id = $res[0]['id'];

		//判断是否ERP公司用户
		if($platform == 'erp'){
			if($c_res && !$c_res[0]['iserp']){
				error(1303);
			}
			if($app->config('sms_verify')){
				//如果开启验证码验证
				$verify_sys = $app->kv->get('verify_'.$platform.'_'.$user_id);
				if(!$verify_sys){
					error(1304);
				}
				if($verify_sys != $verify){
					error(1305);
				}
			}
		}

		//判断是否已经有ticket，如果有就踢掉原有的
		$old_ticket = $app->kv->get($this->uid_prefix.$platform.'_'.$user_id);
		if($old_ticket){
			$this -> logout($old_ticket);
		}
		
		//产生一个ticket
		$ticket_object = new Ticket();
		$ticket = $ticket_object -> create();
		
		//获取角色的权限信息
		$ret = $app->db->select('s_role', '*', [
			'AND'=>[
				'id' => explode(',', $res[0]['rids']),
				'status' => 1
			]
		]);
		$mids = [];
		foreach($ret as $val){
			$mid_list = explode(',', $val['mids']);
			foreach($mid_list as $mid){
				if(!in_array($mid, $mids) && $mid){
					$mids[] = $mid;
				}
			}
		}

		//判断业务员APP 权限
		if($platform == 'salesman'){
			if(!$res[0]['admin'] && !in_array(192, $mids)){
				error(6010);
			}
		}

		$ret = $app->db->select('s_module', '*', [
			'id' => $mids
		]);
		$power = [];
		if($ret){
			foreach($ret as $val){
				$power[] = $val['api'].'/'.$val['function'];
			}
		}
		$now = date('Y-m-d H:i:s');
		//保存登陆状态
		$data_array = [
			'platform'  => $platform,
			'id' 		=> $res[0]['id'], 		//用户ID
			'code' 		=> $res[0]['code'], 	//用户编码 
			'username' 	=> $res[0]['username'], //用户帐号
			'name' 		=> $res[0]['name'], //用户姓名
			'worktype' 	=> $res[0]['worktype'], //工种
			'cid'		=> $res[0]['cid'],		//所属公司ID
			'cname'		=> $res[0]['cname'],	//所属公司名称
			'sids' 		=> explode(',', trim($res[0]['sids'], ',')), 	//仓库权限ID列表
			'rids' 		=> explode(',', trim($res[0]['rids'], ',')), 	//角色ID列表
			'power' 	=> $power, 				//权限
			'logintime' => $now, 				//登录时间
			'admin' 	=> $res[0]['admin'], 	//是否管理员
			'print_tpl' => $print_tpl,
			'mids'		=> $mids,
			'phone'		=> $res[0]['phone'],
			'photo'		=> $res[0]['photo'],
		];
		$data = json_encode($data_array);
		//设置ticket有效时长
		$ticket_period = $app->config($platform.'_ticket_period');
		if(!$ticket_period){
			$ticket_period = $app->config('default_ticket_period');
		}

		//保存2个key，用于ticket踢人
		$app->kv->setex($this->ticket_prefix.$ticket, $ticket_period, $data);
		$app->kv->setex($this->uid_prefix.$platform.'_'.$user_id, $ticket_period, $ticket);

		//更新user表最近登录时间
		$app->db->update('o_user', [
			'logintime' => $now
		], [
			'id' => $res[0]['id']
		]);
		$data_array['ticket'] = $ticket;

		$app->Sneaker->uid = $data_array['id'];
		$app->Sneaker->uname = $data_array['name'];
		$app->Sneaker->cid = $data_array['cid'];
		$app->platform = $platform;


		if($platform == 'salesman' || $platform == 'customer'){
			$data_array['sids'] = implode(',', $data_array['sids']);
			$data_array['rids'] = implode(',', $data_array['rids']);
			unset($data_array['mids']);
		}

		return $data_array;
	}

	/**
	 * 第三方登录
	 *
	 * @param string $openid 第三方openid
	 *
	 * @return string $ticket 登录凭证
	 */
	public function login2($openid, $platform, $state){

		$app = \Slim\Slim::getInstance();
		$platform_list = $app->config('platform');
		if(!in_array($platform, $platform_list)){
			error(1104);
		}

		//通过openid获取uid
		$ut_model = new UserThird();
		$ut_res = $ut_model->read_one([
			'openid'=>$openid,
			'state' =>$state,
			'type'=>1
		]);
		if(!$ut_res){
			error(1309);
		}
		$uid = $ut_res['uid'];
		$state = $ut_res['state'];

		$weixin_config = Other::get_weixin_data($state);
		if(!$weixin_config){
			error(1105);
		}
		$scid = $weixin_config['company_id'];

		//$cid = $ut_res['cid'];

		//验证用户是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'id' => $uid
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}

		$c_res = $app->db->select('o_company', '*', [
			'id' => $res[0]['cid']
		]);

		$print_tpl = $c_res[0]['print_tpl'];

		$user_id = $res[0]['id'];

		//判断是否已经有ticket，如果有就踢掉原有的
//		$old_ticket = $app->kv->get($this->uid_prefix.$platform.'_'.$user_id);
//		if($old_ticket){
//			$this -> logout($old_ticket);
//		}

		//产生一个ticket
		$ticket_object = new Ticket();
		$ticket = $ticket_object -> create();

		$now = date('Y-m-d H:i:s');
		//保存登陆状态
		$data_array = [
			'platform'  => $platform,
			'id' 		=> $res[0]['id'], 		//用户ID
			'code' 		=> $res[0]['code'], 	//用户编码
			'username' 	=> $res[0]['username'], //用户帐号
			'name' 		=> $res[0]['name'], //用户姓名
			'worktype' 	=> $res[0]['worktype'], //工种
			'cid'		=> $res[0]['cid'],		//所属公司ID
			'cname'		=> $res[0]['cname'],	//所属公司名称
			'sids' 		=> explode(',', trim($res[0]['sids'], ',')), 	//仓库权限ID列表
			'rids' 		=> explode(',', trim($res[0]['rids'], ',')), 	//角色ID列表
			//'power' 	=> $power, 				//权限
			'logintime' => $now, 				//登录时间
			'admin' 	=> $res[0]['admin'], 	//是否管理员
			'print_tpl' => $print_tpl,
			//'mids'		=> $mids,
			'phone'		=> $res[0]['phone'],
			'photo'		=> $res[0]['photo'],
			'scid'      => $scid
		];
		$data = json_encode($data_array);
		//设置ticket有效时长
		$ticket_period = $app->config($platform.'_ticket_period');
		if(!$ticket_period){
			$ticket_period = $app->config('default_ticket_period');
		}

		//保存2个key，用于ticket踢人
		$app->kv->setex($this->ticket_prefix.$ticket, $ticket_period, $data);
		$app->kv->setex($this->uid_prefix.$platform.'_'.$user_id, $ticket_period, $ticket);
		//更新user表最近登录时间
		$app->db->update('o_user', [
			'logintime' => $now
		], [
			'id' => $res[0]['id']
		]);
		$data_array['ticket'] = $ticket;

		$app->Sneaker->uid = $data_array['id'];
		$app->Sneaker->uname = $data_array['name'];
		$app->Sneaker->cid = $data_array['cid'];
		$app->platform = $platform;

		if($platform == 'salesman' || $platform == 'customer'){
			$data_array['sids'] = implode(',', $data_array['sids']);
			$data_array['rids'] = implode(',', $data_array['rids']);
		}

		return $data_array;
	}

	/**
	 * 登录
	 *
	 * @param string $username 帐号
	 * @param string $password 密码
	 *
	 * @return string $ticket 登录凭证
	 */
	public function login3($username, $platform, $verify = ''){

		$app = \Slim\Slim::getInstance();
		$platform_list = $app->config('platform');
		if(!in_array($platform, $platform_list)){
			error(1104);
		}

		//验证用户名是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'username' => $username
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}

		$c_res = $app->db->select('o_company', '*', [
			'id' => $res[0]['cid']
		]);

		$print_tpl = $c_res[0]['print_tpl'];

		$user_id = $res[0]['id'];

		//判断是否ERP公司用户
		if($platform == 'erp'){
			if($c_res && !$c_res[0]['iserp']){
				error(1303);
			}
			if($app->config('sms_verify')){
				//如果开启验证码验证
				$verify_sys = $app->kv->get('verify_'.$platform.'_'.$user_id);
				if(!$verify_sys){
					error(1304);
				}
				if($verify_sys != $verify){
					error(1305);
				}
			}
		}

		//判断是否已经有ticket，如果有就踢掉原有的
		$old_ticket = $app->kv->get($this->uid_prefix.$platform.'_'.$user_id);
		if($old_ticket){
			$this -> logout($old_ticket);
		}

		//产生一个ticket
		$ticket_object = new Ticket();
		$ticket = $ticket_object -> create();

		$scids = [];
		$cc_res = $app->db->select('r_customer', '*', [
			'ccid' => $res[0]['cid']
		]);
		foreach($cc_res as $val){
			$scids[] = $val['cid'];
		}

		$now = date('Y-m-d H:i:s');
		//保存登陆状态
		$data_array = [
			'platform'  => $platform,
			'id' 		=> $res[0]['id'], 		//用户ID
			'code' 		=> $res[0]['code'], 	//用户编码
			'username' 	=> $res[0]['username'], //用户帐号
			'name' 		=> $res[0]['name'], //用户姓名
			'worktype' 	=> $res[0]['worktype'], //工种
			'cid'		=> $res[0]['cid'],		//所属公司ID
			'cname'		=> $res[0]['cname'],	//所属公司名称
			'sids' 		=> explode(',', trim($res[0]['sids'], ',')), 	//仓库权限ID列表
			'rids' 		=> explode(',', trim($res[0]['rids'], ',')), 	//角色ID列表
			//'power' 	=> $power, 				//权限
			'logintime' => $now, 				//登录时间
			'admin' 	=> $res[0]['admin'], 	//是否管理员
			'print_tpl' => $print_tpl,
			//'mids'		=> $mids,
			'phone'		=> $res[0]['phone'],
			'photo'		=> $res[0]['photo'],
			'scid_list'      => $scids
		];
		$data = json_encode($data_array);
		//设置ticket有效时长
		$ticket_period = $app->config($platform.'_ticket_period');
		if(!$ticket_period){
			$ticket_period = $app->config('default_ticket_period');
		}

		//保存2个key，用于ticket踢人
		$app->kv->setex($this->ticket_prefix.$ticket, $ticket_period, $data);
		$app->kv->setex($this->uid_prefix.$platform.'_'.$user_id, $ticket_period, $ticket);
		//更新user表最近登录时间
		$app->db->update('o_user', [
			'logintime' => $now
		], [
			'id' => $res[0]['id']
		]);
		$data_array['ticket'] = $ticket;

		$app->Sneaker->uid = $data_array['id'];
		$app->Sneaker->uname = $data_array['name'];
		$app->Sneaker->cid = $data_array['cid'];
		$app->platform = $platform;

		if($platform == 'salesman' || $platform == 'customer'){
			$data_array['sids'] = implode(',', $data_array['sids']);
			$data_array['rids'] = implode(',', $data_array['rids']);
		}

		return $data_array;
	}
	
	/**
	 * 登出
	 *
	 * @param string $ticket 登录凭证
	 *
	 * @return bool 执行结果
	 */
	public function logout($ticket){
		//判断ticket基本合法
		$app = \Slim\Slim::getInstance();
		
		$ticket_object = new Ticket();
		$res = $ticket_object -> check($ticket);
		if(!$res){
			error(8000);
		}

		//读取ticket信息
		$res = $app->kv->get($this->ticket_prefix.$ticket);
		if($res){
			$res_data = json_decode($res, True);
			$app->Sneaker->uid = $res_data['id'];
			$app->Sneaker->uname = $res_data['name'];
			$app->Sneaker->cid = $res_data['cid'];
		}

		//让ticket失效
		return $app->kv->delete($this->ticket_prefix.$ticket);
	}
	
	/**
	 * 获取登录状态
	 *
	 * @param string $ticket 登录凭证
	 * 
	 * @return array 登录状态信息
	 */
	public function login_status($ticket){
		//判断ticket基本合法
		$app = \Slim\Slim::getInstance();

		$ticket_object = new Ticket();
		$res = $ticket_object -> check($ticket);
		if(!$res){
			error(8000);
		}

		//读取ticket信息
		$res = $app->kv->get($this->ticket_prefix.$ticket);
		if(!$res){
			error(8001);
		}

		$res_data = json_decode($res, True);
		$user_id = $res_data['id'];
		$platform = $res_data['platform'];

		//刷新ticket剩余时间
		$ticket_period = $app->config($platform.'_ticket_period');
		$app->kv->setTimeout($this->ticket_prefix.$ticket, $ticket_period);
		$app->kv->setTimeout($this->uid_prefix.$platform.'_'.$user_id, $ticket_period);

		return $res_data;
	}


	public function login_refresh($uid, $platform){
		$app = \Slim\Slim::getInstance();

		$ticket = $app->kv->get($this->uid_prefix.$platform.'_'.$uid);
		if($ticket){
			$res = $app->db->select('o_user', '*', [
				'id' => $uid
			]);

			$c_res = $app->db->select('o_company', '*', [
				'id' => $res[0]['cid']
			]);
			$print_tpl = $c_res[0]['print_tpl'];

			//获取角色的权限信息
			$ret = $app->db->select('s_role', '*', [
				'AND'=>[
					'id' => explode(',', $res[0]['rids']),
					'status' => 1
				]
			]);
			$mids = [];
			foreach($ret as $val){
				$mid_list = explode(',', $val['mids']);
				foreach($mid_list as $mid){
					if(!in_array($mid, $mids) && $mid){
						$mids[] = $mid;
					}
				}
			}
			$ret = $app->db->select('s_module', '*', [
				'id' => $mids
			]);
			$power = [];
			if($ret){
				foreach($ret as $val){
					$power[] = $val['api'].'/'.$val['function'];
				}
			}

			$now = date('Y-m-d H:i:s');
			//保存登陆状态
			$data = json_encode([
				'platform'  => $platform,
				'id' 		=> $res[0]['id'], 		//用户ID
				'code' 		=> $res[0]['code'], 	//用户编码 
				'username' 	=> $res[0]['username'], //用户帐号
				'name' 		=> $res[0]['name'], //用户姓名
				'worktype' 	=> $res[0]['worktype'], //工种
				'cid'		=> $res[0]['cid'],		//所属门店ID
				'cname'		=> $res[0]['cname'],	//所属公司名称
				'sids' 		=> explode(',', trim($res[0]['sids'], ',')), 	//仓库权限ID列表
				'rids'		=> explode(',', trim($res[0]['rids'], ',')),
				'power' 	=> $power, 				//权限
				'logintime' => $now, 				//登录时间
				'admin' 	=> $res[0]['admin'], 	//是否管理员
				'print_tpl' => $print_tpl,
				'mids'		=> $mids,
				'phone'		=> $res[0]['phone'],
				'photo'		=> $res[0]['photo'],
			]);

			//设置ticket有效时长
			$ticket_period = $app->config($platform.'_ticket_period');
			
			//刷新ticket内容 
			$app->kv->setex($this->ticket_prefix.$ticket, $ticket_period, $data);
		}
		return True;
	}


	public function logout_by_uid($uid, $platform){
		$app = \Slim\Slim::getInstance();

		$ticket = $app->kv->get($this->uid_prefix.$platform.'_'.$uid);
		if($ticket){
			$this->logout($ticket);
		}
		return True;
	}

	/**
	 * 商城登录
	 *
	 * @param string $username 帐号
	 * @param string $password 密码
	 *
	 * @return array
	 */
	public function mall_login($username, $password){

		$app = \Slim\Slim::getInstance();

		//验证用户名是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'username' => $username
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}
		//验证密码是否正确
		if($res[0]['password'] != my_password_hash($password)){
			error(1302);
		}
		$user_id = $res[0]['id'];

		//获取角色的权限信息
//		$ret = $app->db->select('s_role', '*', [
//			'id' => explode(',', $res[0]['rids'])
//		]);
//		$mids = [];
//		foreach($ret as $val){
//			$mid_list = explode(',', $val['mids']);
//			foreach($mid_list as $mid){
//				if(!in_array($mid, $mids) && $mid){
//					$mids[] = $mid;
//				}
//			}
//		}
//		$ret = $app->db->select('s_module', '*', [
//			'id' => $mids
//		]);
//		$power = [];
//		if($ret){
//			foreach($ret as $val){
//				$power[] = $val['api'].'/'.$val['function'];
//			}
//		}
		$now = date('Y-m-d H:i:s');

		$scids = [];
		$s_res = $app->db->select('r_customer','*',[
			'ccid' => $res[0]['cid']
		]);
		foreach($s_res as $val){
			$scids[] = $val['cid'];
		}

		$c_res = $app->db->select('o_company','*',[
			'id'=>$res[0]['cid']
		]);

		//保存登陆状态
		$data_array = [
			'id' 		=> $res[0]['id'], 		//用户ID
			'code' 		=> $res[0]['code'], 	//用户编码
			'username' 	=> $res[0]['username'], //用户帐号
			'name' 		=> $res[0]['name'], //用户姓名
			'worktype' 	=> $res[0]['worktype'], //工种
			'cid'		=> $res[0]['cid'],		//所属公司ID
			'cname'		=> $res[0]['cname'],	//所属公司名称
			'areapro'   => $c_res[0]['areapro'],
			'areacity'  => $c_res[0]['areacity'],
			'areazone'  => $c_res[0]['areazone'],
			'address'   => $c_res[0]['address'],
			'contactor' => $c_res[0]['contactor'],
			'contactor_phone' => $c_res[0]['contactor_phone'],
			'sids' 		=> explode(',', trim($res[0]['sids'], ',')), 	//仓库权限ID列表
			'rids' 		=> explode(',', trim($res[0]['rids'], ',')), 	//角色ID列表
			//'power' 	=> $power, 				//权限
			'logintime' => $now, 				//登录时间
			'admin' 	=> $res[0]['admin'], 	//是否管理员
			'scids'     => $scids
		];
		return $data_array;
	}

	public function create_verify($username, $password, $platform){
		$app = \Slim\Slim::getInstance();

		if(strlen($password) >= 32){
			$password = rsa_decrypt($password);
		}

		//验证用户名是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'username' => $username
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}
		//验证密码是否正确
		if($res[0]['password'] != my_password_hash($password)){
			error(1302);
		}

		$phone = $res[0]['phone'];
		if(!$phone){
			error(1307);
		}
		if(strlen($phone)!= 11 || !is_numeric($phone)){
			error(1308);
		}

		//判断是否ERP公司用户
		$c_res = $app->db->select('o_company', '*', [
			'id' => $res[0]['cid']
		]);
		if($c_res && !$c_res[0]['iserp']){
			error(1303);
		}

		$verify = mt_rand(1000, 9999);
		$verify_period = $app->config('verify_period');

		$app->kv->setex('verify_'.$platform.'_'.$res[0]['id'], $verify_period, $verify);


		$sms_param = [
			'phone' => $phone,
			//'message' => '['.$verify.']是您的验证码（盛世酩德）',
			'message' => $verify,
			'source_from' => 'ERP',
			'tempcode' => 'SMS_4040319',
			'system_name' => 'ERP',
			'freeSign' => '登录验证',
			'sign' => '__'
		];
		$sms_url = $app->config('smsUrl');
		$res = curl($sms_url, $sms_param);
		$res_dict = json_decode($res, True);
		if($res_dict['status'] != 200){
			error(1306);
		}

		return $phone;
	}

	public function create_verify2($username, $platform){
		$app = \Slim\Slim::getInstance();

		//验证用户名是否存在并有效
		$res = $app->db->select('o_user', '*', [
			'username' => $username
		]);
		if(!isset($res[0])){
			error(1300);
		}
		if($res[0]['status'] == 0){
			error(1301);
		}

		$phone = $res[0]['phone'];
		if(!$phone){
			error(1307);
		}
		if(strlen($phone)!= 11 || !is_numeric($phone)){
			error(1308);
		}

		$verify = mt_rand(1000, 9999);
		$verify_period = $app->config('verify_period');

		$app->kv->setex('verify_'.$platform.'_'.$res[0]['id'], $verify_period, $verify);

		$sms_param = [
			'phone' => $phone,
			//'message' => '['.$verify.']是您的验证码（盛世酩德）',
			'message' => $verify,
			'source_from' => 'WEIXIN',
			'tempcode' => 'SMS_4040319',
			'system_name' => '99云商',
			'freeSign' => '登录验证',
			'sign' => '__'
		];
		$sms_url = $app->config('smsUrl');
		$res = curl($sms_url, $sms_param);
		$res_dict = json_decode($res, True);
		if($res_dict['status'] != 200){
			error(1306);
		}

		return $phone;
	}

	public function create_verify3($phone, $platform){
		$app = \Slim\Slim::getInstance();

		if(strlen($phone)!= 11 || !is_numeric($phone)){
			error(1308);
		}

		$verify = mt_rand(1000, 9999);
		$verify_period = $app->config('verify_period');

		$app->kv->setex('verify_'.$platform.'_'.$phone, $verify_period, $verify);

		$sms_param = [
			'phone' => $phone,
			//'message' => '['.$verify.']是您的验证码（盛世酩德）',
			'message' => $verify,
			'source_from' => 'PBS',
			'tempcode' => 'SMS_4040319',
			'system_name' => '99云商',
			'freeSign' => '登录验证',
			'sign' => '__'
		];
		$sms_url = $app->config('smsUrl');
		$res = curl($sms_url, $sms_param);
		$res_dict = json_decode($res, True);
		if($res_dict['status'] != 200){
			error(1306);
		}
		return $phone;
	}

}

