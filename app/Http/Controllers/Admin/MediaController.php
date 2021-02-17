<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Media;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
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
            return response()->json([
                'is_success' => false,
                'message' => 'Validator fails',
                'errors' => $validator->errors()
            ], 401);
        }

        if ($request->file('media')) {
            // store media into /storage/uploads folder
            $mediaPath = $request->file('media');
            $name = $mediaPath->getClientOriginalName();

            /**
             * Store to storage
             * And get url with relative path
             * 
             * @return string path/to/image
             */
            $path = $request->file('media')->store('/uploads', 'public');

            $media = new Media;
            $media->name = $name;
            $media->url = "/storage/" . $path;

            try {
                $media->save();

                return response()->json([
                    'is_success' => true,
                    'message' => 'uploading media successfully',
                    'media' => $media
                ]);
            } catch (Exception $e) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'There is a wrong when uploading your images',
                    'errors' => $e->getMessage(),
                ]);
            }
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        //
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
        //
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    /**
     * Rules for validates
     * 
     * @return array
     */
    public function rules()
    {
        return [
            'media' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
