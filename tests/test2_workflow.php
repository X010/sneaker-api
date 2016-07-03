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
    private $ticket = [];
    /**
     * init
     */
    public function __construct(){
        $this->db = get_db();
        //1. DB：创建商品g1
        $this->g1 = $this->db->insert('o_goods',[
            'code'=>'10008',
            'name'=>'单元测试商品',
            'spec'=>12,
            'pkgspec'=>'1*12',
            'unit'=>'个',
            'bid'=>1,
            'tid'=>1,
            'status'=>1,
            'barcode'=>'6908888800000',
            'tax_rate'=>'0.17'
        ]);
        //2. DB：创建公司c1
        $this->c1 = $this->db->insert('o_company',[
            'code'=>'188',
            'name'=>'单元测试公司',
            'address'=>'湖南省 长沙市 开福区 北辰三角洲',
            'type'=>4,
            'basedate'=>12,
            'status'=>1,
            'iserp'=>1,
            'ismall'=>0,
        ]);
        //3. DB：创建仓库s1
        $this->s1 = $this->db->insert('o_store',[
            'code'=>'1888',
            'name'=>'单元测试仓库',
            'address'=>'湖南省 长沙市 开福区 北辰三角洲',
            'cid'=>$this->c1,
            'status'=>1,
            'isreserve'=>1
        ]);
        //4. DB：创建员工u1，是c1的管理员，拥有s1权限
        $this->u1 = $this->db->insert('o_user',[
            'code'=>'18888',
            'username'=>'test88',
            'password'=>my_password_hash('123456'),
            'name'=>'单元测试员工1',
            'cid'=>$this->c1,
            'sids'=>$this->s1,
            'cname'=>'单元测试公司',
            'phone'=>'13800001111',
            'admin'=>1,
            'status'=>1,
            'belong'=>1
        ]);


    }


    /**
     * main
     */
    public function test_all(){
        //5. 登陆员工u1，后面操作走u1登陆的接口
        $this->login();

        //6. 新建员工u2，为外借员工
        $this->create_user();

        //7. 新建客户c2，业务员为u1，出库仓库为s1
        $this->create_customer();

        //8. 为客户c2添加业务员u2
        $this->add_salesman();

        //9. 新建供应商c3
        $this->create_supplier();

        //10. c1 添加商品 g1，选择供应商c3
        $this->add_goods();

        //11. c1向c3 下订单，订购 商品g1，生成订单o1
        $this->create_order();

        //12. c1将订单o1转换成入库单si1
        $this->create_stock_in();

        //13. 审核si1
        $this->check_stock_in();

        //14. c1查看库存，针对g1查看批次库存，核对库存数量和金额
        $this->read_reserve1();

        //15. c1向c2 下出库单，销售大于库存的g1，生成出库单so1
        $this->create_stock_out();

        //16. 审核so1 ，判断返回的状态是否出库待配
        $this->check_stock_out1();

        //17. 修改g1的数量到小于库存到数量，再次审核so1
        $this->check_stock_out2();

        //18. 重复12步骤，再次核对库存数量
        $this->read_reserve2();

        //19. 退货操作，c1创建c2 的退回单，并审核
        $this->check_return_in();

        //20. 退货操作，c1退货c3，并审核
        $this->check_return_out();

    }

    /**
     * clean
     */
    public function __destruct(){
        //21. 退出登陆u1
        //22. 清除数据：档案数据g1，c1，s1，u1，u2，c2，c3
        //23. 清除数据：单据：o1，so1，si1，退货单据

        //登出
        //清除用户
        $this->db->delete('o_user', ['username[~]' => 'test%']);
        //清除仓库
        $this->db->delete('o_store', ['name' => '单元测试仓库']);
        //清除商品
        $this->db->delete('o_goods', ['name[~]' => '%单元测试商品%']);
        $this->db->delete('o_company_goods', ['gname[~]' => '%单元测试商品%']);
        $this->db->delete('o_store_goods', ['gname[~]' => '%单元测试商品%']);

        //清除入库单
        $this->db->delete('b_stock_in', ['cname[~]' => '单元测试%']);
        //清除入库单详单
        $this->db->delete('b_stock_in_glist', ['gname[~]' => '%单元测试商品%']);
        //清除出库单
        $this->db->delete('b_stock_out', ['cname[~]' => '单元测试%']);
        //清除出库单详单
        $this->db->delete('b_stock_out_glist', ['gname[~]' => '%单元测试商品%']);
        //清除订单
        $this->db->delete('b_order', ['in_cname[~]' => '单元测试%']);
        //清除订单详单
        $this->db->delete('b_order_glist', ['gname[~]' => '%单元测试商品%']);

        //清除报表
        $this->db->delete('f_stock_in', ['gname[~]' => '%单元测试商品%']);
        $this->db->delete('f_stock_out', ['gname[~]' => '%单元测试商品%']);
        $this->db->delete('f_adjust', ['gname[~]' => '%单元测试商品%']);
        $this->db->delete('f_sell_customer', ['gname[~]' => '%单元测试商品%']);
        $this->db->delete('f_sell_supplier', ['gname[~]' => '%单元测试商品%']);

        //清除关系
        $this->db->delete('r_customer', ['cname[~]' => '单元测试%']);
        $this->db->delete('r_customer_salesman', ['cname[~]' => '单元测试%']);
        $this->db->delete('r_supplier', ['cname[~]' => '单元测试%']);

        //清除库存
        $sql = "select * from `o_company` where `name` like '单元测试%'";
        $res = $this->db->query($sql)->fetchAll(PDO::FETCH_ASSOC);
        foreach ($res as $v){
            $this->db->delete('r_goods_supplier',['cid'=>$v['id']]);
            $this->db->delete('r_reserve',['cid'=>$v['id']]);
        }

        //清除公司
        $this->db->delete('o_company', ['name[~]' => '%单元测试%']);
    }



    ///////////////////////////// 单元测试 ///////////////////////////////////
    public function login(){
        $ret = login('test88', '123456');
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->ticket = $ret['msg']['ticket'];
    }

    public function create_user(){
        $url = get_api_host() . '/user/create';

        $data = [
            'username' => 'test89',
            'password' => '123456',
            'name' => '单元测试员工2',
            'sids' => $this->s1,
            'rids' => '1',
            'worktype' => '1',
            'phone' => '18900001111',
            'belong' => '2',
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->u2 = $ret['msg']['user_id'];
    }

    public function create_customer(){
        $url = get_api_host() . '/customer/register';
        $data = [
            'name' => '单元测试客户',
            'gtids' => '1',
            'type' => 4,
            'contactor' => '小王',
            'contactor_phone' => '13412344321',
            'username' => 'test90',
            'password' => '123456',
            'my_suid' => $this->u1,
            'my_sid' => $this->s1,
            'my_period' => '30',
            'phone' => '13412344321',
            'address' => '湖南省 长沙市 开福区 金星北路 是大气层小区',
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->c2 = $ret['msg']['id'];
    }

    public function add_salesman(){
        $url = get_api_host() . '/customer/add_salesman';
        $data = [
            'suid' => $this->u2,
            'ccid' => $this->c2,
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        //默认业务员是u1
        //$this->assertEquals($this->u1, $ret['msg']['default_id']);
    }

    public function create_supplier(){
        $url = get_api_host() . '/supplier/register';
        $data = [
            'name' => '单元测试供应商',
            'gtids' => '1',
            'type' => 1,
            'contactor' => '小黑',
            'contactor_phone' => '13412344322',
            'my_discount' => '0.9',
            'my_auto_delete' => '7',
            'my_period' => 30,
            'address' => '湖南省 长沙市 开福区 金星北路 是大气层小区',
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->c3 = $ret['msg']['id'];
    }

    public function add_goods(){
        $url = get_api_host() . '/company_goods/create';
        $data = [
            'gid'=>$this->g1,
            'gtid'=>1,
            'out_cid'=>$this->c3,
            'in_price'=>'2.00',
            'out_price1'=>'2.50',
            'out_price2'=>'3.50',
            'out_price3'=>'4.00',
            'out_price4'=>'5.00',
        ];

        $data = [
            'data' => json_encode([$data]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function create_order(){
        $url = get_api_host() . '/order/check';

        $data = [
            'in_sid'=>$this->s1,
            'out_cid'=>$this->c3,
            'buid'=>$this->u2,
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>100,
                    'unit_price'=>'2.00',
                    'amount_price'=>'200.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->o1 = $ret['msg']['order_id'];
    }

    public function create_stock_in(){
        $url = get_api_host() . '/stock_in/create';

        $data = [
            'order_id'=>$this->o1,
            'buid'=>$this->u2,
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>100,
                    'unit_price'=>'2.00',
                    'amount_price'=>'200.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->si1 = $ret['msg']['id'];
    }

    public function check_stock_in(){
        $url = get_api_host() . '/stock_in/check/'. $this->si1;

        $data = [
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>100,
                    'unit_price'=>'2.00',
                    'amount_price'=>'200.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
    }

    public function read_reserve1(){
        $url = get_api_host() . '/f_reserve/read';

        $data = [
            'sid' => $this->s1,
            'gid' => $this->g1,
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('100', $ret['msg']['add_up']['total']);
    }

    public function create_stock_out(){
        $url = get_api_host() . '/stock_out/precheck';

        $data = [
            'in_cid'=>$this->c2,
            'out_sid'=>$this->s1,
            'suid'=>$this->u1,
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>200,
                    'unit_price'=>'2.00',
                    'amount_price'=>'200.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->so1 = $ret['msg']['id'];
    }

    public function check_stock_out1(){
        $url = get_api_host() . '/stock_out/check/'. $this->so1;

        $data = [
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>200,
                    'unit_price'=>'3.00',
                    'amount_price'=>'600.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(3, $ret['msg']['status']);
    }

    public function check_stock_out2(){
        $url = get_api_host() . '/stock_out/check/'. $this->so1;

        $data = [
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>80,
                    'unit_price'=>'3.00',
                    'amount_price'=>'240.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals(4, $ret['msg']['status']);
    }

    public function read_reserve2(){
        $url = get_api_host() . '/f_reserve/read';

        $data = [
            'sid' => $this->s1,
            'gid' => $this->g1,
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->assertEquals('20', $ret['msg']['add_up']['total']);
    }

    public function check_return_in(){
        $url = get_api_host() . '/return_in/check';
        $data = [
            'out_cid'=>$this->c2,
            'in_sid'=>$this->s1,
            'buid'=>$this->u1,
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>60,
                    'unit_price'=>'3.00',
                    'amount_price'=>'180.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->si2 = $ret['msg']['id'];
    }

    public function check_return_out(){
        $url = get_api_host() . '/return_out/check';
        $data = [
            'in_cid'=>$this->c3,
            'out_sid'=>$this->s1,
            'suid'=>$this->u1,
            'goods_list'=>json_encode([
                [
                    'gid'=>$this->g1,
                    'total'=>80,
                    'unit_price'=>'2.00',
                    'amount_price'=>'160.00'
                ]
            ]),
            'ticket' => $this->ticket
        ];
        $res = curl_post($url, $data);
        $ret = json_decode($res, True);
        if (!$ret || $ret['err'] != 0){var_dump($res);}
        $this->assertNotNull($ret);
        $this->assertEquals(0, $ret['err']);
        $this->so2 = $ret['msg']['id'];
    }

}
