<?php

namespace Tuhra\Uabpay;

class UabPay
{
	/* UAB Credentials Start */
	// define('UAB_KEY', 'FDB13EB47FA9');
	// define('UAB_USERNAME', 'UABMM202048030548275411563');
	// define('UAB_PASSWORD', 'admin@2020');
	// define('UAB_CHANNEL', 'TRUST OO');
	// define('UAB_MERCHENTUSERID', 'UABMM202048030548275411563');
	// define('UAB_APPNAME', 'saisaipay');
	// define('UAB_AMOUNT', '200');
	// define('UAB_CALLBACK', 'http://157.230.254.63/uab/callback');
	// define('UAB_EXIPRE', '180');
	// define('UAB_LOGIN_URL', 'http://webapi.uatuab.com:8080/API/Ver01/Wallet/Wallet_Login');
	// define('UAB_MSISDN_URL', 'http://webapi.uatuab.com:8080/API/Ver03/Wallet/Wallet_CheckPhoneNoAPIV3');
	// define('UAB_PAYMENT_URL', 'http://webapi.uatuab.com:8080/API/Ver01/Wallet/Wallet_PaymentAPI');
	/* UAB Credentials End */

	public function encrypted($string) {
		$hash = hash_hmac('sha1', $string, config('uab.uab_key') , false);
		return strtoupper($hash);
	}

	public function uabLogin() {
		$data = [
			"UserName" => config('uab.uab_user'),
			"Password" => config('uab.uab_password')
		];

        $data = json_encode($data);
		$result = $this->postapirequest(config('uab.uab_login_url'), $data);
		$json = $result['res'];
		return json_decode($json, TRUE);
	}

	public function checkUabMsisdn($msisdn, $token) {
		$string = config('uab.uab_channel') . config('uab.uab_merchant_userid') . config('uab.uab_app_name') . $msisdn;
		$hash = $this->encrypted($string);
		$data = [
			'Channel' => config('uab.uab_channel'),
			'MerchantUserId' => config('uab.uab_merchant_userid'),
			'AppName' =>  config('uab.uab_app_name'),
			'UabpayPhoneNo' => $msisdn,
			'HashValue' => $hash
		];
        $data = json_encode($data);
		$result = $this->postapirequest(config('uab.uab_msisdn_url'), $data, $token);
		$json = $result['res'];
		return json_decode($json, TRUE);

	}

	public function paymentAPI($msisdn, $token) {
		$invoice = $this->getInvoiceNo();
		$sequenceNo = $this->getSequenceNo();
		$remark = config('uab.uab_remark');
		$string = config('uab.uab_channel') . config('uab.uab_app_name') . config('uab.uab_merchant_userid') . $msisdn . '200' . $remark . $invoice . $sequenceNo . config('uab.uab_callback') . config('uab.uab_expire');
		$hash = $this->encrypted($string);

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
		$result = $this->postapirequest(config('uab.uab_payment_url'), $data, $token);
		// uablogcreation($sequenceNo, $result['req'], $result['res']);
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


	private function postapirequest($url, $data, $token=null) {

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


	private function getInvoiceNo() {
		// $invoice = Invoice::orderby('id', 'DESC')->first();
		// $i = 1;
		// if ($invoice) {
		// 	$i = $invoice->id + 1;
		// }
		// $invNo = 'HEALTHCARE' . str_pad($i, 5, "0", STR_PAD_LEFT);
		// $row = new Invoice;
		// $row->invoice = $invNo;
		// $row->save();
		// return $invNo;
		return 'HEALTHCARESEQUENCENO' . rand(100,999).time().rand(100,999);
	}

	private function getSequenceNo() {
		return 'HEALTHCARESEQUENCENO' . rand(100,999).time().rand(100,999);
	}


}