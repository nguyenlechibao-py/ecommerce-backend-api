<?php

namespace App\Http\Middleware;

use App\Product;
use Closure;

class ViewProduct
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
        $product_id = $request->route('id');
        $product = Product::find($product_id);
        if(!$product) {
            return response()->json([
                'is_sucsess' => false,
                'message' => 'Product not found',
            ], 404);
        }
        $product->increment('view');
        return $next($request);
    }
}
