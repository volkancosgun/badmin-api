<?php

namespace App\Http\Controllers\Order;

use App\User;
use DateTime;
use App\Order;
use App\Product;
use App\Setting;
use App\Customer;
use App\OrderItem;
use App\CustomerLocation;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Intervention\Image\Facades\Image;
use App\Http\Requests\Order\OrderRequest;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\File\UploadedFile;

class OrderController extends Controller
{
    protected $SD_APIURL = 'https://my.sevdesk.de/api/v1/';

    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $orders = Order::select('orders.*',
            'customers.business_name as business_name',
            'customers.name as customer_name'
        )->join('customers', 'customers.id', '=', 'orders.customer_id')
            ->where('orders.status', '!=', -1)->get();

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
        $o->delivered_date = $request->delivered_date;
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

            /* Fatura Oluştur
        $setting = $this->getSettings();

        if($setting->sevdesk_status) {
        $this->newInvoice($o->id);
        }
         **/

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
                $oi->tax = $row['tax'];
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

        $o['user'] = User::find($o->user_id);
        $o['customer'] = Customer::find($o->customer_id);
        $o['location'] = CustomerLocation::find($o->location_id);
        $o['items'] = OrderItem::select('order_items.*',
            'products.name as product_name'
        )->join('products', 'products.id', '=', 'order_items.product_id')
            ->where('order_items.order_id', $o->id)->get();

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

    private function _errResponse($msg)
    {
        return response()->json(['error' => true, 'msg' => $msg], Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    private function _errSevdeskApi()
    {
        return response()->json(['error' => true, 'msg' => 'Api bağlantı hatası.']);
    }

    private function _sccResponse($res)
    {
        return response()->json($res, Response::HTTP_OK, array(), JSON_PRETTY_PRINT);
    }

    public function newReceipt($orderId)
    {
        // Data
        $data = array();

        // Sipariş
        $order = Order::find($orderId);

        // Eğer sipariş yoksa
        if (!count($order)) {
            return $this->_errResponse('Sipariş bulunamadı.');
        }

        // Data Sipariş
        $data['order'] = $order;

        // Müşteri
        $customer = Customer::find($order->customer_id);

        // Eğer müşteri yoksa
        if (!count($customer)) {
            return $this->_errResponse('Müşteri bulunamadı.');
        }

        // Data müşteri
        $data['customer'] = $customer;

        // sd Müşteri Talebi
        $contact = $this->sd_runModel('Contact/' . $customer->sevdesk_contact_id);

        // sd Müşteri yoksa
        if ($contact['httpCode'] == 400) {

            $addCustomer = $this->sd_addContact($customer);

            if ($addCustomer['httpCode'] == 400) {
                $this->_errResponse('sevDesk tarafına müşteri aktarılamadı.');
            }

            if ($addCustomer['httpCode'] == 401) {
                $this->_errSevdeskApi();
            }

            $addCustomerId = $addCustomer['response']->objects->id;
            if ($addCustomerId) {
                $customer->sevdesk_contact_id = $addCustomerId;
                $customer->update();
            }
        }

        if ($contact['httpCode'] == 401) {
            return $this->_errSevdeskApi();
        }

        return false;
    }

    public function saveInvoice($invoiceId)
    {

        // Sipariş
        $order = Order::where('sevdesk_order_id', $invoiceId)->first();

        // Sipariş var mı
        if (!count($order)) {
            return $this->_errResponse('Sipariş bulunamadı.');
        }

        $response = $this->sd_runModelPost('Invoice/' . $invoiceId . '/render');

        if ($response['httpCode'] == '400') {
            return $this->_errResponse('Fatura dosyası oluşturulamadı.');
        }

        if ($response['httpCode'] == '401') {
            return $this->_errSevdeskApi();
        }

        // API yanıtı
        $res = $response['response']->objects;

        // Döküman IDsi
        $docId = $res->docId;

        // BASE64 Thumbs Kodu
        $docBase64 = $res->thumbs[0];

        // Pdf önizleme linki
        $docPdf = $res->pdfUrl;

        // JPG Formatında Fatura Oluştur
        $img = Image::make($docBase64)->encode('jpg');
        $path = "uploads/invoices/{$docId}.jpg";
        $img->save(public_path($path));
        $img_url = '/' . $path;

        // Fatura JPG Dosya Yolu
        $docPath = $img_url;

        // Order tablosuna veriler işle
        $order->sevdesk_doc_id = $docId;
        $order->sevdesk_doc_pdf = $docPdf;
        $order->update();

        // PDF Kaydet
        $info = pathinfo($docPdf);
        $contents = file_get_contents($docPdf);
        $file = 'uploads/invoices/' . $info['basename'].'.pdf';
        file_put_contents($file, $contents);
        $uploaded_file = new UploadedFile($file, $info['basename']);

        // Sonuç
        return $this->_sccResponse($order);

    }

    public function newInvoice($orderId)
    {

        // Ayarları kontrol et
        $setting = Setting::find(1);

        // Eğer ayarlar yoksa
        if (!count($setting)) {
            return $this->_errResponse('Api ayarları yapılmamış');
        }

        // sd API KEY
        $api_key = $setting->sevdesk_apikey;

        // sd USER ID
        $api_userId = $setting->sevdesk_userid;

        // sd USER MAIL
        $api_email = $setting->sevdesk_email;

        // API Durumu
        $api_status = $setting->sevdesk_status;

        // Eğer API Deaktif edilmişse
        if (!$api_status) {
            return $this->_errResponse('sevDesk API pasif durumda');
        }

        // Sipariş
        $order = Order::find($orderId);

        // Eğer sipariş yoksa
        if (!count($order)) {
            return $this->_errResponse('Sipariş bulunamadı.');
        }

        // Müşteri
        $customer = Customer::find($order->customer_id);

        // Eğer müşteri yoksa
        if (!count($customer)) {
            return $this->_errResponse('Müşteri bulunamadı.');
        }

        // sd Musteri Talebi
        $contact = $this->sd_runModel('Contact/' . $customer->sevdesk_contact_id);

        // sd Müşteri yoksa
        if ($contact['httpCode'] == 400) {

            $addCustomer = $this->sd_addContact($customer);

            if ($addCustomer['httpCode'] == 400) {
                $this->_errResponse('sevDesk tarafına müşteri aktarılamadı.');
            }

            if ($addCustomer['httpCode'] == 401) {
                $this->_errSevdeskApi();
            }

            $addCustomerId = $addCustomer['response']->objects->id;
            if ($addCustomerId) {
                $customer->sevdesk_contact_id = $addCustomerId;
                $customer->update();
            }
        }

        if ($contact['httpCode'] == 401) {
            return $this->_errSevdeskApi();
        }

        // Fatura Adresi
        $address = CustomerLocation::find($order->location_id);

        // Eğer fatura adresi yoksa
        if (!count($address)) {
            return $this->_errResponse('Fatura adresi bulunamadı.');
        }

        $data = array();
        $data['order'] = $order;
        $data['customer'] = $customer;
        $data['address'] = $address;

        // sd Fatura sorgula
        $invoiceFind = $this->sd_runModel('Invoice/' . $order->sevdesk_order_id);

        if ($invoiceFind['httpCode'] == 401) {
            return $this->_errSevdeskApi();
        }

        /* if($invoiceFind['httpCode'] == 400) {
        return $this->_errResponse('Fatura sorgulanamadı.');
        } */

        if ($invoiceFind['httpCode'] == 200) {
            return $this->_errResponse('Bu sipariş için zaten sevDesk faturası oluşturulmuş.');
        }

        // sd Fatura Numarası Talebi
        $nextIN = $this->sd_runModel('Invoice/Factory/getNextInvoiceNumber/?invoiceType=RE&useNextNumber=1');

        // Eğer api bağlantı hatası varsa
        if ($nextIN['httpCode'] == 401) {
            return $this->_errSevdeskApi();
        }
        // sd Fatura Numarası
        $nextInvoiceNumber = $nextIN['response']->objects;

        // Fatura Tarihi
        $invoiceDate = $order->created_at;

        // Teslimat Tarihi
        $deliveryDate = $order->delivered_date;

        // Teslimat Adresi
        $adr = $address->address;

        // Fatura verileri
        $fields = array(
            'header' => 'Invoice' . $nextInvoiceNumber,
            'invoiceNumber' => $nextInvoiceNumber,
            'invoiceType' => "RE",
            'invoiceDate' => $invoiceDate->format(DateTime::ISO8601),
            'deliveryDate' => $order->delivered_date,
            'contactPerson[id]' => $api_userId,
            'contactPerson[objectName]' => "SevUser",
            'contact[id]' => $customer->sevdesk_contact_id,
            'contact[objectName]' => 'Contact',
            'discountTime' => '0',
            'taxRate' => "0",
            'taxText' => 'Umsatzsteuer ausweisen',
            'taxType' => "default",
            'smallSettlement' => '0',
            'currency' => 'EUR',
            'discount' => '0',
            'status' => 100,
            'address' => $adr,
        );

        // Fatura oluşturma talebi
        $invoice = $this->sd_runModelPost('Invoice', $fields);

        $data['invoice'] = $invoice;

        // Eğer fatura oluşturulamadıysa
        if ($invoice['httpCode'] == 400) {
            return $this->_errResponse('Fatura oluşturulamadı.');
        }

        // Eğer Api bağlantı sağlanamadıysa
        if ($invoice['httpCode'] == 401) {
            return $this->_errSevdeskApi();
        }

        // Fatura Id
        $invoiceId = $invoice['response']->objects->id;

        // Sipariş Idsini işle
        $order->sevdesk_order_id = $invoiceId;
        $order->save();

        // Ürünler
        $orderItems = OrderItem::where('order_id', $orderId)->get();

        // Fatura ürün verileri
        $invoicePos = array();
        foreach ($orderItems as $row) {

            $product = Product::find($row->product_id);

            $fields_items = array(
                'invoice[id]' => $invoiceId,
                'invoice[objectName]' => "Invoice",
                'name' => $product->name,
                'quantity' => $row->amount,
                'price' => $row->price,
                'unity[id]' => '1', //Stk
                'unity[objectName]' => "Unity",
                'taxRate' => $row->tax,
            );

            $invoicePos[] = $this->sd_runModelPost('InvoicePos', $fields_items);
        }

        $data['invoicePos'] = $invoicePos;

        // Fatura dosyasını oluştur
        $data['doc'] = $this->saveInvoice($invoiceId);

        // Fatura Mail verileri
        $email_fields = array(
            'toEmail' => $api_email,
            'subject' => "Fatura " . $invoiceId,
            'text' => "Merhaba,<br/>yeni fatura oluşturuldu.<br/>Tpanel (sevdeskApi)",
        );

        // Fatura Mail talebi
        $sendEmail = $this->sd_runModelPost('Invoice/' . $invoiceId . '/sendViaEmail/', $email_fields);

        $data['email'] = $sendEmail;

        // Sonuç verileri
        return $this->_sccResponse($data);

    }

    private function sd_addContact($customer)
    {
        $gender = 'w';
        if ($customer->gender == 'male') {
            $gender = 'm';
        }

        $fields = array(
            'customerNumber' => $customer->customer_number,
            'familyname' => $customer->sur_name,
            'surename' => $customer->name,
            'gender' => $gender,
            'category[id]' => 3,
            'category[objectName]' => 'Category',
        );

        return $this->sd_runModelPost('Contact', $fields);
    }

    private function sd_runModelUpload($api_model, $fields = array())
    {

        $settings = $this->getSettings();

        $api_status = $settings->sevdesk_status;

        if (!$api_status) {
            return $this->_errResponse('sevDesk API aktif değil.');
        }

        $api_key = '$settings->sevdesk_apikey';

        $request = curl_init();

        curl_setopt_array($request, array(
            CURLOPT_URL, $this->SD_APIURL . '' . $api_model,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "POST",
            CURLOPT_POSTFIELDS => $field,
            CURLOPT_SAFE_UPLOAD, true,
            //CURLOPT_SSL_VERIFYPEER, false,
            CURLOPT_HTTPHEADER => array(
                "accept: application/json",
                "authorization: " . $api_key,
                "cache-control: no-cache",
                "content-type: multipart/form-data;",
            ),
        )
        );

        $response = curl_exec($request);
        //$httpResponseCode = curl_getinfo($request, CURLINFO_HTTP_CODE);
        curl_close($request);
        $json = json_decode($response);

        /* $res = array();
        $res['response'] = $json;
        $res['httpCode'] = $httpResponseCode; */

        //curl_close($request);

        return json_decode($response);
    }

    private function sd_runModel($api_model)
    {

        $settings = $this->getSettings();

        $api_status = $settings->sevdesk_status;

        if (!$api_status) {
            return $this->_errResponse('sevDesk API aktif değil.');
        }

        $api_key = $settings->sevdesk_apikey;

        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->SD_APIURL . '' . $api_model);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);

        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $api_key,
            "Content-Type: application/x-www-form-urlencoded",
        ));
        $res = array();
        $response = json_decode(curl_exec($ch));

        $httpResponseCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $res['httpCode'] = $httpResponseCode;

        $res['response'] = $response;
        curl_close($ch);

        return $res;
    }

    private function getSettings()
    {
        // Ayarları kontrol et
        $setting = Setting::find(1);

        // Eğer ayarlar yoksa
        if (!count($setting)) {
            return $this->_errResponse('Sistem ayarlarını kontrol ediniz.');
        }

        return $setting;

    }

    private function sd_runModelPost($api_model, $fields = array())
    {

        $settings = $this->getSettings();

        $api_status = $settings->sevdesk_status;

        if (!$api_status) {
            return $this->_errResponse('sevDesk API aktif değil.');
        }

        $api_key = $settings->sevdesk_apikey;

        $ch = curl_init();

        curl_setopt($ch, CURLOPT_URL, $this->SD_APIURL . '' . $api_model);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HEADER, false);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($fields));
        curl_setopt($ch, CURLOPT_HTTPHEADER, array(
            "Authorization: " . $api_key,
            "Content-Type: application/x-www-form-urlencoded",
        ));

        $res = array();

        $response = json_decode(curl_exec($ch));
        $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
        curl_close($ch);

        $res['httpCode'] = $httpcode;
        $res['response'] = $response;

        return $res;
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
