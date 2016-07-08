<?php
/**
 * Created by IntelliJ IDEA.
 * User: b3st9u
 * Date: 16/7/3
 * Time: 16:55
 */
function wx_express($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    switch ($action) {
        case 'express_get':
            $province = get_value($data, 'province'); //获取收货人的省份
            $express_res = null;
            if ($province) {
                $express_res = $app->db2->query("select a.id as id, b.express as express,a.express_id as express_id  from db_province_express as a,db_express as b where a.status<>9 and b.status<>9 and a.express_id=b.id and province='$province'")->fetchAll();
            }
            respCustomer($express_res);
            break;
        case 'express_order_price':
            //根据上传的商品信息来获取物流价格
            $express_detail_id = get_value($data, 'express_detail_id');
            $goods_ids = get_value($data, 'gid');
            $scid = get_value($data, 'scid');
            $res = null;
            if (!empty($goods_ids) && $express_detail_id) {
                $give_good_list = json_decode($goods_ids, true);
                $res = $app->db2->select('db_province_express', '*', ["id" => $express_detail_id])[0];
                if ($res && $give_good_list) {
                    $weight_total = 0;
                    foreach ($give_good_list as $good) {
                        //根据GOODS获取商品数据
                        $mgood = $app->db2->select('db_goods', '*', ["id" => $good['id']])[0];
                        if ($mgood) {
                            if ($mgood['isbind'] == 1) {
                                //绑定商品
                                $bind_goods = $app->db2->select('db_goods_bind', '*', ["mgid" => $good['id']]);
                                if ($bind_goods) {
                                    $single_weight = 0; //单个绑定商品的重量
                                    foreach ($bind_goods as $sbg) {
                                        $db_goods_sbg = $app->db2->select('db_goods', '*', ['id' => $sbg['child_mgid']])[0];
                                        if ($db_goods_sbg) {
                                            $single_weight += getGoodWeight($db_goods_sbg['gid'], $scid);
                                        }
                                    }
                                    $weight_total += ($single_weight * $good['qty']);
                                }
                            } else {
                                if ($mgood['pkgsize'] == 1) {
                                    //打包商品
                                    $weight_total += ((getGoodWeight($mgood['gid'], $scid) * $mgood['spec']) * $good['qty']);
                                } else {
                                    //非绑定商品
                                    $weight_total += (getGoodWeight($mgood['gid'], $scid) * $good['qty']); //还需要算上数量
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
            respCustomer($res);
            break;
        case 'express_balance_get':
            //获取该用户的物流余额
            $scid = get_value($data, 'scid');
            $ccid = get_value($data, 'ccid');
            $res = [];
            if ($scid && $ccid) {
                $res = $app->db->select("r_customer", "*", ["AND" => ["cid" => scid, "ccid" => $ccid]]);
            }
            respCustomer($res);
            break;
        case 'express_price_get':
            $province = get_value($data, 'province');//收货省份
            if (!$province) {
                error(300, '请填写收货地址');
            }
            $express_id = get_value($data, 'express_id');//快递公司id
            $good_id = get_value($data, 'good_id');//货物id
            $good_count = get_value($data, 'good_count');//数量
            $weight = $app->db->get('o_company_goods', ['weight'], ['gid' => $good_id]);//查询货物箱重
            if (!$weight) {
                error(300, '无法获取箱重');
            }
            //查询快递价格
            $express_price = $app->db2->get('db_province_express', ['first_price', 'continue_price'], ['AND' => ['province' => $province, 'express_id' => $express_id, 'status' => 1]]);

            $data = 0;
            if ($express_price) {
                $first_price = $express_price['first_price'];
                $continue_price = $express_price['continue_price'];
                $continue_weight = (int)$weight * (int)$good_count - 1;//续重重量 箱重＊数量－首重1kg
                $price = $first_price + $continue_weight * $continue_price;//计算运费 首重价格＋(续重重量＊续重价格)
                $data = $price;
            } else {
                error(300, '无法获取运费');
            }
            respCustomer($data);
            break;
        default:
            error(1100);
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