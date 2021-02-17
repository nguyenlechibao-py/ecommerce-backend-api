<?php

namespace App\Http\Controllers;

use App\Http\Resources\CategoryResource;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new CategoryResource(Category::all());
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
        $category = Category::create([
            'name' => $request->name,
            'description' => $request->description ?? "",
            'image' => $request->image ?? "",
        ]);
        return response()->json(['is_success' => true, 'message' => 'Category has been created', 'data' => $category]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Category::find($id);
        if (!$category)
            return response()->json(['is_success' => false, 'message' => 'Category doesn\'t exist'], 404);
        return new CategoryResource($category);
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
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['is_success' => false, 'message' => 'Category doesn\'t exist'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'unique:categories|max:255',
            'description' => 'max:255',
            'image' => 'max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['is_success' => false, 'message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        $category->update($request->all());
        return response()->json(['is_success' => true, 'message' => 'Category has been updated', 'data' => $category], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $category = Category::find($id);
        if (!$category) {
            return response()->json(['is_success' => false, 'message' => 'Category doesn\'t exist'], 404);
        }
        $category->delete();
        return response()->json(['is_success' => true, 'message' => 'Category has been deleted'], 200);
    }

    /**
     * Rules for category fields 
     * 
     * @return array;
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:categories|max:255',
            'description' => 'max:255',
            'image' => 'max:255',
        ];
    }
}
