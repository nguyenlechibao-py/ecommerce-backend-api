<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Media;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\Response;

class MediaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $paginate = $request->query('paginate');
        if(empty($paginate))
            $paginate = 10;
        $media = Media::paginate($paginate);
        return response()->json([
            'is_success' => true,
            'data' => $media,
        ]);
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
        $media = Media::find($id);
        if(!$media) {
            return response()->json([
                'is_success' => false,
                'message' => 'Media doesn\'t exist',
            ], 404);
        }
        return response()->json([
            'is_success' => true,
            'data' => $media,
        ], 200);
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
        $media = Media::find($id);
        if(!$media) {
            return response()->json([
                'is_success' => false,
                'message' => 'Media doesn\'t exist',
            ], 404);
        }
        $validator = Validator::make($request->all(), ['media' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048']);
        if ($validator->fails()) {
            return response()->json([
                'is_success' => false,
                'message' => 'Validator fails',
                'errors' => $validator->errors()
            ], 401);
        }
        // delete old image
        $fileUrl = $media->url;
        $file = str_replace('/storage', '', $fileUrl);
        Storage::disk('public')->delete($file);
        // check media and update
        if($request->file('media')) {
            $mediaFile = $request->file('media');
            $name = $mediaFile->getClientOriginalName();
            /**
             * Store to storage
             * And get url with relative path
             * 
             * @return string path/to/image
             */
            $path = $request->file('media')->store('/uploads', 'public');
            try {
                $media->update([
                    'name' => $name,
                    'url' => "/storage/" . $path,
                ]);
                return response()->json([
                    'is_success' => true,
                    'message' => 'Media has been updated successfully',
                    'media' => $media,
                ], 200);
            }
            catch(Exception $e) {
                return response()->json([
                    'is_success' => false,
                    'message' => 'Something went wrong when uploading your images, try again later',
                    'errors' => $e->getMessage(),
                ], 500);
            }
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $media = Media::find($id);
        if(!$media) {
            return response()->json([
                'is_success' => false,
                'message' => 'Media doesn\'t exist',
            ], 404);
        }
        $filePath = $media->url;
        // delete in database
        $media->delete();
        // delete in uploads folder
        try {
            $fileUrl = $media->url;
            $file = str_replace('/storage', '', $fileUrl);
            $storage = Storage::disk('public')->delete($file);
            if($storage) {
                return response()->json([
                    'is_success' => true,
                    'message' => 'Media has been deleted.',
                ], 200);
            }
        }
        catch(Exception $e) {
            return response()->json([
                'is_success' => false,
                'message' => 'Something went wrong when deleting your images, try again later',
                'errors' => $e->getMessage(),
            ], 500);
        }
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