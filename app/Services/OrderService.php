<?php

namespace App\Services;

use App\Exceptions\InvalidRequestException;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\User;
use App\Models\UserAddress;
use Carbon\Carbon;

class OrderService
{
    public function store(User $user, UserAddress $userAddress, $remark, $items)
    {
        //开启一个事务

        $order = \DB::transaction(function () use ($user, $userAddress, $remark, $items) {

            //更新此地址最后使用时间

            $userAddress->update(['last_used_at' => Carbon::now()]);
            //创建订单

            $order = new Order([
                'address' => [
                    'address'       => $userAddress->full_address,
                    'zip'           => $userAddress->zip,
                    'contact_name'  => $userAddress->contact_name,
                    'contact_phone' => $userAddress->contact_phone,
                ],
                'remark'   => $remark,
                'total_amount' => 0,
            ]);

            //订单关联到当前用户

            $order->user()->associate($user);

            //写入数据库

            $order->save();

            $total_amount = 0;

            //便利提交的SKu

            foreach ($items as $data) {
                $sku = ProductSku::find($data['sku_id']);

                //创建一个OrderItem 并直接与当前订单关联

                $item = $order->items()->make([
                    'amount' => $data['amount'],
                    'price'  => $sku->price,
                ]);
                $item->product()->associate($sku->product_id);
                $item->productSku()->associate($sku);
                $item->save();
                $total_amount += $sku->price * $data['amount'];
                if ($sku->decreaseStock($data['amount']) <= 0) {
                    throw new InvalidRequestException('该商品库存不足');
                }
            }

            //更新订单总金额
            $order->update(['total_amount'=>$total_amount]);

            //将下单的商品从购物车中删除
            $skuIds = collect($items)->pluck('sku_id')->all();
            app(CartService::class)->remove($skuIds);

            return $order;
        });

        //这里我们直接使用dispatch函数

        dispatch(new CloseOrder($order, config('app.order_ttl')));
        return $order;
    }
}
