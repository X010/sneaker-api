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


class CustomerSalesman extends Object{

    /**
     * 数据库字段（只允许以下字段写入）
     */
    protected $format_data = ['*cid', '*cname', 'ccid', 'ccname', 'suid', 'suname','type'];

    protected $search_data = ['suname'];

    //可排序的字段
    protected $order_data = ['type','id','createtime'];

    /**
     * constructor
	 *
     * @param  int 	$id 	ID
     */
	public function __construct($id = NULL){
		parent::__construct('r_customer_salesman', $id);
	}

}




