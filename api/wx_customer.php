<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * wx_customer 微信端客户接口
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     api
 */

define('IMG_PREFIX', 'http://photo.ms9d.com/og'); //图片地址默认前缀
define('IMG_DEFAULT_URL', 'http://photo.ms9d.com/og/default.png'); //默认图片地址

define('MARKET_GIVEWAYA_MAIN', 0); //营销主商品

define('PAY_STATUS_SUCCESS', 9); //支付成功状态
define('PAY_STATUS_CREATE', 1); //订单创建

define('CACHE_TIME', 60); //60s 缓存

function wx_customer($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $uid = $app->Sneaker->uid;
    switch ($action) {
        case 'user_logout':
            //用户退出  page: 我的  直接采用方正的退出接口
            return;
            param_need($data, ['ticket']);
            $ticket = get_value($data, 'ticket', '');
            $param = [
                'ticket' => $ticket,
            ];
            $resp = curl($app->config('logoutUrl'), $param);
            //不再处理结果
            respCustomer([], 0);
            break;
        case 'user_get':
            //登陆信息获取 page : all 判断当前ticket是否已登陆
            $user_info = $app->Sneaker->user_info;
            $fields = ['id', 'code', 'name', 'address', 'type', 'contactor', 'address', 'contactor_phone'];
            $company = $app->db->get('o_company', $fields, ['id' => $user_info['cid']]);
            if ($company) {
                $company['typeStr'] = get_company_type($company['type']);
            }
            $user = [
                'id' => $user_info['id'],
                'username' => $user_info['username'],
                'name' => $user_info['name'],
                'cid' => $user_info['cid'],
                'cname' => $user_info['cname'],
                'sids' => $user_info['sids'],
                'rids' => $user_info['rids'],
                'phone' => $user_info['phone'],
                'photo' => $user_info['photo'],
            ];
            $user['com'] = $company;

            $pbs_id = $app->config('b2c_id')['pbs'];
            if (get_value($data, 'scid') == $app->config('b2c_id')['pbs']) {
                //鹏博士的情况，返回余额和到期时间
                $c_model = new Customer();
                $c_res = $c_model->read_one([
                    'ccid' => $user_info['cid'],
                    'cid' => $pbs_id
                ]);
                $user['vip_level'] = $c_res['cctype'];
                $user['vip_end_date'] = $c_res['vip_end_date'];
                $user['vip_logistics'] = $c_res['vip_logistics'];
                $user['vip_level_name'] = '非会员';
                $vip_product = $app->config('vip_product');
                foreach ($vip_product as $val) {
                    $vip_level = explode('_', $val['product_id'])[0];
                    if ($vip_level == $user['vip_level']) {
                        $name = explode('（', $val['name'])[0];
                        $user['vip_level_name'] = $name;
                        break;
                    }
                }
            }
            respCustomer($user);
            break;
        case 'user_total':
            //用户统计信息
            $back = array(
                'supplierCount' => 0, //供应商数量
                'favCount' => 0, //收藏数量
                'orderCount' => 0, //订单数
            );
            $scid = get_value($data, 'scid');
            $cid_list = $app->db->select('r_customer', 'cid', ['ccid' => $scid]);
            if ($cid_list) {
                $back['supplierCount'] = $app->db->count('o_company', ['id' => $cid_list]);
            }
            unset($cid_list);
            $good_list = $app->db2->select('db_favorite', 'mgid', ['and' => ['status' => 1, 'cid' => $cid]]);
            if ($good_list) {
                $back['favCount'] = $app->db2->count('db_goods', ['AND' => ['status' => 1, 'company_id' => $scid, 'id' => $good_list]]);
            }
            unset($good_list);
            $back['orderCount'] = $app->db2->count('db_order', ['cid' => $cid]);
            respCustomer($back);
            break;
        case 'address_save':
            //保存收件人信息  新增/更新 page: 新建收货地址
            param_need($data, ['contacts', 'phone', 'street']); //联系人信息必填
            $params = [
                'province' => get_value($data, 'province', ''),
                'city' => get_value($data, 'city', ''),
                'county' => get_value($data, 'county', ''),
                'street' => get_value($data, 'street'),
                'contacts' => get_value($data, 'contacts'),
                'phone' => get_value($data, 'phone'),
                'memo' => get_value($data, 'memo'),
                'def' => intval(get_value($data, 'def')),  //是否默认收货地址
                'id' => intval(get_value($data, 'id')),
                'cid' => $cid,
                'status' => 1,
                'uid' => $uid,
            ];
            if ($params['def']) {
                //如果是添加默认地址，则将其他设置为非默认
                $app->db2->update('db_addressee', ['def' => 0], ['cid' => $cid]);
            }
            if ($params['id']) {
                //如果是有传id,则表示更新
                $id = $params['id'];
                unset($params['id']);
                $res = $app->db2->update('db_addressee', $params, ['id' => $id]);
            } else {
                $count = $app->db2->count('db_addressee', ['cid' => $cid]);
                if (!$count) {
                    $params['def'] = 1; //如果是新增的第一个地址，则为默认地址
                }
                $res = $app->db2->insert('db_addressee', $params);
            }
            if ($res) {
                respCustomer(null);
            } else {
                error();
            }
            break;
        case 'address_del':
            //删除收货地址 page: 我的收货地址 删除
            param_need($data, ['id']); //必填
            $id = get_value($data, 'id');
            $res = $app->db2->update('db_addressee', ['status' => 0], ['AND' => ['id' => $id, 'cid' => $cid]]);
            if ($res) {
                respCustomer(null);
            } else {
                error();
            }
            break;
        case 'address_setdef':
            //设置默认地址  将其他设置为非默认地址 page: 我的收货地址 设为默认
            param_need($data, ['id']); //必填
            $id = get_value($data, 'id');
            start_action();
            $app->db2->update('db_addressee', ['def' => 0], ['cid' => $cid]);
            $res = $app->db2->update('db_addressee', ['def' => 1], ['AND' => ['id' => $id, 'cid' => $cid]]);
            if ($res) {
                respCustomer($cid);
            } else {
                error();
            }
            break;
        case 'address_getdef':
            //获取默认收货地址 page: 填写订单
            $address = [];
            $fields = ['id', 'contacts', 'phone', 'province', 'city', 'county', 'street'];
            $address_arr = $app->db2->select('db_addressee', $fields, ['AND' => ['cid' => $cid, 'status' => 1, 'def' => 1], 'LIMIT' => 1]);
            if ($address_arr) {
                $address_arr = $app->db2->select('db_addressee', $fields, ['AND' => ['cid' => $cid, 'status' => 1, 'LIMIT' => 1]]);
            }
            if ($address_arr) {
                $address = current($address_arr);
            }
            respCustomer($address);
            break;
        case 'address_customer':
            //读取客户收货地址 page: 我的收货地址
            param_need($data, ['ccid']); //必填
            $ccid = get_value($data, 'ccid');
            $fields = ['id', 'contacts', 'phone', 'province', 'city', 'county', 'street', 'def'];
            $address_arr = $app->db2->select('db_addressee', $fields, ['and' => ['cid' => $ccid, 'status' => 1]]);
            respCustomer($address_arr);
            break;
        case 'column_get':
            //获取公司对应的栏目信息 page: 分类
            param_need($data, ['scid']); //必填
            $scid = get_value($data, 'scid');
            $fields = ['id', 'name'];
            $cate_arr = $app->db2->select('db_cate', $fields, ['AND' => ['company_id' => $scid, 'type' => 1, 'publish' => 1], 'ORDER' => 'csort asc']);
            respCustomer($cate_arr);
            break;
        case 'column_wechat':
            //微信商城获取焦点图 page: 首页
            param_need($data, ['scid']); //必填

            $scid = get_value($data, 'scid');
            $is_open_cache = $app->config('open_cache');
            if ($is_open_cache) {
                $key = $app->config('wc_cache_prefix') . 'column_wechat' . $scid;
                $cache = $app->kv->get($key);
                if ($cache) {
                    respCustomer(unserialize($cache));
                    return;
                }
            }

            $fields = ['type', 'description'];
            $cate_arr = $app->db2->select('db_showcase', $fields, ['AND' => ['company_id' => $scid, 'publish' => 1], 'ORDER' => 'csort asc']);

            $is_open_cache && $app->kv->setex($key, CACHE_TIME, serialize($cate_arr));
            respCustomer($cate_arr);
            break;
        case 'column_good':
            //首页展示的栏目跟商品 page: 首页
            param_need($data, ['scid']);
            $scid = get_value($data, 'scid');
            $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer([], 0);
                return;
            }
            $columns = $app->db2->select('db_cate', ['id', 'name', 'wx_home_goods'], ['AND' => ['company_id' => $scid, 'type' => 1, 'publish' => 1, 'wx_home' => 1], 'ORDER' => 'csort asc']);
            if (!$columns) {
                respCustomer([], 0);
                return;
            }
            $data = [];

            $is_open_cache = $app->config('open_cache');
            if ($is_open_cache) {
                $key = $app->config('wc_cache_prefix') . 'column_good' . $scid;
                $cache = $app->kv->get($key);
                if ($cache) {
                    respCustomer(unserialize($cache));
                    return;
                }
            }


            //每个栏目下不固定个数，只能循环找
            $fields = "id,gname,unit,barcode,gcode,gphoto,cateid,company_id,`status`,company_name,publish,isbind,`restrict`,pkgsize,shop_price,marketid,retail_price,spec,gid,price_style";
            foreach ($columns as $k => $c) {
                $data[$c['id']] = $c;
                $data[$c['id']]['goods'] = [];

                $sql = "select {$fields} from db_goods where company_id={$scid} and flagdel=0 and publish=1 and cateid={$c['id']} and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL) or (FIND_IN_SET({$customer['sid']},sids))) order by top desc,`order` desc,tid desc,id desc limit {$c['wx_home_goods']}";
                $goods_arr = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if (!$goods_arr) {
                    continue;
                }
                foreach ($goods_arr as $k => $good) {
                    $goods_arr[$k] = genartePhoto($good, true);
                    if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                        $goods_arr[$k] = paddingSingleMarket($goods_arr[$k], $cid, $customer['cctype'], $scid, false);
                    }

                    $goods_arr[$k]['unit_base'] = '';
                    if ($good['pkgsize'] == 1) {
                        $unit_good = $app->db2->get('db_goods', ['unit', 'retail_price'], ['AND' => ['gid' => $good['gid'], 'pkgsize' => 0]]);
                        if ($unit_good) {
                            $goods_arr[$k]['unit_base'] = $unit_good['unit'];
                            $goods_arr[$k]['retail_price'] = $unit_good['retail_price'];
                        }
                        unset($unit_good);
                    }
                }

                if ($goods_arr) {
                    $data[$c['id']]['goods'] = supplementGoodsPrice($goods_arr, $cid);
                }
            }
            $cache = array_values($data);
            $is_open_cache && $app->kv->setex($key, CACHE_TIME, serialize($cache));
            respCustomer($cache);
            break;
        case 'com_supplier':
            //获取供应商信息 page : 登陆后选择供应商用
            $com_arr = $app->db->select('o_company', '*', ['id' => $app->db->select('r_customer', 'cid', ['ccid' => $cid])]);

            $scids = [];
            foreach ($com_arr as $val) {
                $scids[] = $val['id'];
            }

            $mall = $app->db2->select('db_mall', '*', ['AND' => ['company_id' => $scids, 'enable' => 1]]);

            foreach ($mall as $val) {
                foreach ($com_arr as $key2 => $val2) {
                    if ($val['company_id'] == $val2['id']) {
                        $com_arr[$key2]['mall_name'] = $val['name'];
                        $com_arr[$key2]['cs_phone'] = $val['cs_phone'];
                        break;
                    }
                }
            }
            respCustomer($com_arr);
            break;
        case 'com_supplierdetail':
            //获取供应商详细信息
            param_need($data, ['scid']); //供应商id必填
            $scid = get_value($data, 'scid');
            $company = $app->db->get('o_company', '*', ['id' => $scid]);
            respCustomer($company);
            break;
        case 'com_customer':
            //获取客户列表
            return;
            //select * from o_company where id in(select ccid from r_customer_salesman where  cid=#{company_id} and suid=#{user_id})
            $com_arr = $app->db2->select('o_company', '*', ['id' => $app->db2->select('r_customer_salesman', 'ccid', ['and' => ['cid' => $cid, 'suid' => $uid]])]);
            if (!$com_arr) {
                $com_arr = [];
            }
            respCustomer($com_arr);
            break;
        case 'com_forcid':
            //根据公司获取商品分类 page : 分类
            param_need($data, ['scid']);
            $scid = get_value($data, 'scid', 0);
            $code = get_value($data, 'code', '');
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);
            $data = [];
            if ($code) {
                $code .= "__";
                $data = $app->db->select('o_company_goods_type', ['code', 'name'], ['AND' => ['cid' => $scid, 'code[~]' => $code], 'ORDER' => 'code asc']);
            } else {
                //获取顶级分类，由于顶级分类由用户在商城后台，设计显示层级及展示顺序
                $showLayout = 1;
                $ret = $app->db2->get('db_company_setting', ['cvalue'], ['AND' => ['company_id' => $scid, 'ckey' => 'show_cate_layout']]);
                if ($ret) {
                    $showLayout = intval($ret['cvalue']);
                }
                if ($showLayout < 1) {
                    $data = $app->db->select('o_company_goods_type', '*', ['AND' => ['cid' => $scid, 'code[~]' => "__"], 'ORDER' => 'code asc']);
                } else {
                    $length = $showLayout * 2;
                    $sql = "select id,code,name from o_company_goods_type where cid={$scid} and length(code)={$length}";
                    $data = $app->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                    if ($data) {
                        $comTypes = $app->db2->select('db_goods_type', ['tid', 'csort'], ['company_id' => $scid]);

                        if ($comTypes) {
                            $comGtypeMap = [];
                            foreach ($comTypes as $v) {
                                $comGtypeMap[$v['tid']] = $v;
                            }
                            $filterGtype = [];
                            foreach ($data as $k => $v) {
                                if (isset($comGtypeMap[$v['id']])) {
                                    $v['companyGtype'] = $comGtypeMap[$v['id']];
                                    $v['csort'] = $comGtypeMap[$v['id']]['csort'];
                                    $filterGtype[] = $v;
                                }
                            }
                            usort($filterGtype, 'sortByCsort');
                            $data = $filterGtype;
                        }
                    }
                }
            }
            respCustomer($data);
            break;
        case 'fav_can':
            //能否收藏
            return;
            param_need($data, ['mgid']); //商品id必填
            $mgid = get_value($data, 'mgid');
            //select * from db_favorite where status=#{status} and cid=#{cid} and mgid=#{mgid}
            $total = $app->db2->count('db_favorite', '*', ['and' => ['cid' => $cid, 'mgid' => $mgid, 'status' => 1]]);
            $res = !$total;
            respCustomer($res);
            break;
        case 'fav_list':
            //收藏列表
            //TODO
            return;
            param_need($data, ['scid']); //必填
            $scid = get_value($data, 'scid');
            $page = get_value($data, 'page');
            $limit = get_value($data, 'limit');
            //select count(1) from db_favorite where status=1 and cid=#{cid}
            $where = ['cid' => $cid, 'status' => 1];
            $count = $app->db2->select('db_favorite', $where);
            if (!$count) {
                respCustomer(null, $count);
                exit;
            }

            //select * from db_goods where status=1 and company_id=#{scid} and id in(select mgid from db_favorite where  status=1 and cid=#{cid}) limit #{page},#{limit}
            $mgid_arr = $app->db2->select('db_favorite', 'mgid', $where);
            $good_arr = $app->db2->select('db_goods', '*', ['and' => ['id' => $mgid_arr, 'company_id' => $scid, 'status' => 1], 'limit' => 20]);
            $data = [];
            foreach ($good_arr as $k => $good) {
                unset($good_arr[$k]['content']); //读取列表不返回content

                $good_arr[$k] = genartePhoto($good);
                if ($good['isbind'] == 1 && $good['pkgsize'] == 1 && $good['shop_price'] == 1) {

                }
            }
            respCustomer($data, $count);
            break;
        case 'fav_del':
            //删除一个收藏
            return;
            param_need($data, ['mgid']); //商品id必填
            $mgid = get_value($data, 'mgid');
            $res = $app->db2->update('db_favorite', ['status' => 0], ['and' => ['cid' => $cid, 'mgid' => $mgid, 'status' => 1]]);
            if ($res) {
                respCustomer(null); //更新成功
            } else {
                error();
            }
            break;
        case 'fav_save':
            //收藏商品
            return;
            param_need($data, ['mgid']); //必填
            $mgid = get_value($data, 'mgid', 0);
            if (!$mgid) {
                error();
            }
            $params = [
                'status' => 1,
                'cid' => $cid,
                'uid' => $uid,
                'mgid' => $mgid,
                'create_time' => date('Y-m-d H:i:s'),
            ];
            $res = $app->db2->insert('db_favorite', $params);
            if ($res) {
                respCustomer(null); //更新成功
            } else {
                error();
            }
            break;
        case 'feedback':
            // page : 意见反馈
            param_need($data, ['ccid']);
            $ccid = get_value($data, 'ccid', 0);
            $memo = get_value($data, 'memo', null);
            if ($ccid <= 0) {
                error();
            }
            $params = [
                'uid' => $uid,
                'cid' => $cid,
                'ccid' => $ccid,
                'latitude' => get_value($data, 'latitude', null),
                'longgitude' => get_value($data, 'longgitude', null),
                'altitude' => get_value($data, 'altitude', null),
                'accuracy' => get_value($data, 'accuracy', null),
                'altitudeAccuracy' => get_value($data, 'altitudeAccuracy', null),
                'heading' => get_value($data, 'heading', null),
                'speed' => get_value($data, 'speed', null),
                'timestamp' => get_value($data, 'timestamp', null),
                'createtime' => date('Y-m-d H:i:s'),
                'source' => 1,
                'baidu_latitude' => '',
                'baidu_longgitude' => '',
            ];
            if ($params['latitude'] && $params['longgitude']) {
                $baidu = getBaiduGeo($params['latitude'], $params['longgitude']);
                if ($baidu) {
                    $params['baidu_latitude'] = $baidu['latitude'];
                    $params['baidu_longgitude'] = $baidu['longgitude'];
                }
            }
            if (!is_null($memo)) {
                $params['source'] = 3;
                $params['memo'] = $memo;
            }
            $res = $app->db->insert('o_geolocation', $params);
            if ($res) {
                respCustomer(null);
                return;
            }

            error();
            break;
        case 'geo_track':
            //跟踪用户信息  暂时无用
            $location_arr = $app->db->select('o_geolocation', '*', ['uid' => $uid]);
            respCustomer($location_arr);
            break;

        case 'good_forcate':
            //读取公司下面的商品列表,根据栏目ID  page : 分类
            $scid = get_value($data, 'scid');
            $cateid = get_value($data, 'cateid');
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);
            $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer([], 0);
                return;
            }
            $start = ($page - 1) * $limit;
            if ($start < 0) {
                $start = 0;
            }
            $fields = "id,gname,unit,barcode,gcode,gphoto,cateid,company_id,`status`,company_name,publish,isbind,`restrict`,pkgsize,shop_price,marketid,retail_price,spec,gid,price_style";
            $sql = "select {$fields} from db_goods where company_id={$scid} and flagdel=0 and publish=1 and cateid={$cateid} and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL) or (FIND_IN_SET({$customer['sid']},sids))) order by top desc,`order` desc,tid desc,id desc limit {$start}, $limit";
            $goods_arr = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if (!count($goods_arr)) {
                respCustomer($goods_arr, 0);
                exit;
            }
            $date = date('Y-m-d H:i:s');
            foreach ($goods_arr as $k => $good) {
                $goods_arr[$k] = genartePhoto($good, true);
                if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                    $goods_arr[$k] = paddingSingleMarket($goods_arr[$k], $cid, $customer['cctype'], $scid, false);
                }
                if (isset($goods_arr[$k]['main_good'])) {
                    unset($goods_arr[$k]['main_good']);
                }
                $goods_arr[$k]['unit_base'] = '';
                if ($good['pkgsize'] == 1) {
                    $unit_good = $app->db2->get('db_goods', ['unit', 'retail_price'], ['AND' => ['gid' => $good['gid'], 'pkgsize' => 0]]);
                    if ($unit_good) {
                        $goods_arr[$k]['unit_base'] = $unit_good['unit'];
                        $goods_arr[$k]['retail_price'] = $unit_good['retail_price'];
                    }
                    unset($unit_good);
                }

                //买赠
                $goods_arr[$k]['market'] = [];
                if ($good['marketid']) {
                    $mids = explode(',', $good['marketid']);
                    $markets = $app->db2->select("db_market", ['id', 'title', 'iconName'], ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
                    if ($markets) {
                        $goods_arr[$k]['market'] = $markets;
                    }
                }
            }
            $sql = "select count(1) as t from db_goods where company_id=$scid and flagdel=0 and publish=1 and cateid=$cateid and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids)))";
            $count = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            $total = current($count);
            if ($goods_arr) {
                $goods_arr = supplementGoodsPrice($goods_arr, $cid);
            }

            respCustomer($goods_arr, $total['t']);
            break;
        case 'good_fortype':
            //根据分类读取商品信息  page: 首页 - 商品展示
            param_need($data, ['scid', 'page', 'limit']);
            $code = get_value($data, 'code', '');
            $scid = get_value($data, 'scid');
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);


            if (!$scid) {
                error('404', '商品信息未找到');
                exit;
            }
            //后期再开放缓存
            $is_open_cache = $app->config('open_cache');
            if ($is_open_cache) {
                $ckey = $app->config('wc_cache_prefix') . md5('c:' . $code . 'scid:' . $scid . 'page:' . $page . 'limit:' . $limit);
                $cache = $app->kv->get($ckey);
                if ($cache) {
                    $cache_data = unserialize($cache);
                    respCustomer($cache_data['data'], $cache_data['count']);
                    return;
                }
            }


            $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer([], 0);
                exit;
            }
            $start = ($page - 1) * $limit;
            $goods_arr = [];
            $fields = ['id', 'gphoto', 'unit', 'gname', 'barcode', 'company_id', '`restrict`', 'isbind', 'pkgsize', 'shop_price', 'gcode', 'marketid', 'retail_price', 'gid', 'spec', 'price_style'];
            $fields_str = implode(',', $fields);
            if ($code) {
                $code .= '%';
                $sql1 = "select {$fields_str} from db_goods where company_id={$scid} and flagdel=0 and publish=1 and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL) or (FIND_IN_SET({$customer['sid']},sids))) and tcode like '{$code}' order by top desc,`order` desc,tid desc,id desc limit {$start}, {$limit}";
                $sql2 = "select count(1) as t from db_goods where company_id={$scid} and flagdel=0 and publish=1 and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids))) and tcode like '{$code}'";

            } else {
                $sql1 = "select {$fields_str} from db_goods where  publish=1 and flagdel=0 and company_id={$scid} and  (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids)))  order by top desc,`order` desc,tid desc,id desc limit {$start}, {$limit}";
                $sql2 = "select count(1) as t from db_goods where  publish=1 and flagdel=0 and company_id={$scid} and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids)))";

            }
            //商品
            $goods_arr = $app->db2->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
            if (!count($goods_arr)) {
                respCustomer($goods_arr, 0);
                exit;
            }
            $date = date('Y-m-d H:i:s');
            foreach ($goods_arr as $k => $good) {
                $goods_arr[$k] = genartePhoto($good, true);
                $goods_arr[$k]['content'] = '';
                if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                    $goods_arr[$k] = paddingSingleMarket($goods_arr[$k], $cid, $customer['cctype'], $scid);
                }
                $goods_arr[$k]['unit_base'] = '';
                if ($good['pkgsize'] == 1) {
                    $unit_good = $app->db2->get('db_goods', ['unit', 'retail_price'], ['AND' => ['gid' => $good['gid'], 'pkgsize' => 0]]);
                    if ($unit_good) {
                        $goods_arr[$k]['unit_base'] = $unit_good['unit'];
                        $goods_arr[$k]['retail_price'] = $unit_good['retail_price'];
                    }
                    unset($unit_good);
                }
                //绑定商品的主商品
                if (isset($goods_arr[$k]['main_good'])) {
                    unset($goods_arr[$k]['main_good']);
                }

                //买赠
                $goods_arr[$k]['market'] = [];
                if ($good['marketid']) {
                    $mids = explode(',', $good['marketid']);
                    $markets = $app->db2->select("db_market", ['id', 'title', 'iconName'], ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
                    if ($markets) {
                        $goods_arr[$k]['market'] = $markets;
                    }
                }
            }
            if ($goods_arr) {
                $goods_arr = supplementGoodsPrice($goods_arr, $cid);
            }
            //数量
            $count = $app->db2->query($sql2)->fetchAll(PDO::FETCH_ASSOC);
            $total = current($count);

            if ($is_open_cache) {
                $cache = serialize(['data' => $goods_arr, 'count' => $total['t']]);
                $app->kv->setex($ckey, CACHE_TIME, $cache);
            }

            unset($cache);

            respCustomer($goods_arr, $total['t']);
            break;
        case 'good_cart':
            //读取商品详情 page: 购物车 根据id展示商品
            param_need($data, ['item', 'scid']);

            $post = get_value($data, 'item', '');
            $scid = get_value($data, 'scid');

            // $post = json_encode([['id'=>332,'total'=>1],['id'=>246,'total'=>2]]);
            $item = json_decode($post, true);
            $resp = ['goods' => [], 'total_price' => 0];
            if (!$item) {
                respCustomer($resp);
            }

            //客户关系确定
            $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer($resp, 0);
                exit;
            }

            $good_req = [];
            foreach ($item as $v) {
                $good_req[$v['mgid']] = $v['total'];
            }
            $good_id = array_keys($good_req);

            $good_marketid = [];
            //获取商品
            $fields = ['id', 'gphoto', 'gname', 'unit', 'marketid', 'barcode', 'place', 'pkgspec', 'restrict', 'gcode', 'isbind', 'pkgsize', 'company_id', 'gid', 'spec', 'shop_price', 'price_style'];
            $where = ['id' => $good_id, 'company_id' => $scid, 'flagdel' => 0, 'publish' => 1];
            $good_req_list = $app->db2->select('db_goods', $fields, ['AND' => $where]);
            $date = date('Y-m-d H:i:s');
            if ($good_req_list) {
                foreach ($good_req_list as $k => $good) {
                    $good = genartePhoto($good, false);

                    if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                        $good = paddingSingleMarket($good, $cid, $customer['cctype'], $scid);
                    }
                    if (isset($good['main_good'])) {
                        unset($good['main_good']);
                    }
                    $good['total'] = $good_req[$good['id']];

                    //买赠
                    $good['market'] = [];
                    if ($good['marketid']) {
                        $mids = explode(',', $good['marketid']);
                        $markets = $app->db2->select("db_market", ['id', 'title', 'iconName'], ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
                        if ($markets) {
                            $good['market'] = $markets;
                        }

                        $good_marketid[$good['id']] = $good['marketid'];
                    }

                    $good_req_list[$k] = $good;
                }
            }
            //获取基础商品价格
            $good_list = supplementGoodsPrice($good_req_list, $cid);

            //计算总价
            $total_price = 0;
            foreach ($good_list as $k => $v) {
                $good_list[$k]['total_price'] = 0;
                if (isset($v['price'])) {
                    $good_list[$k]['total_price'] = $v['price'] * $good_req[$v['id']];
                }
                $total_price += $good_list[$k]['total_price'];
            }

            $resp['goods'] = $good_list;
            $resp['total_price'] = $total_price;
            respCustomer($resp, count($good_list));
            break;
        case 'good_info':
            //读取商品详情 page: 商品详情页
            param_need($data, ['id', 'scid']);
            $mgid = get_value($data, 'id', 0);
            $scid = get_value($data, 'scid');
            $fields = ['id', 'gphoto', 'gname', 'unit', 'marketid', 'barcode', 'place', 'pkgspec', 'restrict', 'gcode', 'isbind', 'pkgsize', 'company_id', 'content', 'retail_price', 'gid', 'spec', 'price_style'];
            $good = $app->db2->get('db_goods', $fields, ['id' => $mgid]);
            if ($good) {
                $good = genartePhoto($good, true);
                $good = paddingSingleMarket($good, $cid, 0, $scid);
                if (empty($good['content'])) {
                    $good['content'] = '';
                } else {
                    $good['content'] = htmlspecialchars_decode($good['content']);
                }
                if ($good['pkgsize'] == 1) {
                    $good['unit_base'] = '';
                    $unit_good = $app->db2->get('db_goods', ['unit', 'retail_price'], ['AND' => ['gid' => $good['gid'], 'pkgsize' => 0]]);
                    if ($unit_good) {
                        $good['unit_base'] = $unit_good['unit'];
                        $good['retail_price'] = $unit_good['retail_price'];
                    }
                }
            }
            $goods_arr = [$good];
            $good_list = supplementGoodsPrice($goods_arr, $cid);
            unset($goods_arr);
            respCustomer($good_list[0], 1);
            break;
        case 'good_search':
            //商品搜索 page : 首页 商品搜索
            param_need($data, ['scid']);
            $key = get_value($data, 'k', '');
            $scid = get_value($data, 'scid');

            $customer = $app->db->get('r_customer', '*', ['and' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer([], 0);
                exit;
            }
            $key = '%' . $key . '%';
            $fields = 'id,gphoto,gname,unit,barcode,company_id,`restrict`,marketid,isbind,pkgsize,shop_price,gcode,spec,gid,price_style';
            $sql1 = "select {$fields} from  db_goods where company_id={$scid} and flagdel=0 and publish=1 and (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids))) and gname like '{$key}' order by top desc,`order` desc,tid desc,id desc";
            // echo $sql1;
            // exit;
            $goods_arr = $app->db2->query($sql1)->fetchAll(PDO::FETCH_ASSOC);
            if (empty($goods_arr)) {
                respCustomer([], 0);
                exit;
            }
            foreach ($goods_arr as $k => $good) {
                $goods_arr[$k] = genartePhoto($good);
                $goods_arr[$k]['content'] = '';
                if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                    $goods_arr[$k] = paddingSingleMarket($goods_arr[$k], $cid, $customer['cctype'], $scid);
                }
                if (isset($goods_arr[$k]['main_good'])) {
                    unset($goods_arr[$k]['main_good']);
                }
                $goods_arr[$k]['retail_price'] = '';
                $goods_arr[$k]['unit_base'] = '';
                if ($good['pkgsize'] == 1) {
                    $unit_good = $app->db2->get('db_goods', ['unit', 'retail_price'], ['AND' => ['gid' => $good['gid'], 'pkgsize' => 0]]);
                    if ($unit_good) {
                        $goods_arr[$k]['unit_base'] = $unit_good['unit'];
                        $goods_arr[$k]['retail_price'] = $unit_good['retail_price'];
                    }
                }
            }

            $goods_arr = supplementGoodsPrice($goods_arr, $cid);

            respCustomer($goods_arr, count($goods_arr));
            break;
        case 'good_getgiveawaybyid':
            //读取赠品列表
            param_need($data, ['mgid', 'scid']);
            $msgid = get_value($data, 'mgid');
            $scid = get_value($data, 'scid');
            $good = $app->db2->get('db_goods', '*', ['and' => ['id' => $id, 'flagdel' => 0, 'publish' => 1]]);
            if (empty($good)) {
                respCustomer([], 0);
                exit;
            }
            $good = genartePhoto($good);
            $good = paddingSingleMarket($good, $cid, 0, $scid); //要看一下
            $goodses = [];
            if ($good && $good['isbind'] == 1) {
                $sql = "select *  from db_goods as g,db_goods_bind as bd where g.id=bd.child_mgid and bd.mgid={$good['id']}";
                $goodses = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
                if ($goodses) {
                    foreach ($goodses as $k => $g) {
                        $goodses[$k] = genartePhoto($g);
                    }
                }
            }
            $res = [];
            if ($goodses) {
                foreach ($goodses as $v) {
                    if ($v['giveaway'] == 1) {
                        $res[] = $v;
                    }
                }
            }
            respCustomer($res, count($res));
            break;
        case 'good_hot':
            //热门商品信息  page : 首页
            param_need($data, ['scid']);
            $scid = get_value($data, 'scid');
            $limit = get_value($data, 'limit');
            $goods_arr = [];
            //List<Goods> goodses = this.goodsService.getHotGoodsByCid(scid, limit, getLoginUser(request).getCid());
            $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
            if (!$customer) {
                respCustomer([], 0);
                exit;
            }
            $fields = 'id,gphoto,gname,unit,barcode,company_id,`restrict`,marketid,isbind,pkgsize,shop_price,gcode,price_style';
            $sql = "select {$fields} from db_goods where company_id={$scid} and flagdel=0 and publish=1 and  (FIND_IN_SET({$customer['cctype']},cctype)) and ((sids is NULL)or(FIND_IN_SET({$customer['sid']},sids)))  order by salesNum desc limit 0,{$limit}";
            $goods_arr = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            foreach ($goods_arr as $k => $good) {
                $goods_arr[$k] = genartePhoto($good);
                $goods_arr[$k]['content'] = '';
                if ($good['isbind'] == 1 || $good['pkgsize'] == 1 || $good['shop_price'] == 1) {
                    $goods_arr[$k] = paddingSingleMarket($goods_arr[$k], $cid, $customer['cctype'], $scid);
                }
                if (isset($goods_arr[$k]['main_good'])) {
                    unset($goods_arr[$k]['main_good']);
                }
            }
            //没数据
            if (!$goods_arr) {
                respCustomer([], 0);
                exit;
            }
            $count = count($goods_arr);
            if ($count % 2 == 1) {
                //截取偶数个
                $count--;
                $goods_arr = array_slice($goods_arr, 0, $count);
            }
            $goods_arr = supplementGoodsPrice($goods_arr, $cid);
            respCustomer($goods_arr, $count);
            break;
        case 'market_mgid':
            //根据商品ID获取营销活动信息  page: 分类  商品展示
            param_need($data, ['scid', 'mgid']);
            $scid = get_value($data, 'scid');
            $mgid = get_value($data, 'mgid');
            $mids = get_value($data, 'mids', '');
            if ($scid && $mgid && !empty($mids)) {
                $date = date('Y-m-d H:i:s');
                $mid_arr = explode(',', $mids);
                $markets = $app->db2->select("db_market", '*', ['AND' => ['id' => $mid_arr, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
                if ($markets) {
                    respCustomer($markets, count($markets));
                    return;
                }
            }
            respCustomer([], 0);
            break;
        case 'order_trace':
            //跟踪订单的处理信息 page: 订单 - 订单跟踪
            param_need($data, ['id', 'orderno']);
            $id = get_value($data, 'id');
            $orderno = get_value($data, 'orderno');

            $order = $app->db2->get('db_order', ['cid'], ['id' => $id]);
            if (!$order) {
                respCustomer([], 0);
                return;
            }
            $order_opers = [];
            if ($order['cid'] == $cid) {
                $fields = ['oper_time', 'status', 'action'];
                $order_opers = $app->db2->select('db_order_oper', $fields, ['order_no' => $orderno]);
            }
            respCustomer($order_opers, count($order_opers));
            break;
        case 'order_infoid':
            //读取订单信息根据订单ID page: 订单 - 订单详情
            param_need($data, ['id']);
            $id = get_value($data, 'id');
            $fields_order = ['id', 'erp_order_id', 'order_id', 'create_time', 'supplier_company_name', 'delivery', 'pt', 'ispay', 'status', 'total_amount', 'contacts', 'phone', 'receipt', 'cid', 'scid','express_money'];
            $order = $app->db2->get('db_order', $fields_order, ['id' => $id]);
            if (!$order) {
                respCustomer([], 0);
                return;
            }
            $fields_order_item = ['gname', 'giveaway', 'total', 'bindTotal', 'total_amount', 'gphoto', 'bindId'];
            $items = $app->db2->select("db_order_item", $fields_order_item, ['order_id' => $order['order_id'], 'ORDER' => 'giveaway asc']);
            $order['items'] = $items;
            //订单归属问题
            if ($order['cid'] != $cid) {
                respCustomer([], 0);
                return;
            }
            $order['erp_order_id'] = empty($order['erp_order_id']) ? '生成中' : $order['erp_order_id'];
            respCustomer($order, 1);
            break;
        case 'order_akey':
            //一键下单读取商品列表
            param_need($data, ['id', 'scid']);
            $id = get_value($data, 'id');
            $scid = get_value($data, 'scid');
            if ($id < 0 || $scid < 0) {
                respCustomer([], 0);
                return;
            }
            //获取订单信息
            $order = $app->db2->get('db_order', '*', ['id' => $id]);
            if (!$order) {
                respCustomer([], 0);
                return;
            }
            $items = $app->db2->select("db_order_item", '*', ['order_id' => $order['order_id'], 'ORDER' => 'giveaway asc']);
            $order['items'] = $items;
            if ($items) {
                $order['goodsList'] = [];
                foreach ($items as $v) {
                    $order['goodsList'][] = [
                        'gname' => $v['gname'],
                        'amount_price' => $v['total_amount'] * 100,
                        'total' => $v['total'],
                    ];
                }
            }
            //商品信息
            $goodses = [];
            foreach ($items as $v) {
                if ($v['giveaway'] == 1) {
                    continue;
                }
                if ($v['bindId']) {
                    $good_id = $v['bindId'];
                } else {
                    $good_id = $v['mgid'];
                }
                $good = $app->db2->get('db_goods', '*', ['and' => ['id' => $good_id, 'flagdel' => 0, 'publish' => 1]]);
                $good = genartePhoto($good);
                $good = paddingSingleMarket($good, $cid, 0, $scid);
                if ($good && $good['publish'] == 1) {
                    //判断商品是不是打包商品，如果是打包商品需要判断该客户是否可以购买
                    $cust = $app->db->get('r_customer', '*', ['and' => ['cid' => $cid, 'ccid' => $scid]]);
                    if ($cust) {
                        if (false !== strpos($good['cctype'], $cust['cctype']) && false !== strpost($good['sids'], $cust['sid'])) {
                            $goodses[] = $good;
                        }
                    }
                } else {
                    $goodses[] = $good;
                }
            }
            $goodses = supplementGoodsPrice($goodses, $scid);
            respCustomer($goodses, count($goodses));
            break;
        case 'order_cart':
            //获取送货紧程度列表 page: 填写订单
            //综合送货方式、送货时间、支付方式 到一个接口
            $scid = get_value($data, 'scid', 0);
            $fid = 2;
            $delivery = $app->db->select('s_config_detail', ['memo', 'value'], ['AND' => ['fid' => $fid, 'status' => 1]]);
            foreach ($delivery as $k => $v) {
                $delivery[$k]['value'] = intval($v['value']);
            }

            $pay_type = [];
            $ret = $app->db2->get('db_company_setting', 'cvalue', ['and' => ['company_id' => $scid, 'ckey' => 'show_pay_type']]);
            if ($ret) {
                $dict = $app->config('pay_type_dict');
                $pay_id_arr = explode(',', $ret);
                foreach ($pay_id_arr as $k) {
                    $pay_type[] = ['key' => intval($k), 'value' => $dict[$k]];
                }
            } else {
                $pay_type[] = ['key' => 1, 'value' => '货到付款'];
            }
            //配送信息
            $ret = $app->db2->get('db_mall', ['delivery_time', 'delivery_fee'], ['company_id' => $scid]);
            $info = [];
            if ($ret) {
                $info['time'] = $ret['delivery_time'];
                $info['fee'] = $ret['delivery_fee'];
            }

            $data = [
                'delivery' => $delivery,
                'paytype' => $pay_type,
                'info' => $info,
            ];
            respCustomer($data, count($data));
            break;

        case 'order_delivery':
            //获取送货紧程度列表 page: 填写订单
            $fid = 2;
            $data = $app->db->select('s_config_detail', ['memo', 'value'], ['AND' => ['fid' => $fid, 'status' => 1]]);
            respCustomer($data, count($data));
            break;
        case 'order_info_no':
            //根据超级订单号获取订单合集信息
            param_need($data, ['id']);
            $super_order_id = get_value($data, 'id');
            $super_orders = $app->db2->select('db_order', '*', ['super_order_id' => $super_order_id]);
            if (!$super_orders) {
                respCustomer([], 0);
                return;
            }
            $data = ['super_order_id' => $super_order_id, 'total_amount' => 0];
            foreach ($super_orders as $k => $order) {
                // $super_orders[$k]['items'] = $app->db2->select('db_order_item','*',['and'=>['order_id'=>$order['order_id']],'ORDER'=>'giveaway asc']);
                $data['total_amount'] += $order['total_amount'];
            }
            respCustomer($data, 1);
            break;
        case 'order_cancel':
            //取消订单  page: 订单 - 订单详情
            param_need($data, ['id']);
            $id = get_value($data, 'id');
            if (!$id) {
                respCustomer([], 0);
            }
            $order = $app->db2->get('db_order', ['cid', 'status', 'id', 'order_id'], ['id' => $id]);
            if (!$order || $order['cid'] != $cid) {
                respCustomer([], 0);
            }
            if ($order['status'] == 0) {
                //更新状态
                $date = date('Y-m-d H:i:s');
                //update db_order set status=#{status} where id=#{id}
                $app->db2->update('db_order', ['status' => 8], ['id' => $id]);
                $oper = [
                    'order_no' => $order['order_id'],
                    'order_id' => $order['id'],
                    'status' => 8,
                    'oper_time' => $date,
                    'action' => '',
                ];
                $app->db2->insert('db_order_oper', $oper);
            } elseif ($order['status'] == 1) {
                //需要通知ERP
                $url = $app->config('orderCancelUrl') . $order['id'];
                $resp = curl($url);
                $data = json_decode($resp, true);
                if ($data && $data['status'] == '0000') {
                    //erp取消成功
                    $app->db2->update('db_order', ['status' => 8], ['id' => $id]);
                }
            }
            respCustomer([], 1);
            break;
        case 'order_list':
            //读取订单接口  page: 我的订单
            $status = get_value($data, 'status', -1);
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);
            if ($limit > 20 || $limit <= 0) {
                $limit = 20;
            }
            $start = ($page - 1) * $limit;
            $start = ($start <= 0) ? 0 : $start;
            $fields_order = ['id', 'super_order_id', 'order_id', 'status', 'ispay', 'pt', 'total_amount', 'supplier_company_name'];

            if ($status == -1) {
                $orders = $app->db2->select('db_order', $fields_order, ['cid' => $cid, 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
                $count = $app->db2->count('db_order', ['cid' => $cid]);
            } else {
                if ($status == 3) {
                    $status = [3, 4];
                }
                $orders = $app->db2->select('db_order', $fields_order, ['AND' => ['status' => $status, 'cid' => $cid], 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
                $count = $app->db2->count('db_order', ['AND' => ['status' => $status, 'cid' => $cid]]);
            }
            if ($orders) {
                $fields_order_item = ['giveaway', 'gname', 'total', 'bindTotal', 'bindId'];
                foreach ($orders as $k => $order) {
                    $orders[$k]['items'] = $app->db2->select('db_order_item', $fields_order_item, ['order_id' => $order['order_id']]);
                }
            }

            respCustomer($orders, $count);
            break;
        case 'order_create':
            //创建订单  page: 购买下单  购买商品
            param_need($data, ['orderItem', 'ccid', 'scid', 'addresseeId', 'delivery', 'pt']);
            $scid = get_value($data, 'scid'); //供应商id
            $ccid = get_value($data, 'ccid'); //客户公司id
            $pt = get_value($data, 'pt', 1); //支付方式
            $addresseeId = get_value($data, 'addresseeId', 0); //收货地址
            $delivery = get_value($data, 'delivery', "0"); //发货方式
            $favorable = get_value($data, 'favorable', 0); //优惠金额
            $memo = get_value($data, 'memo', ''); //备注
            $orderItem = get_value($data, 'orderItem');
            $order_item_list = json_decode($orderItem, true);
            $company = $app->db->get('o_company', '*', ['id' => $data['ccid']]);
            $coupid_id = get_value($data, 'couid');
            if (!$company || !$order_item_list) {
                error(7101, 'ccid');
            }

            //计算物流费用
            $express_detail_id = get_value($data, 'express_detail_id');
            $res = null;
            if ($express_detail_id) {
                $give_good_list = $order_item_list;
                $res = $app->db2->select('db_province_express', '*', ["id" => $express_detail_id])[0];
                if ($res && $give_good_list) {
                    $weight_total = 0;
                    foreach ($give_good_list as $good) {
                        //根据GOODS获取商品数据
                        $mgood = $app->db2->select('db_goods', '*', ["id" => $good['mgid']])[0];
                        if ($mgood) {
                            if ($mgood['isbind'] == 1) {
                                //绑定商品
                                $bind_goods = $app->db2->select('db_goods_bind', '*', ["mgid" => $good['mgid']]);
                                if ($bind_goods) {
                                    $single_weight = 0; //单个绑定商品的重量
                                    foreach ($bind_goods as $sbg) {
                                        $db_goods_sbg = $app->db2->select('db_goods', '*', ['id' => $sbg['child_mgid']])[0];
                                        if ($db_goods_sbg) {
                                            $single_weight += getGoodWeight($db_goods_sbg['gid'], $scid);
                                        }
                                    }
                                    $weight_total += ($single_weight * $good['total']);
                                }
                            } else {
                                if ($mgood['pkgsize'] == 1) {
                                    //打包商品
                                    $weight_total += (getGoodWeight($mgood['gid'], $scid) * $mgood['spec'] * $good['total']);
                                } else {
                                    //非绑定商品
                                    $weight_total += (getGoodWeight($mgood['gid'], $scid) * $good['total']); //还需要算上数量
                                }
                            }
                        }
                    }
                    $res['weight'] = $weight_total;
                    //根据重量计算价格,以克计算
                    if ($weight_total <= 1000) {
                        $res['express_price'] = $res['first_price'];
                    } else {
                        $res['express_price'] = ((float)($weight_total - 1000)) / 1000 * $res['continue_price'] + $res['first_price'];
                    }

                }
            }


            $real_order_list = [];
            $tmp_order_list = [];
            foreach ($order_item_list as $item) {
                if (!$item['mgid'] || !$item['total'] || !$item['scid']) {
                    error(7101, 'orderItem');
                }
                //补充商品信息
                $good = $app->db2->get('db_goods', '*', ['AND' => ['id' => $item['mgid'], 'flagdel' => 0, 'publish' => 1]]);
                if (!$good || $good['company_id'] != $item['scid']) {
                    error(7102);
                }
                if ($good['publish'] != 1) {
                    error(7103);
                }

                $good = genartePhoto($good);
                $good = paddingSingleMarket($good, $cid, 0, $scid);
                $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $item['scid'], 'ccid' => $company['id']]]);
                if (!$customer) {
                    error(7104, $item['mgid']);
                }
                if ($customer['suid']) {
                    $item['suid'] = $uid;
                }
                $itemOrder = null;
                if ($good['isbind'] == 1 && $good['pkgsize'] == 0) {
                    //绑定数据
                    $itemOrder = Strategy::bind($scid, $ccid, $uid, $good, $customer, $item, $memo, false);
                } else if ($good['isbind'] == 0 && $good['pkgsize'] == 1) {
                    //大小包状,如果是大包装则将需将数量进行换算
                    $itemOrder = Strategy::pkg($scid, $ccid, $uid, $good, $customer, $item, $memo, false);
                } else if ($good['isbind'] == 0 && $good['pkgsize'] == 0) {
                    //原始商品分支
                    //判断该商品是否有营销活动,如果有活动.需要将问题信息获取到
                    $itemOrder = Strategy::original($scid, $ccid, $uid, $good, $customer, $item, $memo, false);
                }
                if (!is_null($itemOrder)) {
                    $tmp_order_list[] = $itemOrder;
                    $real_order_list = array_merge($real_order_list, $itemOrder['items']);
                }
            }
            if (count($real_order_list) <= 0) {
                error(7103);
            }

            //对订单进行拆分
            $order = separate_order($real_order_list);
            if (!$order) {
                respCustomer([], 1);
            }
            //保存订单信息
            if ($addresseeId) {
                $address = $app->db2->get('db_addressee', '*', ['id' => $addresseeId]);
                if (!$address) {
                    error(7101, 'address');
                }
                $order['delivery'] = $delivery;
                $order['cid'] = $company['id'];
                $order['company_name'] = $company['name'];
                $order['scid'] = $scid;
                $order['create_time'] = date('Y-m-d H:i:s');
                $platform_dict = $app->config('platform_dict');
                $order['platform'] = isset($platform_dict[$scid]) ? $platform_dict[$scid] : 'customer'; //默认微信商城 客户自下单
                $order['ispay'] = 10; //默认未支付
                $order['status'] = 0;
                $order['uid'] = $uid;
                $order['contacts'] = $address['contacts'];
                $order['phone'] = $address['phone'];
                $order['pt'] = $pt;
                $order['favorable'] = $favorable;

                $receipt = $address['province'] ? $address['province'] : '';
                $receipt .= $address['city'] ? ' ' . $address['city'] : ' ';
                $receipt .= $address['county'] ? ' ' . $address['county'] : ' ';
                $receipt .= $address['street'] ? ' ' . $address['street'] : ' ';
                $order['receipt'] = $receipt;
                $order['memo'] = $memo;
                $scompany = $app->db->get('o_company', '*', ['id' => $order['scid']]);
                if (!$scompany) {
                    error(7105, '公司' . $order['scid'] . '未找到');
                }
                $order['supplier_company_name'] = $scompany['name'];

                $doublePrice = 0;
                foreach ($tmp_order_list as $o) {
                    if (!$o['total_amount']) {
                        continue;
                    }
                    $doublePrice += $o['total_amount'];
                }
                $order['total_amount'] = $doublePrice;
                //订单总金额,还需要减去优惠金额
                if ($favorable && $order['total_amount'] > $favorable) {
                    $order['total_amount'] = ($order['total_amount'] - $favorable);
                } else {
                    $order['favorable'] = 0;
                }

                //如果使用优惠劵,则需要减去优惠金额
                if ($coupid_id > 0) {
                    $coupon_detail = $app->db2->select("db_coupon_detail", "*", ["id" => $coupid_id]);
                    if ($coupon_detail) {
                        $now = date('Y-m-d H:i:s');
                        if ($coupon_detail[0]['coupon_use_start'] <= $now && $now <= $coupon_detail[0]['coupon_use_end'] && $coupon_detail[0]['status'] == 3) {
                            $coupon_money = $coupon_detail[0]['coupon_money'];

                            //将该红包设置为已使用
                            $updateid = $app->db2->update("db_coupon_detail", ["status" => 4, "use_time" => $now, "use_order_no" => $order["order_id"]], ["id" => $coupid_id]);

                            if ($updateid) {
                                if ($order['total_amount'] > $coupon_money) {
                                    $order['total_amount'] = $order['total_amount'] - $coupon_money;
                                    $order['favorable'] = $order['favorable'] + $coupon_money;
                                } else {
                                    $order['favorable'] = $order['favorable'] + $order['total_amount'];
                                    $order['total_amount'] = 0;
                                }
                            }
                        }
                    }
                }
            }
            $order['items'] = $real_order_list;

            start_action();
            $order_id = $order['order_id'];
            foreach ($order['items'] as $item) {
                $item['order_id'] = $order_id;
                unset($item['suid']);
                $app->db2->insert('db_order_item', $item);
            }

            unset($order['items']);
            $order['express_money'] = $res['express_price'];
            $order['total_amount']=$order['express_money']+ $order['total_amount'];//加上物流费用
            $insert_id = $app->db2->insert('db_order', $order);
            $oper = [
                'order_no' => $order_id,
                'order_id' => $insert_id,
                'status' => 1,
                'oper_time' => date('Y-m-d H:i:s'),
                'action' => '创建订单',
            ];
            $app->db2->insert('db_order_oper', $oper);

            //根据规则赠送优惠劵
            //第一步根据供应商ID获取供应商的优惠活动
            $current_time = date('Y-m-d H:i:s');
            $coupon_list = $app->db2->select('db_coupon', '*', ["AND" =>
                ['company_id' => $scid, 'coupon_status[!]' => 9, 'coupon_send_start[<=]' => $current_time, 'coupon_send_end[>=]' => $current_time],
                'ORDER' => ['coupon_send_start']
            ]);
            //获取满足条件第一个优惠劵规则
            if ($coupon_list) {
                $get_coupon = null;
                foreach ($coupon_list as $coupon) {
                    if ($coupon['coupon_type'] == 1) {
                        //按用户发放,一个用户只允许领取一张
                        //判断该用户是否已经领取过该类红包了
                        $exist_coupon = $app->db2->select('db_coupon_detail', '*', ['AND' => ['coupon_id' => $coupon['id'], 'ccid' => $ccid]]);
                        if ($exist_coupon) {
                            continue;
                        } else {
                            $get_coupon = $coupon;
                            break;
                        }
                    } else if ($coupon['coupon_type'] == 2) {
                        //按订单发放,一个订单领取一张
                        if ($order['total_amount'] >= $coupon['coupon_small_money']) {
                            $get_coupon = $coupon;
                            break;
                        }
                    } else if ($coupon['coupon_type'] == 3) {
                        //按商品发送,在包括这些商品的订单中获取
                        $mall_id = $coupon['merchandise'];
                        $coupon_check_res = checkItemToCoupon($order_item_list, $mall_id);
                        if ($coupon_check_res) {
                            $get_coupon = $coupon;
                            break;
                        }
                    }
                }

                if ($get_coupon) {
                    //找到对应的红包类别进行红包生成
                    $coupon_data['company_id'] = $scid;
                    $coupon_data['coupon_id'] = $get_coupon['id'];
                    $coupon_data['coupon_name'] = $get_coupon['coupon_name'];
                    $coupon_data['coupon_order_id'] = $insert_id;
                    $coupon_data['coupon_order_no'] = $order['order_id'];
                    $coupon_data['create_time'] = $current_time;
                    $coupon_data['status'] = 2;
                    $coupon_data['coupon_money'] = $get_coupon['coupon_money'];
                    $coupon_data['coupon_small_money'] = $get_coupon['coupon_small_money'];
                    $coupon_data['coupon_type'] = $get_coupon['coupon_type'];
                    $coupon_data['coupon_send_start'] = $get_coupon['coupon_send_start'];
                    $coupon_data['coupon_send_end'] = $get_coupon['coupon_send_end'];
                    $coupon_data['coupon_use_start'] = $get_coupon['coupon_use_start'];
                    $coupon_data['coupon_use_end'] = $get_coupon['coupon_use_end'];
                    $coupon_data['customer_name'] = $company['name'];
                    $coupon_data['ccid'] = $ccid;
                    $coupon_data['merchandise'] = $get_coupon['merchandise'];

                    $send_coupon_id = $app->db2->insert('db_coupon_detail', $coupon_data);
                    if ($send_coupon_id) {
                        //修改当前该红包的发送个数
                        $app->db2->update("db_coupon", ['send_num[+]' => 1], ['id' => $get_coupon['id']]);
                    }
                }

            }

            if ($insert_id) {
                respCustomer($order['super_order_id']);
            }
            respCustomer([]);
            break;
        case 'vip_create':
            //鹏博士VIP创建订单  page: 购买会员
            param_need($data, ['ccid', 'scid', 'productid']);
            $scid = get_value($data, 'scid'); //供应商id
            $ccid = get_value($data, 'ccid'); //客户公司id
            $productid = get_value($data, 'productid', 0); //会员产品id
            $platform = get_value($data, 'platform', 'pbs');
            $company = $app->db->get('o_company', ['name'], ['id' => $ccid]);
            if (!$company) {
                error(7101, 'ccid');
            }
            $scompany = $app->db->get('o_company', ['name'], ['id' => $scid]);
            if (!$scompany) {
                error(7101, 'scid');
            }
            $product = [];
            $product_dict = $app->config('vip_product');
            foreach ($product_dict as $v) {
                if ($v['product_id'] == $productid) {
                    $product = $v;
                    break;
                }
            }
            if (empty($product)) {
                error(7108);
            }
            $is_up = 0;
            $b2c = $app->config('b2c_id');
            if ($scid == $b2c['pbs']) {
                $c_model = new Customer();
                $res = $c_model->get_price($productid, $cid, $platform);
                if ($res && $res['type'] == 1) {
                    //余额，够就直接升级
                    $product['price'] = 0;
                    $is_up = 1;
                } else {
                    //余额不够 就补充余额
                    $product['price'] = $res['price'];
                }

            }

            $now = date('Y-m-d H:i:s');
            $order = [
                'order_id' => OrderUtil::generate_order_id(),
                'create_time' => $now,
                'total_amount' => $product['price'],
                'status' => ($product['price'] == 0) ? PAY_STATUS_SUCCESS : PAY_STATUS_CREATE,
                'cid' => $cid,
                'company_name' => $company['name'],
                'scid' => $scid,
                'supplier_company_name' => $scompany['name'],
                'update_time' => $now,
                'uid' => $uid,
                'platform' => 'pbs',
                'product_id' => $productid,
                'product_name' => $product['name'],
                'isup' => $is_up,
            ];
            start_action();
            $insert_id = $app->db2->insert('db_order_vip', $order);
            if ($insert_id) {

                if ($product['price'] == 0) {
                    //升级
                    $c_model = new Customer();
                    $ret = $c_model->change_vip($productid, $cid, $platform);
                    if ($ret > 1) {
                        //升级错
                        $err_dict = $app->config('errcode');
                        $msg = isset($err_dict[$ret]) ? $err_dict[$ret] : '会员升级失败，请联系管理员';
                        error(300, $msg);
                    }
                    //升级流程
                    respCustomer('success');
                    return;
                }
                //支付流程
                respCustomer($order['order_id']);
                return;
            }
            respCustomer([]);
            break;
        case 'viporder_list':
            //鹏博士VIP订单列表  page: 我的 购买记录  只显示购买成功的
            param_need($data, ['scid']);
            $scid = get_value($data, 'scid'); //供应商id
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);
            $platform = get_value($data, 'platform', 'pbs');
            $scompany = $app->db->get('o_company', ['name'], ['id' => $scid]);
            if (!$scompany) {
                error(7101, 'scid');
            }
            $start = ($page - 1) * $limit;

            $total = $app->db2->count('db_order_vip', ['AND' => ['uid' => $uid, 'scid' => $scid, 'status' => PAY_STATUS_SUCCESS]]);
            if (!$total) {
                respCustomer([], 0);
            }
            $data = $app->db2->select('db_order_vip', ['order_id', 'id', 'create_time', 'notify_time', 'product_id', 'product_name', 'status', 'total_amount', 'isup'], ['AND' => ['uid' => $uid, 'scid' => $scid, 'status' => PAY_STATUS_SUCCESS], 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
            // echo $app->db2->last_query();
            // exit;
            respCustomer($data, $total);
            break;
        case 'app_version':
            $data = [
                'version' => '2.0.0',
                'url' => 'http://download.ms9d.com/app/android/android-debug-222.apk',
                'description' => "1、修复商品批发价价格显示问题<br/>2、修复启动画面时间太长问题<br/>3、修复启动画面时间太长问题"
            ];
            respCustomer($data);
            break;
        default:
            error(1100);
    }

}

/**
 * 对别订单
 * @param $order_ites_list 商品列表
 * @param $maill_ids 红包需要购买的列表
 */
function checkItemToCoupon($order_item_list, $mall_id)
{
    if ($mall_id) {
        $mall_flag = 0;
        $mall_ids = split(",", $mall_id);
        foreach ($order_item_list as $wx_order) {
            foreach ($mall_ids as $mid) {
                if ($wx_order['mgid'] == $mid) {
                    $mall_flag++;
                }
            }
        }
        if (sizeof($mall_ids) == $mall_flag) {
            return true;
        }
    }
    return false;
}

/**
 * 分割订单号
 * @param  [type] $order_items [description]
 * @return [type]              [description]
 */
function separate_order($order_items)
{
    $current = current($order_items);
    $super_order_id = OrderUtil::generate_super_order_id();
    $order = [
        'items' => [],
        'super_order_id' => $super_order_id,
        'erp_order_id' => '',
        'order_id' => OrderUtil::generate_order_id(),
        'create_time' => '',
        'total_amount' => 0,
        'receipt' => '',
        'province' => '',
        'city' => '',
        'county' => '',
        'status' => 0,
        'cid' => 0,
        'company_name' => '',
        'update_time' => '',
        'scid' => $current['scid'],
        'uid' => 0,
        'memo' => '',
        'ispay' => 0,
        'favorable' => 0,
        'pt' => 0,
        'platform' => '',
        'suid' => $current['suid'],
    ];
    return $order;
}


/**
 * 订单号生成工具
 */
class OrderUtil
{
    /**
     * 生成一个订单号
     * @return [type] [description]
     */
    public static function generate_order_id()
    {
        return date('YmdHis') . '' . (rand(0, 899999) + 100000);
    }


    /**
     * 生成一个超级订单号
     * @return [type] [description]
     */
    public static function generate_super_order_id()
    {
        return '9' . date('YmdHis') . '' . (rand(0, 899999) + 100000);
    }
}

/**
 * 策略封装类
 *
 */
class Strategy
{

    /**
     * 商品绑定策略
     * @param  [type]  $scid       [description]
     * @param  [type]  $ccid       [description]
     * @param  [type]  $uid       [description]
     * @param  [type]  $good       [description]
     * @param  [type]  $customer   [description]
     * @param  [type]  $order_item [description]
     * @param  [type]  $memo       [description]
     * @param  boolean $is_saleman [description]
     * @return [type]              [description]
     */
    public static function bind($cid, $ccid, $uid, $good, $customer, $old_order_item, $memo, $is_saleman = false)
    {
        $order = [
            'items' => [],
            'super_order_id' => '',
            'erp_order_id' => '',
            'order_id' => '',
            'create_time' => '',
            'total_amount' => 0,
            'receipt' => '',
            'province' => '',
            'city' => '',
            'county' => '',
            'status' => 0,
            'cid' => 0,
            'company_name' => '',
            'update_time' => '',
            'scid' => 0,
            'uid' => 0,
            'memo' => '',
            // 'ispay' => 0,
            'favorable' => 0,
            'pt' => 0,
            'platform' => '',
        ];
        $order_items = [];
        //检测打包商品可以购买
        $can_shop = false;
        if ($good['cctype'] && false !== strpos(',' . $good['cctype'] . ',', ',' . $customer['cctype'] . ',')) {
            $can_shop = true;
        }

        if (!$can_shop) {
            throw new Exception("该打包商品主商品已下架");
        }
        $app = \Slim\Slim::getInstance();
        //检测里面的商品是否有下架了的,如果有下架的,则可以购买该商品
        $market_good_list = [];
        $main_good = get_good_by_id($good['id'], $cid, $ccid);
        if ($main_good && $main_good['isbind'] == 1) {
            $sql = "select *  from db_goods as g,db_goods_bind as bd where g.id=bd.child_mgid and bd.mgid={$good['id']}";
            $market_good_list = $app->db2->query($sql)->fetchAll(PDO::FETCH_ASSOC);
            if ($market_good_list) {
                foreach ($market_good_list as $k => $v) {
                    $market_good_list[$k] = genartePhoto($v);
                }
            }
        }
        $old_price = supplementGoodsPriceReturnMap($market_good_list, $ccid);
        if ($old_price && $market_good_list) {
            //进行限购判断
            if ($good['restrict'] && ($good['restrict'] < $old_order_item['total'])) {
                $old_order_item['total'] = $good['restrict'];
            }

            //存在打包商品，开始换算打包商品
            foreach ($market_good_list as $g) {
                $total_num = $g['num'] * $old_order_item['total'];
                $bind_item = [
                    'scid' => $old_order_item['scid'],
                    'mgid' => $g['id'],
                    'bindId' => $old_order_item['mgid'],
                    'bindTotal' => $old_order_item['total'],
                    'total' => $total_num,
                    'suid' => ($is_saleman) ? $uid : $customer['suid'],
                    'gbarcode' => $g['barcode'],
                    'gcode' => $g['gcode'],
                    'gname' => $g['gname'],
                    'gid' => $g['gid'],
                    'memo' => $good['gcode'],
                    'giveaway' => 0,
                    'gphoto' => $g['gphoto'],
                    'total_amount' => $old_price[$g['gcode']] * $total_num,
                    'market_id' => 0,
                    'price' => 0,
                    'order_id' => '',
                ];
                $order_items[] = $bind_item;
                $order['total_amount'] = $order['total_amount'] + $bind_item['total_amount'];
            }

            //读取总价
            //从Shop_Price里面读取价格
            $shop_price = $app->db2->get("db_shop_price", '*', ['AND' => ['company_id' => $cid, 'cctype' => $customer['cctype'], 'sid' => $customer['sid'], 'mgid' => $old_order_item['mgid']]]);
            $total_price = $shop_price['price'] * $old_order_item['total'];

            //最后一个商品的价格等于总额减去前面商品价格后的余额
            $pre_amount = 0;
            $size = count($order_items);
            $current = 0;
            foreach ($order_items as $k => $item) {
                $price = (($item['total_amount'] / $order['total_amount'] * $total_price) * 100);
                if ($size == $current) {
                    $price = $total_price - $pre_amount;
                    $order_items[$k]['total_amount'] = $price;
                } else {
                    $pre_amount += $price / 100.0;
                    $order_items[$k]['total_amount'] = $price / 100.0;
                }

                $current++;
            }
            $order['total_amount'] = $total_price;
        }

        //针对商品类营销活动
        //是否有营销活动,如果有营销活动,则将商品投放营销活动策略中去
        $date = date('Y-m-d H:i:s');
        $mids = explode(',', $good['marketid']);
        $markets = $app->db2->select('db_market', '*', ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
        if ($markets) {
            foreach ($markets as $market) {
                $order = Activeity::buy_free($order, $market, $uid, $customer, $good, $old_order_item);
            }
        }

        if (is_null($order['items'])) {
            $order['items'] = [];
        }
        $order['items'] = array_merge($order['items'], $order_items);
        return $order;
    }


    /**
     * 大小包状 打包商品
     * @param  [type]  $cid            [description]
     * @param  [type]  $ccid           [description]
     * @param  [type]  $uid           [description]
     * @param  [type]  $goods          [description]
     * @param  [type]  $customer       [description]
     * @param  [type]  $old_order_item [description]
     * @param  [type]  $memo           [description]
     * @param  boolean $is_saleman [description]
     * @return [type]                  [description]
     */
    public static function pkg($cid, $ccid, $uid, $goods, $customer, $old_order_item, $memo, $is_saleman = false)
    {
        $order = [
            'items' => [],
            'super_order_id' => '',
            'erp_order_id' => '',
            'order_id' => '',
            'create_time' => '',
            'total_amount' => 0,
            'receipt' => '',
            'province' => '',
            'city' => '',
            'county' => '',
            'status' => 0,
            'cid' => 0,
            'company_name' => '',
            'update_time' => '',
            'scid' => 0,
            'uid' => 0,
            'memo' => '',
            // 'ispay' => 0,
            'favorable' => 0,
            'pt' => 0,
            'platform' => '',
        ];
        $orderItem = [];

        //检测打包商品可以购买
        $can_shop = false;
        if ($goods['cctype'] && false !== strpos(',' . $goods['cctype'] . ',', ',' . $customer['cctype'] . ',')) {
            $can_shop = true;
        }

        if (!$can_shop) {
            throw new Exception("该打包商品主商品已下架");
        }
        $app = \Slim\Slim::getInstance();
        $main_order_item = [
            'gbarcode' => $goods['barcode'],
            'gcode' => $goods['gcode'],
            'gname' => $goods['gname'],
            'gid' => $goods['gid'],
            'giveaway' => 0,
            'gphoto' => $goods['gphoto'],
            'memo' => $goods['gcode'],
            'suid' => ($is_saleman) ? $uid : $customer['suid'],
            'mgid' => $goods['id'],
            'bindTotal' => $old_order_item['total'],
            'total' => 0,
            'market_id' => 0,
            // 'market_name' => '',
            'price' => 0,
            'scid' => 0,
            'order_id' => '',
            'total_amount' => 0,
            'bindId' => 0,
        ];

        //从Shop_Price里面读取总价价格
        $shop_price = $app->db2->get("db_shop_price", '*', ['AND' => ['company_id' => $cid, 'cctype' => $customer['cctype'], 'sid' => $customer['sid'], 'mgid' => $goods['id']]]);
        if ($shop_price && $shop_price['price']) {
            $main_order_item['price'] = $shop_price['price'];
            $main_order_item['total_amount'] = $shop_price['price'] * $old_order_item['total'];

            //将不是打包商品的订单号添加列表中去
            $main_order_item['total'] = $goods['spec'] * $old_order_item['total'];
            $orderItem[] = $main_order_item;
            $order['total_amount'] = $shop_price['price'] * $old_order_item['total'];
        }

        //针对商品类营销活动
        //是否有营销活动,如果有营销活动,则将商品投放营销活动策略中去
        $date = date('Y-m-d H:i:s');
        $mids = explode(',', $goods['marketid']);
        $markets = $app->db2->select('db_market', '*', ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
        if ($markets) {
            foreach ($markets as $market) {
                $order = Activeity::buy_free($order, $market, $uid, $customer, $goods, $old_order_item);
            }
        }

        if (is_null($order['items']) || empty($order['items'])) {
            $order['items'] = [];
        }
        $order['items'] = array_merge($order['items'], $orderItem);
        unset($order['item']);
        return $order;
    }


    /**
     * 原始商品
     * @param  [type]  $cid            [description]
     * @param  [type]  $ccid           [description]
     * @param  [type]  $uid           [description]
     * @param  [type]  $goods          [description]
     * @param  [type]  $customer       [description]
     * @param  [type]  $old_order_item [description]
     * @param  [type]  $memo           [description]
     * @param  boolean $is_saleman [description]
     * @return [type]                  [description]
     */
    public static function original($cid, $ccid, $uid, $goods, $customer, $old_order_item, $memo, $is_saleman = false)
    {
        $order = [
            'items' => [],
            'super_order_id' => '',
            'erp_order_id' => '',
            'order_id' => '',
            'create_time' => '',
            'total_amount' => 0,
            'receipt' => '',
            'province' => '',
            'city' => '',
            'county' => '',
            'status' => 0,
            'cid' => 0,
            'company_name' => '',
            'update_time' => '',
            'scid' => 0,
            'uid' => 0,
            'memo' => '',
            // 'ispay' => 0,
            'favorable' => 0,
            'pt' => 0,
            'platform' => '',
        ];
        $orderItem = [];

        $main_order_item = [
            'gbarcode' => $goods['barcode'],
            'gcode' => $goods['gcode'],
            'gname' => $goods['gname'],
            'gid' => $goods['gid'],
            'giveaway' => 0,
            'gphoto' => $goods['gphoto'],
            'memo' => $goods['gcode'],
            'total' => $old_order_item['total'],
            'suid' => ($is_saleman) ? $uid : $customer['suid'],
            'mgid' => $goods['id'],
            'bindTotal' => $old_order_item['total'],
            'market_id' => 0,
            // 'market_name' => '',
            'price' => 0,
            'scid' => 0,
            'order_id' => '',
            'total_amount' => 0,
            'bindId' => 0,
        ];
        $app = \Slim\Slim::getInstance();
        //获取价格
        $goodses = [];
        $goodses[] = $goods;
        $prices = supplementGoodsPriceReturnMap($goodses, $ccid);
        if ($prices) {
            //检测是否有营销策略
            $price = isset($prices[$goods['gcode']]) ? $prices[$goods['gcode']] : 0;
            if ($price) {
                $main_order_item['price'] = $price;
                $main_order_item['total_amount'] = $price * $main_order_item['total'];
                $orderItem[] = $main_order_item;
                $order['total_amount'] = $price * $main_order_item['total'];
            }
        }
        //针对商品类营销活动
        //是否有营销活动,如果有营销活动,则将商品投放营销活动策略中去
        $date = date('Y-m-d H:i:s');
        $mids = explode(',', $goods['marketid']);
        $markets = $app->db2->select('db_market', '*', ['AND' => ['id' => $mids, 'start_time[<]' => $date, 'end_time[>]' => $date]]);
        if ($markets) {
            foreach ($markets as $market) {
                $order = Activeity::buy_free($order, $market, $uid, $customer, $goods, $old_order_item);
            }
        }

        if (is_null($order['items'])) {
            $order['items'] = [];
        }
        $order['items'] = array_merge($order['items'], $orderItem);
        return $order;
    }

}


/**
 * 获取单个商品的ID
 * @param $good
 */
function getGoodWeight($good, $scid)
{
    $app = \Slim\Slim::getInstance();
    //从基础资料中读取商品的Weight信息
    if ($good) {
        $o_good = $app->db->select('o_company_goods', '*', ['AND' => ['gid' => $good, 'in_cid' => $scid]]);
        if ($o_good) {
            return $o_good[0]['weight'];
        }
    }
    return 0;
}

/**
 * 活动
 */
class Activeity
{

    /**
     * 买赠策略
     * @param  [type] $type [description]
     * @return [type]       [description]
     */
    public static function buy_free($order, $market, $uid, $customer, $goods, $orderItem)
    {
        if (empty($order) || empty($market) || !$uid || empty($customer) || empty($goods)) {
            return;
        }

        $now = time();
        if (strtotime($market['start_time']) > $now || strtotime($market['end_time']) < $now) {
            return $order;
        }

        $strategy = $market['strategy'];
        if (empty($strategy)) {
            return $order;
        }
        if (empty($order['item'])) {
            $order['item'] = [];
        }
        $app = \Slim\Slim::getInstance();
        $give_good_list = json_decode($strategy, true);
        if ($give_good_list) {
            foreach ($give_good_list as $give) {
                $zn_good = $app->db2->get('db_goods', '*', ['id' => $give['mgid']]);
                if ($zn_good) {
                    $zn_good = genartePhoto($zn_good);
                    $order_item = [
                        'gbarcode' => $zn_good['barcode'],
                        'gcode' => $zn_good['gcode'],
                        'gname' => $zn_good['gname'],
                        'gid' => $zn_good['gid'],
                        'giveaway' => 1,
                        'gphoto' => $zn_good['gphoto'],
                        'total' => $give['total'] * $orderItem['total'],
                        'price' => 0,
                        'total_amount' => 0,
                        'market_id' => $market['id'],
                        'memo' => $goods['gcode'],
                        'bindTotal' => $give['total'],
                        'mgid' => $zn_good['id'],
                        'gid' => $zn_good['gid'],
                        'market_name' => '',
                        'scid' => 0,
                        'order_id' => '',
                        'suid' => 0,
                        'bindId' => 0,
                    ];
                    $order['items'][] = $order_item;
                }
            }
        }
        return $order;
    }
}

/**
 * 根据id获取商品信息
 * @param  [type] $mgid [description]
 * @param  [type] $cid  [description]
 * @param  [type] $ccid [description]
 * @return [type]       [description]
 */
function get_good_by_id($mgid, $cid, $ccid)
{
    $app = \Slim\Slim::getInstance();
    $good = $app->db2->get('db_goods', '*', ['AND' => ['id' => $mgid, 'flagdel' => 0, 'publish' => 1]]);
    if ($good) {
        $good = genartePhoto($good);
        $good = paddingSingleMarket($good, $cid, 0, $ccid);
    }
    return $good;
}

/**
 * 针对原始商品
 * @param  [type]  $goods  [description]
 * @param  [type]  $cid    [description]
 * @param  integer $cctype [description]
 * @return [type]          [description]
 */
function supplementGoodsPriceReturnMap($goods, $cid)
{
    $first = current($goods);
    $good_erp = [];
    foreach ($goods as $good) {
        if ($good['isbind'] == 0) {
            $good_erp[] = ['gcode' => $good['gcode'], 'scid' => $good['company_id']];
        }
    }
    $app = \Slim\Slim::getInstance();
    $params = [
        'cid' => $cid,
        'scid' => $first['company_id'],
        'goods_list' => json_encode($good_erp),
    ];
    if ($good_erp) {
        $resp = curl($app->config('erpPriceUrl'), $params);
        $data = json_decode($resp, true);
        if ($data && $data['status'] == "0000" && $data['msg']) {
            $price_arr = $data['msg'];
            return $price_arr;  //['10000162'=>201.0]
        }
    }

    return false;
}

/**
 * 从erp补充商品价格信息
 * @param  [type]  $goods  [description]
 * @param  [type]  $cid    [description]
 * @param  integer $cctype [description]
 * @return [type]          [description]
 */
function supplementGoodsPrice($goods, $cid, $cctype = 0)
{
    $first = current($goods);
    $good_erp = [];
    foreach ($goods as $good) {
        if ($good['isbind'] == 0 && $good['pkgsize'] == 0) {
            $good_erp[] = ['gcode' => $good['gcode'], 'scid' => $good['company_id']];
        }
    }
    $app = \Slim\Slim::getInstance();
    $params = [
        'cid' => $cid,
        'scid' => $first['company_id'],
        'goods_list' => json_encode($good_erp),
    ];
    if ($good_erp) {
        $resp = curl($app->config('erpPriceUrl'), $params);
        $data = json_decode($resp, true);
        if ($data && $data['status'] == "0000" && $data['msg']) {
            $price_arr = $data['msg'];
            foreach ($goods as $k => $good) {
                if ($good['isbind'] == 0 && $good['pkgsize'] == 0) {
                    if (isset($price_arr[$good['gcode']]) && $good['isbind'] != 1) {
                        $goods[$k]['price'] = $price_arr[$good['gcode']];
                    }
                }
            }
        }
    }
    return $goods;
}

/**
 * 补充商品的绑定信息
 * @param  [type] $good [description]
 * @param  [type] $cid   [description]
 * @param  [type] $ccid  [description]
 * @return [type]        [description]
 */
function paddingSingleMarket($good, $cid, $ccid, $scid, $need_bind_good = true)
{
    $app = \Slim\Slim::getInstance();
    //select * from r_customer where cid=#{cid} and ccid=#{cus_com_id}
    $customer = $app->db->get('r_customer', '*', ['AND' => ['cid' => $scid, 'ccid' => $cid]]);
    if ($customer) {
        $shop_price = getShopPriceByCcTypeAndSidAndMgid($scid, $customer['cctype'], $customer['sid'], $good['id']);
        if ($shop_price) {
            $good['price'] = $shop_price['price'];
        }
    }
    //绑定商品
    if ($good['isbind'] == 1 && $need_bind_good) {
        $goods_list = $app->db2->query("select * from db_goods as g,db_goods_bind as bd where g.id=bd.child_mgid and bd.mgid={$good['id']}")->fetchAll(PDO::FETCH_ASSOC);
        if ($goods_list) {
            //组合打包信息文字
            $goods_map = [];
            foreach ($goods_list as $g) {
                $goods_map[$g['giveaway']][] = $g;
            }
            $main_good = $goods_map[MARKET_GIVEWAYA_MAIN];  //主商品类型
            foreach ($main_good as $k => $m) {
                $m = genartePhoto($m);
                $good['main_good'][] = $m;
            }
        }
    }
    return $good;
}

/**
 * 生成商品图片
 * @param  [type] $good [description]
 * @param  [type] $need_good_list 是否添加gphoto_list
 * @return [type]       [description]
 */
function genartePhoto($good, $need_good_list = false)
{
    $need_good_list && $good['gphoto_list'] = [];
    if (!empty($good['gphoto']) && $good['gphoto'] != "0") {

        if (false !== strpos($good['gphoto'], ',')) {
            $tmp = explode(',', $good['gphoto']);
            $good['gphoto'] = IMG_PREFIX . '/' . $good['gcode'] . '_' . $tmp[0] . '.jpg';
            if ($need_good_list) {
                foreach ($tmp as $v) {
                    $good['gphoto_list'][] = IMG_PREFIX . '/' . $good['gcode'] . '_' . $v . '.jpg';
                }
            }

        } else {
            $good['gphoto'] = IMG_PREFIX . '/' . $good['gcode'] . '_' . $good['gphoto'] . '.jpg';
            $need_good_list && $good['gphoto_list'][] = $good['gphoto'];
        }
    } else {
        $good['gphoto'] = IMG_DEFAULT_URL;
        $need_good_list && $good['gphoto_list'][] = $good['gphoto'];
    }
    return $good;
}

/**
 * 根据客户类型,仓库ID,商品ID获取价格信息
 *
 * @param  [type] $cid    [description]
 * @param  [type] $cctype [description]
 * @return [type]         [description]
 */
function getShopPriceByCcTypeAndSidAndMgid($cid, $cctype, $sid, $mgid)
{
    $app = \Slim\Slim::getInstance();
    $shop_price = $app->db2->get('db_shop_price', '*', ['AND' => ['company_id' => $cid, 'cctype' => $cctype, 'sid' => $sid, 'mgid' => $mgid]]);
    return $shop_price;
}


/**
 * 将x, y 转换为百度坐标
 *
 */
function getBaiduGeo($latitude, $longgitude)
{
    $api = 'http://api.map.baidu.com/geoconv/v1/?coords=%s,%s&from=1&to=5&ak=6f7c8b781a1ede44b6b4fa27078c6dd0';
    $api = sprintf($api, $longgitude, $latitude);
    $data = curl($api);
    if (!$data) {
        return false;
    }
    $json = json_decode($data, true);
    if ($json['result'] && $json['status'] == 0) {
        return ['longgitude' => $json['result']['x'], 'latitude' => $json['result']['x']];
    }
    return false;
}


function sortByCsort($a, $b)
{
    if ($a['csort'] >= $b['csort']) {
        return 1;
    }
    return -1;
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
