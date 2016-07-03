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

class WorkFlow_Test extends PHPUnit_Framework_TestCase{
    private $data = [];
    private $id = 0;
    private $uid = [];
    private $ticket = [];
    private $cid = [];
    private $cname = [];
    private $sid = [];
    private $sname = [];
    private $gid = [];

    /**
     * init
     */
    public function __construct(){
        $this->db = get_db();
        //以下是工作流初始化准备工作
        //注册账户1，账户2
        $this->reg_user_for_unittest('unittest1');
        $this->reg_user_for_unittest('unittest2');

        //登录账户1，账户2
        $this->login('unittest1');
        $this->login('unittest2');

        //创建账户1公司、账户2公司
        $this->create_company('unittest1');
        $this->create_company('unittest2');

        //公司2把公司1添加成客户
        $this->add_customer('unittest1', 'unittest2');

        //创建账户1仓库、账户2仓库
        $this->create_store('unittest1');
        $this->create_store('unittest2');

        //赋权账户1，账户2，的仓库1，仓库2权限
        $this->set_store_power('unittest1');
        $this->set_store_power('unittest2');

        //创建商品品牌和类型
        $this->data['bid'] = $this->db->insert('o_goods_brand', ['name' => '测试品牌','code' => 'CSPP']);
        $this->data['tid'] = $this->db->insert('o_goods_type',['name' => '测试类型','code'=>'99']);

        //创建仓库1商品1、商品2，仓库2商品3
        $this->create_goods('unittest1', 'g1');
        $this->create_goods('unittest1', 'g2');
        $this->create_goods('unittest2', 'g3');

        //创建仓库商品1，2
        $this->create_company_goods('unittest1', 'g1');
        $this->create_company_goods('unittest1', 'g2');
        $this->create_company_goods('unittest2', 'g1');
        $this->create_company_goods('unittest2', 'g2');

        //创建仓库1商品1、商品2初始库存
        $this->db->insert('r_reserve', [
            'cid'=>$this->cid['unittest1'],
            'sid'=>$this->sid['unittest1'],
            'gid'=>$this->gid['g1'],
            'total'=>999,
            'freeze_total'=>0,
            'batch'=>1,
            'order_id'=>'888899991',
            'from'=>1,
            'unit_price'=>20
        ]);

        $this->db->insert('r_reserve', [
            'cid'=>$this->cid['unittest1'],
            'sid'=>$this->sid['unittest1'],
            'gid'=>$this->gid['g2'],
            'total'=>999,
            'freeze_total'=>0,
            'batch'=>1,
            'order_id'=>'888899992',
            'from'=>1,
            'unit_price'=>30
        ]);
        $this->db->insert('r_reserve', [
            'cid'=>$this->cid['unittest2'],
            'sid'=>$this->sid['unittest2'],
            'gid'=>$this->gid['g3'],
            'total'=>999,
            'freeze_total'=>0,
            'batch'=>1,
            'order_id'=>'888899993',
            'from'=>1,
            'unit_price'=>40
        ]);

    }


    /**
     * main
     */
    public function test_all(){
        //发起采购订单从C1,S1 进货G1，订单号O1
        $this->create_order1();
        //发起采购订单从C1,S2 进货G1（ERROR，仓库无权限）
        $this->create_order2();
        //发起采购订单从C1,S1 进货G3（ERROR，G3不属于S1）
        $this->create_order3();
        //修改采购订单O1，的C1，S1属性（无效果，不允许修改）
        $this->update_order1();
        //修改采购订单O1,的商品清单
        $this->update_order2();
        //使用U1根据O1创建出库单SO1（ERROR，O1还未审核）
        $this->create_stockout1();
        //使用U2账号根据O1创建入库单SI1（ERROR，O1还未审核）
        $this->create_stockin1();
        //审核采购订单O1
        $this->check_order1();
        //修改采购订单O1（ERROR，已审核不允许修改）
        $this->update_order3();
        //审核采购订单O1（ERROR，已审核不允许再审核）
        $this->check_order2();
        //直接创建审核采购订单从C1,S1 进货G2，订单号O2
        $this->check_order3();
        //审核采购订单O2（ERROR，已审核不允许再审核）
        $this->check_order4();
        //切换账号U1
        //预创建出库单
        $this->precreate_stockout1();
        //根据O1创建出库单SO1
        $this->create_stockout2();
        //直接创建出库单
        $this->dircreate_stockout1();
        //修改SO1
        $this->update_stockout1();
        //修改SO1出库公司和仓库属性（无效果，不允许修改）
        $this->update_stockout2();
        //预审SO1
        $this->precheck_stockout1();
        //再预审SO1（ERROR，不允许再次预审)
        $this->precheck_stockout2();
        //修改SO1（ERROR，预审后不允许修改）
        $this->update_stockout3();
        //根据O2创建并预审出库单SO2
        $this->precheck_stockout3();
        //再预审SO2（ERROR，不允许再次预审）
        $this->precheck_stockout4();
        //切换账号U2
        //根据O1创建入库单SI1
        $this->create_stockin2();
        //直接创建入库单
        $this->dircreate_stockin1();
        //修改SI1
        $this->update_stockin1();
        //审核SI1
        $this->check_stockin1();
        //再审核SI1（ERROR，不允许再审核）
        $this->check_stockin2();
        //修改SI1（ERROR，不能修改了）
        $this->update_stockin2();
        //根据O2创建并审核入库单SI2
        $this->check_stockin3();
        //再审核SI2（ERROR，不允许再审核）
        $this->check_stockin4();
        //登陆账号U1
        //审核出库单SO1、SO2
        $this->check_stockout1();
        $this->check_stockout2();
        //再审核SO1（ERROR，不允许再审核）
        $this->check_stockout3();
        //预审SO1（ERROR，都审核了不允许预审）
        $this->precheck_stockout5();
        //修改SO1（ERROR，这时候不能修改了）
        $this->update_stockout4();
        //收尾工作，删除所有创建的数据
        //TODO
    }

    /**
     * clean
     */
    public function __destruct(){
        //登出
        //清除用户
        $this->db->delete('o_user', ['username[~]' => 'unittest%']);
        //清除仓库
        $this->db->delete('o_store', ['name[~]' => '%unittest%']);
        $this->db->delete('o_store', ['name' => '单元测试仓库']);
        //清除商品
        $this->db->delete('o_goods', ['name[~]' => '%单元测试商品%']);
        $this->db->delete('o_store_goods', ['gname[~]' => '%单元测试商品%']);
        //清除入库单
        $this->db->delete('b_stock_in', ['uname[~]' => 'unittest%']);
        //清除入库单详单
        $this->db->delete('b_stock_in_glist', ['gname[~]' => '%单元测试商品%']);
        //清除出库单
        $this->db->delete('b_stock_out', ['uname[~]' => 'unittest%']);
        //清除出库单详单
        $this->db->delete('b_stock_out_glist', ['gname[~]' => '%单元测试商品%']);
        //清除库存
        $sql = "select r.id from `r_reserve` as r,`o_company` as c where r.cid=c.id and (c.name like '单元测试公司%' or c.name like 'unittest%')";
        $res = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $v){
            $this->db->delete('r_reserve',['id'=>$v['id']]);
        }
        $this->db->delete('r_reserve',['order_id[~]'=>'88889999%']);
        //清除订单
        $this->db->delete('b_order', ['uname[~]' => 'unittest%']);
        //清除订单详单
        $this->db->delete('b_order_glist', ['gname[~]' => '%单元测试商品%']);
        //清除公司
        $this->db->delete('o_company', ['name[~]' => '%unittest%']);
        $this->db->delete('o_company', ['name[~]' => '%单元测试公司%']);
        //清除测试品牌和类型
        $this->db->delete('o_goods_type', ['name' => '测试类型']);
        $this->db->delete('o_goods_brand', ['name' => '测试品牌']);
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

    public function create_store($username = 'unittest'){
        $store_data = [
            'name' => '单元测试仓库'.$username,
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
        $this->sid[$username] = $ret['msg']['store_id'];
        $this->sname[$username] = $store_data['name'];
    }

    public function add_customer($user1, $user2){
        $url = get_api_host() . "/customer/create";
        $this->data = [
            'ticket' => $this->ticket[$user1],
            'ccid' => $this->cid[$user2],
            'ccname' => '客户别名',
            'cctype' => '1'
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
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

    public function create_store_goods($username = 'unittest', $gname = null){
        $url = get_api_host() . "/store_goods/create";
        $data = [[
            'in_sid' => $this->sid[$username],
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

    public function create_order1(){
        $url = get_api_host() . '/order/create';
        $data = [
            'in_sid' => $this->sid['unittest2'],
            #'in_sname' => $this->sname['unittest2'],
            'out_cid' => $this->cid['unittest1'],
            #'out_cname' => $this->cname['unittest1'],
            #'out_sid' => $this->sid['unittest1'],
            #'out_sname' => $this->sname['unittest1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g1'] = $ret['msg']['order_id'];
    } 

    public function create_order2(){
        $url = get_api_host() . '/order/create';
        $data = [
            'in_sid' => $this->sid['unittest2'],
            'out_cid' => $this->cid['unittest1'],
            #'out_sid' => $this->sid['unittest2'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        //这种情况不再限制
        //$this->assertEquals(1, $ret['err']);
    } 

    public function create_order3(){
        $url = get_api_host() . '/order/create';
        $data = [
            'in_sid' => $this->sid['unittest2'],
            'out_cid' => $this->cid['unittest1'],
            #'out_sid' => $this->sid['unittest1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g3'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        //$this->assertEquals(1, $ret['err']);
    } 

    public function update_order1(){
        $url = get_api_host() . '/order/update/'. $this->oid['g1'];
        $data = [
            'out_cid' => 99,
            #'out_sid' => 88,
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    } 

    public function update_order2(){
        $url = get_api_host() . '/order/update/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                'gid' => $this->gid['g1'],
                'total' => 10,
                'unit_price' => 20,
            ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    } 

    public function create_stockout1(){
        $url = get_api_host() . '/stock_out/create';
        $data = [
            'order_id' => $this->oid['g1'],
            'out_sid' => $this->sid['unittest1'],
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    } 

    public function create_stockin1(){
        $url = get_api_host() . '/stock_in/create';
        $data = [
            'order_id' => $this->oid['g1'],
            'in_sid' => $this->sid['unittest2'],
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    } 

    public function check_order1(){
        $url = get_api_host() . '/order/check/'. $this->oid['g1'];
        $data = [
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function update_order3(){
        $url = get_api_host() . '/order/update/'. $this->oid['g1'];
        $data = [
            'goods_list' => json_encode([[
                'gid' => $this->gid['g1'],
                'total' => 20,
                'unit_price' => 20,
            ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function check_order2(){
        $url = get_api_host() . '/order/check/'. $this->oid['g1'];
        $data = [
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function check_order3(){
        $url = get_api_host() . '/order/check';
        $data = [
            'in_sid' => $this->sid['unittest2'],
            #'in_sname' => $this->sname['unittest2'],
            'out_cid' => $this->cid['unittest1'],
            #'out_cname' => $this->cname['unittest1'],
            #'out_sid' => $this->sid['unittest1'],
            #'out_sname' => $this->sname['unittest1'],
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->oid['g2'] = $ret['msg']['order_id'];
    }

    public function check_order4(){
        $url = get_api_host() . '/order/check/'. $this->oid['g2'];
        $data = [
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function precreate_stockout1(){
        $url = get_api_host() . '/stock_out/precreate';
        $data = [
            'order_id' => $this->oid['g1'],
            'out_sid' => $this->sid['unittest1'],
            'memo' => 'test',
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
    }

    public function create_stockout2(){
        $url = get_api_host() . '/stock_out/create';
        $data = [
            'order_id' => $this->oid['g1'],
            'out_sid' => $this->sid['unittest1'],
            'memo' => 'test',
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
        $this->soid['g1'] = $ret['msg']['id'];
    }

    public function dircreate_stockout1(){
        $url = get_api_host() . '/stock_out/dircreate';
        $data = [
            'in_cid' => $this->cid['unittest2'],
            'out_sid' => $this->sid['unittest1'],
            'memo' => 'test',
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
        //$this->soid['g1'] = $ret['msg']['id'];
    }

    public function dircreate_stockin1(){
        $url = get_api_host() . '/stock_in/dircreate';
        $data = [
            'out_cid' => $this->cid['unittest1'],
            'in_sid' => $this->sid['unittest2'],
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        //$this->soid['g1'] = $ret['msg']['id'];
    }    

    public function update_stockout1(){
        $url = get_api_host() . '/stock_out/update/'. $this->soid['g1'];
        $data = [
            'memo' => 'test2',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 30,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    public function update_stockout2(){
        $url = get_api_host() . '/stock_out/update/'. $this->soid['g1'];
        $data = [
            'memo' => 'test3',
            'cid' => 20,
            'sid' => 80,
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function precheck_stockout1(){
        $url = get_api_host() . '/stock_out/precheck/'. $this->soid['g1'];
        $data = [
            'memo' => 'test4',
            'goods_list' => json_encode([[
                'gid' => $this->gid['g1'],
                'total' => 40,
                'unit_price' => 50,
            ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function precheck_stockout2(){
        $url = get_api_host() . '/stock_out/precheck/'. $this->soid['g1'];
        $data = [
            'memo' => 'test5',
            'goods_list' => json_encode([[
                'gid' => $this->gid['g1'],
                'total' => 50,
                'unit_price' => 60,
            ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function update_stockout3(){
        $url = get_api_host() . '/stock_out/update/'. $this->soid['g1'];
        $data = [
            'memo' => 'test6',
            'goods_list' => json_encode([[
                'gid' => $this->gid['g1'],
                'total' => 60,
                'unit_price' => 60,
            ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function precheck_stockout3(){
        $url = get_api_host() . '/stock_out/precheck';
        $data = [
            'order_id' => $this->oid['g2'],
            'out_sid' => $this->sid['unittest1'],
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
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
        $this->soid['g2'] = $ret['msg']['id'];
    }

    public function precheck_stockout4(){
        $url = get_api_host() . '/stock_out/precheck/'. $this->soid['g2'];
        $data = [
            'memo' => 'test10',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
                    'total' => 30,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function create_stockin2(){
        $url = get_api_host() . '/stock_in/create';
        $data = [
            'order_id' => $this->oid['g1'],
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 20,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->siid['g1'] = $ret['msg']['id'];
    }

    public function update_stockin1(){
        $url = get_api_host() . '/stock_in/update/'. $this->siid['g1'];
        $data = [
            'memo' => 'test2',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 30,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_stockin1(){
        $url = get_api_host() . '/stock_in/check/'. $this->siid['g1'];
        $data = [
            'memo' => 'test3',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 40,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function check_stockin2(){
        $url = get_api_host() . '/stock_in/check/'. $this->siid['g1'];
        $data = [
            'memo' => 'test4',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 50,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function update_stockin2(){
        $url = get_api_host() . '/stock_in/update/'. $this->siid['g1'];
        $data = [
            'memo' => 'test5',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 60,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function check_stockin3(){
        $url = get_api_host() . '/stock_in/check';
        $data = [
            'order_id' => $this->oid['g2'],
            'memo' => 'test5',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
                    'total' => 10,
                    'unit_price' => 10,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->siid['g2'] = $ret['msg']['id'];
    }

    public function check_stockin4(){
        $url = get_api_host() . '/stock_in/check/'. $this->siid['g2'];
        $data = [
            'memo' => 'test2',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
                    'total' => 50,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function check_stockout1(){
        $url = get_api_host() . '/stock_out/check/'. $this->soid['g1'];
        $data = [
            'memo' => 'test',
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
    }

    public function check_stockout2(){
        $url = get_api_host() . '/stock_out/check/'. $this->soid['g2'];
        $data = [
            'memo' => 'test2',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g2'],
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

    public function check_stockout3(){
        $url = get_api_host() . '/stock_out/check/'. $this->soid['g1'];
        $data = [
            'memo' => 'test',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 50,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function precheck_stockout5(){
        $url = get_api_host() . '/stock_out/precheck/'. $this->soid['g1'];
        $data = [
            'memo' => 'test5',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 70,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    public function update_stockout4(){
        $url = get_api_host() . '/stock_out/update/'. $this->soid['g1'];
        $data = [
            'memo' => 'test8',
            'goods_list' => json_encode([[
                    'gid' => $this->gid['g1'],
                    'total' => 80,
                    'unit_price' => 50,
                ]]),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 1){var_dump($res);}
        $this->assertEquals(1, $ret['err']);
    }  

}
