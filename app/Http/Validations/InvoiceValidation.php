<?php

namespace App\Http\Validations;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Invoice;

class InvoiceValidation
{
    /**
     * Sending process validation
     * @param Request $request
     * @param $input
     * @return array
     */
    public function checkIsValidSending(Request $request, $input)
    {
        $currentInvoice = Invoice::find($input['id']);
        $receiver = User::where('id', $input['receiver_id'])->with('roles')->first();

        // Invoice owner control
        if ($request->user()->id != $currentInvoice->sender_id)
        {
            return ['message' => 'Not valid invoice selected!', 'status' => 403];
        }

        // Sender and receiver cannot be the same
        if ($request->user()->id == $input['receiver_id'])
        {
            return ['message' => 'Not valid sender selected!', 'status' => 403];
        }

        // Sender must be Freelancer or Business
        $senderRole = $request->user()->roles->pluck('name')->first();
        if (! in_array($senderRole, ['Freelancer', 'Business']))
        {
            return ['message' => 'Not valid sender role!', 'status' => 403];
        }

        // Receiver must be Freelancer or Business
        if (! in_array($receiver->roles[0]->name, ['Freelancer', 'Business']))
        {
            return ['message' => 'Not valid receiver role!', 'status' => 403];
        }

        // Freelancer cannot send to Freelancer
        if ($receiver->roles[0]->name == 'Freelancer' && $senderRole == 'Freelancer')
        {
            return ['message' => 'Not valid receiver role for Freelancer!', 'status' => 403];
        }

        return [];
    }

    /**
     * Check invoice and viewer is valid
     * @param Request $request
     * @param $invoice
     * @return array
     */
    public function checkViewIsValid(Request $request, $invoice)
    {
        // Invoice owner control
        if (! in_array($request->user()->id, [$invoice->sender_id, $invoice->receiver_id]))
        {
            return ['message' => 'Not valid invoice selected!', 'status' => 403];
        }

        // Viewer must be Freelancer or Business
        $currentRole = $request->user()->roles->pluck('name')->first();
        if (! in_array($currentRole, ['Freelancer', 'Business']))
        {
            return ['message' => 'Not valid role for current user!', 'status' => 403];
        }

        return [];
    }
}