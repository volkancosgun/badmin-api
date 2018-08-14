<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Support\Facades\Auth;
use App\CustomerGroup;
use Illuminate\Http\Request;
use App\Http\Requests\CustomerGroupRequest;
use Symfony\Component\HttpFoundation\Response;

class CustomerGroupController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        //$groups = CustomerGroup::where('status', 1)->orderBy('id', 'desc')->get();
        $groups = CustomerGroup::where('status', '!=', -1)->orderBy('id', 'desc')->get();
        return response()->json($groups, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        //return datatables()->of(CustomerGroup::all())->toJson();
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
    public function store(CustomerGroupRequest $request)
    {

        $group = new CustomerGroup;
        $group->user_id = auth()->user()->id;
        $group->name = $request->name;
        $group->description = $request->description;
        $group->status = $request->status;
        $group->save();
        return response()->json($group, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $group = CustomerGroup::find($id);
        if($group) {
            return response()->json($group, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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

    public function update_status(Request $request, $id)
    {
        return response()->json($request, Response::HTTP_OK);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(CustomerGroupRequest $request, $id)
    {
        $group = CustomerGroup::find($id);
        $group->name = $request->name;
        $group->description = $request->description;
        $group->status = $request->status;

        $group->update();
        return response()->json($group, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        $response = CustomerGroup::find($id)->update(['status' => -1]);
        return response()->json(['update_id' => $id], Response::HTTP_OK);
    }

}
