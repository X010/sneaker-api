<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of company
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

/**
 * TODO:
 */
class Company extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = [
        '*code', 'name', 'simple_name', 'py_name','address', 'init_process', 'tax_no', 'account_no', 'license', 'fax',
        'phone', 'pay_team_in', 'pay_team_out', 'lawrep', 'contactor', 'contactor_phone', 'email', 'type', 'status',
        'memo', 'basedate', 'iserp','areapro','areacity','areazone','areatype','gtids','gtnames','print_tpl','create_cid',
        'financedate'
    ];

    //搜索字段
    protected $search_data = ['name','code','py_name','simple_name'];

    /**
     * code 自动生成的前缀  11-公司  21-商品  31-仓库 51-员工
     */
    protected $code_pre = '11';

    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('o_company', $id);
	}

    /**
     * 插入新公司
     *
     * @param array $data  company info
     */
    public function add($data){
        //判断名称是否可能重复
        $this->my_base_check($data);
        $data['code'] = $this->get_code(); //自动生成code
        $res = $this->create($data);
        return $res;
    }

    /**
     * 更新公司信息
     *
     * @param $data
     * @return False|int
     */
    public function my_update($data){
        //判断名称是否可能重复
        $this->my_base_check($data);
        start_action();
        if(isset($data['name'])){
            //更改user表和customer表的cname字段
            $this->app->db->update('o_user', [
                'cname'=>$data['name']]
                , ['cid'=>$this->id]);
            $this->app->db->update('r_customer', [
                'ccname'=>$data['name']]
            , ['ccid'=>$this->id]);
            $this->app->db->update('r_customer_salesman', [
                'ccname'=>$data['name']]
            , ['ccid'=>$this->id]);
            $this->app->db->update('r_supplier', [
                'scname'=>$data['name']]
            , ['scid'=>$this->id]);
        }
        $res = $this->update_by_id($data);
        return $res;
    }

    //基础检测，检测公司名称、税号、营业执照是否有重复的
    public function my_base_check($data){
        //公司名称判断
        if(isset($data['name'])) {
            $ret = $this->has([
                'name' => $data['name'],
                'id[!]' => $this->id
            ]);
            if ($ret) {
                error(1703);
            }
        }
//        //税号判断
//        if(isset($data['tax_no'])){
//            $ret = $this->has([
//                'AND' => [
//                    'tax_no'=>$data['tax_no'],
//                    'id[!]'=>$this->id
//                ],
//            ]);
//            if($ret){
//                error(1704);
//            }
//        }
//        //营业执照
//        if(isset($data['license'])){
//            $ret = $this->has([
//                'AND' => [
//                    'license'=>$data['license'],
//                    'id[!]'=>$this->id
//                ],
//            ]);
//            if($ret){
//                error(1705);
//            }
//        }
        return True;
    }

    /**
     * 重置客户的第一个用户密码
     *
     * @return array
     * @throws SneakerException
     */
    public function my_reset_password(){
        //如果目标是ERP公司，直接报错
        $res = $this->is_erp($this->id);
        if($res){
            error(1741);
        }

        //判断当前公司是否目标公司的第一个供应商，如果不是则无权限操作
        $c_model = new Customer();
        $c_res = $c_model->read_one([
            'ccid'=>$this->id,
            'orderby'=>'id^asc'
        ]);
        if(!$c_res || $c_res['cid']!=$this->app->Sneaker->cid){
            error(1740);
        }

        //找到目标公司下的第一个默认用户
        $u_model = new User();
        $u_res = $u_model->get_first_user($this->id);

        //重置该用户密码，并返回信息给前端
        $password = $this->app->config('default_password');
        $u_model->update([
            'password' => my_password_hash($password),
        ],[
            'id' => $u_res['id']
        ]);
        return [
            'username'=>$u_res['username'],
            'password'=>$password
        ];
    }

    /**
     * 供应商报表
     *
     * @param $data
     * @return array|False
     */
    public function form_supplier($data){
        //首先必须是自己的供应商
        $s_model = new Supplier();
        $s_res = $s_model->read_list_nopage([
            'cid'=>$data['cid']
        ]);
        $sids = [];
        $periods = [];
        foreach($s_res as $val){
            $sids[] = $val['scid'];
            $periods[$val['scid']] = $val['period'];
        }
        $param['id'] = $sids;

        //如果指定了类型
        if(get_value($data, 'types')){
            $param['type'] = explode(',', $data['types']);
        }
        //如果指定了区域
        if(get_value($data, 'areapro')){
            $param['areapro'] = $data['areapro'];
        }
        if(get_value($data, 'areacity')){
            $param['areacity'] = $data['areacity'];
        }
        if(get_value($data, 'areazone')){
            $param['areazone'] = $data['areazone'];
        }
        //关键字
        if(get_value($data, 'search')){
            $param['search'] = $data['search'];
        }
        //分页
        if(get_value($data, 'page')){
            $param['page'] = $data['page'];
        }
        if(get_value($data, 'page_num')){
            $param['page_num'] = $data['page_num'];
        }

        $res = $this->read_list($param);
        if($res['count']){
            foreach($res['data'] as $key=>$val){
                $res['data'][$key]['period'] = get_value($periods, $val['id']);
                $res['data'][$key]['address'] = format_address($val['address']);
            }
        }
        return $res;
    }

    /**
     * 客户报表
     *
     * @param $data
     * @return array
     */
    public function form_customer($data){

        $where_db = [
            'r_customer.cid'=>$data['cid']
        ];
        if(get_value($data, 'types')){
            $where_db['r_customer.cctype'] = explode(',', $data['types']);
        }
        if(get_value($data, 'suids')){
            $where_db['r_customer.suid'] = explode(',', $data['suids']);
        }
        if(get_value($data, 'begin_date')){
            $where_db['r_customer.createtime[>=]'] = $data['begin_date'].' 00:00:00';
        }
        if(get_value($data, 'end_date')){
            $where_db['r_customer.createtime[<=]'] = $data['end_date']. ' 23:59:59';
        }

        //如果指定了区域
        if(get_value($data, 'areapro')){
            $where_db['o_company.areapro'] = $data['areapro'];
        }
        if(get_value($data, 'areacity')){
            $where_db['o_company.areacity'] = $data['areacity'];
        }
        if(get_value($data, 'areazone')){
            $where_db['o_company.areazone'] = $data['areazone'];
        }
        //关键字
        if(get_value($data, 'search')){
            $where_db['OR'] = [
                'name[~]' => '%'.$data['search'].'%',
                'code[~]' => '%'.$data['search'].'%',
                'py_name[~]' => '%'.$data['search'].'%',
            ];
        }

        $where['AND'] = $where_db;
        $all_count = $this->app->db->count('r_customer', [
            '[>]o_company'=>['ccid'=>'id']
        ],[
            'ccid',
        ],$where);

        $where_db2 = $where_db;
        $where_db2['r_customer.trade_total[>]'] = 0;
        $where['AND'] = $where_db2;
        $trade_totals = $this->app->db->count('r_customer', [
            '[>]o_company'=>['ccid'=>'id']
        ],[
            'ccid',
        ],$where);

        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $start_count = ($page - 1) * $page_num;
        $where['AND'] = $where_db;
        $where['LIMIT'] = [$start_count, $page_num];

        $res = $this->app->db->select('r_customer',[
            '[>]o_company'=>['ccid'=>'id']
        ],[
            'r_customer.period',
            'r_customer.suname',
            'r_customer.trade_total',
            'r_customer.cctype',
            'r_customer.sid',
            'r_customer.ccname',
            'r_customer.contactor',
            'r_customer.contactor_phone',
            'o_company.address',
            'o_company.areapro',
            'o_company.areacity',
            'o_company.areazone',
            'o_company.gtnames',
            'o_company.phone',
            'o_company.license',
            'o_company.tax_no',
            'o_company.name'
        ],$where);

        $page_count = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $page_count ++;
        }

        $res = Change::go($res, 'sid', 'out_sname', 'o_store');

        $result = [
            'data' => $res,
            'count' => $all_count,
            'trade_total' => $trade_totals,
            'page_count' => $page_count
        ];

        return $result;
    }


    //根据省、市、区信息获取区域类型
    public function get_area_type($data){
        $areatype = 4;
        if(!get_value($data, 'areazone')){
            $areatype = 3;
        }
        if(!get_value($data, 'areacity')){
            $areatype = 2;
        }
        if(!get_value($data, 'areapro')){
            $areatype = 1;
        }
        return $areatype;
    }

    //判断公司是否ERP公司 True-是 False-不是
    public function is_erp($id){
        $res = $this->read_by_id($id);
        if($res[0]['iserp']){
            return True;
        }
        else{
            return False;
        }
    }
}




