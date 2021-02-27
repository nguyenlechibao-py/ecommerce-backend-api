<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Order;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\OrderInvoice;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate');
        if(empty($paginate)) {
            $paginate = 20;
        }
        $orders = Order::paginate($paginate);
        foreach ($orders as $order) {
            $order->products = Order::find($order->id)->products;
        }
        return response()->json([
            'is_success' => true,
            'data' => $orders,
        ]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        // validate request
        $validator = Validator::make($request->all(), $this->rules());
        if($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => $validator->messages(),
            ], Response::HTTP_BAD_REQUEST);
        }
        // create order
        $order = Order::create([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'user_id' => $request->user_id,
            'total' => $request->total,
            'quantity' => $request->quantity,
        ]);
        // decode request to array
        $products = json_decode(json_encode($request->products, true));
        // check empty in cart
        if(empty($products))
            return response()->json([
                'is_success' => false,
                'message' => 'Cart must not be empty',
            ]);
        // add product to pivot
        if(is_array($products)) {
            foreach($products as $product) {
                $order->products()->attach($product->id, [
                    'unit_price' => $product->unit_price,
                    'quantity' => $product->quantity, 
                ]);
            }
        }
        else {
            $order->products()->attach($products->id, [
                'unit_price' => $products->unit_price,
                'quantity' => $products->quantity,
            ]);
        }
        // add product to response
        $order->products;
        // send order through email
        Mail::to($request->email)->send(new OrderInvoice($order));
        // response
        return \response()->json([
            'is_success' => true,
            'data' => $order,
        ], 200);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $order = Order::find($id);
        if(!$order) {
            return response()->json([
                'is_success' => false,
                'message' => 'Order doesn\'t exist',
            ], 404);
        }
        $order->products;
        return response()->json([
            'is_success' => true,
            'data' => $order,
        ], 200);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $order = Order::find($id);
        if(!$order) {
            return response()->json([
                'is_success' => false,
                'message' => 'Order doesn\'t exist',
            ], 404);
        }
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validator fails',
                'errors' => $validator->errors()
            ], 401);
        }
        $order->update([
            'name' => $request->name,
            'email' => $request->email,
            'phone' => $request->phone,
            'address' => $request->address,
            'user_id' => $request->user_id,
            'total' => $request->total,
            'quantity' => $request->quantity,
        ]);
        $products = json_decode(json_encode($request->products, true));
        // check empty in cart
        if(empty($products))
            return response()->json([
                'is_success' => false,
                'message' => 'Cart must not be empty',
                'data' => $products,
            ]);
        $order->products()->detach();
        // add product to pivot
        if(is_array($products)) {
            foreach($products as $product) {
                $order->products()->attach($product->id, [
                    'unit_price' => $product->unit_price,
                    'quantity' => $product->quantity, 
                ]);
            }
        }
        else {
            $order->products()->attach($products->id, [
                'unit_price' => $products->unit_price,
                'quantity' => $products->quantity,
            ]);
        }
        // include products in order response
        $order->products;
        return response()->json([
            'is_success' => true,
            'message' => 'Order has been updated',
            'data' => $order,
        ]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $order = Order::find($id);
        if(!$order) {
            return response()->json([
                'is_success' => false,
                'message' => 'Order doesn\'t exist',
            ], 404);
        }
        $order->delete();
        return \response()->json([
            'is_success' => true,
            'message' => 'Order has been deleted',
        ]);
    }

    /**
     * Rules for validation
     * 
     * @return array
     */ 
    public function rules() {
        return [
            'name' => 'required|max:255',
            'email' => 'required|max:255',
            'address' => 'max:255',
            'phone' => 'max:255',
            'user_id' => 'required',
            'total' => 'required',
            'quantity' => 'required',
        ];
    }
}
