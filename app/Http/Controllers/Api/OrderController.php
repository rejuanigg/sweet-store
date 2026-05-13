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
use Illuminate\Support\Facades\Auth;

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

    public function update(Order $order, UpdateOrderRequest $request)
    {
        foreach ($order->orderDetails as $item)
            {
                if($request->status == 'cancelled'){
                    $stocks = $item->product->stocks->first();
                    $reinstate = $stocks->quantity + $item['quantity'];
                    $stocks->quantity = $reinstate;
                    $stocks->save();
                }
            }



        $editOrder = $this->service->update($order, $request->validated());

        $resource = new OrderResource($editOrder);

        return $resource->response()->setStatusCode(200);
    }
}
