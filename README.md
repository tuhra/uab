# Uab Pay Integration

### Installation

To get started with UabPay, use Composer to add the package to your project's dependencies:

### 
	composer require tuhra/uabpay

After installing the uabpay package, register the service provider

###
	Tuhra\Uabpay\UabPayServiceProvider::class,

in your config/app.php configuration file:

### 
	'providers' => [
	    /*
	    * Package Service Providers...
	    */
	    Tuhra\Uabpay\UabPayServiceProvider::class,
	],

### Configuration

run the below command to publish the package assets.

###
	php artisan vendor:publish --tag="uab-config"

change uab pay credentials from config/uab.php

###
	return [
	    'uab_key' => 'UAB_KEY',
	    'uab_user' => 'UAB_USER',
	    'uab_password' => 'UAB_PASSWORD',
	    'uab_channel' => 'UAB_CHANNEL',
	    'uab_merchant_userid' => 'UAB_MARCHANT_ID',
	    'uab_app_name' => 'UAB_APP_NAME',
	    'uab_amount' => 'CHARGE_AMOUNT',
	    'uab_callback' => 'CALLBACKURL',
	    'uab_expire' => 'EXPIRE_SECOND',
	    'uab_remark' => 'YOUR_REMARK',
	    'uab_login_url' => 'UAB_LOGIN_URL',
	    'uab_msisdn_url' => 'UAB_MSISDN_URL',
	    'uab_payment_url' => 'UAB_PAYMENT_URL',
	];


### Usage

###
	use Tuhra\Uabpay\Uabpay;

Generate uab token
	$token = Uabpay::generateToken();

Validation UAB Msisdn
	







