<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * power
 *
 * @author      fish <fish386@163.com>
 * @copyright   2015 fish
 * @version     0.0.1
 * @package     model
 */

/**
 * 计划任务类
 */
class Plan{
    
    //设置计划任务
    static public function create($name, $api, $param, $time){
        $app = \Slim\Slim::getInstance();
        $url = $app->config('quartzUrl');
        $data = [
            //'jobName'=>$name,
            'name'=>$name,
            //'requestUrl'=>$api.'?'.http_build_query($param),
            'httpurl'=>$api.'?'.http_build_query($param),
            //'param'=>$param,
            'param'=>'',
            'cron'=>time_chg($time),
            'jobgroup'=>'ErpJobGroup',
            'tiggergroup'=>'ErpTiggerGroup'
            //'cron'=>'0 30 20 14 12 ? 2015-2015',
        ];

        $data = http_build_query($data);
        $res = curl($url.'?'.$data);
        $res_dict = json_decode($res, True);
        if($res_dict['status'] != '200'){
            error(9910, $res_dict['status']);
        }
        else{
            $jobname = $res_dict['message'];
            return $jobname;
        }
    }

    //取消计划任务
    static public function delete($name){
        return True;
    }

    
}

