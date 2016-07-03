<?php 
/**
 * Sneaker - a business framework based on Slim
 *
 * Ticket
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     core
 */

class Ticket{
	
	protected $key1 = 'XPKLBSMTZU';
	protected $key2 = 'runner^_^5people';	
		
	/**
	 * create ticket
	 * 
	 * @return string 	ticket
	 */
	function create(){
		list($usec, $sec) = explode(" ", microtime());
		$t = $sec;
		$mid = '';
		do{
			$a = $t % 36;
			//0-9 数字 10-35 字母A-Z
			if($a > 9){
				$b = $a + 55;
				$a = chr($b);
			}
			$mid .= $a;
			$t = (int)($t/36);
		}while($t);
		//小数位不足部分补X，保证4位
		$usec = (int)($usec*10000);
		$usec = str_pad($usec, 4, 'X', STR_PAD_LEFT);
		$mid = $usec. $mid;
		//补全到15位
		$len = strlen($mid);
		for($i=0;$i<15-$len;$i++){
			$mid .= chr(mt_rand(65,90));
		}
		//第十六位校验位,前15位的ascii码相加除以10的余数
		$w16 = 0;
		for($i=0;$i<15;$i++){
			$w16 += ord($mid[$i]);
		}
		$w16 = $w16 % 10;
		$mid .= $this->key1[$w16];
		$sastr = sha1($mid. $this->key2);
		$sastr = substr($sastr, 12, 4);
		$mid .= strtoupper($sastr);
		return $mid;
	}	
	
	/**
	 * check ticket
	 *	ticket 基本规则校验
	 *
	 * @param  ticket
	 * @return bool  	True 	ticket符合规
	 * 					False 	ticket非法
	 */
	function check($sid){
		$mystr = substr($sid, 0, 16);
		$sastr = substr($sid, 16);
		$sares = sha1($mystr. $this->key2);
		$sares = strtoupper(substr($sares, 12, 4));
		if($sares != $sastr) return false;
	
		$cstr = $mystr[15];
		$mystr = substr($mystr, 0, 15);
		$w16 = 0;
		for($i=0;$i<15;$i++){
			$w16 += ord($mystr[$i]);
		}
		$w16 = $w16 % 10;
		if($cstr != $this->key1[$w16])
			return false;
		else
			return true;
	}
	
}