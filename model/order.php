<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of order
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

class Order extends Bill{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['*in_cid', '*in_cname', 'in_sid', 'in_sname', '*out_cid', '*out_cname', 'out_sid',
        'out_sname', '*type', 'amount', 'uid','uname', 'cuid', 'cuname', 'ouid', 'ouname', 'iuid', 'iuname', 'buid',
        'buname', 'suid', 'suname', 'rank', 'status','memo', 'tax_amount', 'checktime', 'mall_orderno','receipt',
        'contacts','phone','pay_type','ispaid','auto_delete_date','hash', 'business', 'visit_memo','discount_amount',
        'small_amount','gids','box_total','split_status','visit_status','box_total2','from'];

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data_glist = ['*id', '*order_id', 'sn', '*gid', '*gname', '*gpyname', '*gcode', '*gbarcode', '*gtid',
        '*gbid', '*gspec', '*gunit', '*gtax_rate', '*total', '*unit_price', '*amount_price', '*memo', '*tax_price', '*reserveid',
        'business'];


    //搜索字段自动匹配
    protected $search_data = ['id'];

    /**
     * 订单状态ID2name
     */
    protected $status_name = [
        1 => '未审核',
        2 => '已审核',
        3 => '已结算',
        9 => '已作废',
    ];

    //需要分和元转换的金额字段
    protected $amount_data = ['amount','tax_amount','discount_amount','small_amount'];

    protected $order_data = ['id'];

    protected $sidname = 'in_sid';
    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('b_order', $id);
	}

    /**
     * 创建新订单 
     *
     * @param array $data  order info
     *
     * @return int/bool order id
     */
    public function add($data){

        //商品列表整理
        $data_format = Order::format($data);
        $data = $data_format['data'];
        $glist = $data_format['glist'];

        $glist = $this->get_sn($glist);

        $data['hash'] = $this->my_hash($glist);

        //创建订单
        //$data['id'] = make_bill_id(1); 
        $data['status'] = isset($data['status']) ? $data['status'] : 1; //default:未审核

        start_action();

        //检测客户是否首次下单，如果是首次下单，则填写首次下单时间
        if($data['type'] == 1 && $data['out_cid']){
            $c_model = new Customer();
            $c_res = $c_model->read_one([
                'cid'=>$data['out_cid'],
                'ccid'=>$data['in_cid'],
                'first_order_time'=>'null'
            ]);
            if($c_res){
                $c_model->update([
                    'first_order_time'=>date('Y-m-d H:i:s')
                ],['id'=>$c_res['id']]);
            }
        }

        $res = $this->create_all($data, $glist);
        return $res;
    }

    /**
     * 修改订单内容
     *
     * @param array $data  order info
     *
     * @return int/bool order id
     */
    public function modify($data){
        start_action();
        if (get_value($data, 'goods_list')){
            //商品列表整理
            $data_format = Order::format($data);
            $data = $data_format['data'];
            $glist = $data_format['glist'];
            $glist = $this->get_sn($glist);

            $data['hash'] = $this->my_hash($glist);
            //判断商品列表哈希值是否一致，如果一致则不去更新商品列表
            $my_res = $this->read_by_id();
            if($data['hash'] == $my_res[0]['hash']){
                $res = $this->update_by_id($data);
            }
            else{
                //修改订单
                $res = $this->update_all($data, $glist);
            }
        } else {
            $res = $this->update_by_id($data);
        }
        return $res ? $this->id : $res;
    }


    /**
     * 查看订单内容
     *
     * @return array order info
     */
    public function view($type = 'in'){
        //获取订单信息

        if($type == 'out'){
            $this->sidname = 'out_sid';
        }

        $res = $this->read_all_by_id();
        if (!$res) error(3000);
        $data = $res[0];

        $data['status'] = $this->status_name[$data['status']];

        if($data['suid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$data['suid']]);
            $data['suphone'] = $temp[0];
        }
        if($data['buid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$data['buid']]);
            $data['buphone'] = $temp[0];
        }
        return $data;
    }

    /**
     * 查看订单内容
     *
     * @return array order info
     */
    public function view_out(){
        //获取订单信息
        //设置仓库为出库仓库，是为了读取列表的时候显示库存按照出库仓库来显示
        $this->sidname = 'out_sid';
        $res = $this->read_all_by_id();
        if (!$res) error(3000);
        $data = $res[0];
        if($data['out_sid']){
            Power::check_my_sid($data['out_sid']);
        }
        if($data['suid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$data['suid']]);
            $data['suphone'] = $temp[0];
        }
        if($data['buid']){
            $temp = $this->app->db->select('o_user','phone',['id'=>$data['buid']]);
            $data['buphone'] = $temp[0];
        }
        $data['status'] = $this->status_name[$data['status']];
        return $data;
    }

    /**
     * 浏览订单列表
     *
     * @param $data sql_where
     * @param $fields sql_fields
     *
     * @return array order list
     */
    public function view_list($data, $fields='*'){
        //获取订单列表
        $ret = $this->read_list($data, $fields);
        if ($ret['data']){
            foreach ($ret['data'] as $k => $v){
                $ret['data'][$k]['amount'] = $v['amount'];
                $ret['data'][$k]['status'] = $this->status_name[$v['status']];
            }
        }
        return $ret;
    }

    //检查订单的状态、类型、公司属性是否正确
    public function my_power($id, $status, $type, $cname='in_cid', $uname=''){
        $res = $this->read_by_id($id);
        if(!$res){
            error(3000);
        }
        if($res[0][$cname] != $this->app->Sneaker->cid){
            error(3101);
        }
        if($status && $res[0]['status'] != $status){
            if($res[0]['status'] == 9){
                error(3108);
            }
            error(3103);
        }
        if($type && $res[0]['type'] != $type){
            error(3102);
        }
        if($uname == 'iuid' && $res[0]['iuid']){
            error(3104);
        }
        if($uname == 'ouid' && $res[0]['ouid']){
            error(3105);
        }
        return $res[0];
    }

    //判断商城订单号是否已经存在
    public function check_mall_orderno($mall_orderno){
        $res = $this->has([
            'mall_orderno'=>$mall_orderno
        ]);
        return $res;
    }


    // 工具方法：三类单据均可共用，直接通过静态方式调用

    /**
     * 针对goods_list进行格式化
     */
    static public function format($data, $cid=Null){
        $app = \Slim\Slim::getInstance();

        if(!$cid){
            $cid = $app->Sneaker->cid;
        }

        //商品列表整理
        $glist_src = json_decode($data['goods_list'], True); //array
        if (!$glist_src) error(3002);
        unset($data['goods_list']);
        $gids = []; //商品ID列表
        $glist = []; //最终的商品列表
        $amount = 0; //订单总价
        foreach ($glist_src as $item => $goods){
            if (!get_value($goods, 'gid')) error(3004);
            //只有冲正单和修正单的数量才可以为负数，其它单据数量不可为负数
            if(in_array(get_value($data, 'status'), [11,13])){
                if (!get_value($goods, 'total')) error(3005);
            }
            else{
                if (!get_value($goods, 'total') || $goods['total']<0) error(3005);
            }
            //不判断价格，确实有可能存在价格为0的情况
            if (!isset($goods['unit_price']) || yuan2fen($goods['unit_price'])<0) error(3006);
            $gids[] = $goods['gid'];
            //如果没有传总金额，就是用单价乘以数量成为总金额，如果传了总金额就不去覆盖它
            if (!get_value($goods, 'amount_price')){
                $goods['amount_price'] = floatval($goods['unit_price']) * intval($goods['total']); //强制覆盖
            }
            $amount += $goods['amount_price'];
            $glist[] = $goods; //最终列表以ID为key
        }

        $data['gids'] = ','.implode(',',$gids).',';

        if(get_value($data, 'discount_amount')){
            $data['amount'] = price_sub($amount, $data['discount_amount']);
        }
        else{
            $data['amount'] = $amount;
        }
        $data['tax_amount'] = 0;
        $cg_model = new CompanyGoods();
        $gid_list = [];
        $cg_res = $cg_model->read_list_nopage([
            'in_cid'=>$cid,
            'gid'=>$gids
        ]);
        if (!$cg_res) error(3003);

        foreach($cg_res as $val){
            $gid_list[$val['gid']] = $val;
        }

        $data['box_total'] = 0;
        $data['box_total2'] = 0;
        foreach($glist as $key=>$goods){
            $res_goods = get_value($gid_list, $goods['gid'], []);
            $glist[$key]['gname'] = get_value($res_goods, 'gname');
            $glist[$key]['gcode'] = get_value($res_goods, 'gcode');
            $glist[$key]['gspec'] = get_value($res_goods, 'gspec');
            $glist[$key]['gpyname'] = get_value($res_goods, 'gpyname');
            $glist[$key]['gbarcode'] = get_value($res_goods, 'gbarcode');
            $glist[$key]['gunit'] = get_value($res_goods, 'gunit');
            $glist[$key]['gtax_rate'] = get_value($res_goods, 'gtax_rate');
            $glist[$key]['gtid'] = get_value($res_goods, 'gtid');
            $glist[$key]['gbid'] = get_value($res_goods, 'gbid');
            $glist[$key]['gisbind'] = get_value($res_goods, 'gisbind');

            $tax = get_tax($goods['amount_price'], $glist[$key]['gtax_rate']);
            $glist[$key]['tax_price'] = $tax['tax_price'];
            $data['tax_amount'] += $tax['tax_price'];

            //赠品不计入总箱数
            if(yuan2fen($glist[$key]['unit_price']) > 0){
                $data['box_total'] += round($glist[$key]['total']/$glist[$key]['gspec'], 4);
            }
            //赠品总箱数
            if(yuan2fen($glist[$key]['unit_price']) == 0){
                $data['box_total2'] += round($glist[$key]['total']/$glist[$key]['gspec'], 4);
            }

            //默认给生产日期和到效日期添加一个key
            if(!get_value($goods, 'prodate')){
                $glist[$key]['prodate'] = Null;
            }
            if(!get_value($goods, 'expdate')){
                $glist[$key]['expdate'] = Null;
            }
        }

        $glist = dict2list($glist);

        //return
        $ret['data'] = $data;
        $ret['glist'] = $glist;
        return $ret;

    }

    public function auto_delete(){
        $today = date('Y-m-d');
        $yestoday = date('Y-m-d', time()-24*3600);
        $db_where = [
            'AND'=>[
                'iuid'=>Null,
                'auto_delete_date'=>[$today, $yestoday],
                'status'=>2,
                'ispaid'=>0
            ]
        ];
        $res = $this->update([
            'status'=>9
        ],$db_where);
        return $res;
    }

    public function get_on_way($sid, $gids){
        $res = $this->read_list_nopage([
            'status'=>2,
            'in_sid'=>$sid,
            'iuid'=>'null'
        ]);
        $orderid_list = [];
        foreach($res as $val){
            $orderid_list[] = $val['id'];
        }
        $result = [];
        if($orderid_list){
            $res = $this->app->db->select('b_order_glist', '*', [
                'AND'=>[
                    'order_id'=>$orderid_list,
                    'gid'=>$gids
                ]
            ]);

            foreach($res as $val){
                if(!isset($result[$val['gid']])){
                    $result[$val['gid']] = 0;
                }
                $result[$val['gid']] += $val['total'];
            }
        }
        return $result;
    }

    public function my_split($data){
        $split_goods_list = json_decode($data['goods_list'], True);
        $res = $this->read_by_id();
        $res = $res[0];
        $res['split_status'] = 2;

        $old_goods_list = $this->app->db->select('b_order_glist','*',['order_id'=>$this->id,'ORDER'=>'total DESC']);
        $new_split_goods_list = [];
        foreach($split_goods_list as $val){
            $split_gid = $val['gid'];
            $split_total = $val['total'];
            if(!$split_total){
                continue;
            }
            $total = 0;
            foreach($old_goods_list as $val2){
                if($val2['gid'] == $split_gid){
                    $total += $val2['total'];
                }
            }
            if($total < $split_total){
                error(3115);
            }

            foreach($old_goods_list as $key2=>$val2){
                if($val2['gid'] == $split_gid){
                    if($split_total > $val2['total']){
                        //如果需要的更多
                        $split_total -= $val2['total'];
                        $new_split_goods_list[] = [
                            'gid'=>$split_gid,
                            'total'=>$val2['total'],
                            'unit_price'=>fen2yuan($val2['unit_price'])
                        ];
                        $old_goods_list[$key2]['total'] = 0;
                    }
                    else{
                        //如果当前批次已经可以满足
                        $new_split_goods_list[] = [
                            'gid'=>$split_gid,
                            'total'=>$split_total,
                            'unit_price'=>fen2yuan($val2['unit_price'])
                        ];
                        $old_goods_list[$key2]['total'] -= $split_total;
                        break;
                    }
                }
            }
        }

        start_action();
        //把老单的商品种类和数量都更新一遍
        $new_old_goods_list = [];
        foreach($old_goods_list as $val){
            if($val['total'] > 0){
                $new_old_goods_list[] = [
                    'gid'=>$val['gid'],
                    'total'=>$val['total'],
                    'unit_price'=>fen2yuan($val['unit_price'])
                ];
            }
        }
        $res['goods_list'] = json_encode($new_old_goods_list);
        $this->modify($res);

        //生成拆完的新单
        Power::set_oper($res);
        Power::set_oper($res, 'cuid', 'cuname');
        $res['goods_list'] = json_encode($new_split_goods_list);
        $new_order_id = $this->add($res);

        return $new_order_id;
    }

    //业务员订单达成率
    public function form_order_rate($data){
        $ugid = get_value($data, 'ugid');
        $begin_date = get_value($data, 'begin_date');
        $end_date = get_value($data, 'end_date');
        $sids = get_value($data, 'sids');
        $suid = get_value($data, 'suid');
        $cid = get_value($data, 'cid');
        $belong = get_value($data, 'belong');

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 20);

        $where_db = " t1.out_cid=$cid and t1.type=1 ".
            "and t1.checktime>='". addslashes($begin_date). " 00:00:00' and t1.checktime<='". addslashes($end_date). " 23:59:59'";
        //如果传了仓库，要按仓库为检索条件
        if($sids){
            $where_db .= " and t1.out_sid in (".addslashes($sids).")";
        }
        if($suid){
            $where_db .= " and t1.suid=$suid";
        }
        if($belong){
            $u_model = new User();
            $u_res = $u_model->read_list_nopage(['cid'=>$cid,'belong'=>$belong]);
            $uid_list = [];
            foreach($u_res as $val){
                $uid_list[] = $val['id'];
            }
            if($uid_list){
                $where_db .= " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $where_db .= " and t1.suid is null";
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
                $where_db .= " and t1.suid in (".implode(',',$uid_list).")";
            }
            else{
                $where_db .= " and t1.suid is null";
            }
        }

        $count_sql = "select count(distinct t1.suid) as val0,count(*) as val1,sum(case t1.status when 2 then 1 else 0 end) as val2,".
            "sum(case t2.status when 4 then 1 else 0 end) as val3 from `b_order` t1 left join `b_stock_out` t2 ".
            " on t1.id=t2.order_id where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];
        $add_up['order_count'] = $count_res[0]['val1'];
        $add_up['checked_order_count'] = $count_res[0]['val2'];
        $add_up['checked_stock_out_count'] = $count_res[0]['val3'];
        $add_up['rate1'] = my_rate($count_res[0]['val2'], $count_res[0]['val1']);
        $add_up['rate2'] = my_rate($count_res[0]['val3'], $count_res[0]['val2']);

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t1.suid,t1.suname,count(*) as val1,sum(case t1.status when 2 then 1 else 0 end) as val2,".
            "sum(case t2.status when 4 then 1 else 0 end) as val3 from `b_order` t1 left join `b_stock_out` t2 ".
            " on t1.id=t2.order_id where ". $where_db;
        $sql .= " group by t1.suid order by val1 desc";
        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;

        $r_res = $this->app->db->query($sql)->fetchAll();

        $r_result = [];
        foreach($r_res as $val){
            $r_result[] = [
                'suid'=>$val['suid'],
                'suname'=>$val['suname'],
                'order_count'=>$val['val1'],
                'checked_order_count'=>$val['val2'],
                'checked_stock_out_count'=>$val['val3'],
                'rate1'=>my_rate($val['val2'], $val['val1']),
                'rate2'=>my_rate($val['val3'], $val['val2'])
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$r_result,
            'add_up'=>$add_up
        ];
    }

    //业务员订单达成率-详情
    public function form_order_rate_detail($data){

        $begin_date = get_value($data, 'begin_date');
        $end_date = get_value($data, 'end_date');
        $sids = get_value($data, 'sids');
        $suid = get_value($data, 'suid');
        $cid = get_value($data, 'cid');

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 20);

        $where_db = " t1.out_cid=$cid and t1.type=1 ".
            "and t1.checktime>='". addslashes($begin_date). " 00:00:00' and t1.checktime<='". addslashes($end_date). " 23:59:59'";
        //如果传了仓库，要按仓库为检索条件
        if($sids){
            $where_db .= " and t1.out_sid in (".addslashes($sids).")";
        }
        if($suid){
            $where_db .= " and t1.suid=$suid";
        }
        $where_db .= " and t1.status=2 and (t2.status<>4 or t2.status is null)";

        $count_sql = "select count(*) as val0 from `b_order` t1 left join `b_stock_out` t2 ".
            " on t1.id=t2.order_id where ". $where_db;
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t1.id as order_id,t1.checktime,t2.id as stock_out_id,t2.status,t1.in_cname,t1.rank,t1.visit_memo from `b_order` t1 left join `b_stock_out` t2 ".
            " on t1.id=t2.order_id where ". $where_db;
        $sql .= " order by t1.id desc";
        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;

        $r_res = $this->app->db->query($sql)->fetchAll();

        $r_result = [];
        foreach($r_res as $val){
            $r_result[] = [
                'order_id'=>$val['order_id'],
                'order_time'=>$val['checktime'],
                'delay_days'=>days_sub(date('Y-m-d'),$val['checktime']),
                'stock_out_id'=>$val['stock_out_id'],
                'stock_out_status'=>$val['status'],
                'rank'=>$val['rank'],
                'in_cname'=>$val['in_cname'],
                'visit_memo'=>$val['visit_memo']
            ];
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$r_result
        ];
    }

    //业务员单品铺货查询
    public function form_salesman_goods($data){
        $ugid = get_value($data, 'ugid');
        $gid = get_value($data, 'gid');
        $suid = get_value($data, 'suid');
        $cid = get_value($data, 'cid');
        $begindate = get_value($data, 'begin_date');
        $enddate = get_value($data, 'end_date');
        $belong = get_value($data, 'belong');
        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;

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

        $cg_model = new CompanyGoods();
        $cg_res = $cg_model->read_one([
            'in_cid'=>$cid,
            'gid'=>$gid
        ]);
        $gspec = $cg_res['gspec'];

        //正常销售数据
        $count_sql = "select count(distinct t1.in_cid,t1.suid) as val0,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val6".
            " from b_order t1,b_order_glist t2 where t1.id=t2.order_id and t2.gid=$gid and t1.status=2 and t1.type=1".
            " and t1.out_cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59'";
        if($suid){
            $count_sql .= " and t1.suid=$suid";
        }
        if($belong_sql){
            $count_sql .= $belong_sql;
        }
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val0'];
        $add_up = [
            'total' => $count_res[0]['val2'],
            'box_total'=>round($count_res[0]['val2']/$gspec, 2),
            'free_total' => $count_res[0]['val6'],
            'free_box_total'=>round($count_res[0]['val6']/$gspec, 2),
            'amount' => fen2yuan($count_res[0]['val3'])
        ];

        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }

        $sql = "select t1.in_cid as val0,t1.in_cname as val1,sum(case when t2.amount_price>0 then t2.total else 0 end) as val2,".
            "sum(case when t2.amount_price>0 then t2.amount_price else 0 end) as val3,".
            "sum(case when t2.amount_price=0 then t2.total else 0 end) as val6,t1.suid as val4,t1.suname as val5 from b_order t1,b_order_glist t2 where t1.id=t2.order_id and t2.gid=$gid and t1.status=2 and t1.type=1".
            " and t1.out_cid=$cid and t1.checktime>='$begindate 00:00:00' and t1.checktime<='$enddate 23:59:59'";
        if($suid){
            $sql .= " and t1.suid=$suid";
        }
        if($belong_sql){
            $sql .= $belong_sql;
        }
        $sql .= " group by t1.suid,t1.in_cid";
        $sql .= ' limit '. $start_count. ','. $page_num;
        $sql_res = $this->app->db->query($sql)->fetchAll();

        $result = [];
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
                'box_total'=>round($val['val2']/$gspec, 2),
                'amount'=>fen2yuan($val['val3']),
                'suid'=>$suid,
                'suname'=>$val['val5'],
                'free_total' => $val['val6'],
                'free_box_total'=>round($val['val6']/$gspec, 2),
            ];
        }

        $result = dict2list($result);
        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];

    }

}




