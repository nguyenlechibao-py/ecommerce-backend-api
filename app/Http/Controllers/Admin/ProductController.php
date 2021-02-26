<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Http\Resources\ProductResource;
use App\Product;
use \Exception;
use \Validator;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     * 
     * @param \Illuminate\Http\Response $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate');
        if(!isset($paginate) || empty($paginate))
            $paginate = 10;
        $products = Product::paginate($paginate);
        foreach($products as $product) {
            $product->media = Product::find($product->id)->media;
            $product->category = Product::find($product->id)->category;
            $product->tags = Product::find($product->id)->tags;
        }
        return response()->json([
            'is_success' => true,
            'data' => new ProductResource($products),
        ], 200);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['is_success' => false, 'message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        $product = Product::create([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price ?? 0,
            'media_id' => $request->media_id,
            'category_id' => $request->category_id,
            'quantity' => $request->quantity,
            'is_show' => $request->is_show,
        ]);
        // add tag
        if(!empty($request->tags)) {
            // update new changes
            $tags = json_decode($request->tags);
            if(\is_array($tags)) {
                // multi tags
                $product->tags()->sync($tags);
            }
            else
                // 1 tag
                $product->tags()->attach($tags);
        }
        // include tags in response
        $product->tags;
        // convert category_id to category
        $category = Product::find($product->id)->category;
        $product->category = $category;
        // convert media
        $media = Product::find($product->id)->media;
        $product->media = $media;
        // include tags in response
        $product->tags;
        // convert category_id to category
        $category = Product::find($product->id)->category;
        $product->category = $category;
        // convert media
        $media = Product::find($product->id)->media;
        $product->media = $media;
        return response()->json(['is_success' => true, 'message' => 'Product has been created', 'data' => $product]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json([
                'is_success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        // include tags in response
        $product->tags;
        // convert category_id to category
        $product->category = Product::find($product->id)->category;
        // convert media
        $product->media = Product::find($product->id)->media;
        return response()->json([
            'is_success' => true,
            'data' => new ProductResource($product),
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
        $product = Product::find($id);
        if(!$product) {
            return response()->json([
                'is_success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'required|max:255',
            'description' => 'max:255',
            'price' => 'max:11',
            'quantity' => 'max:11',
            'media_id' => 'max:11',
            'category_id' => 'required|max:11',
        ]);
        if ($validator->fails()) {
            return response()->json(['is_success' => false, 'message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        $product->update([
            'name' => $request->name,
            'description' => $request->description ?? '',
            'price' => $request->price ?? 0,
            'quantity' => $request->quantity ?? 0,
            'media_id' => $request->media_id,
            'category_id' => $request->category_id,
            'is_show' => $request->is_show,
        ]);
        // add tag
        if(!empty($request->tags)) {
            // detach all tags in product
            $product->tags()->detach();
            // update new changes
            $tags = json_decode($request->tags);
            if(\is_array($tags)) {
                // multi tags
                $product->tags()->sync($tags);
            }
            else
                // 1 tag
                $product->tags()->attach($tags);
        }
        // include tags in response
        $product->tags;
        // convert category_id to category
        $category = Product::find($product->id)->category;
        $product->category = $category;
        // convert media
        $media = Product::find($product->id)->media;
        $product->media = $media;
        return response()->json(['is_success' => true, 'message' => 'Product has been created', 'data' => $product]);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $product = Product::find($id);
        if(!$product) {
            return response()->json([
                'is_success' => false,
                'message' => 'Product not found',
            ], 404);
        }
        try {
            if($product->delete()) {

                return response()->json([
                    'is_success' => true,
                    'message' => 'Product has been deleted',
                ], 200);
            }
        }
        catch(Exception $e) {
            return \response()->json([
                'is_success' => false,
                'message' => 'Something went wrong when deleting product, try again later!',
            ], 500);
        }
    }

    /**
     * Rules for product fields 
     * 
     * @return array;
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:products|max:255',
            'description' => 'max:255',
            'price' => 'max:11',
            'quantity' => 'max:11',
            'media_id' => 'max:11',
            'category_id' => 'required|max:11',
        ];
    }
}