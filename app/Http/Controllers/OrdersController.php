<?php

namespace App\Http\Controllers;

use App\Events\OrderReviewed;
use App\Exceptions\InvalidRequestException;
use App\Http\Requests\ApplyRefundRequest;
use App\Http\Requests\OrderRequest;
use App\Http\Requests\SendReviewRequest;
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
    //首页  ceshi
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


    public function received(Order $order, Request $request)
    {
        //校验权限

        $this->authorize('own', $order);

        //判断订单发货状态是否为已发货

        if ( $order->ship_status !== Order::SHIP_STATUS_DELIVERED ) {
            throw new InvalidRequestException('发货状态不正确');
        }

        //更新发货状态为已收到
        $order->update([
            'ship_status' => Order::SHIP_STATUS_RECEIVED
        ]);

        //返回原页面
//        return redirect()->back();
        // 改为ajax  返回订单信息
        return $order;
    }

    public function review(Order $order)
    {
       //验教权限

        $this->authorize('own', $order);

        //判断是否已支付

        if (!$order->paid_at) {

            throw new InvalidRequestException('该订单未支付,不可评价');

        }

         //使用load方法加载关联数据

        return view('orders.review', ['order' => $order->load(['items.productSku', 'items.product'])]);
    }

    public function sendReview(Order $order, SendReviewRequest $request)
    {
        //校验权限
        $this->authorize('own', $order);

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未支付,不可评价');
        }

        //判断是否已评价

        if ($order->reviewed) {
            throw new InvalidRequestException('该订单已评价,不可重复提交');
        }

        $reviews = $request->input('reviews');

        //开启事务

        \DB::transaction(function () use ($reviews, $order) {

            //遍历用户提交的数据

            foreach ($reviews as $review) {
               $orderItem = $order->items()->find($review['id']);

               //保存评分评价

                $orderItem->update([
                    'rating' => $review['rating'],
                    'review' => $review['review'],
                    'reviewed_at' => Carbon::now(),
                ]);
            }

            //将订单标记为已评价

            $order->update([
                'reviewed' => true
            ]);
            event(new OrderReviewed($order));
        });

        return redirect()->back();
    }


    public function applyRefund(Order $order, ApplyRefundRequest $request)
    {
       //校验该订单是否属于该用户

        $this->authorize('own', $order);

        //判断该订单是否已付款

        if (!$order->paid_at) {
            throw new InvalidRequestException('该订单未付款');
        }

        //判断订单退款状态是否正确

        if ($order->refund_status !== Order::REFUND_STATUS_PENDING) {
            throw new InvalidRequestException('该订单已申请退款，请勿重复申请');
        }

        //将用户的申请理由放到订单extra字段

        $extra = $order->extra ?: [];

        $extra['refund_reason'] = $request->input('reason');

        //将订单退款状态改为已申请退款

        $order->update([
            'refund_status' => Order::REFUND_STATUS_APPLIED,
            'extra'         => $extra,
        ]);
        return $order;
    }
}
