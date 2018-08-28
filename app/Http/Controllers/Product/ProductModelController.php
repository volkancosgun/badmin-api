<?php

namespace App\Http\Controllers\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\Product\ProductModelRequest;
use App\ProductModel;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class ProductModelController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $models = ProductModel::where('status', '!=', -1)->get();

        return response()->json($models, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
    public function store(ProductModelRequest $req)
    {
        $m = $req->id ? ProductModel::find($req->id) : new ProductModel();

        $m->brand_id = $req->brand_id;
        $m->name = $req->name;
        $m->user_id = auth()->user()->id;
        $m->status = $req->status;

        if ($req->id) {
            $m->update();
        } else {
            $m->save();
        }

        return response()->json($m, Response::HTTP_CREATED, array(), JSON_PRETTY_PRINT);

    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $model = ProductModel::find($id);
        
        return response()->json($model, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    public function showBrand($id)
    {
        $models = ProductModel::where('brand_id', $id)->where('status', '!=', -1)->get();

        return response()->json($models, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
