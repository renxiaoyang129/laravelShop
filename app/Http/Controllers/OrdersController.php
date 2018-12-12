<?php

namespace App\Http\Controllers;

use App\Exceptions\InvalidRequestException;
use App\Http\Requests\OrderRequest;
use App\Jobs\CloseOrder;
use App\Models\Order;
use App\Models\ProductSku;
use App\Models\UserAddress;
use App\Services\CartService;
use App\Services\OrderService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    //首页
    public function index(Request $request)
    {
       $orders = Order::query()
           //使用with方法进行预加载 避免n+1
           ->with(['items.product', 'items.productSku'])
           ->where('user_id', $request->user()->id)
           ->orderBy('created_at', 'desc')
           ->paginate();
//dd($orders);
       return view('orders.index', ['orders'=>$orders]);
    }


    //订单详情页
    public function show(Order $order, Request $request)
    {
        $this->authorize('own', $order);

        return view('orders.show', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }


    //下订单  引入cartService
    public function store(OrderRequest $request, OrderService $orderService)
    {
        $user  = $request->user();
        $address = UserAddress::find($request->input('address_id'));
//        // 开启一个数据库事务
//        $order = \DB::transaction(function () use ($user, $request, $cartService) {
//            $address = UserAddress::find($request->input('address_id'));
//            // 更新此地址的最后使用时间
//            $address->update(['last_used_at' => Carbon::now()]);
//            // 创建一个订单// 将地址信息放入订单中
//            $order   = new Order([
//                'address'      => [
//                    'address'       => $address->full_address,
//                    'zip'           => $address->zip,
//                    'contact_name'  => $address->contact_name,
//                    'contact_phone' => $address->contact_phone,
//                ],
//                'remark'       =>  $request->input('remark'),
//                'total_amount' => 0,
//            ]);
//            // 订单关联到当前用户
//            $order->user()->associate($user);
//            // 写入数据库
//            $order->save();
//
//            $totalAmount = 0;
//            $items       = $request->input('items');
//            // 遍历用户提交的 SKU
//            foreach ($items as $data) {
//                $sku  = ProductSku::find($data['sku_id']);
//                // 创建一个 OrderItem 并直接与当前订单关联
//                $item = $order->items()->make([
//                    'amount' => $data['amount'],
//                    'price'  => $sku->price,
//                ]);
//                $item->product()->associate($sku->product_id);
//                $item->productSku()->associate($sku);
//                $item->save();
//                $totalAmount += $sku->price * $data['amount'];
//                if ($sku->decreaseStock($data['amount']) <= 0) {
//                    throw new InvalidRequestException('该商品库存不足');
//                }
//            }
//
//            // 更新订单总金额
//            $order->update(['total_amount' => $totalAmount]);
//
//            // 将下单的商品从购物车中移除
//            $skuIds = collect($items)->pluck('sku_id');
////            $user->cartItems()->whereIn('product_sku_id', $skuIds)->delete();
//            $cartService->remove($skuIds);
//            return $order;
//        });
//        $this->dispatch(new CloseOrder($order, config('app.order_ttl')));
//        return $order;
        return $orderService->store($user, $address, $request->input('remark'), $request->input('items'));

    }
}
