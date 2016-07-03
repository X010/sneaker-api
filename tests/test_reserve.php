<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Unit-test of company
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     tests
 */

include_once "common.php";

class Reserve_Test extends PHPUnit_Framework_TestCase{
    private $data = [];
    private $id = 0;
    private $uid = [];
    private $ticket = [];
    private $cid = [];
    private $cname = [];
    private $sid = [];
    private $sname = [];
    private $gid = [];
    private $oid = [];
    /**
     * init
     */
    public function __construct(){
        $this->db = get_db();
    }


    /**
     * main
     */
    public function test_all(){
        //注册账户
        $this->reg_user_for_unittest('unittest1');


        //登陆账户
        $this->login('unittest1');


        //创建公司
        $this->create_company('unittest1');


        //创建仓库
        $this->create_store('unittest1','s1');
        $this->create_store('unittest1','s2');


        //创建商品品牌和类型
        $this->data['bid'] = $this->db->insert('o_goods_brand', ['name' => '测试品牌','code' => 'CSPP']);
        $this->data['tid'] = $this->db->insert('o_goods_type',['name' => '测试类型','code'=>'99']);

        //创建商品
        $this->create_goods('unittest1', 'g1');

        //创建仓库商品
        $this->create_company_goods('unittest1', 'g1');

        //生成报溢单
        $this->create_overflow();
        //修改报溢单
        $this->update_overflow();
        //审核报溢单
        $this->check_overflow();
        //查看报溢单
        $this->read_overflow();
        //查看报溢单详情
        $this->read_detail_overflow();
        //查看库存商品
        $this->reserve_read_goods();
        //查看库存批次
        $this->reserve_read();
        //修正报溢单
        $this->stock_in_repaire();
        //冲正报溢单
        $this->stock_in_flush();
        //重新审核报溢单，不然没有库存没法报损
        $this->check_overflow2();        
        //生成报损单
        $this->create_loss();
        //修改报损单
        $this->update_loss();
        //审核报损单
        $this->check_loss();
        //查看报损单
        $this->read_loss();
        //查看报损单详情
        $this->read_detail_loss();
        //修正报损单
        $this->stock_out_repaire();
        //冲正报损单
        $this->stock_out_flush();
        //生成调拨单
        $this->create_transfer();
        //修改调拨单
        $this->update_transfer();
        //审核调拨单
        $this->check_transfer();
        //查看调拨单
        $this->read_transfer();
        //查看调拨单详情
        $this->read_detail_transfer();
    }

    /**
     * clean
     */
    public function __destruct(){
        //登出
        //清除用户
        $this->db->delete('o_user', ['username[~]' => 'unittest%']);
        //清除库存
        $sql = "select r.id from `r_reserve` as r,`o_company` as c where r.cid=c.id and (c.name like '单元测试公司%' or c.name like 'unittest%')";
        $res = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $v){
            $this->db->delete('r_reserve',['id'=>$v['id']]);
        }
        //清除公司
        $this->db->delete('o_company', ['name[~]' => '%unittest%']);
        $this->db->delete('o_company', ['name[~]' => '%单元测试公司%']);
        //清除仓库
        $this->db->delete('o_store', ['name[~]' => '%unittest%']);
        //清除商品
        $this->db->delete('o_goods', ['name[~]' => '%单元测试商品%']);
        $this->db->delete('o_store_goods', ['name[~]' => '%单元测试商品%']);
        //清除测试品牌和类型
        $this->db->delete('o_goods_type', ['name' => '测试类型']);
        $this->db->delete('o_goods_brand', ['name' => '测试品牌']);
        $this->db->delete('r_customer', ['ccname' => 'unittest']);
        //清除入库单
        $this->db->delete('b_stock_in', ['uname[~]' => 'unittest%']);
        //清除入库单详单
        $this->db->delete('b_stock_in_glist', ['gname[~]' => '%单元测试商品%']);
        //清除出库单
        $this->db->delete('b_stock_out', ['uname[~]' => 'unittest%']);
        //清除出库单详单
        $this->db->delete('b_stock_out_glist', ['gname[~]' => '%单元测试商品%']);
 
    }



    ///////////////////////////// 辅助测试 ///////////////////////////////////

    private function reg_user_for_unittest($username = 'username'){
        $res = reg_user($username);
        $this->assertNotNull($res);
        $this->assertEquals(0, $res['err']);
        $this->uid[$username] = $res['msg']['user_id'];
     }

    private function login($username = 'unittest'){
        $ret = login($username, '111111');
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->ticket[$username] = $ret['msg']['ticket'];
    }

    public function create_company($username = 'unittest'){
        $company_data = [
            'name' => '单元测试公司'.$username,
            'simple_name' => '单测',
            'address' => '北京市',
            'type' => '1',
            'init_process' => '0',
            'tax_no' => '1234567890',
            'account_no' => '6666777788889999',
            'license' => '2345678901',
            'fax' => '3456789012',
            'phone' => '13012345678',
            'pay_team_in' => '30',
            'pay_team_out' => '40',
            'lawrep' => '擎天柱',
            'contactor' => '大黄蜂',
            'contactor_phone' => '15011112222',
            'email' => 'linvo@126.com',
            'memo' => '这里是备注信息',
        ];
        $company_data['ticket'] = $this->ticket[$username];
        $url = get_api_host() . '/company/create';
        $res = curl_post($url, $company_data);
        $ret = json_decode($res, True);
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->cid[$username] = $ret['msg']['company_id'];
        $this->cname[$username] = $company_data['name'];
    } 

    public function create_store($username = 'unittest', $store = 'name'){
        $store_data = [
            'name' => '单元测试仓库'.$store,
            'address' => '北京市',
            'phone' => '1',
            'contactor' => '小蜜蜂',
            'memo' => '这里是备注信息',
        ];
        $store_data['ticket'] = $this->ticket[$username];

        $url = get_api_host() . '/store/create';
        $res = curl_post($url, $store_data);
        $ret = json_decode($res, True);
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->sid[$store] = $ret['msg']['store_id'];
        $this->sname[$store] = $store_data['name'];
    }

    public function set_store_power($username = 'unittest'){
        $data = [
            'sids' => $this->sid[$username]
        ];
        $data['ticket'] = $this->ticket[$username];
        $url = get_api_host() . '/user/update/'. $this->uid[$username];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function create_goods($username = 'unittest', $gname = null){
        $data = [
            'name' => '单元测试商品'.$gname,
            'spec' => '瓶',
            'bid' => $this->data['bid'],
            'tid' => $this->data['tid'],
            'ispkg' => '1',
            'isbind' => '1',
            'trademark' => '1',
            'valid_period' => '360',
            'price_type' => '1',
            'shipping_price' => '30',
            'tax_rate' => '17',
        ];
        $data['ticket'] = $this->ticket[$username];
        $url = get_api_host() . '/goods/create';
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->gid[$gname] = $ret['msg']['goods_id'];
    }

    public function create_company_goods($username = 'unittest', $gname = null){
        $url = get_api_host() . "/company_goods/create";
        $data = [[
            'in_cid' => $this->cid[$username],
            'gid' => $this->gid[$gname]
        ]];
        $my_data = [
            'data' => json_encode($data),
            'ticket' => $this->ticket[$username]
        ];
        $res = curl_post($url, $my_data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    public function create_store_goods($username = 'unittest', $store = null, $gname = null){
        $url = get_api_host() . "/store_goods/create";
        $data = [[
            'in_sid' => $this->sid[$store],
            'gid' => $this->gid[$gname]
        ]];
        $my_data = [
            'data' => json_encode($data),
            'ticket' => $this->ticket[$username]
        ];
        $res = curl_post($url, $my_data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    ///////////////////////////// 单元测试 ///////////////////////////////////

    public function create_overflow(){
        $url = get_api_host() . '/overflow/create';
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g1'] = $ret['msg']['id'];
    }

    public function update_overflow(){
        $url = get_api_host() . '/overflow/update/'. $this->oid['g1'];
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 30,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_overflow(){
        $url = get_api_host() . '/overflow/check/'. $this->oid['g1'];
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 30,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_overflow2(){
        $url = get_api_host() . '/overflow/check';
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g1'] = $ret['msg']['id'];
    }

    public function read_overflow(){
        $url = get_api_host() . '/overflow/read';
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_detail_overflow(){
        $url = get_api_host() . '/overflow/read/'. $this->oid['g1'];
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function reserve_read_goods(){
        $url = get_api_host() . '/reserve/read_goods';
        $data = [
            'sid' => $this->sid['s1'],
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);   
    }

    public function reserve_read(){
        $url = get_api_host() . '/reserve/read_goods';
        $data = [
            'sid' => $this->sid['s1'],
            'gid' => $this->gid['g1'],
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);   
    }

    public function stock_in_repaire(){
        $url = get_api_host() . '/stock_in/repaire/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 40,
                    'unit_price' => 70,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['c1'] = $ret['msg']['id'];
    }
    
    public function stock_in_flush(){
        $url = get_api_host() . '/stock_in/flush/'. $this->oid['c1'];
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function create_loss(){
        $url = get_api_host() . '/loss/create';
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 10,
                    'unit_price' => 30,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g1'] = $ret['msg']['id'];
    }

    public function update_loss(){
        $url = get_api_host() . '/loss/update/'. $this->oid['g1'];
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_loss(){
        $url = get_api_host() . '/loss/check/'. $this->oid['g1'];
        $data = [
            'sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 1,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_loss(){
        $url = get_api_host() . '/loss/read';
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_detail_loss(){
        $url = get_api_host() . '/loss/read/'. $this->oid['g1'];
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function stock_out_repaire(){
        $url = get_api_host() . '/stock_out/repaire/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 2,
                    'unit_price' => 70,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['c1'] = $ret['msg']['id'];
    }
    
    public function stock_out_flush(){
        $url = get_api_host() . '/stock_out/flush/'. $this->oid['c1'];
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function create_transfer(){
        $url = get_api_host() . '/transfer/create';
        $data = [
            'in_sid' => $this->sid['s2'],
            'out_sid' => $this->sid['s1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 10,
                    'unit_price' => 30,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g1'] = $ret['msg']['id'];
    }

    public function update_transfer(){
        $url = get_api_host() . '/transfer/update/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 5,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_transfer(){
        $url = get_api_host() . '/transfer/check/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 8,
                    'unit_price' => 60,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_transfer(){
        $url = get_api_host() . '/transfer/read';
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_detail_transfer(){
        $url = get_api_host() . '/transfer/read/'. $this->oid['g1'];
        $data = [
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }
}
