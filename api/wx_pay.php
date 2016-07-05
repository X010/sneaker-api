<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * 微信公众号支付相关 通知接口（内部API）
 *
 * @author      liyi <lyliyi2009@gmail.com>
 * @copyright   2016 liyi
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} notice/paid 通知支付结果
 * @apiName notice/paid
 * @apiGroup Notice
 * @apiVersion 0.0.1
 * @apiDescription 通知支付结果
 *
 * @apiParam {int} type 类型1-出库单通知 2-结算单通知 3-订单通知
 * @apiParam {int} pay_type 支付方式
 * @apiParam {string} id 单据号，可能是出库单号，结算单号，订单号，根据type变化
 * @apiParam {string} amount 支付金额，单位元。用于二次核对
 *
 */
define('PAY_SERVER_URL', 'http://pay2.ms9d.com');

define('PAY_STATUS_SUCCESS', 9); //支付成功状态

function wx_pay($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $uid = $app->Sneaker->uid;
    switch ($action) {
        case 'bank':
            //银行汇款 账号信息
            param_need($data, ['scid']);
            $scid = get_value($data, 'scid', 0);
            $type = get_value($data, 'type', 3);
            // 0 所有  3 银行转账  4对公汇款
            $type = intval($type);
            if (!in_array($type, [0, 3, 4])) {
                $type = 3;
            }
            $where = ['company_id' => $scid];
            if ($type) {
                $where['type'] = $type;
            }
            $bank_list = $app->db2->select('db_company_bank', ['id', 'bank_name', 'card_no', 'account_name', 'csort'], ['AND' => $where, 'ORDER' => 'csort asc']);
            if (!$bank_list) {
                error(7100);
            }
            respCustomer($bank_list, 1);
            break;
        case 'type':
            //支付方式 page: 填写订单
            $scid = get_value($data, 'scid', 0);
            $type = [];
            $pay_type = $app->db2->get('db_company_setting', 'cvalue', ['and' => ['company_id' => $scid, 'ckey' => 'show_pay_type']]);
            if ($pay_type) {
                $dict = $app->config('pay_type_dict');
                $pay_id_arr = explode(',', $pay_type);
                foreach ($pay_id_arr as $k) {
                    $type[] = ['key' => $k, 'value' => $dict[$k]];
                }
            } else {
                $type[] = ['key' => 1, 'value' => '货到付款'];
            }
            respCustomer($type);
            break;
        case 'callback':
            //订单支付成功 接收网关的异步通知  商品购买回调接口
            //1、订单更新为已支付
            //2、通知erp更新
            param_need($data, ['order_id', 'total_amount', 'scid', 'sign']); //订单id、金额、供应商公司id、签名不能为空
            $scid = get_value($data, 'scid');
            $order_id = get_value($data, 'order_id');
            $amount = get_value($data, 'total_amount');
            // $scid = 2854;
            $config = getPayConfig($scid);
            if (!$config) {
                echo 'fail';
                exit;
            }
            $from_payserver = checkPaySign($data, $config['secret']);
            if (!$from_payserver) {
                echo 'fail';
                exit;
            }
            $app->db2->update('db_order', ['status' => PAY_STATUS_SUCCESS, 'ispay' => 9], ['order_id' => $order_id]);

            //当订单支付后同样需要将优惠劵设为有效
            $exist_coupon = $app->db2->select('db_coupon_detail', '*', ['coupon_order_no' => $order_id]);
            if ($exist_coupon) {
                //将该红包设置为可使用状态
                $now = date('Y-m-d H:i:s');
                if ($exist_coupon[0]['coupon_use_start'] <= $now && $now <= $exist_coupon[0]['coupon_use_end'] && $exist_coupon[0]['status'] == 2) {
                    $app->db2->update("db_coupon_detail", ["status" => 3], ['coupon_order_no' => $order_id]);
                }
            }

            $order = $app->db2->get('db_order', '*', ['order_id' => $order_id]);
            //操作记录
            $oper = [
                'order_no' => $order_id,
                'order_id' => $order['id'],
                'status' => 9,
                'oper_time' => date('Y-m-d H:i:s'),
                'action' => '',
            ];
            $app->db2->insert('db_order_oper', $oper);

            $erp_order = convertErpOrder($order);

            $params = [
                'data' => json_encode(array($erp_order)),
            ];
            $erp_order_create_url = $app->config('orderCreateUrl');
            $resp = curl($erp_order_create_url, $params);
            // $resp = josn_encode(['err'=>0,'status'=>200,'msg'=>[]]);

            $data = json_decode($resp, true);
            if ($data && $data['err'] == 0) {
                echo 'success';
                exit;
            } else {
                $msg = $data['msg'];
                if (is_array($msg)) {
                    foreach ($msg as $v) {
                        if (isset($v['errCode']) && $v['errCode']) {
                            $app->db2->update('db_order', ['update_time' => date('Y-m-d H:i:s'), 'status' => 99], ['order_id' => $v['orderNo']]);
                        }
                    }
                }

            }
            echo 'fail';
            exit;
            break;
        case 'callback_vip':
            //订单支付成功 接收网关的异步通知  鹏博士会员购买回调接口
            //1、订单更新为已支付
            //2、通知erp更新
            param_need($data, ['order_id', 'total_amount', 'scid', 'sign']); //订单id、金额、供应商公司id、签名不能为空
            $scid = get_value($data, 'scid');
            $order_id = get_value($data, 'order_id');
            $amount = get_value($data, 'total_amount');
            $b2c = $app->config('b2c_id');
            $scid = $b2c['pbs'];
            $config = getPayConfig($scid);
            if (!$config) {
                echo 'fail';
                exit;
            }
            $from_payserver = checkPaySign($data, $config['secret']);
            if (!$from_payserver) {
                echo 'fail';
                exit;
            }
            start_action();
            $now = date('Y-m-d H:i:s');
            $up = [
                'update_time' => $now,
                'notify_time' => $now,
                'status' => PAY_STATUS_SUCCESS
            ];
            $affect = $app->db2->update('db_order_vip', $up, ['order_id' => $order_id]);
            if ($affect) {
                $order = $app->db2->get('db_order_vip', '*', ['order_id' => $order_id]);
                $c_model = new Customer();
                //cid,scid,product_id
                $c_model->vip_up($order);
                end_action();
                echo 'success';
                exit;
            }
            end_action();
            echo 'fail';
            exit;
            break;
        case 'order':
            //准备调起微信去支付某个订单  鹏博士购买会员VIP服务专用
            param_need($data, ['scid', 'soid', 'openid', 'url', 'productid']);
            $scid = get_value($data, 'scid');
            $product_name = '商城下单';
            $url = get_value($data, 'url');
            $product_id = get_value($data, 'productid', 0);
            //soid super_order_id
            $soid = get_value($data, 'soid');
            $open_id = get_value($data, 'openid');
            $order = $app->db2->get('db_order_vip', '*', ['order_id' => $soid]);
            if (!$order) {
                error(404, '未找到对应订单');
                exit;
            }
            $b2c = $app->config('b2c_id');
            if ($product_id && $scid == $b2c['pbs']) {
                $vip_product = $app->config('vip_product');
                foreach ($vip_product as $v) {
                    if ($product_id == $v['product_id']) {
                        $order['service_name'] = $v['name'];
                        $product_name = $v['name'];
                        break;
                    }
                }
            }

            // if ($order['uid'] != $cid) {
            //     error(400, '暂无权限操作：只能支付自己下的订单');
            // }
            $config = getPayConfig($scid);
            if (!$config) {
                error(501, '此供应商暂时未开通支付功能');
            }
            //js sdk config信息
            $access_token = Wechat::get_access_token($config['appid'], $config['appsecret']);
            $js_ticket = Wechat::get_js_ticket($access_token, $config['appid']);
            $js_data = array(
                'noncestr' => Wechat::createNoncestr(),
                'timestamp' => time(),
                'url' => $url,
                'jsapi_ticket' => $js_ticket,
            );
            $js_data['signature'] = Wechat::js_sign($js_data);
            $js_data['appid'] = $config['appid'];
            unset($js_data['url']);

            //支付信息
            $pay_order = [
                'platform' => $config['platform'],
                'product_name' => $product_name,
                'amount' => $order['total_amount'] * 100,
                'email_payer' => $open_id,
                'channel_id' => $config['wechat']['channel_id'],
                'company_id' => $scid,
                'product_id' => 1,
                'channel_type' => 'WXJS',
                'business_order_id' => $order['order_id'],
                'notify_url' => $app->config('vipNotifyUrl'),
                'open_id' => $open_id,
            ];
            $pay_order['sign'] = paySign($pay_order, $config['secret']);
            $pay = getWXJSPay($pay_order);
            $data = [
                'order' => $order, //订单信息
                'config' => $js_data,
                'pay' => $pay,  //微信js支付签名信息
            ];
            respCustomer($data);
            break;
        case 'order_info':
            //准备调起微信去支付某个订单 商城购买商品
            param_need($data, ['scid', 'soid', 'openid', 'url']);
            $scid = get_value($data, 'scid');
            $product_name = '商城下单';
            $url = get_value($data, 'url');
            $product_id = get_value($data, 'productid', 0);
            //soid super_order_id
            $soid = get_value($data, 'soid');
            $open_id = get_value($data, 'openid');
            $order = $app->db2->get('db_order', ['super_order_id', 'order_id', 'create_time', 'total_amount', 'uid', 'company_name', 'status', 'receipt', 'supplier_company_name'], ['super_order_id' => $soid]);
            if (!$order) {
                error(404, '未找到对应订单');
                exit;
            }
            $order_item = $app->db2->get('db_order_item', ['gname'], ['AND' => ['order_id' => $order['order_id'], 'giveaway' => 0], 'ORDER' => 'id desc']);
            if ($order_item) {
                $order['gname'] = $order_item['gname'];
                $product_name = $order_item['gname'];
            }
            $b2c = $app->config('b2c_id');
            if ($product_id && $scid == $b2c['pbs']) {
                $vip_product = $app->config('vip_product');
                foreach ($vip_product as $v) {
                    if ($product_id == $v['product_id']) {
                        $order['service_name'] = $v['name'];
                        break;
                    }
                }
            }

            // if ($order['uid'] != $cid) {
            //     error(400, '暂无权限操作：只能支付自己下的订单');
            // }
            $config = getPayConfig($scid);
            if (!$config) {
                error(501, '此供应商暂时未开通支付功能');
            }
            //js sdk config信息
            $access_token = Wechat::get_access_token($config['appid'], $config['appsecret']);
            $js_ticket = Wechat::get_js_ticket($access_token, $config['appid']);
            $js_data = array(
                'noncestr' => Wechat::createNoncestr(),
                'timestamp' => time(),
                'url' => $url,
                'jsapi_ticket' => $js_ticket,
            );
            $js_data['signature'] = Wechat::js_sign($js_data);
            $js_data['appid'] = $config['appid'];
            unset($js_data['url']);

            //支付信息
            $pay_order = [
                'platform' => $config['platform'],
                'product_name' => $product_name,
                'amount' => $order['total_amount'] * 100,
                'email_payer' => $open_id,
                'channel_id' => $config['wechat']['channel_id'],
                'company_id' => $scid,
                'product_id' => 1,
                'channel_type' => 'WXJS',
                'business_order_id' => $order['order_id'],
                'notify_url' => $app->config('orderNotifyUrl'),
                'open_id' => $open_id,
            ];
            $pay_order['sign'] = paySign($pay_order, $config['secret']);
            $pay = getWXJSPay($pay_order);
            $data = [
                'order' => $order, //订单信息
                'config' => $js_data,
                'pay' => $pay,  //微信js支付签名信息
            ];
            respCustomer($data);
            break;
        case 'ispaid':
            //轮训订单是否支付成功
            param_need($data, ['scid', 'soid']);
            $scid = get_value($data, 'scid');
            $soid = get_value($data, 'soid');
            if (9 == substr($soid, 0, 1)) {
                //商品订单
                $order = $app->db2->get('db_order', ['super_order_id', 'order_id', 'create_time', 'total_amount', 'uid', 'ispay', 'status', 'scid'], ['super_order_id' => $soid]);
            } else {
                //鹏博士VIP订单
                $order = $app->db2->get('db_order_vip', ['cid', 'status', 'scid'], ['order_id' => $soid]);
            }

            $data = ['result' => 'fail'];
            if (!$order) {
                respCustomer($data);
                exit;
            }
            //暂时去掉
            // if ($order['uid'] != $cid || $order['scid']!=$scid) {
            //     respCustomer($data);
            //     exit;
            // }
            if ($order['status'] == PAY_STATUS_SUCCESS) {
                $data['result'] = 'success';
            }
            respCustomer($data);
            break;
        case 'js_init':
            //单独获取微信js支付初始化信息
            param_need($data, ['scid', 'soid', 'openid', 'url']);
            $scid = get_value($data, 'scid');
            $product_name = '商城下单';
            $url = get_value($data, 'url');
            $product_id = get_value($data, 'productid', 0);
            //soid super_order_id
            $soid = get_value($data, 'soid');
            $open_id = get_value($data, 'openid');
            $order = $app->db2->get('db_order', ['super_order_id', 'order_id', 'create_time', 'total_amount', 'uid', 'company_name', 'status', 'receipt', 'supplier_company_name'], ['super_order_id' => $soid]);
            if (!$order) {
                error(404, '未找到对应订单');
                exit;
            }
            $order_item = $app->db2->get('db_order_item', ['gname'], ['AND' => ['order_id' => $order['order_id'], 'giveaway' => 0], 'ORDER' => 'id desc']);
            if ($order_item) {
                $order['gname'] = $order_item['gname'];
                $product_name = $order_item['gname'];
            }
            $b2c = $app->config('b2c_id');
            if ($product_id && $scid == $b2c['pbs']) {
                $vip_product = $app->config('vip_product');
                foreach ($vip_product as $v) {
                    if ($product_id == $v['product_id']) {
                        $order['service_name'] = $v['name'];
                        break;
                    }
                }
            }

            // if ($order['uid'] != $cid) {
            //     error(400, '暂无权限操作：只能支付自己下的订单');
            // }
            $config = getPayConfig($scid);
            if (!$config) {
                error(501, '此供应商暂时未开通支付功能');
            }
            //js sdk config信息
            $access_token = Wechat::get_access_token($config['appid'], $config['appsecret']);
            $js_ticket = Wechat::get_js_ticket($access_token, $config['appid']);
            $js_data = array(
                'noncestr' => Wechat::createNoncestr(),
                'timestamp' => time(),
                'url' => $url,
                'jsapi_ticket' => $js_ticket,
            );
            $js_data['signature'] = Wechat::js_sign($js_data);
            $js_data['appid'] = $config['appid'];
            unset($js_data['url']);
            $data = [
                'order' => $order, //订单信息
                'config' => $js_data,
                // 'pay'   => $pay,  //微信js支付签名信息 
            ];
            respCustomer($data);
            break;
        case 'alipaysec':
            //支付宝app支付
            param_need($data, ['scid', 'soid', 'ticket']);
            $product_name = '商城下单';
            $soid = get_value($data, 'soid');
            $scid = get_value($data, 'scid');
            $order = $app->db2->get('db_order', ['super_order_id', 'order_id', 'create_time', 'total_amount', 'uid', 'company_name', 'status', 'receipt', 'supplier_company_name'], ['super_order_id' => $soid]);
            if (!$order) {
                error(404, '未找到对应订单');
                exit;
            }
            $order_item = $app->db2->get('db_order_item', ['gname'], ['AND' => ['order_id' => $order['order_id'], 'giveaway' => 0], 'ORDER' => 'id desc']);
            if ($order_item) {
                $order['gname'] = $order_item['gname'];
                $product_name = $order_item['gname'];
            }
            $config = getPayConfig($scid);
            if (!$config) {
                error(501, '此供应商暂时未开通支付功能');
            }
            $pay_order = [
                'platform' => $config['platform'],
                'product_name' => $product_name,
                'amount' => $order['total_amount'] * 100,
                'email_payer' => $order['uid'],
                'channel_id' => $config['alipay']['channel_id'],
                'company_id' => $scid,
                'product_id' => 1,
                'business_order_id' => $order['order_id'],
                'notify_url' => $app->config('orderNotifyUrl'),
            ];
            $pay_order['sign'] = paySign($pay_order, $config['secret']);
            $pay = new Pay();
            $pay_info = $pay->alipaySec($pay_order);
            parse_str($pay_info['data']['url'], $url);
            unset($pay_info['data']['url']);
            $pay_info['data']['udata'] = $url;
            $data = $pay_info['data'];  //微信js支付签名信息 

            respCustomer($data);
            break;
        default:
            error(1100);
    }
}

/**
 * 微信操作封装, 主要用于微信js sdk使用
 *
 */
class Wechat
{
    const API_URL = 'https://api.weixin.qq.com';
    private static $access_token;
    private static $expries_time = 0;
    private static $ticket;
    private static $ticket_expries_time = 0;

    /**
     * 获取access_token
     * access_token 默认2小时过期
     * 会先从cache中获取, 如果未过期，就取值返回
     * 如果已过期，重新从微信获取，塞入cache
     *
     *
     * @param  [string] $app_id     appid
     * @param  [string] $app_secret appsecret
     * @return [string]             access_token
     */
    public static function get_access_token($app_id, $app_secret)
    {
        //从会话中获取
        if (isset(self::$access_token) && time() < self::$expries_time) {
            return self::$access_token;
        }
        //从cache中获取
        $now = time();
        $hkey = 'TOKEN_' . $app_id;
        $app = \Slim\Slim::getInstance();
        $cache = $app->kv->hgetall($hkey);
        if ($cache && isset($cache['expries']) && $now <= $cache['expries']) {
            self::$access_token = $cache['access_token'];
            self::$expries_time = $cache['expries'];
            return self::$access_token;
        }
        //从wechat中获取
        $url = self::API_URL . "/cgi-bin/token?grant_type=client_credential&appid=" . $app_id . "&secret=" . $app_secret;
        $content = curl($url);
        $ret = json_decode($content, true);//{"access_token":"ACCESS_TOKEN","expires_in":7200}
        if (array_key_exists('errcode', $ret) && $ret['errcode'] != 0) {
            return false;
        }
        self::$access_token = $ret['access_token'];
        self::$expries_time = time() + intval($ret['expires_in']);
        $app->kv->hmset($hkey, ['access_token' => self::$access_token, 'expries' => self::$expries_time]);
        return self::$access_token;
    }

    /**
     * 获取js sdk签名使用的 js_ticket
     * 默认2小时过期
     * 会先从cache中获取, 如果未过期，就取值返回
     * 如果已过期，重新从微信获取，塞入cache
     * @param  [string] $access_token
     * @return [string]               js_ticket
     */
    public static function get_js_ticket($access_token, $app_id)
    {
        //从会话中获取
        if (isset(self::$ticket) && time() < self::$ticket_expries_time) {
            return self::$ticket;
        }
        //从cache中获取
        $now = time();
        $hkey = 'JSTICKET_' . $app_id;
        $app = \Slim\Slim::getInstance();
        $cache = $app->kv->hgetall($hkey);
        if ($cache && isset($cache['expries']) && $now <= $cache['expries']) {
            self::$ticket = $cache['ticket'];
            self::$ticket_expries_time = $cache['expries'];
            return self::$ticket;
        }
        //从wechat中获取
        $url = self::API_URL . '/cgi-bin/ticket/getticket?access_token=' . $access_token . '&type=jsapi';
        $content = curl($url);
        $ret = json_decode($content, true);
        if (array_key_exists('errcode', $ret) && $ret['errcode'] != 0) {
            return false;
        }
        self::$ticket = $ret['ticket'];
        self::$ticket_expries_time = time() + intval($ret['expires_in']);
        $app->kv->hmset($hkey, ['ticket' => self::$ticket, 'expries' => self::$ticket_expries_time]);
        return self::$ticket;
    }

    /**
     * 生成随机字符串(默认32位长度)
     * @param  integer $length [description]
     * @return [type]          [description]
     */
    public static function createNoncestr($length = 32)
    {
        $chars = "abcdefghijklmnopqrstuvwxyz0123456789";
        $str = "";
        for ($i = 0; $i < $length; $i++) {
            $str .= substr($chars, mt_rand(0, strlen($chars) - 1), 1);
        }
        return $str;
    }

    /**
     * js sdk签名
     * @param  [type] $param [description]
     * @return [type]        [description]
     */
    public static function js_sign($param)
    {
        if (ksort($param)) {
            $string = '';
            foreach ($param as $key => $value) {
                if (empty($value)) {
                    continue;
                }
                $string .= strtolower($key) . "=$value&";
            }
            $string = rtrim($string, '&');
            return sha1($string);
        }
        return false;
    }
}

/**
 * 将订单转换为ERP的订单
 * @param  [type] $order 商城订单
 * @return [type]        array
 */
function convertErpOrder($order)
{
    $app = \Slim\Slim::getInstance();
    $erp = [
        'contacts' => $order['contacts'],
        'phone' => $order['phone'],
        'in_cid' => $order['cid'],
        'out_cid' => $order['scid'],
        'memo' => $order['memo'],
        'rank' => 0,
        'orderNo' => $order['order_id'],
        'receipt' => $order['receipt'],
        'delivery' => $order['delivery'],
        'platform' => $order['platform'],
        'express'=>$order['express_money'],
        'ispaid' => 1,
        'pay_type' => 1,
        'uid' => $order['uid'],
        'favorable' => $order['favorable'],
        'items' => [],
        'small_amount' => 0,
    ];
    $data = $app->db2->select('db_order_item', '*', ['order_id' => $order['order_id']]);
    foreach ($data as $v) {
        $erp['items'][] = [
            'amount' => $v['total_amount'],
            'gid' => $v['gid'],
            'total' => $v['total'],
            'unit_price' => $v['price'],
            'amount_price' => $v['total_amount'],
            'main_gcode' => $v['memo'],
        ];
    }
    $erp['goods_list'] = $erp['items'];
    return $erp;
}

/**
 *  供应商支付信息配置
 * @param  [type] $scid 供应商id
 * @return [type]       [description]
 */
function getPayConfig($scid = 0)
{
    if (!$scid) {
        return false;
    }

    $config = [
        '13' => [  //盛世明德
            'appid' => 'wx3ec60ae27a01b14b',
            'appsecret' => 'f4b7a841925a56f13cb293add4a0f086',
            'platform' => 1029,
            'secret' => 'eb27a397ba6793f669d634334a935a83',
            'wechat' => [ //微信支付配置
                'channel_id' => 109
            ],
            'alipay' => [ //ali无线支付配置
                'channel_id' => 113
            ],
        ],
        '2854' => [ //郎斐
            'appid' => 'wx3ec60ae27a01b14b',
            'appsecret' => 'f4b7a841925a56f13cb293add4a0f086',
            'platform' => 1031,
            'secret' => 'eb27a397ba6793f669d634334a935a83',
            'wechat' => [ //微信支付配置
                'channel_id' => 111
            ],
            'alipay' => [ //ali支付配置
            ],
        ],
        '2346' => [ //鹏博士
            'appid' => 'wx0c1c76cdc174a36a',
            'appsecret' => '3c7a506ef261b18c23e8a698e731a947',
            'platform' => 1032,
            'secret' => 'eb27a397ba6793f669d634334a931032',
            'wechat' => [ //微信支付配置
                'channel_id' => 112
            ],
            'alipay' => [ //ali支付配置
            ],
        ],
    ];
    if ($scid && isset($config[$scid])) {
        return $config[$scid];
    }
    return false;
}

/**
 * APP支付
 * 封装
 *
 * 支付宝无线: alipaySec
 *
 * 微信APP: wechatApp
 */
class Pay
{
    public function wechatApp($pay_order)
    {
        $resp = curl(PAY_SERVER_URL . '/api/order/create', $pay_order);
        $data = json_decode($resp, true);
        if ($data['errno']) {
            return false;
        }
        return $data;
    }

    /**
     * 支付宝无线支付
     * @param  [type] $pay_order [description]
     * @return [type]            [description]
     */
    public function alipaySec($pay_order)
    {
        $resp = curl(PAY_SERVER_URL . '/api/order/create', $pay_order);
        $data = json_decode($resp, true);
        if ($data['errno']) {
            return false;
        }
        return $data;
    }
}


/**
 * 获取微信js支付的package
 *
 * @param  [type] $order [description]
 * @param  [type] $scid  [description]
 * @return [type]        [description]
 */
function getWXJSPay($pay_order)
{
    $resp = curl(PAY_SERVER_URL . '/api/order/create', $pay_order);
    $data = json_decode($resp, true);
    if ($data['errno']) {
        return false;
    }
    $resp = [];
    $resp['package'] = $data['data']['url'];
    $resp['order_id'] = $data['data']['order_id'];
    return $resp;
}

/**
 * 支付签名
 * @param  [type] $param      待签名参数
 * @param  [type] $secret_key 签名秘钥
 * @return [type]             签名串
 */
function paySign($param, $secret_key = null)
{
    if (empty($param)) {
        return false;
    }

    if (!ksort($param)) {
        return false;
    }

    $string = '';
    foreach ($param as $key => $value) {
        $string .= "$key=$value&";
    }

    $string .= ("secret_key=" . $secret_key);
    $string = strtolower($string);
    $sign = sha1($string);

    return $sign;
}

/**
 * 检查支付回调签名
 * @param  [type] $post_data  接收数据
 * @param  [type] $secret_key 签名key
 * @return [type]             bool
 */
function checkPaySign($post_data, $secret_key = null)
{
    if (!isset($post_data['sign'])) {
        return false;
    }
    if (is_null($secret_key)) {
        return false;
    }

    $sign = $post_data['sign'];

    $param = array();
    foreach ($post_data as $key => $value) {
        if ($key == 'sign') {
            continue;
        }
        $param[$key] = $value;
    }
    if (empty($param)) {
        return false;
    }
    if ($sign == paySign($param, $secret_key)) {
        return true;
    }

    return false;
}
