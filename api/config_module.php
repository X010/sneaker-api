<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * module
 *
 * @author      jeffwu <x010@foxmail.com>
 * @copyright   2015 jeffwu
 * @version     0.0.1
 * @package     api
 */

/**
 * @api {post} config_module/read 浏览权限模块列表
 * @apiName config_module/read
 * @apiGroup config_module
 * @apiVersion 0.0.1
 * @apiDescription 浏览权限模块列表
 *
 * @apiSuccess {int} id ID
 * @apiSuccess {string} name 模块名称
 * @apiSuccess {string} menu 模块组名
 * @apiSuccess {string} api api接口分组
 * @apiSuccess {string} function api函数名
 *
 */

function config_module($action, $id = NULL)
{
    init_menu_and_module_name(__FUNCTION__); //初始化当前操作的菜单和模块名
    $app = \Slim\Slim::getInstance();
    $my_module = new Module();
    $data = $app->params;
    switch ($action) {
        case 'read':
            //读取Module数据
            //init_log_oper($action, '浏览角色权限');
            param_check($data, ['page' => "/^\d+$/", 'page_num' => "/^\d+$/"]);
            $data['page'] = get_value($data, 'page', '1');
            $data['page_num'] = get_value($data, 'page_num', '5');
            $data['orderby'] = 'menu^asc';
            $res = $my_module->read_list($data);
            success($res);
            break;
        default:
            error(1100);
    }
}


