<?php

namespace Tuhra\Uabpay;

use Tuhra\Uabpay\Model\Invoice;
use Tuhra\Uabpay\Model\UabLog;

class UabPay
{
	public static function encrypted($string) {
		$hash = hash_hmac('sha1', $string, config('uab.uab_key') , false);
		return strtoupper($hash);
	}

	public static function generateToken() {
		$data = [
			"UserName" => config('uab.uab_user'),
			"Password" => config('uab.uab_password')
		];

        $data = json_encode($data);
		$result = self::postapirequest(config('uab.uab_login_url'), $data);
		$token_array = json_decode($result['res'], true);
		return $token_array['Token'];
	}

	public static function checkUabMsisdn($msisdn, $token) {
		$string = config('uab.uab_channel') . config('uab.uab_merchant_userid') . config('uab.uab_app_name') . $msisdn;
		$hash = self::encrypted($string);
		$data = [
			'Channel' => config('uab.uab_channel'),
			'MerchantUserId' => config('uab.uab_merchant_userid'),
			'AppName' =>  config('uab.uab_app_name'),
			'UabpayPhoneNo' => $msisdn,
			'HashValue' => $hash
		];
        $data = json_encode($data);
		$result = self::postapirequest(config('uab.uab_msisdn_url'), $data, $token);
		$json = $result['res'];
		return json_decode($json, TRUE);

	}

	public static function paymentAPI($msisdn, $token) {
		$invoice = self::getInvoiceNo();
		$sequenceNo = self::getSequenceNo();
		$remark = config('uab.uab_remark');
		$string = config('uab.uab_channel') . config('uab.uab_app_name') . config('uab.uab_merchant_userid') . $msisdn . config('uab.uab_amount') . $remark . $invoice . $sequenceNo . config('uab.uab_callback') . config('uab.uab_expire');
		$hash = self::encrypted($string);

		$data = [
			'Channel' => config('uab.uab_channel'),
			'AppName' =>  config('uab.uab_app_name'),
			'MerchantUserId' => config('uab.uab_merchant_userid'),
			'InvoiceNo' => $invoice,
			'SequenceNo' => $sequenceNo,
			'Amount' => config('uab.uab_amount'),
			'Remark' => $remark,
			'WalletUserID' => $msisdn,
			'CallBackUrl' => config('uab.uab_callback'),
			'ExpiredSeconds' => config('uab.uab_expire'),
			'HashValue' => $hash
		];
        $data = json_encode($data);
		$result = self::postapirequest(config('uab.uab_payment_url'), $data, $token);
		self::uablogcreation($sequenceNo, $result['req'], $result['res']);
		return $result['res'];
	}

	public function callbackConfirm($array) {
		$ReferIntegrationId = $array['ReferIntegrationId'];
		$ItemListJsonStr = [
				"ItemId" => '001',
				"Quantity" => '1',
				"EachPrice" => '200'
			];
		$json = json_encode($ItemListJsonStr);
		$string = '000Success' . $json . 'Data';
		$hash = $this->encrypted($string);
		$data = [
			'ReferIntegrationId' => $ReferIntegrationId,
			'DataType' =>  'Data',
			'ItemListJsonStr' => $json,
			'RespDescription' => 'Success',
			'RespCode' => '000',
			'HashValue' => $hash
		];

		return json_encode($data);

	}


	public static function postapirequest($url, $data, $token=null) {

		$header = array(
		    'Accept: application/json',
		    'Content-Type: application/json'
		);

		if (NULL !== $token) {
			$header = array(
			    'Accept: application/json',
			    'Content-Type: application/json',
			    'Authorization:' .$token
			);
		}

	    $ch = curl_init();
	    curl_setopt($ch, CURLOPT_URL, $url);
	    curl_setopt($ch, CURLOPT_POST, 1);
	    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
	    curl_setopt($ch, CURLOPT_HTTPHEADER, $header);
	    curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
	    curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);
	    curl_setopt($ch, CURLOPT_SSL_CIPHER_LIST, 'DEFAULT@SECLEVEL=1');
	    $result = curl_exec($ch);
	    if(curl_errno($ch)){
	        \Log::info('Curl error: ' . curl_error($ch));
	    }
	    $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
	    curl_close($ch);
	    return ['res' => $result, 'req' => $data];
	}


	public static function getInvoiceNo() {
		$invoice = Invoice::orderby('id', 'DESC')->first();
		$i = 1;
		if ($invoice) {
			$i = $invoice->id + 1;
		}
		$invNo = config('uab.uab_invoice_prefix') . str_pad($i, 5, "0", STR_PAD_LEFT);
		$row = new Invoice;
		$row->invoice = $invNo;
		$row->save();
		return $invNo;
	}

	public static function getSequenceNo() {
		return config('uab.uab_seq_prefix') . rand(100,999).time().rand(100,999);
	}

	public static function uablogcreation($seqNo, $req, $res) {
		$log = new UabLog;
		$log->SequenceNo = $seqNo;
		$log->reqBody = $req;
		$log->resBody = $res;
		$log->save();
	}


}



