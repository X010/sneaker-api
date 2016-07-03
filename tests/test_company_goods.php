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

class CompanyGoods_Test extends PHPUnit_Framework_TestCase{
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
    }


    /**
     * main
     */
    public function test_all(){
        //注册账户
        $this->reg_user_for_unittest('unittest1');
        $this->reg_user_for_unittest('unittest2');


        //登陆账户
        $this->login('unittest1');
        $this->login('unittest2');


        //创建公司
        $this->create_company('unittest1');
        $this->create_company('unittest2');


        //创建仓库
        $this->create_store('unittest1');
        $this->create_store('unittest2');


        //创建商品品牌和类型
        $this->data['bid'] = $this->db->insert('o_goods_brand', ['name' => '测试品牌','code' => 'CSPP']);
        $this->data['tid'] = $this->db->insert('o_goods_type',['name' => '测试类型','code'=>'99']);

        //创建商品
        $this->create_goods('unittest1', 'g1');

        //创建公司商品
        $this->create_company_goods();
        //第二个账号创建公司商品
        $this->create_company_goods2();
        //第二个账号向第一个账号授权
        $this->create_customer();
        //查看公司商品是否存在
        $this->exists_company_goods1();
        //设置公司商品价格、供应商
        $this->update_company_goods();
        //读取仓库价格
        $this->read_company_goods();
        //读取仓库价格列表
        $this->read_company_goods2();
        //取消仓库价格
        $this->delete_company_goods();
        //查看公司商品是否存在
        $this->exists_company_goods2();
    }

    /**
     * clean
     */
    public function __destruct(){
        //登出
        //清除用户
        $this->db->delete('o_user', ['username[~]' => 'unittest%']);
        //清除公司
        $this->db->delete('o_company', ['name[~]' => '%unittest%']);
        $this->db->delete('o_company', ['name[~]' => '%单元测试公司%']);
        //清除仓库
        $this->db->delete('o_store', ['name[~]' => '%unittest%']);
        //清除商品
        $this->db->delete('o_goods', ['name[~]' => '%单元测试商品%']);
        $this->db->delete('o_company_goods', ['name[~]' => '%单元测试商品%']);
        //清除测试品牌和类型
        $this->db->delete('o_goods_type', ['name' => '测试类型']);
        $this->db->delete('o_goods_brand', ['name' => '测试品牌']);
        $this->db->delete('r_customer', ['ccname' => 'unittest']);
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

    ///////////////////////////// 单元测试 ///////////////////////////////////

    public function create_company_goods(){
        $url = get_api_host() . "/company_goods/create";
        $data = [[
            'gid' => $this->gid['g1'],
            'out_cid' => $this->cid['unittest1'],
            'in_price' => 20,
            'out_price1' => 30,
            'out_price2' => 40,
            'out_price3' => 50
        ]];
        $my_data = [
            'data' => json_encode($data),
            'ticket' => $this->ticket['unittest1']
        ];
        $res = curl_post($url, $my_data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->sgid = $ret['msg'];
    } 

    public function create_company_goods2(){
        $url = get_api_host() . "/company_goods/create";
        $data = [[
            'gid' => $this->gid['g1'],
            'out_cid' => $this->cid['unittest1'],
            'in_price' => 20,
            'out_price1' => 30,
            'out_price2' => 40,
            'out_price3' => 50
        ]];
        $my_data = [
            'data' => json_encode($data),
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $my_data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        #$this->sgid = $ret['msg'];
    } 

    public function create_customer(){
        $url = get_api_host() . "/customer/create";
        $data = [
            'ccid' => $this->cid['unittest1'],
            'ccname' => 'unittest',
            'cctype' => 1,
            'ticket' => $this->ticket['unittest2']
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    public function exists_company_goods1(){
        $url = get_api_host() . "/exists/company_goods";
        $this->data = [
            'ticket' => $this->ticket['unittest1'],
            'gid' => $this->gid['g1'],
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(True, $ret['msg']['result']);
    }

    public function exists_company_goods2(){
        $url = get_api_host() . "/exists/company_goods";
        $this->data = [
            'ticket' => $this->ticket['unittest1'],
            'gid' => $this->gid['g1'],
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(False, $ret['msg']['result']);
    }

    public function update_company_goods(){
        $url = get_api_host() . "/company_goods/update/". $this->sgid;
        $data = [
            'out_cid' => $this->cid['unittest2'],
            'out_sid' => $this->sid['unittest2'],
            'in_price' => 10,
            'out_price1' => 20,
            'out_price2' => 30,
            'out_price3' => 40
        ];
        $data['ticket'] = $this->ticket['unittest1'];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }    

    public function read_company_goods(){
        $url = get_api_host() . "/company_goods/read/";
        $data = [
            'in_sid' => $this->sid['unittest1'],
        ];
        $data['ticket'] = $this->ticket['unittest1'];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }    

    public function read_company_goods2(){
        $url = get_api_host() . "/company_goods/read/". $this->sgid;
        $data['ticket'] = $this->ticket['unittest1'];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    public function delete_company_goods(){
        $url = get_api_host() . "/company_goods/delete/". $this->sgid;
        $data['ticket'] = $this->ticket['unittest1'];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

}
