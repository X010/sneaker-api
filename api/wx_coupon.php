<?php
/**
 *  微信商城优惠劵
 *  此接口不需要ticket
 *
 *    用户登录/绑定用
 */
function wx_coupon($action, $id = Null)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $data = $app->params;
    $cid = $app->Sneaker->cid;
    $uid = $app->Sneaker->uid;


    switch ($action) {
        case 'list':
            param_need($data, ['ccid']);
            $status = get_value($data, 'status', 1);
            $page = get_value($data, 'page', 1);
            $limit = get_value($data, 'limit', 20);
            $start = ($page - 1) * $limit;

            $company = $app->db->get('o_company', '*', ['id' => $data['ccid']]);
            if (!$company) {
                error(7101, 'ccid');
            }

            $res = [];
            switch ($status) {
                case  1://获取所有红包数据
                    $res = $app->db2->select("db_coupon_detail", "*", ["AND" => ['status[!]' => [9, 2], 'ccid' => $company['id']], 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
                    break;
                case  3://获取未使用的红包
                    $res = $app->db2->select("db_coupon_detail", "*", ["AND" => ['status' => 3, 'ccid' => $company['id']], 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
                    break;
                case 4://获取已使用的红包
                    $res = $app->db2->select("db_coupon_detail", "*", ["AND" => ['status' => 4, 'ccid' => $company['id']], 'ORDER' => 'id DESC', 'LIMIT' => [$start, $limit]]);
                    break;
            }
            respCustomer($res);
            break;

        case 'order_coupon':
            //获取本次订单可以使用的红包
            $res = array();
            $order_item = get_value($data, 'items');
            $order_total_money = get_value($data, 'order_total_money');
            $ccid = get_value($data, 'ccid');
            if ($order_item && $order_total_money && $ccid) {
                $ccid_coupon = $app->db2->select("db_coupon_detail", "*", ["AND" => ["ccid" => $ccid, 'status' => 3]]);
                if ($ccid_coupon) {
                    $current_time = date('Y-m-d H:i:s');
                    foreach ($ccid_coupon as $single_coupon) {
                        if ($single_coupon['coupon_use_start'] <= $current_time && $current_time <= $single_coupon['coupon_use_end']) {
                            if ($single_coupon['coupon_type'] == 1) {
                                array_push($res, $single_coupon);//按用户发送,直接可以使用
                            } else if ($single_coupon['coupon_type'] == 2) {
                                if ($single_coupon['coupon_small_money'] <= $order_total_money) {
                                    array_push($res, $single_coupon); //判断订单金额
                                }
                            } else if ($single_coupon['coupon_type'] == 3) {
                                $order_item_data = json_decode($order_item, true);
                                $order_item_migs = array();
                                if ($order_item_data) {
                                    foreach ($order_item_data as $oid) {
                                        array_push($order_item_migs, (int)$oid['id']);
                                    }
                                    //需要判断商品的归属
                                    $good_str = $single_coupon['merchandise'];
                                    if ($good_str) {
                                        $migs_ids = explode(',', $good_str);
                                        if ($migs_ids) {
                                            $res_p = true;
                                            foreach ($migs_ids as $mid) {
                                                if ($mid && $mid != "" && !empty($mid)) {
                                                    $imid = (int)$mid;
                                                    if (!in_array($imid, $order_item_migs)) {
                                                        $res_p = false;
                                                        break;
                                                    }
                                                }
                                            }
                                            if ($res_p) {
                                                array_push($res, $single_coupon); //判断订单金额
                                            }
                                        }
                                    }
                                }
                            }
                        } else {
                            $app->db2->update("db_coupon_detail", ["status" => 8], ["id" => $single_coupon['id']]);
                        }
                    }
                }
            }
            respCustomer($res);
            break;
        case 'check':
            $res = [];
            param_need($data, ['couid']);
            $couid = $data["couid"];
            $coupon_detail = $app->db2->select("db_coupon_detail", "*", ["id" => $couid]);
            if ($coupon_detail) {
                $now = date('Y-m-d H:i:s');
                if ($coupon_detail[0]['coupon_use_start'] <= $now && $now <= $coupon_detail[0]['coupon_use_end'] && $coupon_detail[0]['status'] == 3) {
                    respCustomer($coupon_detail);
                } else {
                    respCustomer($res, 0, 400, "coupon not effect");
                }
            }
            respCustomer($res, 0, 404, "coupon no found");
            break;
        case 'active_card':
            $res = [];
            $card_no = get_value($data, 'card_no');
            $ccid = get_value($data, 'ccid');
            $scid = get_value($data, 'scid');
            if ($card_no) {
                $coupon_id=get_coupon_id($card_no);

            }
            respCustomer($res);
            break;
    }


}

/**
 * 获取红包ID
 * @param $card_no
 */
function get_coupon_id($card_no)
{
    $coupon_id = "";
    for ($i = 0; $i < strlen($card_no); $i++) {
        if ($card_no[$i] >= '0' && $card_no[$i] <= '9') {
            $coupon_id=$coupon_id.$card_no[$i];
        }
    }
    return (int)$coupon_id;
}

