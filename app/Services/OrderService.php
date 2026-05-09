<?php
namespace App\Services;

use App\Http\Resources\OrderDetailResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;


class OrderService
{
    public function store(User $user,array $data)
    {
        $order = Order::create([
            'user_id'=> $user->id,
            'datetime'=>now(),
            'total'=>0,
            'status'=>'waiting'
        ]);

        $acc = 0;

        foreach ($data['products'] as $item)
            {
                $product = Product::find($item['product_id']);

                $price = $product->price;

                OrderDetail::create([
                    'order_id' => $order->id,
                    'price' => $price,
                    'product_id'=> $item['product_id'],
                    'quantity' => $item['quantity'],
                ]);

                $acc += $price * $item['quantity'];

            }

        $order->update(['total'=>$acc]);
        return $order;

    }

    public function update(Order $order, array $data)
    {
        $order->update($data);
        return $order->load('orderDetails');    }
}

?>
