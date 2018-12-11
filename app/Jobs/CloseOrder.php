<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Queue\SerializesModels;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;

class CloseOrder implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $order;
    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Order $order, $delay)
    {
        //
        $this->order = $order;

        //设置延迟的时间 delay() 方法的参数代表多少秒之后执行

        $this->delay($delay);

    }

    /**
     * 定义这个任务类具体执行逻辑
     *
     *当队列处理器从队列中取出任务时，会调用handle方法
     */
    public function handle()
    {
        //判断对应的订单是否已被支付
        //如果已经支付则不需要关闭订单

        if ($this->order->paid_at) {
            return;
        }

        //通过事物执行sql
        \DB::transaction(function (){
           //将订单 closed 字段标记为true 就是关闭订单

            $this->order->update(['closed'=>true]);

            //循环遍历订单中商品 Sku 将订单中数量加回sku库存中

            foreach ($this->order->items as $item) {
                 $item->productSku->addStock($item->amount);
            }
        });

    }


}
