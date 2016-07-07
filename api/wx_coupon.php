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
            $res=[];
            $order_item = get_value($data, 'items');
            $order_total_money = get_value($data, 'order_total_money');
            if ($order_item && $order_total_money) {

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
    }


}