<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\RefundDetail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Validator;

class RefundController extends Controller
{
    public function List(): JsonResponse
    {
        $dataSales = Refund::orderBy('created_at', 'desc')->with('products')->get();

        $result = new ResponseApi;
        $result->statusCode(Response::HTTP_OK);
        $result->title('List Refund');
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

        $refund = new Refund();
        $refund->refund_number = $this->generateRefundNumber();
        $refund->total_price = $request->total_price;
        $refund->total_items = $request->total_items;
        $refund->created_by = auth()->user()->id;
        $refund->updated_by = auth()->user()->id;

        $refund->save();

        foreach ($request->product as $product) {
            $salesDetail = new RefundDetail();
            $salesDetail->refund_id = $refund->id;
            $salesDetail->product_id = $product['product_id'];
            $salesDetail->quantity = $product['quantity'];
            $salesDetail->price = $product['price'];
            $salesDetail->save();
        }
        $result->statusCode(Response::HTTP_CREATED);
        $result->message('Created');
        $result->title('Transaction Successful');
        $result->data($refund);
        return $result;
    }

    private function generateRefundNumber(): string
    {
        $lastInvoice = Refund::latest()->first();
        $lastInvoiceNumber = $lastInvoice ? intval(substr($lastInvoice->refund_number, 4)) : 0;
        $newInvoiceNumber = $lastInvoiceNumber + 1;
        return 'TRX-' . str_pad($newInvoiceNumber, 5, '0', STR_PAD_LEFT);
    }
}
