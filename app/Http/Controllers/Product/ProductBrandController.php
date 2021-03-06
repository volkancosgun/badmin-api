<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductBrandRequest;
use App\ProductBrand;
use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Symfony\Component\HttpFoundation\Response;

class ProductBrandController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $brands = ProductBrand::where('status', '!=', -1)->get();

        return response()->json($brands, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
    public function store(ProductBrandRequest $req)
    {
        $b = $req->id ? ProductBrand::find($req->id) : new ProductBrand();

        $b->name = $req->name;
        $b->logo = $req->logo ? $this->_logoStore($req->logo) : $this->_logoRestore($req);
        $b->status = $req->status;


        if ($req->id) {
            $b->update();
        } else {
            $b->save();
        }

        return response()->json($b, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);

    }

    private function _logoRestore($req) {
        if($req->id) {
            $brand = ProductBrand::find($req->id);
            return $brand->logo;
        }

        return null;
    }

    private function _logoStore($file)
    {

        if(!$file) {
            return false;
        }

        $img = Image::make($file)->encode('jpg');
        $img->resize(50,50);
        $hash = md5($img->__toString());
        $path = "uploads/brands/{$hash}.jpg";
        $img->save(public_path($path));
        $img_url = '/' . $path;
        return $img_url;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $brand = ProductBrand::find($id);

        return response()->json($brand, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
}
