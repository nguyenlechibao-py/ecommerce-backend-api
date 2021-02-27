<?php

namespace App\Http\Middleware;

use Closure;
use App\Product;
use Exception;

class CountBoughtProduct
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $products = json_decode(json_encode($request->products, true));
        // check empty card
        if(empty($products)) {
            return response()->json([
                'is_success' => false,
                'message' => 'Cart must not be empty, please buy something',
            ], 400);
        }
        // increase count for each requested product
        try {
            foreach($products as $product) {
                Product::find($product->id)->increment('count', $product->quantity);
            }
        }
        catch(Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => $e->getMessage(),
            ]);
        }
        return $next($request);
    }
}
