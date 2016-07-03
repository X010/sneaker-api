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

class Goods_Test extends PHPUnit_Framework_TestCase{
    private $data = [
            'name' => '单元测试商品',
            'spec' => '瓶',
            'bid' => '1',
            'tid' => '1',
            'ispkg' => '1',
            'isbind' => '1',
            'trademark' => '1',
            'valid_period' => '360',
            'price_type' => '1',
            'shipping_price' => '30',
            'tax_rate' => '17',
    ];
    private $id = 0;

    /**
     * init
     */
    public function __construct(){
        $this->url = get_api_host() . '/goods';
        $this->db = get_db();
        //物理删除之前未清除的数据
        $this->db->delete('o_goods', ['name'=>'单元测试商品']);
        $this->db->delete('o_user', ['username[~]'=>'unittest%']);
        $this->data['bid'] = $this->db->insert('o_goods_brand', ['name' => '测试品牌','code' => 'CSPP']);
        $this->data['tid'] = $this->db->insert('o_goods_type',['name' => '测试类型','code'=>'99']);
        //创建游离用户
        $this->reg_user_for_unittest();
        //登录
        $this->login();
    }


    /**
     * main
     */
    public function test_all(){
        //创建公司并验证
        $this->create_company();
        //创建公司下的商品并验证
        $this->create();
        $this->read_by_id();
        $this->read_by_ticket();
        //修改公司下的仓库
        $this->update();
        $this->check_update();
        $this->update_error();
        //删除公司的仓库
        $this->delete();
        //$this->check_delete(); 
        //读取公司列表
        $this->read();
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
        $this->db->delete('o_goods', ['name'=>'单元测试商品']);
        //删除类型和品牌
        $this->db->delete('o_goods_brand',['id'=>$this->data['bid']]);
        $this->db->delete('o_goods_type',['id'=>$this->data['tid']]);
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
        $this->data['ticket'] = $this->ticket;
    }

    public function create_company(){
        $company_data = [
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
        $company_data['ticket'] = $this->ticket;
        $url = get_api_host() . '/company/create';
        $res = curl_post($url, $company_data);
        $ret = json_decode($res, True);
        $this->cid = $ret['msg']['company_id'];
    } 

    ///////////////////////////// 单元测试 ///////////////////////////////////

    public function create(){
        $url = $this->url . "/create";        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->id = $ret['msg']['goods_id'];
    } 

    public function read_by_id(){
        $url = $this->url . "/read/" . $this->id;
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('1', $ret['msg']['trademark']);
    }

    public function read_by_ticket(){
        $url = $this->url . "/read/0"; //0 for ticket
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    }

    public function update(){
        $param = ['ticket'=>$this->ticket, 'trademark' => '2', 'memo' => '2233'];
        $url = $this->url . "/update/" . $this->id;
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    } 

    public function check_update(){
        $url = $this->url . "/read/" . $this->id; 
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('2', $ret['msg']['trademark']);
    }

    public function update_error(){
        $param = ['ticket'=>$this->ticket, 'trademark' => '2', 'memo' => '2233'];
        $url = $this->url . "/update/xxx"; //error id
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['status'] != 1100){var_dump($res);}
        $this->assertEquals(1100, $ret['status']);
    } 

    public function delete(){
        $url = $this->url . "/delete/" . $this->id;
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);      
    }

    public function check_delete(){
        $url = $this->url . "/read/" . $this->id; 
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        $this->assertEquals(1, $ret['err']);
    }

    /*
    public function create_some(){
        $url = $this->url . "/create";
        $param = $this->data;
        $param['type'] = '2';
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);

        $param['type'] = '3';
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
    } 
    */

    public function read(){
        $url = $this->url . "/read"; 
        $param = ['ticket'=>$this->ticket, 'name' => '单元测试商品'];
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        //$this->assertEquals('3', $ret['msg'][0]['type']);
        //$this->assertEquals('2', $ret['msg'][1]['type']);
        //$this->assertEquals('2', $ret['msg'][2]['type']);
    }

}
