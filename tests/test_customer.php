<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Unit-test of customer
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     tests
 */

include_once "common.php";

class Customer_Test extends PHPUnit_Framework_TestCase{
    private $data_company = [
            'name' => '单元测试公司',
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
    private $data = [];
    public $id = 0;
    public $ids = [];

    /**
     * init
     */
    public function __construct(){
        $this->url_company = get_api_host() . '/company';
        $this->url = get_api_host() . '/customer';
        $this->db = get_db();
        //物理删除之前未清除的公司
        $this->db->delete('o_company', ['name'=>'单元测试公司']);
        $this->db->delete('o_user', ['username[~]'=>'unittest%']);
        //创建游离用户
        $this->reg_user_for_unittest();
        //登录
        $this->login();
        //创建公司
        $this->add_company();
    }


    /**
     * main
     */
    public function test_all(){
        //添加单个客户
        $this->add_customer();
        //判断客户是否存在
        $this->exists_customer1();
        //批量添加客户
        $this->add_customer_batch();
        //读取客户列表通过查询
        $this->read_customers_by_cctype();
        //修改客户信息
        $this->update_customer();
        //读取客户列表
        $this->read_customers();
        //删除客户信息
        $this->delete_customer();
        //判断客户是否存在
        $this->exists_customer2();
        //读取客户列表(空)
        $this->read_customers2();
    }

    /**
     * clean
     */
    public function __destruct(){
        //登出
        $url = get_api_host() . "/login/out";
        $ret = curl_post($url, ['ticket'=>$this->ticket]);
        //物理删除单元测试帐号
        $this->db->delete('o_user', ['username[~]'=>'unittest%']);
        //物理删除公司
        $this->db->delete('o_company', ['name'=>'单元测试公司']);
        //删除客户关系
        $this->db->delete('r_customer', ['ccname'=>'unittest']);
    }



    ///////////////////////////// 辅助测试 ///////////////////////////////////

    private function reg_user_for_unittest(){
        $res = reg_user();
        $this->assertEquals(0, $res['err']);
    }

    private function login(){
        $ret = login('unittest', '111111');
        $this->assertEquals(0, $ret['err']);
        $this->ticket = $ret['msg']['ticket'];
        $this->data_company['ticket'] = $this->ticket;
    }

    private function add_company(){
        $url = $this->url_company . "/create";
        $res = curl_post($url, $this->data_company);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->id = $ret['msg']['company_id'];
    } 

    ///////////////////////////// 单元测试 ///////////////////////////////////

    public function add_customer(){
        $url = $this->url . "/create";
        $this->data = [
            'ticket' => $this->ticket,
            'ccid' => '111',
            'ccname' => 'aaa',
            'cctype' => '1'
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->id = $ret['msg']['id'];
    } 

    public function exists_customer1(){
        $url = get_api_host() . "/exists/customer";
        $this->data = [
            'ticket' => $this->ticket,
            'ccid' => '111',
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(True, $ret['msg']['result']);
    }

    public function exists_customer2(){
        $url = get_api_host() . "/exists/customer";
        $this->data = [
            'ticket' => $this->ticket,
            'ccid' => '111',
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(False, $ret['msg']['result']);
    }


    public function add_customer_batch(){
        $url = $this->url . "/create_batch";
        $this->data = [
            'ticket' => $this->ticket,
            'data' => '[{"ccid":"222","ccname":"bbb","cctype":"2"},{"ccid":"333","ccname":"ccc","cctype":"3"}]'
        ];
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    } 

    public function read_customers_by_cctype(){
        $url = $this->url . "/read";
        $res = curl_post($url, ['ticket'=>$this->ticket, 'cctype'=>'2']);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('222', $ret['msg']['data'][0]['ccid']);
    }

    public function update_customer(){
        $param = ['ticket'=>$this->ticket, 'cctype' => '3'];
        $url = $this->url . "/update/" . $this->id;
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    } 


    public function read_customers(){
        $url = $this->url . "/read";
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        //var_dump($ret['msg']['data'][0]['ccid']);
        $this->assertEquals('333', $ret['msg']['data'][0]['ccid']);
        $this->assertEquals('222', $ret['msg']['data'][1]['ccid']);
        $this->assertEquals('111', $ret['msg']['data'][2]['ccid']);
        foreach ($ret['msg']['data'] as $item){
            $this->ids[] = $item['id'];
        }
    }

    public function delete_customer(){
        foreach ($this->ids as $id){
            $url = $this->url . "/delete/" . $id;
            $res = curl_post($url, ['ticket'=>$this->ticket]);
            $ret = json_decode($res, True);
            if (!$ret || $ret['err'] != 0){var_dump($res);}
            $this->assertEquals(0, $ret['err']);  
        }
    
    }

    public function read_customers2(){
        $url = $this->url . "/read";
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals([], $ret['msg']['data']);
    }



}
