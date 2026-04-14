<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Order;
use App\Models\OrderItem;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Http\Resources\OrderResource;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $user = Auth::user();

        $perPage = $request->get('per_page', 10);

        $query = Order::with(['items.food.category','user'])
            ->latest();

        if ($user->role_id == 2) {
            $query->where('user_id', $user->id);
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        return OrderResource::collection(
            $query->paginate($perPage)
        );
    }

   public function show($id)
   {
       $order = Order::with(['user', 'items.food.category'])->findOrFail($id);

       $user = Auth::user();

       if($user->role_id == 2 && $order->user_id !== $user->id){
           return response()->json([
               'message' => 'Forbidden'
               ], 403);
       }
       return new OrderResource($order->load(['user', 'items.food.category']));
   }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'items' => 'required|array',
            'items.*.food_id' => 'required|exists:foods,id',
            'items.*.quantity' => 'required|integer|min:1',
            'pickup_time' => 'nullable|date',
        ]);

        return DB::transaction(function () use ($validated) {

            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_price' => 0,
                'pickup_time' => $validated['pickup_time'] ?? null,
            ]);


            $foodIds = collect($validated['items'])->pluck('food_id');
            $foods = Food::whereIn('id', $foodIds)->get()->keyBy('id');

            $totalPrice = 0;

            foreach ($validated['items'] as $item) {
                $food = $foods[$item['food_id']];

                $subtotal = $item['quantity'] * $food->price;
                $totalPrice += $subtotal;

                OrderItem::create([
                    'order_id' => $order->id,
                    'food_id' => $food->id,
                    'quantity' => $item['quantity'],
                    'price' => $food->price,
                ]);
            }

            $order->update(['total_price' => $totalPrice]);

            $order->load(['items.food.category']);
            return new OrderResource($order);
        });
    }



    public function updateStatus(Request $request, $id)
    {
        // role check
        $user = Auth::user();
        if (!in_array($user->role_id, [1,3])) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        // validate
        $validated = $request->validate([
            'status' => ['required', Rule::in(Order::statuses())],
        ]);

        // find order
        $order = Order::findOrFail($id);

        // cancelled order
        if ($order->status === Order::STATUS_CANCELLED) {
            return response()->json([
                'message' => 'Cancelled order cannot change status'
            ], 400);
        }

        // ready order
        if ($order->status === Order::STATUS_READY) {
            return response()->json([
                'message' => 'Ready order cannot change status'
            ], 400);
        }

        // allowed transitions
        $allowedTransitions = [
            Order::STATUS_PENDING => [
                Order::STATUS_PREPARING,
                Order::STATUS_CANCELLED
            ],
            Order::STATUS_PREPARING => [
                Order::STATUS_READY
            ],
        ];

        $currentStatus = $order->status;
        $newStatus = $validated['status'];

        if (
            isset($allowedTransitions[$currentStatus]) &&
            !in_array($newStatus, $allowedTransitions[$currentStatus])
        ) {
            return response()->json([
                'message' => 'Invalid status transition'
            ], 400);
        }

        // update
        $order->update([
            'status' => $newStatus
        ]);

        $order->load(['items.food.category','user']);

        return new OrderResource($order);
    }
    public function kitchenOrders(Request $request)
    {
        $user = Auth::user();
        if(!in_array($user->role_id, [1, 3])) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $perPage = $request->get('per_page', 10);

        $orders = Order::with(['user', 'items.food.category'])
            ->whereIn('status', [
                Order::STATUS_PENDING,
                Order::STATUS_PREPARING,
            ])
            ->latest()
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    public function readyOrders(Request $request)
    {
        $user = Auth::user();
        if(!in_array($user->role_id, [1, 3])) {
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }

        $perPage = $request->get('per_page', 10);

        $orders = Order::with(['user', 'items.food.category'])
            ->whereIn('status', [Order::STATUS_READY])
            ->latest()
            ->paginate($perPage);

        return OrderResource::collection($orders);
    }

    public function myOrders()
    {
        $orders = Order::with(['items.food.category'])
            ->where('user_id', Auth::id())
            ->latest()
            ->paginate(10);

        return OrderResource::collection($orders);
    }

    public function cancel($id)
    {
        $order = Order::findOrfail($id);

        if($order->user_id !== Auth::id()){
            return response()->json([
                'message' => 'Forbidden'
            ], 403);
        }
        if (!$order->isPending()) {
            return response()->json([
                'message' => 'Order can not be cancelled'
            ], 400);
        }

        $order->update([
            'status' => Order::STATUS_CANCELLED,
        ]);
        return response()->json([
            'message' => 'Order cancelled'
        ]);
    }

    public function analytics()
    {
        $user = Auth::user();
        if($user->role_id !== 1){
           abort(403, 'Forbidden');
        }

        $totalOrders = Order::count();

        $todayOrders = Order::whereDate('created_at', today())
            ->count();

        $totalRevenue = Order::where('status', Order::STATUS_READY)
            ->sum('total_price');

        $todayRevenue = Order::whereDate('created_at', today())
            ->where('status', Order::STATUS_READY)
            ->sum('total_price');

        $popularFoods = OrderItem::selectRaw('food_id, SUM(quantity) as total')
            ->with(['food:id,name,category_id', 'food.category:id,name'])
            ->groupBy('food_id')
            ->orderByDesc('total')
            ->limit(5)
            ->get();

        $popularFoods = $popularFoods->map(function ($item) {
            return [
                'food_id' => $item->food_id,
                'name' => $item->food->name,
                'category' => $item->food->category->name ?? null,
                'total_sold' => $item->total,
            ];
        });

        $dailyRevenue = Order::selectRaw('DATE(created_at) as date, SUM(total_price) as revenue')
            ->where('status', Order::STATUS_READY)
            ->groupBy('date')
            ->orderByDesc('date')
            ->limit(7)
            ->get();




        return response()->json([
            'orders' => [
                'total' => $totalOrders,
                'today' => $todayOrders,
            ],
            'revenue' => [
                'total' => (float)$totalRevenue,
                'today' => (float)$todayRevenue,
            ],
            'popularFoods' => $popularFoods,
            'dailyRevenue' => $dailyRevenue,
        ]);
    }
}
