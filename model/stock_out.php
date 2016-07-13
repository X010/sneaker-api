<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * store
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

class StockOut extends Bill{

    /**
    * 出库所需字段（必须），如果加星号，代表可以插入但是不可以修改
    */
    protected $format_data = ['order_id','*cid','*cname','sid','sname','type','memo','uid','uname','status',
        'puid','puname','cuid','cuname', 'ruid','runame','suid','suname','amount','repaired_id','negative_id',
        'settle_id','in_cid','in_cname','tax_amount','checktime','settletime','lastdate','cost_amount','pay_type',
        'hash','sorting_id','car_license','business','mall_orderno','receipt','contacts','phone','settle_status',
        'discount_amount','rank','small_amount','settle_type','commission_status','commission_id','gids','box_total',
        'box_total2','express'];
    
    //搜索字段自动匹配
    protected $search_data = ['id','order_id'];

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data_glist = ['*id', '*order_id', 'sn', '*gid', '*gname', '*gpyname', '*gcode', '*gbarcode', '*gtid', '*gbid',
        '*gspec', '*gunit', '*gtax_rate', '*total', '*unit_price', '*amount_price', '*memo', '*tax_price', '*reserveid','cost_price'];


    //需要分和元转换的金额字段
    protected $amount_data = ['amount','tax_amount','cost_amount','discount_amount','small_amount'];

    protected $order_data = ['id'];

    protected $sidname = 'sid';
    // /**
    // * 列表返回字段，如果无此字段默认返回全部
    // */
    // protected $list_return = ['id','code','name','address','contactor','phone','contactor','status','memo','updatetime','createtime'];

    /**
     * constructor
     *
     * @param  int  $id     ID
     */
    public function __construct($id = NULL){
        parent::__construct('b_stock_out', $id);

    }

    /**
     * 预生成出库单
     *
     * @param array $data 字段列表
     * @return array ne_goods 不一致的商品
     *                ne_price 不一致的价格
     */
    public function my_precreate($data){
        $ne_goods = [];
        $ne_price = [];
        //第一步，判断提交商品清单中的所有商品种类是否在出库机构都包含，返回不包含的列表
        $goods_list = json_decode($data['goods_list'], True);
        foreach($goods_list as $val){
            $res = $this->app->db->has('o_company_goods', [
                'AND'=>[
                    'in_cid' => $data['cid'],
                    'gid' => $val['gid']
                ]
            ]);
            if(!$res){
                $ne_goods[] = $val['gid'];
            }
        }

        //判断价格之前，首先要获取客户类型等级
        $c_res = $this->app->db->select('r_customer','*',[
            'AND' => [
                'cid' => $data['cid'],
                'ccid' => $data['in_cid']
            ]
        ]);
        if(!$c_res){
            error(1710);
        }
        $price_name = 'out_price'.$c_res[0]['cctype'];

        //第二步，在包含的商品中，检查出库价格和提交价格是否一致，返回不一致的列表
        $p_model = new Price();
        foreach($goods_list as $val){
            $price = $p_model->get_price($val['gid'], $data['cid'], $data['sid'], $price_name);
            if($price != $val['unit_price']){
                $ne_price[$val['gid']] = $price;
            }
        }
        return [
            'ne_goods' => $ne_goods,
            'ne_price' => $ne_price
        ];
    }

    /**
     * 创建出库单
     *
     * @param array $data 新建字段列表
     * @return int 出库单号
     */
    public function my_create($data){

        //商品列表整理
        $data_format = Order::format($data, $data['cid']);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        //开始处理捆绑转换，将所有大商品转换成小商品
        $glist = $this -> get_sbind($glist, $data['cid'], $data['sid']);
        $glist = $this -> get_sn($glist);
        $data['hash'] = $this->my_hash($glist);

        start_action();

        //创建出库单
        $soid = $this->create_all($data, $glist);
        if (!$soid) error(9902);

        //更新订单字段
        if(isset($data['order_id'])){
            $o_model = new Order($data['order_id']);
            $o_model->modify([
                'ouid'=>$data['uid'], 
                'ouname'=>$data['uname'],
                'out_sid'=>$data['sid'],
                'out_sname'=>$data['sname'],
                'suid'=>get_value($data, 'suid'),
                'suname'=>get_value($data, 'suname')
            ]);


            $o_res = $o_model->read_by_id();
            if($o_res[0]['ispaid'] == 1){
                //如果是已经支付状态
                $sc_model = new SettleCustomer();
                $data['status'] = 2;
                $data['pay_type'] = $o_res[0]['pay_type'];
                $data['ccid'] = $o_res[0]['in_cid'];
                $data['ccname'] = $o_res[0]['in_cname'];
                $data['stock_list'] = json_encode([['id'=>$soid]]);
                $data['settle_type'] = 2;
                $data['cuid'] = get_value($data,'uid');
                $data['cuname'] = get_value($data,'uname');
                $sc_model->my_check($data, 'create', 3);
            }

        }
        return $soid;
    }

    /**
     * 更新出库单
     *
     * @param array $data 更新字段列表
     * @return int 出库单号
     */
    public function my_update($data){
        start_action();
        if (get_value($data, 'goods_list')){
            //商品列表整理
            $data_format = Order::format($data);
            $data = $data_format['data'];
            $glist = $data_format['glist'];

            //开始处理捆绑转换，将所有大商品转换成小商品
            $glist = $this -> get_sbind($glist, $this->app->Sneaker->cid, $data['out_sid']);
            $glist = $this -> get_sn($glist);
            $data['hash'] = $this->my_hash($glist);

            $my_res = $this->read_by_id();

            if($data['hash'] == $my_res[0]['hash']){
                $res = $this->update_by_id($data);
            }
            else{
                //修改订单
                $res = $this->update_all($data, $glist);
            }
            if (!$res) error(9902);
        }
        else{
            start_action();
            $this->update_by_id($data);
        }

        return $this->id;
    }

    
    /**
     * 取消出库单
     *
     * @return int
     */
    public function my_delete(){
        $data = ['status'=>9];
        Power::set_oper($data, 'cuid', 'cuname');
        $res = $this->update_by_id($data);
        return $res;
    }

    /**
     * 预审出库单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @return int 出库单号
     */
    public function my_precheck($data, $type){
        if($type == 'create'){
            $res = $this->my_create($data);
        }
        elseif($type == 'update'){
            $res = $this->my_update($data);
        }
        return $res;
    }


    /**
     * 审核出库单
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @return int 出库单号
     */
    public function my_check($data, $type, $bill_type=1){
        $data['status'] = 4;
        $data['checktime'] = date('Y-m-d H:i:s');
        $rmodel = new Reserve();

        if($type == 'create'){
            $my_id = $this->my_create($data);
            $sid = $data['sid'];
            $ccid = get_value($data, 'in_cid');
            $ccname = get_value($data, 'in_cname');
        }
        else{
            //如果不是退货，不允许修改order_id
            if($bill_type != 2){
                unset($data['order_id']);
            }
            $res = $this->my_update($data);
            $my_id = $this->id;

            $ores = $this->read_by_id();
            $sid = $ores[0]['sid'];
            $ccid = $ores[0]['in_cid'];
            $ccname = $ores[0]['in_cname'];
        }
        $this->set_id($my_id);

        $goods_list = $this->get_goods_list($my_id);

        //先判断库存是否够，只有正常购买出库才允许缺货待配
        if($bill_type == 1){
            $res = $rmodel->check_out($goods_list, $sid);
            if(!$res){
                //如果不够，缺货待配
                //$this->set_id($my_id);
                $this->my_update(['status'=>3]);

//                //缺货待配也要触发自动结算
//                $res = $this->read_by_id($my_id);
//                if($res[0]['settle_status'] == 0){
//                    if($res[0]['order_id']){
//                        $o_model = new Order();
//                        $o_res = $o_model->read_by_id($res[0]['order_id']);
//                        if($o_res[0]['ispaid'] == 1){
//                            //如果是已经支付状态
//                            $sc_model = new SettleCustomer();
//                            $data['status'] = 2;
//                            $data['pay_type'] = $o_res[0]['pay_type'];
//                            $data['ccid'] = $res[0]['in_cid'];
//                            $data['ccname'] = $res[0]['in_cname'];
//                            $data['stock_list'] = json_encode([['id'=>$my_id]]);
//                            $data['settle_type'] = 2;
//                            $sc_model->my_check($data, 'create');
//                        }
//                    }
//                }
                return 3;
            }
        }

        //开始出库
        $r_res = $rmodel -> out($goods_list, $sid, $bill_type, $ccid, $ccname);

        //如果是正常销售，记录销售成本
        $cost_amount = 0;

        if($r_res !== True){
            foreach($r_res as $sn=>$val){
                $this->app->db->update('b_stock_out_glist', ['cost_price'=>$val], [
                    'AND'=>[
                        'sn'=>$sn,
                        'stock_out_id'=>$my_id
                    ]
                ]);
                $cost_amount += $val;
            }
        }

        $this->app->db->update($this->tablename, ['cost_amount'=>$cost_amount],[
            'id'=>$my_id
        ]);


        //如果关联的订单号是已支付状态，则直接进行结算，生成结算单
        if($bill_type == 1){
            $res = $this->read_by_id($my_id);
//            if($res[0]['settle_status'] == 0){
//                if($res[0]['order_id']){
//                    $o_model = new Order();
//                    $o_res = $o_model->read_by_id($res[0]['order_id']);
//                    if($o_res[0]['ispaid'] == 1){
//                        //如果是已经支付状态
//                        $sc_model = new SettleCustomer();
//                        $data['status'] = 2;
//                        $data['pay_type'] = $o_res[0]['pay_type'];
//                        $data['ccid'] = $res[0]['in_cid'];
//                        $data['ccname'] = $res[0]['in_cname'];
//                        $data['stock_list'] = json_encode([['id'=>$my_id]]);
//                        $data['settle_type'] = 2;
//                        $sc_model->my_check($data, 'create');
//                    }
//                }
//            }

            //客户交易次数+1
            $this->app->db->update('r_customer',[
                'trade_total[+]'=>1
            ],[
                'AND'=>[
                    'cid'=>$res[0]['cid'],
                    'ccid'=>$res[0]['in_cid']
                ]
            ]);
        }

        return 4;
    }

    /**
     * 直接审核出库单，不经过预审
     *
     * @param array $data 字段列表
     * @param string $type 类型，新增或修改
     * @return int 出库单号
     */
    public function my_dircheck($data, $type, $bill_type=1){
        //增加库存
        $rmodel = new Reserve();
        if($type == 'create'){
            $res = $this->my_create($data);
            $goods_list = json_decode($data['goods_list'], true);

            //开始出库
            $rmodel -> out($goods_list, $data['sid'], $bill_type);

        }
        elseif($type == 'update'){
            $res = $this->my_update($data);
            //修改审核的时候增加库存，要确保有商品列表
            if(!isset($data['goods_list'])){
                $goods_list = $this->get_goods_list($this->id);
            }
            else{
                $goods_list = json_decode($data['goods_list'], true);
            }

            $ores = $this->read_by_id();
            $sid = $ores[0]['sid'];
            //开始出库
            $rmodel -> out($goods_list, $sid, $bill_type);
        }
        return $res;
    }

    /**
     * 返回出库单详情
     *
     * @return array
     */
    public function my_read(){
        $res = $this -> read_all_by_id();
        $is_cod = 0;    //0-非货到付款 1-是货到付款
        $ispaid = 0;
        if($res[0]['order_id'] && $res[0]['type'] == 1){
            $o_model = new Order();
            $o_res = $o_model->read_by_id($res[0]['order_id']);
            if($o_res){
                $res[0]['receipt'] = $o_res[0]['receipt'];
                $res[0]['contacts'] = $o_res[0]['contacts'];
                $res[0]['phone'] = $o_res[0]['phone'];
                $res[0]['mall_orderno'] = $o_res[0]['mall_orderno'];
                $ispaid = $o_res[0]['ispaid'];
            }
        }

        //立即支付－－－》否
        //货到付款－－－》0账期：是
        //       －－－》有账期：否

        if($ispaid){
            $is_cod = 0;
        }
        else{
            $c_model = new Customer();
            $c_res = $c_model->read_one([
                'cid'=>$res[0]['cid'],
                'ccid'=>$res[0]['in_cid']
            ]);
            $period = 0;
            if($c_res){
                $period = $c_res['period'];
            }
            if(!$period){
                $is_cod = 1;
            }
        }
        $res[0]['is_cod'] = $is_cod;

        if($res[0]['suid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$res[0]['suid']]);
            $res[0]['suphone'] = $temp[0];
        }

        return $res;
    }

    /**
     * 冲正单据
     *
     * @return int 冲正单号
     */
    public function my_flush(){
        $rmodel = new Reserve();
        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];

        $ccid = $data['in_cid'];
        $ccname = $data['in_cname'];

        start_action();
        $data_old = $data;
        //生成一个负单
        foreach($data['goods_list'] as $k1=>$v1){
            unset($data['goods_list'][$k1]['id']);
            foreach($v1 as $k2=>$v2){
                if(in_array($k2,['total','amount_price'])){
                    $data['goods_list'][$k1][$k2] = price_neg($data['goods_list'][$k1][$k2]);
                }    
            }
        }
        $data['amount'] = price_neg($data['amount']);
        $data['status'] = 11;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $this->my_create($data);
        //开始入库这个单
        $goods_list = $data_old['goods_list'];
        $r_res = $rmodel->in($goods_list, $data['sid'], $new_oid, 9, $ccid, $ccname);

        //把老单的字段和状态修正过来
        $this->update_by_id([
            'status'=>10,
            'negative_id'=>$new_oid,
        ]);

        //－－－2016-03-07改成不回写订单
        //如果老单据是根据订单生成，则修改订单的出库状态
//        if($data['order_id']){
//            $o_model = new Order($data['order_id']);
//            $o_model->update_by_id([
//                'ouid'=>Null,
//                'ouname'=>Null
//            ]);
//        }

        return $new_oid;
    }

    /**
     * 修正单据
     *
     * @param array $data_new 修正字段
     * @return int 修正单号
     */
    public function my_repaire($data_new){
        $rmodel = new Reserve();

        //商品列表整理
        $data_format = Order::format($data_new);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        //开始处理捆绑转换，将所有大商品转换成小商品
        $glist = $this -> get_sbind($glist, $this->app->Sneaker->cid, $data['out_sid']);
        $glist = $this -> get_sn($glist);
        $new_hash = $this->my_hash($glist);

        //调出老单据
        $data = $this -> read_all_by_id();
        $data = $data[0];

        $ccid = $data['in_cid'];
        $ccname = $data['in_cname'];

        $old_hash = $data['hash'];

        if($old_hash == $new_hash){
            error(3114);
        }

        start_action();
        $data_old = $data;
        //生成一个负单
        foreach($data['goods_list'] as $k1=>$v1){
            unset($data['goods_list'][$k1]['id']);
            foreach($v1 as $k2=>$v2){
                if(in_array($k2,['total','amount_price'])){
                    $data['goods_list'][$k1][$k2] = price_neg($data['goods_list'][$k1][$k2]);
                }    
            }
        }
        $data['amount'] = price_neg($data['amount']);
        $data['status'] = 13;
        $data['goods_list'] = json_encode($data['goods_list']);
        unset($data['id']);
        unset($data['negative_id']);
        Power::set_oper($data);
        Power::set_oper($data, 'cuid', 'cuname');
        $data['checktime'] = date('Y-m-d H:i:s');
        $new_oid = $this->my_create($data);
        //开始入库这个单
        $goods_list = $data_old['goods_list'];
        $r_res = $rmodel->in($goods_list, $data['sid'], $new_oid, 9, $ccid, $ccname);

        //把老单的字段和状态修正过来
        $this->update_by_id(['status'=>12, 'negative_id'=>$new_oid]);

        //开始入库新单
        //$data['status'] = 3;
        $data['goods_list'] = $data_new['goods_list'];
        $data['repaired_id'] = $this->id;
        $res = $this->my_check($data, 'create');
        return $res;
    }

    /**
     * 通过单号查找商品列表
     *
     * @param int $id 出库单号
     * @return array 商品明细
     */    
    public function get_goods_list($id){
        $app = \Slim\Slim::getInstance();
        $ret = $app->db->select('b_stock_out_glist', '*', ['stock_out_id'=>$id]);
        foreach($ret as $k=>$v){
            foreach($v as $key=>$val){
                if(in_array($key, ['unit_price','amount_price'])){
                    $ret[$k][$key] = fen2yuan($ret[$k][$key]);
                }
            }
        }
        return $ret;    
    }

    /**
     * 权限检测
     *
     * @param int $id 出库单号
     * @param int/array $status 状态，为0时不检测
     * @param int $type 类型，为0时不检测
     * @return array 单据字段
     */
    public function my_power($id, $status, $type, $settle_status=Null){
        $res = $this->read_by_id($id);
        if(!$res){
            error(3120);
        }

        $cid = $this->app->Sneaker->cid;
        if(isset($res[0]['cid']) && $cid && $res[0]['cid'] != $cid){
            error(3121);
        }
        if($type && $res[0]['type'] != $type){
            error(3122);
        }
        if($status){
            if(is_array($status)){
                $flag = 0;
                foreach($status as $status_s){
                    if($res[0]['status'] == $status_s){
                        $flag = 1;
                    }
                }
                if(!$flag){
                    error(3123);
                }
            }
            elseif($res[0]['status'] != $status){
                error(3123);
            }
        }

        if($settle_status !== Null){
            if($res[0]['settle_status'] != $settle_status){
                error(3126);
            }
        }

        if(isset($this->app->Sneaker->user_info)){
            Power::check_my_sid($res[0]['sid']);
        }
        
        return $res[0];
    }

    //业务员业绩统计
    public function form_salesman($data){
        $settle_status = get_value($data, 'settle_status');
        $out_status = ' status in (3,4)';
        $in_status = ' status=2';

        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and suid is not null and type=1 and '. $out_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }
        //如果指定了业务员
        if(get_value($data, 'suids')){
            $where_db .= ' and suid in ('. addslashes($data['suids']). ')';
        }
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);

        $count_sql = "select count(distinct suid) as val,count(*) as val2,sum(amount) as val3 from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'amount'=>fen2yuan($count_res[0]['val3']),
            'order_count'=>$count_res[0]['val2']
        ];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select suid,suname,count(*) as order_count,sum(amount) as amount,count(distinct in_cid) as customer_count from `b_stock_out` where  ". $where_db;

        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'amount desc';
        }

        $sql .= ' group by suid order by '. $orderby;


        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        $suid_list = [];
        foreach($r_res as $val){
            $result[] = [
                'suid'=>$val['suid'],
                'suname'=>$val['suname'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
                'customer_count'=>$val['customer_count']
            ];
            $suid_list[] = $val['suid'];
        }

        $where_db = ' cid='. $data['cid']. ' and buid is not null and type=2 and '. $in_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }

        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $where_db2 = $where_db;
        //如果指定了业务员
        if(get_value($data, 'suids')){
            $where_db2 .= ' and buid in ('. addslashes($data['suids']). ')';
        }
        $count_sql = "select count(*) as val2,sum(amount) as val3 from `b_stock_in` where ". $where_db2;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $add_up['return_amount'] = fen2yuan($count_res[0]['val3']);
        $add_up['return_order_count'] = $count_res[0]['val2'];
        $add_up['sub_amount'] = price_sub($add_up['amount'], $add_up['return_amount']);
        $add_up['sub_order_count'] = $add_up['order_count']-$add_up['return_order_count'];

        //如果指定了业务员
        if($suid_list){
            $where_db .= ' and buid in ('. implode(',', $suid_list). ')';
        }
        else{
            $where_db .= ' and 1=0';
        }

        $sql = "select buid,count(*) as order_count,sum(amount) as amount from `b_stock_in` where  ". $where_db;

        $sql .= ' group by buid ';

        $r_res = $this->app->db->query($sql)->fetchAll();

        $r_result = [];
        foreach($r_res as $val){
            $r_result[$val['buid']] = [
                'suid'=>$val['buid'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
            ];
        }

        foreach($result as $key=>$val){
            $r_data = get_value($r_result, $val['suid'], []);
            $return_amount = get_value($r_data, 'amount', '0.00');
            $return_count = get_value($r_data, 'order_count', '0.00');
            $result[$key]['return_amount'] = $return_amount;
            $result[$key]['return_order_count'] = $return_count;
            $result[$key]['sub_amount'] = price_sub($val['amount'], $return_amount);
            $result[$key]['sub_order_count'] = $val['order_count'] - $return_count;
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];

    }

    //业务员单品铺货查询
    public function form_salesman_goods($data){
        $gid = get_value($data, 'gid');
        $suid = get_value($data, 'suid');
        $cid = get_value($data, 'cid');
        $begindate = get_value($data, 'begin_date');
        $enddate = get_value($data, 'end_date');
        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        //正常销售数据
        $count_sql = "select count(distinct t1.in_cid,t1.suid) as val0,sum(t2.total) as val2,sum(t2.amount_price) as val3 from b_stock_out t1,b_stock_out_glist t2 where t1.id=t2.stock_out_id and t2.gid=$gid and t1.status=4 and t1.type=1".
            " and t1.cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59'";
        if($suid){
            $count_sql .= " and t1.suid=$suid";
        }
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];
        $add_up = [
            'total' => $count_res[0]['val2'],
            'amount' => fen2yuan($count_res[0]['val3'])
        ];

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t1.in_cid as val0,t1.in_cname as val1,sum(t2.total) as val2,sum(t2.amount_price) as val3,t1.suid as val4,t1.suname as val5 from b_stock_out t1,b_stock_out_glist t2 where t1.id=t2.stock_out_id and t2.gid=$gid and t1.status=4 and t1.type=1".
            " and t1.cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59'";
        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        $sql .= " group by t1.suid,t1.in_cid";
        $sql .= ' limit '. $start_count. ','. $page_num;
        $sql_res = $this->app->db->query($sql)->fetchAll();

        $result = [];
        $ccid_list = [];
        foreach($sql_res as $val){
            $ccid = $val['val0'];
            $suid = $val['val4'];
            if($ccid == -1){
                $val['val1'] = '临时客户';
            }

            $result[$ccid.'-'.$suid] = [
                'ccid'=>$ccid,
                'ccname'=>$val['val1'],
                'total'=>$val['val2'],
                'amount'=>fen2yuan($val['val3']),
                'suid'=>$suid,
                'suname'=>$val['val5']
            ];

            $ccid_list[] = $ccid;
        }
        if(!$ccid_list){
            $ccid_str = ' t1.out_cid is null';
        }
        else{
            $ccid_str = ' t1.out_cid in ('. implode(',',$ccid_list).')';
        }
        //计算退货数据，以便得出净销售
        $count_sql = "select count(distinct t1.out_cid,t1.buid) as val0,sum(t2.total) as val2,sum(t2.amount_price) as val3 from b_stock_in t1,b_stock_in_glist t2 where t1.id=t2.stock_in_id and t2.gid=$gid and t1.status=2 and t1.type=2".
            " and t1.cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59' and ". $ccid_str;
        if($suid){
            $count_sql .= " and t1.buid=$suid";
        }
        $count_res = $this->app->db->query($count_sql)->fetchAll();

        $add_up['total'] -= $count_res[0]['val2'];
        $add_up['amount'] = price_sub($add_up['amount'], fen2yuan($count_res[0]['val3']));

        $sql = "select t1.out_cid as val0,sum(t2.total) as val2,sum(t2.amount_price) as val3,t1.buid as val4,t1.buname as val5 from b_stock_in t1,b_stock_in_glist t2 where t1.id=t2.stock_in_id and t2.gid=$gid and t1.status=2 and t1.type=2".
            " and t1.cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59'";
        if($suid){
            $sql .= " and t1.buid=$suid";
        }
        $sql .= " group by t1.buid,t1.out_cid";
        $sql .= ' limit '. $start_count. ','. $page_num;
        $sql_res = $this->app->db->query($sql)->fetchAll();
        foreach($sql_res as $val){
            $ccid = $val['val0'];
            $suid = $val['val4'];
            $key = $ccid.'-'.$suid;
            if(isset($result[$key])){
                $result[$key]['total'] -= $val['val2'];
                $result[$key]['amount'] = price_sub($result[$key]['amount'], $val['val3']);
            }
        }

        $result = dict2list($result);
        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];

    }


    //客户销售排名
    public function form_customer($data){
        $settle_status = get_value($data, 'settle_status');
        $out_status = ' status in (3,4)';
        $in_status = ' status=2';

        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and in_cid is not null and type=1 and '. $out_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }

        if(get_value($data, 'suids')){
            $where_db .= ' and suid in ('. addslashes($data['suids']). ')';
        }

        if(get_value($data, 'cctypes')){
            $c_model = new Customer();
            $c_res = $c_model->read_list([
                'cctype'=>explode(',', $data['cctypes']),
                'cid'=>$data['cid']
            ]);
            $my_cids = [];
            foreach($c_res['data'] as $val){
                $my_cids[] = $val['ccid'];
            }
            if($my_cids){
                $where_db .= ' and in_cid in ('. implode(',', $my_cids). ')';
            }
            else{
                $where_db .= ' and 1=0';
            }
        }
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);

        $count_sql = "select count(distinct in_cid) as val from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select in_cid,in_cname,count(*) as order_count,sum(amount) as amount from `b_stock_out` where  ". $where_db;

        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'amount desc';
        }

        $sql .= ' group by in_cid order by '. $orderby;

        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        $cid_list = [];
        foreach($r_res as $val){
            if($val['in_cid'] == -1){
                $val['in_cname'] = '临时客户';
            }

            $result[] = [
                'id'=>$val['in_cid'],
                'name'=>$val['in_cname'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
            ];

            $cid_list[] = $val['in_cid'];
        }

        $where_db = ' cid='. $data['cid']. ' and out_cid is not null and type=2 and '. $in_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }
        if($cid_list){
            $where_db .= ' and out_cid in ('. implode(',', $cid_list). ')';
        }
        else{
            $where_db .= ' and 1=0';
        }
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $sql = "select out_cid,count(*) as order_count,sum(amount) as amount from `b_stock_in` where  ". $where_db;

        $sql .= ' group by out_cid';

        $r_res = $this->app->db->query($sql)->fetchAll();
        $r_result = [];
        foreach($r_res as $val){
            $r_result[$val['out_cid']] = [
                'id'=>$val['out_cid'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
            ];
        }

        foreach($result as $key=>$val){
            $r_data = get_value($r_result, $val['id'], []);
            $return_amount = get_value($r_data, 'amount', '0.00');
            $return_count = get_value($r_data, 'order_count', '0.00');
            $result[$key]['return_amount'] = $return_amount;
            $result[$key]['return_order_count'] = $return_count;
            $result[$key]['sub_amount'] = price_sub($val['amount'], $return_amount);
            $result[$key]['sub_order_count'] = $val['order_count'] - $return_count;
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result
        ];

    }

    //商品销售排名
    public function form_goods($data){
        $settle_status = get_value($data, 'settle_status');
        $out_status = ' status in (3,4)';
        $in_status = ' status=2';

        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and in_cid is not null and type=1 and '. $out_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }

        if(get_value($data, 'suids')){
            $where_db .= ' and suid in ('. addslashes($data['suids']). ')';
        }
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);

        $pre_sql = "select id from `b_stock_out` where  ". $where_db;
        $pre_res = $this->app->db->query($pre_sql)->fetchAll();
        $stock_ids = [];
        foreach($pre_res as $val){
            $stock_ids[] = $val['id'];
        }
        if(!$stock_ids){
            return [
                'count'=>0,
                'page_count'=>0,
                'data'=>[]
            ];
        }

        $where_db2 = " stock_out_id in (". implode(',', $stock_ids). ")";

        //商品类型字段
        //如果传了类型ID，找到类型下的所有子类型节点，做数据过滤
        if(get_value($data, 'tids')){
            $cgt_model = new CompanyGoodsType();
            $all_tids = $cgt_model->get_ids_by_fids($data['tids'], $data['cid']);
            $where_db2 .= " and gtid in (". implode(',', $all_tids). ")";
        }

        $count_sql = "select count(distinct gid) as val from `b_stock_out_glist` where ". $where_db2;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select gid,gname,gcode,gbarcode,gunit,gspec,gtid,count(*) as order_count,sum(amount_price) as amount ".
              "from `b_stock_out_glist` where  ". $where_db2;

        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'amount desc';
        }

        $sql .= ' group by gid order by '. $orderby;

        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        $gid_list = [];

        foreach($r_res as $val){
            $result[] = [
                'gid'=>$val['gid'],
                'gname'=>$val['gname'],
                'gcode'=>$val['gcode'],
                'gbarcode'=>$val['gbarcode'],
                'gunit'=>$val['gunit'],
                'gspec'=>$val['gspec'],
                'gtid'=>$val['gtid'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
            ];
            $gid_list[] = $val['gid'];

        }
        if($result){
            $result = Change::go($result, 'gtid', 'gtname', 'o_company_goods_type');
        }

        $where_db = ' cid='. $data['cid']. ' and out_cid is not null and type=2 and '. $in_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //如果传了日期
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }

        if(get_value($data, 'suids')){
            $where_db .= ' and buid in ('. addslashes($data['suids']). ')';
        }
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $pre_sql = "select id from `b_stock_in` where  ". $where_db;
        $pre_res = $this->app->db->query($pre_sql)->fetchAll();
        $stock_ids = [];
        foreach($pre_res as $val){
            $stock_ids[] = $val['id'];
        }
        if(!$stock_ids){
            $where_db2 = " 1=0";
        }
        else{
            $where_db2 = " stock_in_id in (". implode(',', $stock_ids). ")";
            if($gid_list){
                $where_db2 .= " and gid in (". implode(',', $gid_list). ")";
            }
            else{
                $where_db2 = " 1=0";
            }
        }

        $sql = "select gid,count(*) as order_count,sum(amount_price) as amount ".
              "from `b_stock_in_glist` where  ". $where_db2;

        $sql .= ' group by gid';

        $r_res = $this->app->db->query($sql)->fetchAll();
        $r_result = [];
        foreach($r_res as $val){
            $r_result[$val['gid']] = [
                'gid'=>$val['gid'],
                'amount'=>fen2yuan($val['amount']),
                'order_count'=>$val['order_count'],
            ];
        }

        foreach($result as $key=>$val){
            $r_data = get_value($r_result, $val['gid'], []);
            $return_amount = get_value($r_data, 'amount', '0.00');
            $return_count = get_value($r_data, 'order_count', '0.00');
            $result[$key]['return_amount'] = $return_amount;
            $result[$key]['return_order_count'] = $return_count;
            $result[$key]['sub_amount'] = price_sub($val['amount'], $return_amount);
            $result[$key]['sub_order_count'] = $val['order_count'] - $return_count;
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result
        ];

    }

    //日对账报表
    public function form_balance($data){
        $settle_status = get_value($data, 'settle_status');
        $out_status = ' status in (3,4)';
        $in_status = ' status=2';

        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and in_cid is not null and type=1 and '. $out_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        $where_db .= " and checktime>='". addslashes($data['date']). " 00:00:00'";
        $where_db .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);

        $count_sql = "select count(distinct sid) as val from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select sid,sname,count(*) as order_count,sum(amount) as amount,sum(cost_amount) as cost_amount".
          " from `b_stock_out` where  ". $where_db;

        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'amount desc';
        }

        $sql .= ' group by sid order by '. $orderby;

        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        foreach($r_res as $val){
            $amount = fen2yuan($val['amount']);
            $cost_amount = fen2yuan($val['cost_amount']);
            $profit_amount = fen2yuan($val['amount']-$val['cost_amount']);
            $profit_percent = num2per($profit_amount/$amount);
            $result[$val['sid']] = [
                'id'=>$val['sid'],
                'name'=>$val['sname'],
                'amount'=>$amount,
                'cost_amount'=>$cost_amount,
                'profit_amount'=>$profit_amount,
                'profit_percent'=>$profit_percent,
                'order_count'=>$val['order_count'],
                'return_amount'=>'0.00',
                'return_cost_amount'=>'0.00',
                'return_profit_amount'=>'0.00',
                'return_profit_percent'=>'0.00%',
                'return_order_count'=>0,
            ];
        }

        $where_db = ' cid='. $data['cid']. ' and out_cid is not null and type=2 and '. $in_status;
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        $where_db .= " and checktime>='". addslashes($data['date']). " 00:00:00'";
        $where_db .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        if($settle_status !== Null){
            $where_db .= ' and settle_status='.addslashes($settle_status);
        }

        $sql = "select sid,sname,count(*) as order_count,sum(amount) as amount,sum(cost_amount) as cost_amount".
          " from `b_stock_in` where  ". $where_db;

        $sql .= ' group by sid';

        $r_res = $this->app->db->query($sql)->fetchAll();
        foreach($r_res as $val){
            $amount = fen2yuan($val['amount']);
            $cost_amount = fen2yuan($val['cost_amount']);
            $profit_amount = fen2yuan($val['amount']-$val['cost_amount']);
            $profit_percent = num2per($profit_amount/$amount);

            if(isset($result[$val['sid']])){
                $result[$val['sid']]['return_amount'] = $amount;
                $result[$val['sid']]['return_cost_amount'] = $cost_amount;
                $result[$val['sid']]['return_profit_amount'] = $profit_amount;
                $result[$val['sid']]['return_profit_percent'] = $profit_percent;
                $result[$val['sid']]['return_order_count'] = $val['order_count'];
            }
            else{
                $result[$val['sid']] = [
                    'id'=>$val['sid'],
                    'name'=>$val['sname'],
                    'amount'=>'0.00',
                    'cost_amount'=>'0.00',
                    'profit_amount'=>'0.00',
                    'profit_percent'=>'0.00%',
                    'order_count'=>0,
                    'return_amount'=>$amount,
                    'return_cost_amount'=>$cost_amount,
                    'return_profit_amount'=>$profit_amount,
                    'return_profit_percent'=>$profit_percent,
                    'return_order_count'=>$val['order_count'],
                ];
            }
        }

        foreach($result as $key=>$val){
            $result[$key]['real_amount'] = price_sub($val['amount'], $val['return_amount']);
            $result[$key]['real_cost_amount'] = price_sub($val['cost_amount'], $val['return_cost_amount']);
            $result[$key]['real_profit_amount'] = price_sub($val['profit_amount'], $val['return_profit_amount']);
            $result[$key]['real_profit_percent'] = num2per($result[$key]['real_profit_amount']/$result[$key]['real_amount']);;
            $result[$key]['real_order_count'] = $val['order_count']-$val['return_order_count'];
        }

        $result = dict2list($result);

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result
        ];

    }


    //应收款查询
    public function form_debit($data){
        $begindate = get_value($data, 'begin_date');
        $last_date = get_value($data, 'last_date');

        $orderby = get_value($data, 'orderby');
        //正常销售流程条件
        $where_db = ' cid='. $data['cid']. ' and in_cid is not null and type=1 ';
        //退货单流程条件
        $where_db2 = ' cid='. $data['cid']. ' and out_cid is not null and type=2 ';

        //如果传了客户，要按客户为检索条件
        if(get_value($data, 'ccids')){
            $where_db .= ' and in_cid in ('. addslashes($data['ccids']). ')';
            $where_db2 .= ' and out_cid in ('. addslashes($data['ccids']). ')';
        }
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
            $where_db2 .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        //$where_db .= " and checktime>='". addslashes($data['date']). " 00:00:00'";
        $where_db .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        $where_db .= " and checktime>='". addslashes($begindate). " 00:00:00'";
        $where_db .= ' and status=4';
        $where_db2 .= " and checktime<='". addslashes($data['date']). " 23:59:59'";
        $where_db2 .= " and checktime>='". addslashes($begindate). " 00:00:00'";
        $where_db2 .= ' and status=2';

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct in_cid) as val,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
            "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
            "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
            "sum(case `settle_status` when 1 then 1 else 0 end) as real_total,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then `amount` else 0 end) as exp_amount,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then 1 else 0 end) as exp_total from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'all_amount'=>fen2yuan($count_res[0]['all_amount']),
            'real_amount'=>fen2yuan($count_res[0]['real_amount']),
            'all_total'=>$count_res[0]['all_total'],
            'real_total'=>$count_res[0]['real_total'],
            'exp_amount'=>fen2yuan($count_res[0]['exp_amount']),
            'exp_total'=>$count_res[0]['exp_total']
        ];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select in_cid,in_cname,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
            "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
            "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
            "sum(case `settle_status` when 1 then 1 else 0 end) as real_total,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then `amount` else 0 end) as exp_amount,".
            "sum(case when lastdate<='". addslashes($last_date). "' and settle_status=0 then 1 else 0 end) as exp_total from `b_stock_out` where  ". $where_db;
        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'all_amount desc';
        }

        $sql .= ' group by in_cid order by '. $orderby;

        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();

        $cid_list = [];
        foreach($r_res as $val){
            $cid_list[] = $val['in_cid'];
        }
        if($cid_list){
            //把退货的数据计算在内
            $where_db2 .= ' and out_cid in ('. implode(',',$cid_list). ')';

            $count_sql = "select sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
                "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
                "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
                "sum(case `settle_status` when 1 then 1 else 0 end) as real_total from `b_stock_in` where ". $where_db2;
            $count_res = $this->app->db->query($count_sql)->fetchAll();
            $add_up['all_amount'] -= get_value($count_res[0], 'all_amount', 0);
            $add_up['real_amount'] -= get_value($count_res[0], 'real_amount', 0);
            $add_up['all_total'] += get_value($count_res[0], 'all_total', 0);
            $add_up['real_total'] += get_value($count_res[0], 'real_total', 0);
            $add_up['exp_amount'] -= get_value($count_res[0], 'all_amount', 0);
            $add_up['exp_total'] += get_value($count_res[0], 'all_total', 0);

            $sql2 = "select out_cid,out_cname,sum(case `settle_status` when 0 then 1 else 0 end) as all_total,".
                "sum(case `settle_status` when 0 then `amount` else 0 end) as all_amount,".
                "sum(case `settle_status` when 1 then `amount` else 0 end) as real_amount,".
                "sum(case `settle_status` when 1 then 1 else 0 end) as real_total from `b_stock_in` where  ". $where_db2;
            $sql2 .= ' group by out_cid';
            $r_res2 = $this->app->db->query($sql2)->fetchAll();
            $r_data2 = [];
            foreach($r_res2 as $val){
                $r_data2[$val['out_cid']] = $val;
            }

            foreach($r_res as $key=>$val){
                $temp_data = get_value($r_data2, $val['in_cid'], []);
                $r_res[$key]['all_amount'] -= get_value($temp_data, 'all_amount', 0);
                $r_res[$key]['real_amount'] -= get_value($temp_data, 'real_amount', 0);
                $r_res[$key]['all_total'] += get_value($temp_data, 'all_total', 0);
                $r_res[$key]['real_total'] += get_value($temp_data, 'real_total', 0);
                $r_res[$key]['exp_amount'] -= get_value($temp_data, 'all_amount', 0);
                $r_res[$key]['exp_total'] += get_value($temp_data, 'all_total', 0);
            }
        }

        $result = [];
        //这里返回的all_amount 等应付，指的是除去实付之后的数字
        foreach($r_res as $val){
            $result[] = [
                'id'=>$val['in_cid'],
                'name'=>$val['in_cname'],
                'all_amount'=>fen2yuan($val['all_amount']),
                'real_amount'=>fen2yuan($val['real_amount']),
                'all_total'=>$val['all_total'],
                'real_total'=>$val['real_total'],
                'exp_amount'=>fen2yuan($val['exp_amount']),
                'exp_total'=>$val['exp_total']
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];
    }

    //业务员应收款查询
    public function form_debit_salesman($data){
        $cid = $data['cid'];
        $suid = $data['suid'];
        $last_date = date('Y-m-d');

        //正常销售流程条件
        $where_db = " cid=$cid and in_cid is not null and suid=$suid and type=1 and settle_status=0 and lastdate<='$last_date' ";
        //退货单流程条件
        $where_db2 = " cid=$cid and out_cid is not null and buid=$suid and type=2 and settle_status=0 ";

        $where_db .= ' and status=4';
        $where_db2 .= ' and status=2';

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct in_cid) as val,".
            "sum(`amount`) as exp_amount,".
            "count(*) as exp_total from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'exp_amount'=>fen2yuan($count_res[0]['exp_amount']),
            'exp_total'=>$count_res[0]['exp_total']
        ];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select in_cid,in_cname,".
            "sum(`amount`) as exp_amount,".
            "count(*) as exp_total,min(lastdate) as lastdate from `b_stock_out` where  ". $where_db;

        $orderby = 'exp_amount desc';

        $sql .= ' group by in_cid order by '. $orderby;

        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();

        $cid_list = [];
        foreach($r_res as $val){
            $cid_list[] = $val['in_cid'];
        }
        if($cid_list){
            //把退货的数据计算在内
            $where_db2 .= ' and out_cid in ('. implode(',',$cid_list). ')';

            $count_sql = "select count(*) as all_total,".
                "sum(`amount`) as all_amount from `b_stock_in` where ". $where_db2;
            $count_res = $this->app->db->query($count_sql)->fetchAll();

            $add_up['exp_amount'] -= fen2yuan(get_value($count_res[0], 'all_amount', 0));
            $add_up['exp_total'] += get_value($count_res[0], 'all_total', 0);

            $sql2 = "select out_cid,out_cname,count(*) as all_total,".
                "sum(`amount`) as all_amount from `b_stock_in` where ". $where_db2;
            $sql2 .= ' group by out_cid';
            $r_res2 = $this->app->db->query($sql2)->fetchAll();
            $r_data2 = [];
            foreach($r_res2 as $val){
                $r_data2[$val['out_cid']] = $val;
            }

            foreach($r_res as $key=>$val){
                $temp_data = get_value($r_data2, $val['in_cid'], []);
                $r_res[$key]['exp_amount'] -= get_value($temp_data, 'all_amount', 0);
                $r_res[$key]['exp_total'] += get_value($temp_data, 'all_total', 0);
            }
        }
        $add_up['exp_amount'] = format_yuan($add_up['exp_amount']);

        $result = [];
        //这里返回的all_amount 等应付，指的是除去实付之后的数字
        foreach($r_res as $val){
            $result[] = [
                'id'=>$val['in_cid'],
                'name'=>$val['in_cname'],
                'exp_amount'=>fen2yuan($val['exp_amount']),
                'exp_total'=>$val['exp_total'],
                'delay_days'=>days_sub($last_date, $val['lastdate'])
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];
    }

    //预应收款查询
    public function form_pre_debit($data){
        $orderby = get_value($data, 'orderby');
        $where_db = ' cid='. $data['cid']. ' and in_cid is not null and type in (1,2)';
        //如果传了仓库，要按仓库为检索条件
        if(get_value($data, 'sids')){
            $where_db .= ' and sid in ('. addslashes($data['sids']). ')';
        }
        if(get_value($data, 'ccids')){
            $where_db .= ' and in_cid in ('. addslashes($data['ccids']). ')';
        }
        if(get_value($data, 'begindate')){
            $where_db .= " and checktime>='". addslashes($data['begindate']). " 00:00:00'";
        }
        if(get_value($data, 'enddate')){
            $where_db .= " and checktime<='". addslashes($data['enddate']). " 23:59:59'";
        }
        $where_db .= ' and status=3';

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct in_cid) as val from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select in_cid,in_cname,count(*) as debt_total,sum(amount) as debt_amount from `b_stock_out` where  ". $where_db;
        if($orderby){
            $orderby = str_replace('^', ' ', $orderby);
        }
        else{
            $orderby = 'debt_amount desc';
        }

        $sql .= ' group by in_cid order by '. $orderby;

        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();

        $result = [];
        foreach($r_res as $val){
            $result[] = [
                'id'=>$val['in_cid'],
                'name'=>$val['in_cname'],
                'debt_amount'=>fen2yuan($val['debt_amount']),
                'debt_total'=>$val['debt_total']
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result
        ];
    }

    public function form_stock_out($data){
        $add_up = $this->sum(['amount'], $data);
        $res = $this->read_list($data);

        if($res['count']){
            $res['data'] = Change::go($res['data'], 'sorting_id', 'duid', 'b_sorting', 'duid');
            $res['data'] = Change::go($res['data'], 'sorting_id', 'duname', 'b_sorting', 'duname');
            $res['data'] = Change::go($res['data'], 'duid', 'dphone', 'o_user', 'phone');
        }

        $res['add_up'] = $add_up;
        if(get_value($data, 'download') == 'excel'){
            $excel_data = [];
            $excel_data[] = ['订单号','出货单号','出货单类型','往来单位','填单人','审核人','业务员','结算人','单据金额','审核时间','单据状态','结算状态','结算时间'];
            foreach($res['data'] as $val){
                $excel_data[] = [$val['order_id'],$val['id'],number_to_name($val['type'],'stock_out_type'),$val['in_cname'],
                    $val['uname'],$val['cuname'], $val['suname'],$val['runame'],$val['amount'],$val['checktime'],
                    number_to_name($val['status'],'stock_out_status'),number_to_name($val['settle_status'],'stock_out_settle_status'),$val['settletime']];
            }
            $excel_data[] = ['总计','','','','','','','',$res['add_up']['amount'],'','','',''];
            write_excel($excel_data, '出库单查询('.date('Y-m-d').')');
        }
        return $res;
    }

    public function form_settle($data){
        $date = get_value($data, 'date');
        $pay_types = get_value($data, 'pay_types');
        $suid = get_value($data, 'suid');

        $where_db = ' cid='. $data['cid']. ' and settle_status=1 ';

        if($pay_types){
            $where_db .= ' and pay_type in ('. addslashes($pay_types). ')';
        }
        if($suid){
            $where_db .= ' and suid='. addslashes($suid);
        }

        $where_db .= " and settletime<='". addslashes($date). " 23:59:59'";
        $where_db .= " and settletime>='". addslashes($date). " 00:00:00'";

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $count_sql = "select count(distinct suid,pay_type) as val,sum(amount) as amount,sum(tax_amount) as tax_amount,".
            "sum(discount_amount) as discount_amount from `b_stock_out` where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'amount'=>fen2yuan($count_res[0]['amount']),
            'tax_amount'=>fen2yuan($count_res[0]['tax_amount']),
            'discount_amount'=>fen2yuan($count_res[0]['discount_amount']),
        ];
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select suid,suname,pay_type,sum(amount) as amount,sum(tax_amount) as tax_amount,".
            "sum(discount_amount) as discount_amount from `b_stock_out` where  ". $where_db;

        //$orderby = 'all_amount desc';

        $sql .= ' group by suid,pay_type';

        $sql .= ' limit '. $start_count. ','. $page_num;
        $r_res = $this->app->db->query($sql)->fetchAll();

        $result = [];
        //这里返回的all_amount 等应付，指的是除去实付之后的数字
        foreach($r_res as $val){
            $result[] = [
                'suid'=>$val['suid'],
                'suname'=>$val['suname'],
                'pay_type'=>$val['pay_type'],
                'amount'=>fen2yuan($val['amount']),
                'tax_amount'=>fen2yuan($val['tax_amount']),
                'discount_amount'=>fen2yuan($val['discount_amount'])
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];
    }

    //业务员商品销量查询
    public function form_goods_salesman($data){
        $ugid = get_value($data, 'ugid');
        $gid = get_value($data, 'gid');
        $cid = get_value($data, 'cid');
        $begin_date = get_value($data, 'begin_date');
        $end_date = get_value($data, 'end_date');
        $suid = get_value($data, 'suid');
        $sids = get_value($data, 'sids');
        $top = get_value($data, 'top', 5000);
        $page = get_value($data, 'page', 1);
        $belong = get_value($data, 'belong');
        $orderby = get_value($data, 'orderby', 'val2');
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        if($gid){
            $top = 1;
        }
        $belong_sql = "";
        if($belong){
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            if($uid_list){
                $belong_sql = " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $belong_sql = " and t1.suid is null";
            }
        }

        if($ugid){
            $ug_model = new UserGroup();
            $ugids = $ug_model->get_ids_by_fid($ugid);
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$cid,'group_id'=>$ugids]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            if($uid_list){
                $belong_sql .= " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $belong_sql .= " and t1.suid is null";
            }
        }

//        $sql = "select t2.gid as val0,sum(t2.total) as val2,sum(t2.amount_price) as val3 from `b_order` t1,`b_order_glist` t2".
//            " where t1.id=t2.order_id and t1.`status`=2 and t1.out_cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
//            " t1.checktime<='$end_date 23:59:59' ";

        $sql = "select t2.gid as val0,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_order` t1,`b_order_glist` t2".
            " where t1.id=t2.order_id and t1.`status`=2 and t1.out_cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59' and t1.type=1 ";

        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        if($sids){
            $sql .= " and t1.out_sid in (".$sids.")";
        }
        if($gid){
            $sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $sql .= $belong_sql;
        }

        $sql .= " group by t2.gid order by $orderby desc limit $top";
        $sql_res = $this->app->db->query($sql)->fetchAll();
        $gid = [];
        $gtotal = [];
        foreach($sql_res as $val){
            $gid[] = $val['val0'];
            $gtotal[$val['val0']] = $val['val2'];
        }
        if($gid){
            if(count($gid) == 1){
                $gid[] = -1;
            }
            $gid = implode(',', $gid);
        }
        else{
            $gid = -1;
        }

        $count_sql = "select count(distinct t2.gid,t1.suid) as val0,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_order` t1,`b_order_glist` t2".
            " where t1.id=t2.order_id and t1.`status`=2 and t1.out_cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59'  and t1.type=1 ";
        if($suid){
            $count_sql .= " and t1.suid=$suid";
        }
        if($sids){
            $count_sql .= " and t1.out_sid in (".$sids.")";
        }
        if($gid){
            $count_sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $count_sql .= $belong_sql;
        }

        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];
        $add_up = [
            'total'=>$count_res[0]['val2'],
            'amount'=>fen2yuan($count_res[0]['val3']),
            'free_total'=>$count_res[0]['val4'],
            'task_total'=>0,
            'complete_rate' => '0%'
        ];

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t2.gid as val0,t2.gname as gname,t2.gspec as gspec,t2.gunit as gunit,t1.suid as val1,t1.suname as suname,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_order` t1,`b_order_glist` t2".
            " where t1.id=t2.order_id and t1.`status`=2 and t1.out_cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59'  and t1.type=1 ";
        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        if($sids){
            $sql .= " and t1.out_sid in (".$sids.")";
        }
        if($gid){
            $sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $sql .= $belong_sql;
        }

        $sql .= " group by t2.gid,t1.suid ";
        $sql .= " order by FIELD (t2.gid,".$gid.") asc,val2 desc";
        $sql .= ' limit '. $start_count. ','. $page_num;
        $sql_res = $this->app->db->query($sql)->fetchAll();
        $result = [];
        foreach($sql_res as $val){
            $result[] = [
                'gid'=>$val['val0'],
                'gname'=>$val['gname'],
                'gspec'=>$val['gspec'],
                'suid'=>$val['val1'],
                'suname'=>$val['suname'],
                'total'=>$val['val2'],
                'box_total'=>round($val['val2']/$val['gspec'],2),
                'amount'=>fen2yuan($val['val3']),
                'free_total'=>$val['val4'],
                'free_box_total'=>round($val['val4']/$val['gspec'],2),
                'goods_total'=>$gtotal[$val['val0']],
                'task_total' => 0,
                'complete_rate' => '0%'
            ];
        }

        //获取财务结账日
        $c_model = new Company();
        $c_res = $c_model->read_one(['id'=>$cid]);
        $finance_date = $c_res['financedate'];

        //确定年份
        $begin_time = strtotime($begin_date);
        $end_time = strtotime($end_date);
        $flag = False;

        if(!$finance_date){
            //如果没有设置财务结账日，使用自然月法则
            $begin_year = date('Y', $begin_time);
            $begin_month = date('m', $begin_time);
            $end_year = date('Y', $end_time);
            $end_month = date('m', $end_time);
            if($begin_year == $end_year && $begin_month == $end_month){
                $flag = True;
            }
        }
        else{
            //如果设置了，使用规定法则，大于基准日月份加一，超过12月年份加一
            $begin_year = date('Y', $begin_time);
            $begin_month = date('m', $begin_time);
            $begin_day = date('d', $begin_time);
            $end_year = date('Y', $end_time);
            $end_month = date('m', $end_time);
            $end_day = date('d', $end_time);

            //同年同月，都小于财务结账日，或者都大于结账日
            if($begin_year == $end_year && $begin_month == $end_month){
                if(($begin_day <= $finance_date && $end_day <= $finance_date) || ($begin_day > $finance_date && $end_day > $finance_date)){
                    $flag = True;
                }
            }
            //同年不同月(差一个月)，begin大于，end小于
            elseif($begin_year == $end_year && $begin_month == $end_month-1){
                if($begin_day > $finance_date && $end_day <= $finance_date){
                    $flag = True;
                }
            }
            //不同年的情况
            elseif($begin_year == $end_year-1 && $begin_month == 12 and $end_month == 1){
                if($begin_day > $finance_date && $end_day <= $finance_date){
                    $flag = True;
                }
            }

            if($flag){
                //在同一个网段
                if($end_day > $finance_date){
                    $end_month++;
                    if($end_month > 12){
                        $end_month = 1;
                        $end_year ++;
                    }
                }
            }
        }

        //如果起始时间和截止时间年月相同，开始计算任务完成率
        if($flag){
        //if(substr($begin_date,0,7) == substr($end_date,0,7)){
        //    $year = substr($begin_date, 0,4);
        //    $month = ltrim(substr($begin_date, 5,2),'0');
            $get_val = 'val'.ltrim($end_month,'0');

            $task_where = "t1.id=t2.task_id and t1.year=$end_year and t1.status=1 and t1.cid=$cid and t1.type=1";
            if($suid){
                $task_where .= " and t1.suid=$suid";
            }
            if($gid){
                $task_where .= " and t2.gid in (".$gid.")";
            }

            $task_sql = "select t2.gid,t1.suid,sum(t2.$get_val) as val2 ".
                "from b_task t1,b_task_glist t2 where $task_where group by t2.gid,t1.suid";
            $task_res = $this->app->db->query($task_sql)->fetchAll();

            $all_task_total = 0;
            foreach($task_res as $val){
                $all_task_total += $val['val2'];
            }
            $add_up['task_total'] = $all_task_total;
//            if($add_up['task_total']){
//                $add_up['complete_rate'] = num2per($add_up['box_total']/$add_up['task_total']);
//            }

            foreach($result as $key=>$val){
                $gid = $val['gid'];
                $suid_temp = $val['suid'];
                $result[$key]['task_total'] = 0;
                $result[$key]['complete_rate'] = '0%';
                foreach($task_res as $val2){
                    if($gid == $val2['gid'] && $suid_temp == $val2['suid']){
                        $result[$key]['task_total'] = $val2['val2'];
                        break;
                    }
                }
                if($result[$key]['task_total']){
                    $result[$key]['complete_rate'] = num2per($val['box_total']/$result[$key]['task_total']);
                }
            }

        }

        $res = [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data' => $result,
            'add_up' => $add_up,
            'show_task' => $flag
        ];

        return $res;

    }

    //业务员商品销量查询
    public function form_goods_salesman_sell($data){
        $ugid = get_value($data, 'ugid');
        $gid = get_value($data, 'gid');
        $cid = get_value($data, 'cid');
        $begin_date = get_value($data, 'begin_date');
        $end_date = get_value($data, 'end_date');
        $suid = get_value($data, 'suid');
        $sids = get_value($data, 'sids');
        $top = get_value($data, 'top', 5000);
        $page = get_value($data, 'page', 1);
        $belong = get_value($data, 'belong');
        $orderby = get_value($data, 'orderby', 'val2');
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        if($gid){
            $top = 1;
        }
        $belong_sql = "";
        if($belong){
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            if($uid_list){
                $belong_sql = " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $belong_sql = " and t1.suid is null";
            }
        }

        if($ugid){
            $ug_model = new UserGroup();
            $ugids = $ug_model->get_ids_by_fid($ugid);
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$cid,'group_id'=>$ugids]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            if($uid_list){
                $belong_sql .= " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $belong_sql .= " and t1.suid is null";
            }
        }


        $sql = "select t2.gid as val0,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_stock_out` t1,`b_stock_out_glist` t2".
            " where t1.id=t2.stock_out_id and t1.`status`=4 and t1.cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59' and t1.type=1 ";

        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        if($sids){
            $sql .= " and t1.sid in (".$sids.")";
        }
        if($gid){
            $sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $sql .= $belong_sql;
        }

        $sql .= " group by t2.gid order by $orderby desc limit $top";
        $sql_res = $this->app->db->query($sql)->fetchAll();
        $gid = [];
        $gtotal = [];
        foreach($sql_res as $val){
            $gid[] = $val['val0'];
            $gtotal[$val['val0']] = $val['val2'];
        }
        if($gid){
            if(count($gid) == 1){
                $gid[] = -1;
            }
            $gid = implode(',', $gid);
        }
        else{
            $gid = -1;
        }

        $count_sql = "select count(distinct t2.gid,t1.suid) as val0,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_stock_out` t1,`b_stock_out_glist` t2".
            " where t1.id=t2.stock_out_id and t1.`status`=4 and t1.cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59' and t1.type=1 ";
        if($suid){
            $count_sql .= " and t1.suid=$suid";
        }
        if($sids){
            $count_sql .= " and t1.sid in (".$sids.")";
        }
        if($gid){
            $count_sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $count_sql .= $belong_sql;
        }

        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];
        $add_up = [
            'total'=>$count_res[0]['val2'],
            'amount'=>fen2yuan($count_res[0]['val3']),
            'free_total'=>$count_res[0]['val4'],
        ];

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t2.gid as val0,t2.gname as gname,t2.gspec as gspec,t2.gunit as gunit,t1.suid as val1,t1.suname as suname,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
            " from `b_stock_out` t1,`b_stock_out_glist` t2".
            " where t1.id=t2.stock_out_id and t1.`status`=4 and t1.cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
            " t1.checktime<='$end_date 23:59:59' and t1.type=1 ";
        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        if($sids){
            $sql .= " and t1.sid in (".$sids.")";
        }
        if($gid){
            $sql .= " and t2.gid in (".$gid.")";
        }
        if($belong_sql){
            $sql .= $belong_sql;
        }

        $sql .= " group by t2.gid,t1.suid ";
        $sql .= " order by FIELD (t2.gid,".$gid.") asc,val2 desc";
        $sql .= ' limit '. $start_count. ','. $page_num;
        $sql_res = $this->app->db->query($sql)->fetchAll();
        $suid_list = [];
        $gid_list = [];
        foreach($sql_res as $val){
            if(!in_array($val['val1'], $suid_list)){
                $suid_list[] = $val['val1'];
            }
            if(!in_array($val['val0'], $gid_list)){
                $gid_list[] = $val['val0'];
            }
        }
        $sql_res2 = [];
        if($gid_list && $suid_list){
            //计算退货的情况
            $count_sql = "select sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
                "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
                "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
                " from `b_stock_in` t1,`b_stock_in_glist` t2".
                " where t1.id=t2.stock_in_id and t1.`status`=2 and t1.cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
                " t1.checktime<='$end_date 23:59:59' and t1.type=2 ";
            if($suid){
                $count_sql .= " and t1.buid=$suid";
            }
            if($sids){
                $count_sql .= " and t1.sid in (".$sids.")";
            }
            if($gid){
                $count_sql .= " and t2.gid in (".$gid.")";
            }
            if($belong_sql){
                $count_sql .= str_replace('suid','buid',$belong_sql);
            }

            $count_res2 = $this->app->db->query($count_sql)->fetchAll();
            $add_up['return_total'] = $count_res2[0]['val2'];
            $add_up['return_amount'] = fen2yuan($count_res2[0]['val3']);
            $add_up['return_free_total'] = $count_res2[0]['val4'];

            $sql = "select t2.gid as val0,t1.buid as val1,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
                "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
                "sum(case when t2.amount_price=0 then t2.total else 0 end) as val4".
                " from `b_stock_in` t1,`b_stock_in_glist` t2".
                " where t1.id=t2.stock_in_id and t1.`status`=2 and t1.cid=$cid and t1.checktime>='$begin_date 00:00:00' and ".
                " t1.checktime<='$end_date 23:59:59' and t1.type=2 ";
            if($suid){
                $sql .= " and t1.buid in (".implode(',',$suid_list). ")";
            }
            if($sids){
                $sql .= " and t1.sid in (".$sids.")";
            }
            if($gid){
                $sql .= " and t2.gid in (".implode(',',$gid_list).")";
            }
            $sql .= " group by t2.gid,t1.buid ";
            $sql_res2 = $this->app->db->query($sql)->fetchAll();

        }

        $result = [];
        foreach($sql_res as $key=>$val){

            $return_total = 0;
            $return_amount = '0.00';
            $return_free_total = 0;
            foreach($sql_res2 as $val2){
                if($val['val1'] == $val2['val1'] && $val['val0'] == $val2['val0']){
                    $return_total = $val2['val2'];
                    $return_amount = fen2yuan($val2['val3']);
                    $return_free_total = $val2['val4'];
                }
            }

            $result[] = [
                'gid'=>$val['val0'],
                'gname'=>$val['gname'],
                'gspec'=>$val['gspec'],
                'suid'=>$val['val1'],
                'suname'=>$val['suname'],
                'total'=>$val['val2'],
                'box_total'=>round($val['val2']/$val['gspec'],2),
                'amount'=>fen2yuan($val['val3']),
                'free_total'=>$val['val4'],
                'free_box_total'=>round($val['val4']/$val['gspec'],2),
                'goods_total'=>$gtotal[$val['val0']],
                'return_total'=>$return_total,
                'return_box_total'=>round($return_total/$val['gspec'],2),
                'return_amount'=>$return_amount,
                'return_free_total'=>$return_free_total,
                'return_free_box_total'=>round($return_free_total/$val['gspec'],2),
            ];
        }

        $res = [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data' => $result,
            'add_up' => $add_up
        ];

        return $res;

    }


    //实际回款明细报表
    public function form_real_back($data){
        $cid = get_value($data, 'cid');
        $begin_date = get_value($data, 'begin_date');
        $end_date = get_value($data, 'end_date');
        $sid = get_value($data, 'sid');
        $stock_type = get_value($data, 'stock_type');
        $page = get_value($data, 'page', 1);
        $belong = get_value($data, 'belong');
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

        $res = [];
        if($stock_type == 1){
            $belong_sql = "";
            if($belong){
                $u_model = new User();
                $u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
                $uid_list = [];
                foreach($u_res as $val){
                    $uid_list[] = $val['id'];
                }
                if($uid_list){
                    $belong_sql = " and t1.suid in (".implode(',',$uid_list).")";
                }
                else{
                    $belong_sql = " and t1.suid is null";
                }
            }
            $count_sql = "select count(distinct t1.suid,t1.in_cid,t2.gid) as val0,sum(t2.amount_price) as val2,sum(t2.total) as val3".
                " from `b_stock_out` t1,`b_stock_out_glist` t2".
                " where t1.id=t2.stock_out_id and t1.`settle_status`=1 and t1.cid=$cid and t1.settletime>='$begin_date 00:00:00' and ".
                " t1.settletime<='$end_date 23:59:59'  and t1.type=1 ".$belong_sql;
            if($sid){
                $count_sql .= " and t1.sid=$sid ";
            }

            $count_res = $this->app->db->query($count_sql)->fetchAll();
            $all_count = $count_res[0]['val0'];
            $add_up = [
                'total'=>$count_res[0]['val3'],
                'amount'=>fen2yuan($count_res[0]['val2'])
            ];

            $all_page = intval($all_count/$page_num);
            if($all_count%$page_num!=0){
                $all_page ++;
            }

            $sql = "select t1.suid,t1.suname,t1.in_cid,t1.in_cname,t2.gid,t2.gname,t2.gbarcode,t2.gunit,t2.gspec,sum(t2.amount_price) as val2,".
                "sum(t2.total) as val3 ".
                " from `b_stock_out` t1,`b_stock_out_glist` t2".
                " where t1.id=t2.stock_out_id and t1.`settle_status`=1 and t1.cid=$cid and t1.settletime>='$begin_date 00:00:00' and ".
                " t1.settletime<='$end_date 23:59:59'  and t1.type=1 ".$belong_sql;
            if($sid){
                $sql .= " and t1.sid=$sid ";
            }

            $sql .= " group by t1.suid,t1.in_cid,t2.gid ";
            //$sql .= " order by FIELD (t2.gid,".$gid.") asc,val2 desc";
            $sql .= ' limit '. $start_count. ','. $page_num;
            $sql_res = $this->app->db->query($sql)->fetchAll();
            $result = [];
            foreach($sql_res as $val){
                $result[] = [
                    'suid'=>$val['suid'],
                    'suname'=>$val['suname'],
                    'ccid'=>$val['in_cid'],
                    'ccname'=>$val['in_cname'],
                    'gid'=>$val['gid'],
                    'gname'=>$val['gname'],
                    'gbarcode'=>$val['gbarcode'],
                    'gunit'=>$val['gunit'],
                    'gspec'=>$val['gspec'],
                    'amount'=>fen2yuan($val['val2']),
                    'total'=>$val['val3']
                ];
            }
            $res = [
                'count'=>$all_count,
                'page_count'=>$all_page,
                'data' => $result,
                'add_up' => $add_up
            ];
        }
        elseif($stock_type == 2){
            $belong_sql = "";
            if($belong){
                $u_model = new User();
                $u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
                $uid_list = [];
                foreach($u_res as $val){
                    $uid_list[] = $val['id'];
                }
                if($uid_list){
                    $belong_sql = " and t1.buid in (".implode(',',$uid_list).")";
                }
                else{
                    $belong_sql = " and t1.buid is null";
                }
            }
            $count_sql = "select count(distinct t1.buid,t1.out_cid,t2.gid) as val0,sum(t2.amount_price) as val2,sum(t2.total) as val3".
                " from `b_stock_in` t1,`b_stock_in_glist` t2".
                " where t1.id=t2.stock_in_id and t1.`settle_status`=1 and t1.cid=$cid and t1.settletime>='$begin_date 00:00:00' and ".
                " t1.settletime<='$end_date 23:59:59'  and t1.type=2 ".$belong_sql;
            if($sid){
                $count_sql .= " and t1.sid=$sid ";
            }

            $count_res = $this->app->db->query($count_sql)->fetchAll();
            $all_count = $count_res[0]['val0'];
            $add_up = [
                'total'=>$count_res[0]['val3'],
                'amount'=>fen2yuan($count_res[0]['val2'])
            ];

            $all_page = intval($all_count/$page_num);
            if($all_count%$page_num!=0){
                $all_page ++;
            }

            $sql = "select t1.buid,t1.buname,t1.out_cid,t1.out_cname,t2.gid,t2.gname,t2.gbarcode,t2.gunit,t2.gspec,sum(t2.amount_price) as val2,".
                "sum(t2.total) as val3 ".
                " from `b_stock_in` t1,`b_stock_in_glist` t2".
                " where t1.id=t2.stock_in_id and t1.`settle_status`=1 and t1.cid=$cid and t1.settletime>='$begin_date 00:00:00' and ".
                " t1.settletime<='$end_date 23:59:59'  and t1.type=2 ".$belong_sql;
            if($sid){
                $sql .= " and t1.sid=$sid ";
            }

            $sql .= " group by t1.buid,t1.out_cid,t2.gid ";
            //$sql .= " order by FIELD (t2.gid,".$gid.") asc,val2 desc";
            $sql .= ' limit '. $start_count. ','. $page_num;
            $sql_res = $this->app->db->query($sql)->fetchAll();
            $result = [];
            foreach($sql_res as $val){
                $result[] = [
                    'suid'=>$val['buid'],
                    'suname'=>$val['buname'],
                    'ccid'=>$val['out_cid'],
                    'ccname'=>$val['out_cname'],
                    'gid'=>$val['gid'],
                    'gname'=>$val['gname'],
                    'gbarcode'=>$val['gbarcode'],
                    'gunit'=>$val['gunit'],
                    'gspec'=>$val['gspec'],
                    'amount'=>fen2yuan($val['val2']),
                    'total'=>$val['val3']
                ];
            }
            $res = [
                'count'=>$all_count,
                'page_count'=>$all_page,
                'data' => $result,
                'add_up' => $add_up
            ];
        }
        return $res;
    }

}

