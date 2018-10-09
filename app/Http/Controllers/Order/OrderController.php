<?php

namespace App\Http\Controllers\Order;

use App\Order;
use App\OrderItem;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\Controller;
use App\Http\Requests\Order\OrderRequest;
use Symfony\Component\HttpFoundation\Response;

class OrderController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        //$orders = Order::where('status', '!=', -1)->get();
        $orders = Order::select('orders.*','customers.business_name as business_name')
        ->join('customers', 'customers.id', '=', 'orders.customer_id')
        ->where('orders.status','!=', -1)->get();

        return response()->json($orders, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
    public function store(OrderRequest $request)
    {
        //return response()->json($request, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);

        $o = $request->id ? Order::find($request->id) : new Order();

        $o->user_id = auth()->user()->id;
        $o->customer_id = $request->customer_id;
        $o->location_id = $request->location_id;
        $o->bill_address = $request->bill_address;
        $o->bill_number = $request->bill_number;
        $o->order_number = $request->id ? $this->getOrderNumber($request->id) : $this->newOrderNumber();
        $o->price = $request->price;
        $o->tax_price = $request->tax_price;
        $o->total_price = $request->total_price;
        $o->status = $request->status;

        $total_items = count($request->items);

        if ($request->id) {
            $o->update();
            if ($total_items > 0) {
                //$this->_updateOrderItems($request->items, $o->id);
            }
        } else {
            $o->save();
            if ($total_items > 0) {
                $this->_addOrderItems($request->items, $o->id);
            }
        }

        return response()->json($o, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    private function _addOrderItems($items = array(), $orderId)
    {
        if (!$orderId) {
            return false;
        }

        foreach ($items as $key => $row) {

            if ($row['product_id']) {
                $oi = new OrderItem();
                $oi->order_id = $orderId;
                $oi->product_id = $row['product_id'];
                $oi->name = 'Yok';
                $oi->amount = $row['amount'];
                $oi->unit = $row['unit'];
                $oi->price = $row['price'];
                $oi->tax = $row['tax'] || NULL;
                $oi->total_price = $row['total_price'];
                $oi->status = 1;

                $oi->save();
            }

        }

        return true;
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $o = Order::find($id);

        return response()->json($o, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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

    private function getOrderNumber($id)
    {
        $p = Order::find($id);

        return $p->order_number;
    }

    private function newOrderNumber()
    {

        $lastData = Order::orderBy('created_at', 'desc')->first();

        if (!$lastData) {
            $number = 100;
        } else {
            $number = $lastData->order_number;
        }

        return sprintf('%06d', intval($number) + 1);

    }
}
