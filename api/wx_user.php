<?php
/**
 *  微信商城
 *  此接口不需要ticket
 *
 * 	用户登录/绑定用
 */

define('PAY_SERVER_URL', 'http://pay2.ms9d.com');

define('PAY_STATUS_SUCCESS',   9); //支付成功状态

function wx_user($action, $id = Null){
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch($action){
    	case 'load':
    	//刚进入首页获取openid时 加载商城配置数据
    		param_need($data, ['state']);
    		$state = get_value($data, 'state','');
    		$data = [];
    		$mall = $app->db2->get('db_mall',['name','intro','logo'],['state'=>$state]);
    		if ($mall) {
                if(!$mall['logo']) {
                    $mall['logo'] = 'http://photo.ms9d.com/og/ord_3903328385.png';
                }
    			$data = $mall;
    		}
    		respCustomer($data);
    		break;
    	case 'hxbind':
    	// 微信 openid 账号绑定
    		param_need($data, ['openid','username','verify','platform','pcode']);
    		$openid   = get_value($data, 'openid', '');
    		$username = get_value($data, 'username', '');
    		$verify   = get_value($data, 'verify', '');
    		$pcode    = get_value($data,'pcode','');
            $platform = get_value($data, 'platform','customer'); //默认微信商城平台

            $user = [];
            $param = [
                'openid' => $openid,
                'username' => $username,
                'verify' => $verify,
                'platform' => $platform,
                'pcode' => $pcode,
            ];
            $resp = curl($app->config('bindHXUrl'), $param);
            $data = json_decode($resp, true);
            if ($data && $data['status']=='0000') {
                $user = $data['msg'];
                $mall = $app->db2->get('db_mall','*', ['AND'=>['company_id'=>$user['scid'],'enable'=>1]]);
                if ($mall) {
                    $user['mall_name'] = $mall['name'];
                    $user['cs_phone']  = $mall['cs_phone'];
                }
                // if ($user) {
                // 	$company = $app->db->get('o_company','*',['id'=>$user['cid']]);
                // 	if ($company) {
                // 		$company['typeStr'] = get_company_type($company['type']);
                // 	}
                // 	$user['com'] = $company;
                // }
            } else {
            	if ($data['err']) {
            		$err_code = intval($data['status']);
            		if ($err_code == 6211) {
            			error(7106, '此帐号已绑定其他微信号');
            		}
            		if ($err_code == 6212) {
            			error(7107, '此微信号已绑定其他账号');
            		}
                    else{
                        error($data['status']);
                    }
            	}
            }

            $count = ($user) ? 1 : 0;
            respCustomer($user, $count);
    	break;
    	case 'bind':
    	// 微信 openid 账号绑定
    		param_need($data, ['openid','username','verify','platform']);
    		$openid   = get_value($data, 'openid', '');
    		$username = get_value($data, 'username', '');
    		$verify   = get_value($data, 'verify', '');
            $platform = get_value($data, 'platform','customer'); //默认微信商城平台

            $user = [];
            $param = [
                'openid' => $openid,
                'platform' => $platform,
                'username' => $username,
                'verify' => $verify,
            ];
            $resp = curl($app->config('bindUrl'), $param);
            $data = json_decode($resp, true);
            if ($data && $data['status']=='0000') {
                $user = $data['msg'];
                $mall = $app->db2->get('db_mall','*', ['AND'=>['company_id'=>$user['scid'],'enable'=>1]]);
                if ($mall) {
                    $user['mall_name'] = $mall['name'];
                    $user['cs_phone']  = $mall['cs_phone'];
                }
            } else {
            	if ($data['err']) {
            		$err_code = intval($data['status']);
            		if ($err_code == 6211) {
            			error(7106, '此帐号已绑定其他微信号');
            		}
            		if ($err_code == 6212) {
            			error(7107, '此微信号已绑定其他账号');
            		}
                    error($err_code, $data['msg']);
            	}
            }

            $count = ($user) ? 1 : 0;
            respCustomer($user, $count);
    	break;
    	case 'wxlogin':
    	//绑定账号 登录
            param_need($data, ['openid','platform']);
            $openid = get_value($data, 'openid', '');
            $platform = get_value($data, 'platform','customer'); //默认微信商城平台

            $user = [];
            $param = [
                'openid' => $openid,
                'platform' => $platform,
            ];
            $resp = curl($app->config('loginUrl'), $param);
            $data = json_decode($resp, true);
            if ($data && $data['status']=='0000') {
                $user = $data['msg'];

                $mall = $app->db2->get('db_mall','*', ['AND'=>['company_id'=>$user['scid'],'enable'=>1]]);
                if ($mall) {
                	$user['mall_name'] = $mall['name'];
                	$user['cs_phone']  = $mall['cs_phone'];
                }
            } else {
            	if ($data['err'] && $data['status']==1309) {
            		// error(7105);
            		error(300,'此微信号尚未绑定账号'); //返回300 客户端引导绑定账号
            	}
            }

            $count = ($user) ? 1 : 0;
            respCustomer($user, $count);
    	break;

        case 'applogin':
            //绑定账号 登录
            param_need($data, ['username','verify','platform']);
            $platform = get_value($data, 'platform','customer'); //默认微信商城平台

            $user = [];
            $param = [
                'username' => $data['username'],
                'verify' => $data['verify'],
                'platform' => $platform,
            ];
            $resp = curl($app->config('loginUrl'), $param);
            $data = json_decode($resp, true);
            if ($data && $data['status']=='0000') {
                $user = $data['msg'];
//
//                $mall = $app->db2->get('db_mall','*', ['AND'=>['company_id'=>$user['scid'],'enable'=>1]]);
//                if ($mall) {
//                    $user['mall_name'] = $mall['name'];
//                    $user['cs_phone']  = $mall['cs_phone'];
//                }
            } else {
                if ($data['err'] && $data['status']==1309) {
                    // error(7105);
                    error(300,'此微信号尚未绑定账号'); //返回300 客户端引导绑定账号
                }
            }

            $count = ($user) ? 1 : 0;
            respCustomer($user, $count);
            break;

        case 'login':
        //用户登录  账号密码登录
            param_need($data, ['username','password']);
            $username = get_value($data, 'username', '');
            $password = get_value($data, 'password', '');
            $platform = get_value($data, 'platform','customer'); //默认微信商城平台
            if (strlen($password) > 32) {
                $password = str_replace('_', '+', $password);
            }

            $user = [];
            $param = [
                'username' => $username,
                'password' => $password,
                'platform' => $platform,
            ];



            $resp = curl($app->config('loginUrl'), $param);

            $data = json_decode($resp, true);
            if ($data && $data['status']=='0000') {
                $user = $data['msg'];
            }

            $count = ($user) ? 1 : 0;
            respCustomer($user, $count);
        break;

        default:
        	error(1100);
    }
}


/**
 * 获取商户类型
 * @param  [type] $type [description]
 * @return [type]       [description]
 */
function get_company_type($type)
{
    switch ($type) {
        case 1:
            $output = "经销商";
            break;
        case 2:
            $output = "酒店饭店";
            break;
        case 3:
            $output = "商场超市";
            break;
        case 4:
            $output = "便利店";
            break;
        default:
            $output = "经销商";
            break;
    }
    return $output;
}
