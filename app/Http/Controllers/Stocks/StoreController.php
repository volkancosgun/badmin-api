<?php

namespace App\Http\Controllers\Stocks;

use App\Store;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Requests\Stocks\StoreRequest;
use Symfony\Component\HttpFoundation\Response;

class StoreController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $store = Store::where('status', 1)->get();
        
        return $this->_scc($store);
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
    public function store(StoreRequest $request)
    {

        $store = $request->id ? $request->id : new Store();

        $store->user_id = auth()->user()->id;
        $store->name = $request->name;
        $store->limit = $request->limit;

        if ($request->id) {
            $store->update();
        } else {
            $store->save();
        }

        return $this->_scc($store);

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

    private function _scc($data)
    {
        return response()->json($data, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    private function _err($msg)
    {
        return response()->json(['error' => true, 'msg' => $msg], Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }
}
