<?php

namespace App\Http\Controllers\Setting;

use App\Customer;
use App\Http\Controllers\Controller;
use App\Setting;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SettingController extends Controller
{

    protected $_sevdeskApiUrl = 'https://my.sevdesk.de/api/v1/';

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
        //
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show()
    {
        $setting = Setting::find(1);

        return response()->json($setting, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
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

    public function sevdesk()
    {

        $apiKey = $_GET['token'] ?: $this->_sevdeskApiKey;

        $this->_sevdeskGetUserInfo($apiKey);

        /* if($this->_sevDeskStatus($apiKey)) {
    echo 'Api bağlantısı sağlandı';
    }else{
    echo "Api bağlantısı başarısız";
    } */
    }

    public function sevdeskSetup(Request $request)
    {

        $res = array();
        $setting = Setting::find(1) ?: new Setting();

        if (!$this->_sevdeskStatus($request->key)) {
            $res['status'] = false;
            return response()->json($res, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        $setting->sevdesk_status = true;
        $setting->sevdesk_apikey = $request->key;

        $userInfo = $this->_sevdeskGetUserInfo($request->key);

        $setting->sevdesk_userid = $userInfo[0]->id;
        $setting->sevdesk_fullname = $userInfo[0]->fullname;
        $setting->sevdesk_email = $userInfo[0]->email;

        $setting->save();

        return response()->json($setting, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    public function sevdeskReset()
    {
        $setting = Setting::find(1) ?: new Setting();

        $setting->sevdesk_status = false;
        $setting->sevdesk_apikey = null;
        $setting->sevdesk_userid = null;
        $setting->sevdesk_fullname = null;
        $setting->sevdesk_email = null;

        $setting->save();

        return response()->json($setting, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    public function sevdeskTransfer($id)
    {

        sleep(1);
        $customer = Customer::find($id);

        if (!count($customer)) {
            return response()->json(['error' => true, 'msg' => 'müşteri bulunamadı.'], Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        $isCustomer = $this->_sevdeskGetContact($customer->sevdesk_contact_id);

        if($isCustomer['status'] == 200) {
            return response()->json(['error' => true, 'msg' => 'zaten sevDesk veritabanında kayıtlı.'], Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        if($isCustomer['status'] == 401) {
            return response()->json(['error' => true, 'msg' => 'API bağlantı sorunu.'], Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

        $addCustomer = $this->_sevdeskAddContact($customer);


        $addCustomerId = $addCustomer['response']->objects->id;

        if($addCustomerId) {
            $customer->sevdesk_contact_id = $addCustomerId;
            $customer->update();
            return response()->json($customer, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
        }

    }

    private function _sevdeskGetContact($id)
    {
        $apiKey = $this->_setting()->sevdesk_apikey;
        return $this->_simpleCurl('Contact/'.$id, $apiKey);
    }

    private function _sevdeskAddContact($customer)
    {

        if ($customer->gender == 'male') {
            $gender = 'm';
        } else {
            $gender = 'w';
        }

        $fields = array(
            'customerNumber' => $customer->customer_number,
            'familyname' => $customer->sur_name,
            'surename' => $customer->name,
            'gender' => $gender,
            'category[id]' => 3,
            'category[objectName]' => 'Category',
        );

        $apiKey = $this->_setting()->sevdesk_apikey;
        return $this->_postCurl('Contact', $apiKey, $fields);
    }

    private function _sevdeskGetUserInfo($apiKey)
    {
        $res = $this->_simpleCurl('SevUser', $apiKey);
        return $res['response']->objects;
    }

    private function _sevdeskCTransfer()
    {
        // Müşterileri sevdesk tarafına gönder.
    }

    /**
     * Sevdesk API durum kontrolü
     * Response: true/false döndürür
     * @param string $apiKey
     */
    private function _sevDeskStatus($apiKey)
    {
        $res = $this->_simpleCurl('SevUser', $apiKey);

        if ($res['status'] != 200) {
            return false;
        }

        return true;
    }

    private function _simpleCurl($_model, $_apiKey)
    {

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->_sevdeskApiUrl . '' . $_model);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $_apiKey,
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $res = array();

        $response = json_decode(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res['status'] = $httpcode;
        $res['response'] = $response;

        return $res;
    }

    private function _postCurl($_model, $_apiKey, $_fields)
    {
        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->_sevdeskApiUrl . '' . $_model);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($_fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $_apiKey,
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $res = array();

        $response = json_decode(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res['status'] = $httpcode;
        $res['response'] = $response;

        return $res;
    }

    private function _setting()
    {
        $s = Setting::find(1);

        if(!count($s)) {
            return false;
        }

        return $s;

    }

}
