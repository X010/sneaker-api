<?php
/**
 * Sneaker - a business framework based on Slim
 *
 * Summary of API document 
 *
 * @author      Linvo <linvo@foxmail.com>
 * @copyright   2015 Linvo
 * @version     0.0.1
 * @package     conf
 */


/**
 * @api {method} API_URI 基本规则
 * @apiName Summary
 * @apiGroup 0_Summary
 * @apiVersion 0.0.1
 * @apiDescription 请求与响应的基本格式（加*为必选参数）
 *
 * @apiParam {string} ticket *登录凭证（注意：以下具体接口中，如无特殊说明，均需传此参数）
 *
 * @apiSuccess {int} err 0：成功
 * @apiSuccess {int} status 0000
 * @apiSuccess {object} msg 详细返回信息
 * @apiSuccessExample Success-Response:
 *     HTTP/1.1 200 OK
 *     {
 *         "err": 0,  
 *         "status": 0000, 
 *         "msg": {
 *             "id": 1, 
 *             "code": 10000, 
 *             "name": "韦小宝"
 *         }
 *     }
 *
 * @apiError (Error 200) {int} err 1：失败
 * @apiError (Error 200) {int} status 错误码
 * @apiError (Error 200) {string} msg 错误描述
 * @apiErrorExample Error-Response:
 *     HTTP/1.1 200 OK
 *     {
 *       "err": 1,
 *       "status": 9999,
 *       "msg": "System Error"
 *     }
 */
 
