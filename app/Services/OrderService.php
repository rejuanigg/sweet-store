<?php
namespace App\Services;

use App\Http\Resources\OrderDetailResource;
use App\Models\Order;
use App\Models\OrderDetail;
use App\Models\Product;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class OrderService
{
    public function store(User $user,array $data)
    {
        return DB::transaction(function() use($user, $data) {
            $acc = 0;

            $order = Order::create([
                'user_id'=> $user->id,
                'datetime'=>now(),
                'total'=>0,
                'status'=>'waiting'
            ]);

            foreach($data['products'] as $item)
                {
                    $product = Product::find($item['product_id']);

                    $stocks = $product->stocks->first();

                    $price = $product->price;

                    if ($item['quantity']>$stocks['quantity']){
                        abort(400, 'Unprocessable Entity');
                    }

                    OrderDetail::create([
                        'order_id' => $order->id,
                        'price' => $price,
                        'product_id'=> $item['product_id'],
                        'quantity' => $item['quantity'],
                    ]);

                    $acc += $price * $item['quantity'];

                    $subtract = $stocks->quantity - $item['quantity'];

                    $stocks->quantity = $subtract;
                    $stocks->save();
                }
            $order->update(['total'=>$acc]);
            return $order;
        }
        );
    }

    public function update(Order $order, array $data)
    {
        $order->update($data);
        return $order->load('orderDetails');
    }
}

?>
