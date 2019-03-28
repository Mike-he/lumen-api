<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class ApiController extends Controller
{
    public function postLoginAction(Request $request)
    {

        $username = $request->get('username');
        $password = $request->get('password');

        try {
            $response = \Requests::post(
                'http://m.coke-food.com/cola-gift-exchange-manager/login',
                [
                    'Content-Type' => 'application/x-www-form-urlencoded',
                ],
                [
                    'username' => $username,
                    'password' => $password
                ]
            );
        } catch (\Exception $e) {
            return new Response([
                'code' => 1,
                'msg' => '登录信息无效！'
            ]);
        }

        try {
            $jsessionId = $response->cookies['JSESSIONID']->value;
        } catch (\Exception $e) {
            return new Response([
                'code' => 1,
                'msg' => '未知错误！'
            ]);
        }

        return new Response([
            'code' => 0,
            'data' => [
                'JSESSIONID' => $jsessionId,
            ],
            'msg' => '登录成功！'
        ]);
    }

    public function getOrdersAction(Request $request)
    {
        $jsessionId = $request->get('JSESSIONID');

        if (empty($jsessionId)) {
            return new Response([
                'code' => 0,
                'msg' => '无效JSESSIONID！'
            ]);
        }

        $pageIndex = $request->get('pageIndex') ?? 0;
        $pageLimit = $request->get('pageLimit') ?? 1;

        $data = [
            'trade_id' => $request->get('trade_id'),
            'receiver_mobile' => $request->get('receiver_mobile'),
            'receiver_name' => $request->get('receiver_name'),
            'start_time' => $request->get('start_time'),
            'end_time' => $request->get('end_time'),
            'status' => $request->get('status'),
        ];

        $responseNew = \Requests::post(
            "http://m.coke-food.com/cola-gift-exchange-manager/trade/?jtStartIndex=$pageIndex&jtPageSize=$pageLimit",
            [
                'Cookie' => 'JSESSIONID=' . $jsessionId,
                'Content-Type' => 'application/x-www-form-urlencoded',
            ],
            $data
        );

        return new Response(json_decode($responseNew->body, true));
    }
}