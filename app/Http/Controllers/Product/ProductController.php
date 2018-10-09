<?php

namespace App\Http\Controllers\Product;

use App\Product;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\Storage;
use App\Http\Requests\Product\ProductRequest;
use Symfony\Component\HttpFoundation\Response;

class ProductController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $products = Product::where('status', '!=', -1)->get();

        return response()->json($products, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
    public function store(ProductRequest $req)
    {

        $p = $req->id ? Product::find($req->id) : new Product();

        $p->user_id = auth()->user()->id;
        $p->category_id = $req->category_id;
        $p->brand_id = $req->brand_id;
        $p->model_id = $req->model_id;
        $p->tax_id = $req->tax_id;
        $p->product_number = $req->product_number ? $req->product_number : $this->newProductNumber();
        $p->code = $req->code;
        $p->name = $req->name;
        $p->_search = $req->_search;
        $p->description = $req->description;
        $p->expiration_at = $req->expiration_at;
        $p->n_weight = $req->n_weight;
        $p->g_weight = $req->g_weight;
        $p->deposit_fee = $req->deposit_fee;
        $p->purchase_price = $req->purchase_price;
        $p->carton_total = $req->carton_total;
        $p->carton_price = $req->carton_price;
        $p->carton_barcode = $req->carton_barcode;
        $p->palette_total = $req->palette_total;
        $p->palette_price = $req->palette_price;
        $p->palette_barcode = $req->palette_barcode;
        $p->container_total = $req->container_total;
        $p->container_price = $req->container_price;
        $p->container_barcode = $req->container_barcode;
        $p->price = $req->price;
        $p->status = $req->status;

        if ($req->id) {
            $p->update();
        } else {
            $p->save();
        }

        return response()->json($p, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $category = Product::find($id);

        return response()->json($category, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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

    private function newProductNumber()
    {

        $lastData = Product::orderBy('created_at', 'desc')->first();

        if (!$lastData) {
            $number = 100;
        } else {
            $number = $lastData->product_number;
        }

        return sprintf('%06d', intval($number) + 1);

    }
}
