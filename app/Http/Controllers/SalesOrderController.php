<?php

namespace App\Http\Controllers;

use App\Models\SalesOrder;
use App\Models\SalesOrderDetail;
use Illuminate\Database\Eloquent\Casts\Json;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class SalesOrderController extends Controller
{
    public function List(): JsonResponse
    {
        $dataSales = SalesOrder::orderBy('created_at', 'desc')->with('products')->get();

        $result = new ResponseApi;
        $result->statusCode(Response::HTTP_OK);
        $result->title('List Sales Order');
        $result->data($dataSales);
        return $result;
    }

    public function Create(Request $request): JsonResponse
    {

        $result = new ResponseApi;

        $validator = Validator::make(request()->all(), [
            'total_price' => 'required|numeric',
            'total_items' => 'required|integer',
            'product' => 'required|array',
            'product.*.product_id' => 'required|integer',
            'product.*.quantity' => 'required|integer',
            'product.*.price' => 'required|numeric'
        ]);

        if ($validator->fails()) {
            $result->statusCode(Response::HTTP_UNPROCESSABLE_ENTITY);
            $result->title('Transaction Failed');
            $result->message('Validation error');
            $result->formError($validator->errors());
            return $result;
        }

        $sales = new SalesOrder();
        $sales->invoice_number = $this->generateInvoiceNumber();
        $sales->total_price = $request->total_price;
        $sales->total_items = $request->total_items;
        $sales->created_by = auth()->user()->id;
        $sales->updated_by = auth()->user()->id;

        $sales->save();

        foreach ($request->product as $product) {
            $salesDetail = new SalesOrderDetail();
            $salesDetail->sales_order_id = $sales->id;
            $salesDetail->product_id = $product['product_id'];
            $salesDetail->quantity = $product['quantity'];
            $salesDetail->price = $product['price'];
            $salesDetail->save();
        }
        $result->statusCode(Response::HTTP_CREATED);
        $result->message('Created');
        $result->title('Transaction Successful');
        $result->data($sales);
        return $result;
    }

    private function generateInvoiceNumber(): string
    {
        $lastInvoice = SalesOrder::latest()->first();
        $lastInvoiceNumber = $lastInvoice ? intval(substr($lastInvoice->invoice_number, 4)) : 0;
        $newInvoiceNumber = $lastInvoiceNumber + 1;
        return 'TRX-' . str_pad($newInvoiceNumber, 5, '0', STR_PAD_LEFT);
    }
}
