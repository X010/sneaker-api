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

class Store_Test extends PHPUnit_Framework_TestCase{
    private $data = [
            'name' => '单元测试仓库',
            'address' => '北京市',
            'phone' => '1',
            'contactor' => '小蜜蜂',
            'memo' => '这里是备注信息',
    ];
    private $id = 0;

    /**
     * init
     */
    public function __construct(){
        $this->url = get_api_host() . '/store';
        $this->db = get_db();
        //物理删除之前未清除的公司
        $this->db->delete('o_store', ['name'=>'单元测试仓库']);
        $this->db->delete('o_user', ['username[~]'=>'unittest%']);
        //创建游离用户
        $this->reg_user_for_unittest();
        //登录
        $this->login();
    }


    /**
     * main
     */
    public function test_all(){
        //创建公司
        $this->create_company();
        //创建公司下的仓库并验证
        $this->create();
        $this->read_by_id();
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
        $this->db->delete('o_user', ['username[~]'=>'unittest%']);
        $this->db->delete('o_company', ['name'=>'单元测试公司%']);
        $this->db->delete('o_store', ['name[~]' => '单元测试仓库%']);
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
            'name' => '单元测试公司X',
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
        $url = $this->url . "/create";
        $res = curl_post($url, $this->data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->id = $ret['msg']['store_id'];
    } 

    public function read_by_id(){
        $url = $this->url . "/read/" . $this->id;
        $res = curl_post($url, ['ticket'=>$this->ticket]);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('1', $ret['msg']['phone']);
    }

    public function update(){
        $param = ['ticket'=>$this->ticket, 'address' => '2', 'memo' => '2233'];
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
        $this->assertEquals('2', $ret['msg']['address']);
    }

    public function update_error(){
        $param = ['ticket'=>$this->ticket, 'address' => '2', 'memo' => '2233'];
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
        $param = ['ticket'=>$this->ticket, 'name' => '单元测试仓库'];
        $res = curl_post($url, $param);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertEquals(0, $ret['err']);
        //$this->assertEquals('3', $ret['msg'][0]['type']);
        //$this->assertEquals('2', $ret['msg'][1]['type']);
        //$this->assertEquals('2', $ret['msg'][2]['type']);
    }

}
