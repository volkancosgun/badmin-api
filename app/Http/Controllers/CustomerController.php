<?php

namespace App\Http\Controllers;

use App\Customer;
use App\CustomerLocation;
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
        $c->business_name = $request->business_name;
        $c->business_manager = $request->business_manager;
        $c->name = $request->name;
        $c->sur_name = $request->sur_name;
        $c->description = $request->description;
        $c->email = $request->email;
        $c->phone = $request->phone;
        $c->phone_lang = $request->phone_lang;
        $c->phone_mobil = $request->phone_mobil;
        $c->phone_mobil_lang = $request->phone_mobil_lang;
        $c->fax = $request->fax;
        $c->fax_lang = $request->fax_lang;
        $c->tax = $request->tax;
        $c->tax_number = $request->tax_number;
        $c->iban = $request->iban;
        $c->bic = $request->bic;
        $c->sepa = $request->sepa;
        $c->status = 1;

        // Müşteri kaydet
        $c->save();

        $insert_id = $c->id;

        // Müşteri adres kaydet
        $this->_locationAdd($insert_id, $request);

        return response()->json(['status' => $c->save(), 'pid' => $insert_id], Response::HTTP_CREATED);

    }

    public function storeLocation(Request $request, $id)
    {
        $response = $this->_locationAdd($id, $request);

            return response()->json(['status' => $response, 'pid' => $id], Response::HTTP_CREATED);
        
    }

    private function _locationAdd($customer_id, $data)
    {
        $cl = new CustomerLocation;

        $cl->customer_id = $customer_id;
        $cl->location_type = $data->l_type;
        $cl->description = $data->l_description;
        $cl->address = $data->l_address;
        $cl->city = $data->l_locality;
        $cl->country = $data->l_country;
        $cl->lat = $data->l_lat;
        $cl->lng = $data->l_lng;
        $cl->locality = $data->l_city;
        $cl->place_id = $data->l_place_id;
        $cl->postal_code = $data->l_postal_code;
        $cl->route = $data->l_route;
        $cl->street_number = $data->l_street_number;
        $cl->status = 1;
        return $cl->save();
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

        $customer_locations = CustomerLocation::where(['customer_id' => $id, 'status' => 1])->get();

        $customer['locations'] = $customer_locations;
        if ($customer) {
            return response()->json($customer, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        return response()->json(['error' => true], Response::HTTP_NOT_FOUND);
    }

    public function showLocation($id)
    {
        $cl = CustomerLocation::find($id);
        if($cl) {
            return response()->json($cl, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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
        $c->business_name = $request->business_name;
        $c->business_manager = $request->business_manager;
        $c->name = $request->name;
        $c->sur_name = $request->sur_name;
        $c->description = $request->description;
        $c->email = $request->email;
        $c->phone = $request->phone;
        $c->phone_lang = $request->phone_lang;
        $c->phone_mobil = $request->phone_mobil;
        $c->phone_mobil_lang = $request->phone_mobil_lang;
        $c->fax = $request->fax;
        $c->fax_lang = $request->fax_lang;
        $c->tax = $request->tax;
        $c->tax_number = $request->tax_number;
        $c->iban = $request->iban;
        $c->bic = $request->bic;
        $c->sepa = $request->sepa;
        $c->save();
        return response()->json($c->save(), Response::HTTP_OK);
    }

    public function updateLocation(Request $request, $id)
    {
        $cl = CustomerLocation::find($id);

        $cl->location_type = $request->l_type;
        $cl->description = $request->l_description;
        $cl->address = $request->l_address;
        $cl->city = $request->l_locality;
        $cl->country = $request->l_country;
        $cl->lat = $request->l_lat;
        $cl->lng = $request->l_lng;
        $cl->locality = $request->l_city;
        $cl->place_id = $request->l_place_id;
        $cl->postal_code = $request->l_postal_code;
        $cl->route = $request->l_route;
        $cl->street_number = $request->l_street_number;
        $cl->save();
        return response()->json($cl->save(), Response::HTTP_OK);
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

    public function destroyLocation($id)
    {
        $cl = CustomerLocation::find($id);

        $response = $cl->update(['status' => -1]);
        return response()->json(['update_id' => $id, 'customer_id' => $cl->customer_id], Response::HTTP_OK);
    }
}
