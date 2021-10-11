<?php

namespace Tuhra\Uabpay\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Tuhra\Uabpay\Model\UabLog;

class UabController extends Controller
{
    public function uabcallback(Request $request)
    {
        \Log::info($request->all());
        $data = json_decode($request->getContent(), TRUE);
        $log = UabLog::where('SequenceNo', $data['ReferIntegrationId'])->first();
        switch ($data['TransactionStatus']) {
            case '000':
                UabLog::find($log->id)->update([
                    'is_callback' => TRUE,
                    'charging_status' => $data['TransactionStatus'],
                    'callback_body' => json_encode($data)
                ]);

                // $this->uabSubscriber($log->customer_id, $data, $log->id);
                // $uab = new UabHelper;
                // $confirm = $uab->callbackConfirm($data);
                // \Log::info('==========================================================================');
                // \Log::info($confirm);
                // return $confirm;
                break;
            default:
                $response = [];
                $response['status'] = FALSE;
                return $response;
                break;
        }
    }

    
}