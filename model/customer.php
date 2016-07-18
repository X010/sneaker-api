<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * model of customer
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     model
 */

/**
 * TODO:
 */
class Customer extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['*cid', '*cname', '*ccid', 'ccname', 'ccpyname', 'cctype', 'suid',
        'suname', 'sid', 'period', 'contactor', 'contactor_phone','first_order_time','last_visit_time',
        'vip_type', 'vip_balance', 'vip_daily_reduce', 'vip_end_date', 'vip_share_count', 'vip_logistics',
        'vip_status', 'vip_reduce_time'];

    private $cctypes = [
        1 => '经销商',
        2 => '酒店饭店',
        3 => '商场超市',
        4 => '便利店',
    ];

    protected $search_data = ['ccname','contactor_phone','ccpyname'];

    //可排序的字段
    protected $order_data = ['cctype','createtime','updatetime','id'];

    protected $amount_data = ['vip_balance', 'vip_daily_reduce', 'vip_logistics'];

    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('r_customer', $id);
	}

    /**
     * 浏览供应商客户关系列表
     *
     * @param array $data   sql where
     * @param array $fileds sql fields
     */
    public function view_list($data, $fileds = '*'){
        $ccid_list = [];
        if(get_value($data, 'suid')){
            $suid = $data['suid'];
            unset($data['suid']);
            $cs_model = new CustomerSalesman();
            $cs_res = $cs_model->read_list_nopage([
                'cid'=>$data['cid'],
                'suid'=>$suid
            ]);
            if($cs_res){
                foreach($cs_res as $val){
                    $ccid_list[] = $val['ccid'];
                }
            }
            else{
                $ccid_list = 'null';
            }
            $data['ccid'] = $ccid_list;
        }

        $res = $this->read_list($data, $fileds);

        $suid_list = [];
        foreach($res['data'] as $key=>$val){
            if($val['suid'] && !in_array($val['suid'], $suid_list)){
                $suid_list[] = $val['suid'];
            }
        }
        if($suid_list){
            $u_res = $this->app->db->select('o_user', '*', ['id'=>$suid_list]);
            $phone_list = [];
            foreach($u_res as $val){
                $phone_list[$val['id']] = $val['phone'];
            }

            foreach($res['data'] as $key=>$val){
                if($val['suid']){
                    $res['data'][$key]['suphone'] = get_value($phone_list, $val['suid']);
                }
            }
        }

        //增加返回业务员电话
//        foreach($res['data'] as $key=>$val){
//            if($val['suid']){
//                $temp = $this->app->db->select('o_user','phone',['id'=>$val['suid']]);
//                $res['data'][$key]['suphone'] = $temp[0];
//            }
//        }

        //增加返回类型名称
        if ($fileds == '*' || in_array('cctype', $fileds)){
            foreach ($res['data'] as $k => $v){
                $res['data'][$k]['cctypename'] = $this->cctypes[$v['cctype']];
            }
        }
        return $res;
    }


    /**
     * 批量添加客户关系
     *
     * @param array $datas   
     */
    public function add_batch($datas, $cid, $cname){
        start_action();
        foreach ($datas as $k => $v){
            $datas[$k]['cid'] = $cid;
            if($datas[$k]['ccid'] == $cid){
                error(1720);
            }
            $datas[$k]['cname'] = $cname;
            $datas[$k]['suname'] = $this->get_name_by_id('o_user', $v['suid']);
            $datas[$k]['type'] = 1;
            require_once 'core/pinyin.php';
            $datas[$k]['ccpyname'] = pinyin($datas[$k]['ccname']);
        }

        //批量插入客户关系
        $res = $this->create_batch($datas);
        $cs_model = new CustomerSalesman();
        //批量插入客户业务员关系
        $cs_model->create_batch($datas);
        return $res;
    }

    /**
     * 推荐客户
     *
     * @param int $cid 公司ID
     * @return array 推荐客户列表
     */
    public function my_recommend($cid){
        //查找公司的所有仓库，经营的地域，合并成一个大地域
        $s_model = new Store();
        $s_res = $s_model->read_list_nopage([
            'cid'=>$cid
        ]);
        $sids = [];
        foreach($s_res as $val){
            $sids[] = $val['id'];
        }
        //取出所有仓库对应的地域，但是重复的太多，需要先通过算法进行地域合并
        $sa_model = new StoreArea();
        $sa_res = $sa_model->read_list_nopage([
            'sid'=>$sids
        ]);
        $areas = [];
        foreach($sa_res as $val){
            $is_add = False;
            $flag = 0;
            if($val['areatype'] == 1){
                //如果是国家级，除非列表中已经存在国家级别了，否则就要加入条件
                foreach($areas as $val2){
                    if($val2['areatype'] == 1){
                        $flag = 1;
                        break;
                    }
                }
                if(!$flag){
                    $is_add = True;
                }
            }
            elseif($val['areatype'] == 2){
                //如果是省级别，必须是没有国家，并且没有相同的省，才加入队列
                foreach($areas as $val2){
                    if($val2['areatype'] == 1){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 2 && $val['areapro']==$val2['areapro']){
                        $flag = 1;
                        break;
                    }
                }
                if(!$flag){
                    $is_add = True;
                }
            }
            elseif($val['areatype'] == 3){
                //同理，如果是市级别，必须是没有国家，没有相同的省，也没有相同的市
                foreach($areas as $val2){
                    if($val2['areatype'] == 1){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 2 && $val['areapro']==$val2['areapro']){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 3 && $val['areacity']==$val2['areacity']){
                        $flag = 1;
                        break;
                    }
                }
                if(!$flag){
                    $is_add = True;
                }
            }
            elseif($val['areatype'] == 4){
                //同理
                foreach($areas as $val2){
                    if($val2['areatype'] == 1){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 2 && $val['areapro']==$val2['areapro']){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 3 && $val['areacity']==$val2['areacity']){
                        $flag = 1;
                        break;
                    }
                    if($val2['areatype'] == 4 && $val['areazone']==$val2['areazone']){
                        $flag = 1;
                        break;
                    }
                }
                if(!$flag){
                    $is_add = True;
                }
            }

            if($is_add){
                $areas[] = [
                    'areatype'=>$val['areatype'],
                    'areapro'=>$val['areapro'],
                    'areacity'=>$val['areacity'],
                    'areazone'=>$val['areazone']
                ];
            }
        }
        $c_model = new Company();
        //找到所有在这个大地域的公司，剔除那些已经加为客户的
        $c_list = [];
        foreach($areas as $area){
            $db_where = [];
            if($area['areatype'] == 1){
                //如果是全国经营，推荐所有数据
                $db_where = [];
            }
            elseif($area['areatype'] == 2){
                //如果是省级经营，推荐同省客户公司，不管市区
                $db_where = [
                    'areapro'=>$area['areapro']
                ];
            }
            elseif($area['areatype'] == 3){
                $db_where = [
                    'areapro'=>$area['areapro'],
                    'areacity'=>$area['areacity']
                ];
            }
            elseif($area['areatype'] == 4){
                $db_where = [
                    'areapro'=>$area['areapro'],
                    'areacity'=>$area['areacity'],
                    'areazone'=>$area['areazone']
                ];
            }
            $c_res = $c_model->read_list_nopage($db_where);
            foreach($c_res as $val){
                $c_list[$val['id']] = $val;
            }
        }
        //找到自己的客户列表，从推荐列表中剔除它们
        $my_res = $this->read_list_nopage([
            'cid'=>$cid
        ]);
        $my_ids = [];
        foreach($my_res as $val){
            $my_ids[] = $val['ccid'];
        }
        $my_ids[] = $cid;

        $new_clist = $c_list;
        foreach($c_list as $key=>$val){
            if(in_array($key, $my_ids)){
                unset($new_clist[$key]);
            }
        }

        //把这些公司的信息都抓取出来返回
        $result = dict2list($new_clist);
        return $result;
    }

    /**
     * 注册客户
     *
     * @param array $data 数据信息
     * @param int $my_cid 公司ID
     * @return bool
     */
    public function my_register($data, $my_cid){
        //写公司信息
        start_action();
        $c_model = new Company();
        //获取地域类型
        $data['areatype'] = $c_model->get_area_type($data);

        //经营范围ID转成名称
        $gtids = get_value($data, 'gtids');
        if($gtids){
            $data['gtnames'] = $this->get_names_by_ids('o_goods_type', $gtids);
        }
        //注册的客户肯定是默认非ERP公司
        $data['iserp'] = 0;
        //公司创建者ID
        $data['create_cid'] = $my_cid;
        //插入公司信息
        $cid = $c_model->add($data);

        //注册一个默认仓库，公用公司的部分属性，仓库未开启库存管理功能
        $data['cid'] = $cid;
        $data['cname'] = $data['name'];
        //默认仓库不开启库存管理
        $data['isreserve'] = 0;
        //从配置读取默认仓库的名称
        $data['name'] = $this->app->config('mall_default_store_name');
        $s_model = new Store();
        $sid = $s_model->my_create($data);

        //注册用户，将用户设置成属于新注册的公司，自动拥有新仓库权限，自动加入采购员角色
        $data['sids'] = $sid;
        $data['name'] = $this->app->config('mall_default_user_name');
        $data['rids'] = $this->app->config('mall_buyer_role');
        //默认用户的用户名和手机号是一样的
        //$data['phone'] = $data['username'];
        //默认用户是本公司的超级管理员
        $data['admin'] = 1;
        $u_model = new User();
        $u_model->my_create($data);

        //本公司把新仓库加为客户
        $my_cname = $this->get_name_by_id('o_company', $my_cid);
        $suname = $this->get_name_by_id('o_user', $data['my_suid']);

        require_once 'core/pinyin.php';
        $data['ccpyname'] = pinyin($data['cname']);

        //插入客户关系表
        $this->create([
            'cid'=>$my_cid,
            'cname'=>$my_cname,
            'ccid'=>$cid,
            'ccname'=>$data['cname'],
            'ccpyname'=>$data['ccpyname'],
            'cctype'=>$data['type'],
            'suid'=>$data['my_suid'],
            'suname'=>$suname,
            'sid'=>$data['my_sid'],
            'period'=>$data['my_period'],
            'contactor'=>$data['contactor'],
            'contactor_phone'=>$data['contactor_phone'],
        ]);

        //添加客户业务员关系表
        $cs_model = new CustomerSalesman();
        $cs_data = [
            'cid'=>$my_cid,
            'cname'=>$my_cname,
            'ccid'=>$cid,
            'ccname'=>$data['cname'],
            'suid'=>$data['my_suid'],
            'suname'=>$suname,
            'type'=>1
        ];
        $cs_model->create($cs_data);
        return $cid;
    }

    /**
     * 注册客户
     *
     * @param array $data 数据信息
     * @param int $my_cid 公司ID
     * @return bool
     */
    public function my_register2($data, $my_cid){
        //写公司信息
        //$data['address'] = '北京市';
        $data['my_period'] = 0;
        $data['iserp'] = 0;
        $data['password'] = '123456';
        $data['simple_name'] = $data['name'];
        $data['phone'] = $data['contactor_phone'] = $data['username'];
        //公司创建者ID
        $data['create_cid'] = $my_cid;
        $u_model = new User();
        $u_res = $u_model->read_one([
            'cid'=>$my_cid,
            'orderby'=>'id^asc'
        ]);
        $data['my_suid'] = $u_res['id'];

        start_action();
        $c_model = new Company();
        //插入公司信息
        $cid = $c_model->add($data);


        //注册一个默认仓库，公用公司的部分属性，仓库未开启库存管理功能
        $data['cid'] = $cid;
        $data['cname'] = $data['name'];
        //默认仓库不开启库存管理
        $data['isreserve'] = 0;
        //从配置读取默认仓库的名称
        $data['name'] = $data['name'].'的柜子';

        $s_model = new Store();
        $sid = $s_model->my_create($data);

        //注册用户，将用户设置成属于新注册的公司，自动拥有新仓库权限，自动加入采购员角色
        $data['sids'] = $sid;
        $data['name'] = $data['contactor'];
        $data['rids'] = '0';
        //默认用户是本公司的超级管理员
        $data['admin'] = 1;

        $uid = $u_model->my_create($data);

        //本公司把新仓库加为客户
        $my_cname = $this->get_name_by_id('o_company', $my_cid);
        $suname = $this->get_name_by_id('o_user', $data['my_suid']);

        require_once 'core/pinyin.php';
        $data['ccpyname'] = pinyin($data['cname']);

        //插入客户关系表
        $this->create([
            'cid'=>$my_cid,
            'cname'=>$my_cname,
            'ccid'=>$cid,
            'ccname'=>$data['cname'],
            'ccpyname'=>$data['ccpyname'],
            'cctype'=>$data['type'],
            'suid'=>$data['my_suid'],
            'suname'=>$suname,
            'sid'=>$data['my_sid'],
            'period'=>$data['my_period'],
            'contactor'=>$data['contactor'],
            'contactor_phone'=>$data['contactor_phone'],
            'vip_type'=>2,
            'vip_end_date'=>get_value($data, 'vip_end_date'),
            'vip_logistics'=>get_value($data, 'vip_logistics'),
            'vip_balance' => get_value($data, 'vip_balance'),
            'vip_daily_reduce' => get_value($data, 'daily_reduce')
        ]);

        //添加客户业务员关系表
        $cs_model = new CustomerSalesman();
        $cs_data = [
            'cid'=>$my_cid,
            'cname'=>$my_cname,
            'ccid'=>$cid,
            'ccname'=>$data['cname'],
            'suid'=>$data['my_suid'],
            'suname'=>$suname,
            'type'=>1
        ];
        $cs_model->create($cs_data);
        $result = [
            'cid' => $cid,
            'uid' => $uid
        ];
        return $result;
    }

    /**
     * 注册客户
     *
     * @param array $data 数据信息
     * @param int $my_cid 公司ID
     * @return bool
     */
    public function my_register3($data, $my_cid){
        //写公司信息
        $data['address'] = '北京市';
        $data['my_period'] = 0;
        $data['iserp'] = 0;
        $data['password'] = '123456';
        $data['simple_name'] = $data['name'];
        $data['phone'] = $data['contactor_phone'] = $data['username'];
        //公司创建者ID
        $data['create_cid'] = $my_cid;
        $u_model = new User();
//        $u_res = $u_model->read_one([
//            'cid'=>$my_cid,
//            'orderby'=>'id^asc'
//        ]);
//        $data['my_suid'] = $u_res['id'];

        start_action();
        $c_model = new Company();
        //插入公司信息
        $cid = $c_model->add($data);


        //注册一个默认仓库，公用公司的部分属性，仓库未开启库存管理功能
        $data['cid'] = $cid;
        $data['cname'] = $data['name'];
        //默认仓库不开启库存管理
        $data['isreserve'] = 0;
        //从配置读取默认仓库的名称
        $data['name'] = $data['name'].'的柜子';

        $s_model = new Store();
        $sid = $s_model->my_create($data);

        //注册用户，将用户设置成属于新注册的公司，自动拥有新仓库权限，自动加入采购员角色
        $data['sids'] = $sid;
        $data['name'] = $data['contactor'];
        $data['rids'] = '0';
        //默认用户是本公司的超级管理员
        $data['admin'] = 1;

        $uid = $u_model->my_create($data);

        //本公司把新仓库加为客户
        $my_cname = $this->get_name_by_id('o_company', $my_cid);
        //$suname = $this->get_name_by_id('o_user', $data['my_suid']);

        require_once 'core/pinyin.php';
        $data['ccpyname'] = pinyin($data['cname']);

        //插入客户关系表
        $this->create([
            'cid'=>$my_cid,
            'cname'=>$my_cname,
            'ccid'=>$cid,
            'ccname'=>$data['cname'],
            'ccpyname'=>$data['ccpyname'],
            'cctype'=>$data['type'],
            'suid'=>'',
            'suname'=>'',
            'sid'=>$data['my_sid'],
            'period'=>$data['my_period'],
            'contactor'=>$data['contactor'],
            'contactor_phone'=>$data['contactor_phone'],
        ]);

        //添加客户业务员关系表
//        $cs_model = new CustomerSalesman();
//        $cs_data = [
//            'cid'=>$my_cid,
//            'cname'=>$my_cname,
//            'ccid'=>$cid,
//            'ccname'=>$data['cname'],
//            'suid'=>$data['my_suid'],
//            'suname'=>$suname,
//            'type'=>1
//        ];
//        $cs_model->create($cs_data);

        $result = [
            'cid' => $cid,
            'uid' => $uid
        ];
        return $result;
    }

    /**
     * 数据权限检测
     *
     * @param int $id 当前数据ID
     * @return mixed 当前记录信息
     */
    public function my_power($id){
        $res = $this->read_by_id($id);
        if (!$res || $res[0]['cid'] != $this->app->Sneaker->cid){
            error(8110);
        } //数据权限验证
        return $res[0];
    }

    /**
     * 添加业务员
     *
     * @param array $data 业务数据
     * @return False|int
     */
    public function add_salesman($data){
        //客户业务员关系如果已经存在就报错
        $cs_model = new CustomerSalesman();
        $res = $cs_model->has([
            'cid'=>$data['cid'],
            'ccid'=>$data['ccid'],
            'suid'=>$data['suid']
        ]);
        if($res){
            error(1750);
        }

        $u_model = new User();
        $u_res = $u_model->read_by_id($data['suid']);
        //获取员工属性，是否自有员工
        $u_belong = $u_res[0]['belong'];

        $c_model = new Customer();

        if($u_belong == 1){
            //如果添加的员工是自有员工
            $cs_res = $cs_model->read_one([
                'cid'=>$data['cid'],
                'ccid'=>$data['ccid'],
                'type'=>1
            ]);
            if($cs_res){
                $default_suid = $cs_res['suid'];
                $default_ures = $u_model->read_by_id($default_suid);
                $default_belong = $default_ures[0]['belong'];
                if($default_belong == 1){
                    //默认也是自有员工，报错
                    error(1751);
                }
            }
            //开始添加，并且设置为默认员工
            start_action();
            $data['type'] = 1;
            $data['cname'] = $this->app->Sneaker->cname;
            $data['suname'] = $this->get_name_by_id('o_user', $data['suid']);
            $data['ccname'] = $this->get_name_by_id('o_company', $data['ccid']);

            //之前的其它业务员全部设置成非默认
            $cs_model->update(['type'=>2],[
                'AND'=>[
                    'cid'=>$data['cid'],
                    'ccid'=>$data['ccid']
                ]
            ]);
            //插入数据
            $cs_id = $cs_model->create($data);
            //更改客户关系表中的默认业务员信息
            $c_model->update(['suid'=>$data['suid'],'suname'=>$data['suname']],[
                'AND'=>[
                    'cid'=>$data['cid'],
                    'ccid'=>$data['ccid']
                ]
            ]);
        }
        else{
            //如果是外借，开始添加，默认业务员不动
            $cs_res = $cs_model->has([
                'cid'=>$data['cid'],
                'ccid'=>$data['ccid']
            ]);
            if($cs_res){
                //如果已经有业务员了，那么添加的肯定不是默认业务员
                $data['type'] = 2;
                $data['cname'] = $this->app->Sneaker->cname;
                $data['suname'] = $this->get_name_by_id('o_user', $data['suid']);
                $data['ccname'] = $this->get_name_by_id('o_company', $data['ccid']);
                $cs_id = $cs_model->create($data);
            }
            else{
                //如果是第一个，那么添加的成为默认业务员
                $data['type'] = 1;
                $data['cname'] = $this->app->Sneaker->cname;
                $data['suname'] = $this->get_name_by_id('o_user', $data['suid']);
                $data['ccname'] = $this->get_name_by_id('o_company', $data['ccid']);
                start_action();
                $cs_id = $cs_model->create($data);
                $c_model->update(['suid'=>$data['suid'],'suname'=>$data['suname']],[
                    'AND'=>[
                        'cid'=>$data['cid'],
                        'ccid'=>$data['ccid']
                    ]
                ]);
            }

        }

        return $cs_id;

    }

    /**
     * 删除业务员
     *
     * @return bool
     */
    public function delete_salesman(){
        $cs_model = new CustomerSalesman($this->id);
        $u_model = new User();
        $c_model = new Customer();
        $cs_res = $cs_model->read_by_id();
        if(!$cs_res){
            error(1210);
        }
        $cid = $this->app->Sneaker->cid;
        $ccid = $cs_res[0]['ccid'];
        $u_type = $cs_res[0]['type'];
        //只能删除本公司的业务员
        if($cs_res[0]['cid'] != $cid){
            error(8110);
        }
        $cs_suid = $cs_res[0]['suid'];
        $cs_ures = $u_model->read_by_id($cs_suid);
        $cs_belong = $cs_ures[0]['belong'];

        if($cs_belong == 1){
            //如果是删除自有业务员
            $cs_model->delete_by_id();
            //按照创建时间找到一个最早的业务员
            $cs_res2 = $cs_model->read_one([
                'cid'=>$cid,
                'ccid'=>$ccid,
                'orderby'=>'createtime^asc'
            ]);
            if($cs_res2){
                //把这个最早的业务员设置为默认业务员
                $cs_model->update(['type'=>1],[
                    'id'=>$cs_res2['id']
                ]);
                $c_model->update(['suid'=>$cs_res2['suid'],'suname'=>$cs_res2['suname']],[
                    'AND'=>[
                        'cid'=>$cid,
                        'ccid'=>$ccid
                    ]
                ]);
            }
            else{
                //如果没有业务员了，那么默认业务员设置为空
                $c_model->update(['suid'=>Null,'suname'=>Null],[
                    'AND'=>[
                        'cid'=>$cid,
                        'ccid'=>$ccid
                    ]
                ]);
            }
        }
        else{
            $cs_model->delete_by_id();
            //如果当前删除的是默认业务员，才需要特殊处理
            if($u_type == 1){
                //当前是默认，删除以后需要找下一个接班人
                $cs_res2 = $cs_model->read_one([
                    'cid'=>$cid,
                    'ccid'=>$ccid,
                    'orderby'=>'createtime^asc'
                ]);
                if($cs_res2){
                    //把这个最早的业务员设置为默认业务员
                    $cs_model->update(['type'=>1],[
                        'id'=>$cs_res2['id']
                    ]);
                    $c_model->update(['suid'=>$cs_res2['suid'],'suname'=>$cs_res2['suname']],[
                        'AND'=>[
                            'cid'=>$cid,
                            'ccid'=>$ccid
                        ]
                    ]);
                }
                else{
                    //如果没有业务员了，那么默认业务员设置为空
                    $c_model->update(['suid'=>Null,'suname'=>Null],[
                        'AND'=>[
                            'cid'=>$cid,
                            'ccid'=>$ccid
                        ]
                    ]);
                }
            }
        }

        return True;
    }

    /**
     * 设置默认业务员
     *
     * @return bool
     */
    public function default_salesman(){
        $cs_model = new CustomerSalesman($this->id);
        $u_model = new User();
        $c_model = new Customer();

        $cs_res = $cs_model->read_by_id();
        if(!$cs_res){
            error(1210);
        }
        $cid = $this->app->Sneaker->cid;
        $ccid = $cs_res[0]['ccid'];
        $suid = $cs_res[0]['suid'];
        $suname = $cs_res[0]['suname'];
        if($cs_res[0]['cid'] != $cid){
            error(8110);
        }

        //获取当前默认业务员
        $cs_res = $cs_model->read_one([
            'cid'=>$cid,
            'ccid'=>$ccid,
            'type'=>1
        ]);

        if($cs_res){
            $default_suid = $cs_res['suid'];
            //如果当前设置的业务员已经是默认业务员
            if($suid == $cs_res['suid']){
                error(1755);
            }
            $default_ures = $u_model->read_by_id($default_suid);
            $default_belong = $default_ures[0]['belong'];
            if($default_belong == 1){
                //如果默认业务员是自有员工，报错
                error(1753);
            }
            //原默认业务员改成普通业务员
            $cs_model->update(['type'=>2],['id'=>$cs_res['id']]);
        }
        //设置新默认业务员
        $cs_model->update_by_id(['type'=>1]);
        $c_model->update(['suid'=>$suid,'suname'=>$suname],[
            'AND'=>[
                'cid'=>$cid,
                'ccid'=>$ccid
            ]
        ]);
        return True;
    }

    //查看业务员档案
    public function form_salesman($data){
        $begin_date = get_value($data, 'begin_date', '2016-01-01');
        $end_date = get_value($data, 'end_date', '9999-01-01');
        $suid = get_value($data, 'suid');
        $cid = get_value($data, 'cid');

        $count_sql = "select count(distinct suid) as val,count(*) as val1 from r_customer where createtime>='$begin_date 00:00:00' and createtime<='$end_date 23:59:59' and cid=$cid";
        if($suid){
            $count_sql .= " and suid=$suid";
        }
        $count_res = $this->app->db->query($count_sql)->fetchAll();
        $all_count = $count_res[0]['val'];
        $add_up = [
            'count'=>$count_res[0]['val1']
        ];
        $page = get_value($data, 'page', 1);
        $page_num = get_value($data, 'page_num', 200);
        $all_page = intval($all_count/$page_num);
        if($all_count%$page_num!=0){
            $all_page ++;
        }
        $sql = "select suid,suname,count(*) as val0 from r_customer where createtime>='$begin_date 00:00:00' and createtime<='$end_date 23:59:59' and cid=$cid";
        if($suid){
            $sql .= " and suid=$suid";
        }
        $sql .= " group by suid order by val0 desc";
        $start_count = ($page - 1) * $page_num;
        $sql .= ' limit '. $start_count. ','. $page_num;
        $res = $this->app->db->query($sql)->fetchAll();

        $result = [];
        foreach($res as $val){
            $result[] = [
                'suid' => $val['suid'],
                'suname' => $val['suname'],
                'count' => $val['val0']
            ];
        }


        if(get_value($data, 'download') == 'excel'){
            $excel_data = [];
            $excel_data[] = ['业务员','客户数量'];
            foreach($result as $val){
                $excel_data[] = [$val['suname'],$val['count']];
            }
            $excel_data[] = ['总计',$add_up['count']];
            write_excel($excel_data, '客户业务员排行('.date('Y-m-d').')');
        }

        return [
            'count'=>$all_count,
            'page_count'=>$all_page,
            'data'=>$result,
            'add_up'=>$add_up
        ];

    }

    public function get_price($product_id, $cid, $platform){
        $vip_config = $this->app->config('vip_product');
        $price = 0;
        foreach($vip_config as $key=>$val){
            if($val['product_id'] == $product_id){
                $price = $val['price'];
                break;
            }
        }
        if(!$price){
            error(6310);
        }
        $scid = $this->app->config('b2c_id')[$platform];

        $c_res = $this->read_one([
            'cid'=>$scid,
            'ccid'=>$cid
        ]);
        $now_cctype = $c_res['cctype'];
        $balance = $c_res['vip_balance'];

        $product_list = explode('_', $product_id);
        $buy_cctype = $product_list[0];
        $product_days = $product_list[1];

        $res = '';
        if($buy_cctype < $now_cctype){
            //更低级转换，报错
            error(6313);
        }
        elseif($buy_cctype == $now_cctype){
            //平级转换，原价
            $vip_end_date = days_add($c_res['vip_end_date'], $product_days);
            $res = [
                'type' => '2',
                'price' => $price,
                'now_vip_end_date' => $c_res['vip_end_date'],
                'buy_vip_end_date' => $vip_end_date
            ];
        }
        else{
            //更高级转换，余额够和不够，2种情况
            if($balance >= $price){
                $daily_reduce = round($price/$product_days-0.005, 2);
                $days = intval($balance/$daily_reduce);
                $vip_end_date = days_add(date('Y-m-d'), $days);
                $res = [
                    'type' => '1',
                    'price' => 0,
                    'now_vip_end_date' => $c_res['vip_end_date'],
                    'buy_vip_end_date' => $vip_end_date
                ];
            }
            else{
                $vip_end_date = days_add(date('Y-m-d'), $product_days);
                $res = [
                    'type' => '2',
                    'price' => $price-$balance,
                    'now_vip_end_date' => $c_res['vip_end_date'],
                    'buy_vip_end_date' => $vip_end_date
                ];
            }
        }
        return $res;
    }

    public function change_vip($product_id, $cid, $platform){
        $vip_config = $this->app->config('vip_product');
        $price = 0;
        foreach($vip_config as $key=>$val){
            if($val['product_id'] == $product_id){
                $price = $val['price'];
                break;
            }
        }
        if(!$price){
            return 6310;
        }
        $scid = $this->app->config('b2c_id')[$platform];

        $c_res = $this->read_one([
            'cid'=>$scid,
            'ccid'=>$cid
        ]);
        $balance = $c_res['vip_balance'];

        if($balance < $price){
            return 6311;
        }

        $product = explode('_',$product_id);
        $product_type = $product[0];
        $product_days = $product[1];
        if($product_type == 1){
            return 6312;
        }

        if($product_type <= $c_res['cctype']){
            return 6313;
        }

        $daily_reduce = round($price/$product_days-0.005, 2);

        $days = intval($balance/$daily_reduce);
        $vip_end_date = days_add(date('Y-m-d'), $days);

        $data['daily_reduce'] = $daily_reduce;
        $data['vip_end_date'] = $vip_end_date;
        $data['cctype'] = $product_type;
        $this->update($data,[
            'AND'=>[
                'cid'=>$scid,
                'ccid'=>$cid
            ]
        ]);

        return True;

    }

    public function vip_up($data){
        //param: cid,scid,product_id
        $cid = get_value($data, 'cid');
        $scid = get_value($data, 'scid');
        $product_id = get_value($data, 'product_id');

        $vip_config = $this->app->config('vip_product');
        $price = 0;
        $logistics = 0;
        foreach($vip_config as $key=>$val){
            if($val['product_id'] == $product_id){
                $price = $val['price'];
                $logistics = $val['logistics'];
                break;
            }
        }
        if(!$price){
            error(6310);
        }
        $c_res = $this->read_one([
            'cid'=>$scid,
            'ccid'=>$cid
        ]);
        $now_cctype = $c_res['cctype'];

        $product = explode('_',$product_id);
        $product_type = $product[0];
        $product_days = $product[1];
        $db_data = [];
        if($product_type == 1){
            error(6312);
        }
        if($now_cctype > $product_type){
            error(6313);
        }
        elseif($now_cctype == $product_type){
            $daily_reduce = round($price/$product_days-0.005, 2);
            $vip_end_date = days_add($c_res['vip_end_date'], $product_days);
            $db_data['daily_reduce'] = $daily_reduce;
            $db_data['vip_end_date'] = $vip_end_date;
            $db_data['vip_balance[+]'] = $price;
            $db_data['vip_logistics[+]'] = $logistics;
        }
        else{
            $daily_reduce = round($price/$product_days-0.005, 2);
            $vip_end_date = days_add(date('Y-m-d'), $product_days);
            $db_data['daily_reduce'] = $daily_reduce;
            $db_data['vip_end_date'] = $vip_end_date;
            $db_data['cctype'] = $product_type;
            $db_data['vip_balance'] = $price;
            $db_data['vip_logistics[+]'] = $logistics;
        }

        $this->update($db_data,[
            'AND'=>[
                'cid'=>$scid,
                'ccid'=>$cid
            ]
        ]);

        return True;
    }

}




