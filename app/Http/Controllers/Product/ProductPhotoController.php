<?php

namespace App\Http\Controllers\Product;

use App\ProductPhoto;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class ProductPhotoController extends Controller
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

        if($request->hasFile('file')) {
            $file = $request->file('file');
            $img = Image::make($file)->encode('jpg');
            $hash = md5($img->__toString());
            $path = "uploads/{$hash}.jpg";
            $img->save(public_path($path));
            $img_url = '/'. $path;

            // thumbnail
            /* $t_img = Image::make($file)->resize(150,150)->encode('jpg');
            $t_path = "uploads/{$hash}-thumbnail.jpg";
            $t_img->save(public_path($t_path));
            $t_path = '/'. $t_path; */

            $pp = new ProductPhoto();
            $pp->product_id = $request->id;
            $pp->photo = "{$hash}.jpg";
            $pp->save();

            return [
                'url' => $img_url 
            ];
        }

        return false;
    }

    private function thumbnailImage($file) {
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $photos = ProductPhoto::where('product_id', $id)->get();

        return response()->json($photos, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
        
        $pp = ProductPhoto::findOrFail($id);
        $file = $pp->photo;
        $filename = public_path().'/uploads/'.$file;
        \File::delete($filename);
        $pp->delete();

        return response()->json($pp, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);

    }
}
