<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests\CustomerRequest;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CustomerController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $customers = Customer::where('customers.status', 1)
            ->select(
                'customers.*',
                'users.name as userName',
                'users.email as userMail',
                'customer_groups.name as groupName',
                'customer_groups.status as groupStatus'
            )
            ->join('users', 'users.id', '=', 'customers.user_id')
            ->join('customer_groups', 'customer_groups.id', '=', 'customers.group_id')
            ->orderBy('customers.id', 'desc')
            ->get();

        return response()->json($customers, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
    public function store(CustomerRequest $request)
    {
        $c = new Customer;
        $c->user_id = auth()->user()->id;
        $c->group_id = $request->group_id;
        $c->customer_number = $this->getNextCustomerNumber();
        $c->name = $request->name;
        $c->sur_name = $request->sur_name;
        $c->description = $request->description;
        $c->email = $request->email;
        $c->phone = $request->phone;
        $c->phone_mobil = $request->phone_mobil;
        $c->fax = $request->fax;
        $c->adr_address = $request->adr_address;
        $c->adr_city = $request->adr_city;
        $c->adr_country = $request->adr_country;
        $c->adr_lat = $request->adr_lat;
        $c->adr_lng = $request->adr_lng;
        $c->adr_locality = $request->adr_locality;
        $c->adr_place_id = $request->adr_place_id;
        $c->adr_postal_code = $request->adr_postal_code;
        $c->adr_route = $request->adr_route;
        $c->adr_street_number = $request->adr_street_number;
        $c->status = 1;
        $c->save();

        return response()->json($c->save(), Response::HTTP_CREATED);

    }

    private function getNextCustomerNumber()
    {
        $lastCustomer = Customer::orderBy('created_at', 'desc')->first();

        if (!$lastCustomer) {
            $number = 100;
        } else {
            $number = $lastCustomer->customer_number;
        }

        return sprintf('%06d', intval($number) + 1);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        /* $customer = Customer::find($id); */
        $customer = Customer::where('customers.id', $id)
            ->select(
                'customers.*',
                'users.name as userName',
                'users.email as userMail',
                'customer_groups.name as groupName',
                'customer_groups.status as groupStatus'
            )
            ->join('users', 'users.id', '=', 'customers.user_id')
            ->join('customer_groups', 'customer_groups.id', '=', 'customers.group_id')
            ->first();
        if ($customer) {
            return response()->json($customer, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        return response()->json(['error' => true], Response::HTTP_NOT_FOUND);
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
    public function update(CustomerRequest $request, $id)
    {
        $c = Customer::find($id);
        $c->user_id = auth()->user()->id;
        $c->group_id = $request->group_id;
        $c->name = $request->name;
        $c->sur_name = $request->sur_name;
        $c->description = $request->description;
        $c->email = $request->email;
        $c->phone = $request->phone;
        $c->phone_mobil = $request->phone_mobil;
        $c->fax = $request->fax;
        $c->adr_address = $request->adr_address;
        $c->adr_city = $request->adr_city;
        $c->adr_country = $request->adr_country;
        $c->adr_lat = $request->adr_lat;
        $c->adr_lng = $request->adr_lng;
        $c->adr_locality = $request->adr_locality;
        $c->adr_place_id = $request->adr_place_id;
        $c->adr_postal_code = $request->adr_postal_code;
        $c->adr_route = $request->adr_route;
        $c->adr_street_number = $request->adr_street_number;

        $c->save();
        return response()->json($c->save(), Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = Customer::find($id)->update(['status' => -1]);
        return response()->json(['update_id' => $id], Response::HTTP_OK);
    }
}
