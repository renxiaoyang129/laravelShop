<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Models\Order;
use Carbon\Carbon;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    //

    public function payByAlipay(Order $order, Request $request)
    {
        // 判断订单是否属于当前用户
        $this->authorize('own', $order);
        // 订单已支付或者已关闭
        if ($order->paid_at || $order->closed) {
            throw new InvalidRequestException('订单状态不正确');
        }
        // 调用支付宝的网页支付
        return app('alipay')->web([
            'out_trade_no' => $order->no, // 订单编号，需保证在商户端不重复
            'total_amount' => $order->total_amount, // 订单金额，单位元，支持小数点后两位
            'subject'      => '支付 Laravel Shop 的订单：'.$order->no, // 订单标题
        ]);

    }

    // 前端回调页面
    public function alipayReturn()
    {
        // 校验提交的参数是否合法
        try{
            app('alipay')->verify();
        }catch (\Exception $e){

            return view('pages.error', ['msg' => '数据不正确']);

        }
        return view('pages.success', ['msg' => '付款成功']);
//        $data = app('alipay')->verify();
//        dd($data);


    }

    // 服务器端回调
    public function alipayNotify()
    {  ///TODO 服务器回调验证报错 undefind index sing 已解决 终端测试回调参数要用双引
         //号
//        $data = app('alipay')->verify();
//        \Log::debug('Alipay notify', $data->all());

        //输入参数
        $data = app('alipay')->verify();

        //如果订单不成功或者结束 不走后面的逻辑

        //所有校验状态 https://docs.open.alipay.com/59/103672

        if ( !in_array($data->trade_status, ['TRADE_SUCCESS', 'TRADE_FINISHED']) ) {

            return app('alipay')->success();

        }
        //$data->out_trade_no 拿到了订单号 在数据库中查找

        $order = Order::where('no', $data->out_trade_no)->first();

        if (!$order) {
            return 'fail';
        }

        //如果这笔订单的状态是已支付

        if ( $order->paid_at ) {
            //返回数据给支付宝
            return app('alipay')->success();
        }

        $order->update([
            'paid_at' => Carbon::now(),//支付时间
            'payment_method' => 'alipay',     //支付方式
            'payment_no' => $data->trade_no,//支付宝订单号
        ]);

        return app('alipay')->success();
    }



}
