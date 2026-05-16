<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreOrderRequest;
use App\Http\Requests\UpdateOrderRequest;
use App\Http\Resources\OrderDetailResource;
use App\Http\Resources\OrderResource;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use App\Services\OrderService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function __construct(
        private OrderService $service
    )
    {}

    public function index()
    {
        $authUser = Auth::user();

        if (in_array($authUser->role, ['employed', 'owner']))
            {
                return OrderResource::collection(Order::all());
            }

        $myOrders = Order::where('user_id', $authUser->id)->get();

        return OrderResource::collection($myOrders);
    }

    public function show(Order $order)
    {
        $authUser = Auth::user();
        $access = in_array($authUser->role, ['employed', 'owner']);
        $accessUser = $order->user_id === $authUser->id;

        if ($access || $accessUser)
            {
                return new OrderResource($order);
            }
        else
            {
                abort(403, 'No tenés permiso para ver esto.');
            }
    }

    public function store(StoreOrderRequest $request)
    {
        $newOrder = $this->service->store($request->user(), $request->validated());

        $resource = new OrderResource($newOrder);

        return $resource->response()->setStatusCode(201);
    }

public function update(Order $order, Request $request)
{
    return DB::transaction(function () use($order, $request){

        $products = $order->orderDetails;
        $oldStatus = $order->status;
        $newStatus = $request->validate([
            'status' => 'required|in:waiting,processing,completed,cancelled'
        ]);

        if ($oldStatus === $newStatus['status']){
            abort(400);
        }
        else if($oldStatus == 'waiting' && $newStatus['status'] == 'cancelled'){
            foreach($products as $item){
                $stocks = $item->product->stocks->first();
                $stocks->quantity += $item->quantity;
                $stocks->save();
            }
        }
        else if($oldStatus == 'processing' && $newStatus['status'] == 'cancelled'){
            abort(400);
        }
        else if($oldStatus == 'cancelled' && ($newStatus['status'] == 'waiting' || $newStatus['status'] == 'processing')){
            foreach($products as $item){
                $stocks = $item->product->stocks->first();
                $stocks->quantity -= $item->quantity;
                $stocks->save();
            }
        }
        else if($oldStatus == 'completed'){
            abort(400);
        }

        $order->update(['status' => $newStatus['status']]);
        return $order;
    });
}
}
