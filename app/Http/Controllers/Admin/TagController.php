<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use App\Tag;
use Illuminate\Support\Facades\Validator;
use Exception;
use App\Http\Resources\TagResource;


class TagController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        return new TagResource(Tag::all());
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
        $tag = Tag::create([
            'name' => $request->name,
        ]);
        return response()->json(['is_success' => true, 'message' => 'Tag has been created', 'data' => $tag]);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $tag = Tag::find($id);
        if (!$tag)
            return response()->json(['is_success' => false, 'message' => 'Tag doesn\'t exist'], 404);
        return new TagResource($tag);
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
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['is_success' => false, 'message' => 'Tag doesn\'t exist'], 404);
        }
        $validator = Validator::make($request->all(), [
            'name' => 'unique:tags|max:255',
        ]);
        if ($validator->fails()) {
            return response()->json(['is_success' => false, 'message' => $validator->messages()], Response::HTTP_BAD_REQUEST);
        }
        $tag->update($request->all());
        return response()->json(['is_success' => true, 'message' => 'Tag has been updated', 'data' => $tag], 200);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $tag = Tag::find($id);
        if (!$tag) {
            return response()->json(['is_success' => false, 'message' => 'Tag doesn\'t exist'], 404);
        }
        $tag->delete();
        return response()->json(['is_success' => true, 'message' => 'Tag has been deleted'], 200);
    }

    /**
     * Rules for tag fields 
     * 
     * @return array;
     */
    public function rules()
    {
        return [
            'name' => 'required|unique:tags|max:255',
        ];
    }
}
