<?php

namespace App\Http\Controllers\API;

use App\Models\Status;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use App\Http\Validations\InvoiceValidation;
use App\Models\Invoice;

class InvoiceController extends BaseController
{
    /**
     * @OA\Get(
     *     path="/auth/invoices",
     *     tags={"Invoices"},
     *     summary="Get list of invoices",
     *     description="Returns list of invoices",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     * )
     */
    public function index(Request $request)
    {
        $invoices = Invoice::with(['sender', 'receiver', 'currency', 'status'])
            ->paginate(10);
        return response()->json($invoices);
    }

    /**
     * @OA\Get(
     *     path="/auth/invoices/{id}",
     *     tags={"Invoices"},
     *     summary="Get invoice information",
     *     description="Returns invoice data",
     * 	   @OA\Parameter(
     * 			name="id",
     *          description = "invoice id",
     * 			required=true,
     * 			in="path",
     * 			@OA\Schema(type="integer")
     * 	   ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     * )
     */
    public function show(Request $request, InvoiceValidation $invoiceValidation, $id)
    {
        $invoice = Invoice::find($id);
        if (is_null($invoice))
        {
            return $this->sendError('Invoice not found.');
        }

        if ($result = $invoiceValidation->checkViewIsValid($request, $invoice))
        {
            return $this->sendError($result['message'], [], $result['status']);
        }

        return $this->sendResponse($invoice, 'Invoice retrieved successfully.');
    }

    /**
     * @OA\Post(
     *     path="/auth/invoices/create",
     *     tags={"Invoices"},
     *     summary="Create invoice",
     *     description="Returns invoice data",
     * 	   @OA\Parameter(
     * 			name="currency_id",
     *          description = "currency id",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="status_id",
     *          description = "status id",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="total",
     *          description = "total amount",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="tax",
     *          description = "tax",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="invoice_date",
     *          description = "invoice date",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="datetime")
     * 	   ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     * )
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'currency_id' => 'required|numeric|exists:currencies,id',
            'status_id' => 'required|numeric|exists:statuses,id',
            'total' => 'required|numeric',
            'tax' => 'required|numeric',
            'invoice_date' => 'required|date',
        ]);

        if ($validator->fails())
        {
            return $this->sendError('Not valid data.');
        }

        $input = $request->all();
        $input['sender_id'] = $request->user()->id; // Set current user
        Invoice::create($input);
        return $this->sendResponse($input, 'Successfully invoice created.', 201);
    }

    /**
     * @OA\Patch(
     *     path="/auth/invoices/assign",
     *     tags={"Invoices"},
     *     summary="Send invoice to user",
     *     description="Returns assign data",
     * 	   @OA\Parameter(
     * 			name="invoice_id",
     *          description = "invoice id",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     * 	   @OA\Parameter(
     * 			name="receiver_id",
     *          description = "invoice receiver user id",
     * 			required=true,
     * 			in="query",
     * 			@OA\Schema(type="integer")
     * 	   ),
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     * )
     */
    public function assign(Request $request, InvoiceValidation $invoiceValidation, Invoice $invoice)
    {
        $input = $request->all();
        $validator = Validator::make($input, [
            'id' => 'required|numeric|exists:invoices,id',
            'receiver_id' => 'required|numeric|exists:users,id',
        ]);

        if ($validator->fails())
        {
            return $this->sendError('Not valid data.');
        }

        if ($result = $invoiceValidation->checkIsValidSending($request, $input))
        {
            return $this->sendError($result['message'], [], $result['status']);
        }

        $invoice->update($input);
        return $this->sendResponse($input, 'Successfully sent.', 200);
    }


    /**
     * @OA\Get(
     *     path="/auth/invoices/expired",
     *     tags={"Invoices"},
     *     summary="Get list of expired invoices",
     *     description="Returns list of expired invoices",
     *     @OA\Response(
     *         response=200,
     *         description="Successful operation"
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Unauthenticated"
     *     ),
     *     @OA\Response(
     *         response=403,
     *         description="Forbidden"
     *     ),
     * )
     */
    public function expired(Request $request)
    {
        $status = Status::where('name', 'Expired')->first();
        $invoices = Invoice::where('status_id', $status->id)
            ->with(['sender', 'receiver', 'currency', 'status'])->orderBy('id', 'Desc')
            ->paginate(10);
        return response()->json($invoices);
    }
}
