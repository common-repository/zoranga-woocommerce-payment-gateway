<?php

/**
 * Zoranga API implementation
 *
 * PHP version 5
 *
 * @category Authentication
 * @package  Zoranga
 * @author   Mbanusi Ikenna <b>Incofabikenna@gmail.com</b>
 * @license  http://opensource.org/licenses/BSD-3-Clause 3-clause BSD
 * @link     https://github.com/incofab
 */
class Zoranga {
	
	private static $apiKey = '',
				   $merchantId = '',
				   $service_url = 'https://zoranga.com/api/v1/';
	
	/**
	 * Initialize Zoranga object with your API key and Merchant ID
	 * @param unknown $apiKey
	 * @param unknown $merchantId
	 */
	function __construct($apiKey, $merchantId, $url) 
	{
		self::$apiKey = $apiKey;
		
		self::$merchantId = $merchantId;
		
		self::$service_url = $url;
	}
	
	private function formatPhoneNo($phone) 
	{
	    $phone = ltrim($phone, '+234');

	    $phone = ltrim($phone, '0');
	    
	    return '0' . $phone;
	}
	
	/**
	 * Make a pin deposit
	 * @param int $amount
	 * @param unknown $depositorsPhoneNo
	 * @param int $network
	 * @param unknown $description
	 * @return Array. The zoranga responses are already converted to array to ease use
	 */
	function pinDeposit($pin, $amount, $depositorsPhoneNo, $network, $more = []) {

		$curl_post_data = array
		(
				'amount' => $amount,
				'apiKey' => self::$apiKey,
				'merchantId' => self::$merchantId,
				'depositors_mobile_number' =>  $this->formatPhoneNo($depositorsPhoneNo),
				'airtime_pin' => $pin,
				'object' => 'pinDeposit',
				'network' => $network,
		);
		
		$curl_post_data = array_merge($curl_post_data, $more);
		
		$curl_response = $this->execute_curl($curl_post_data);
		
		return json_decode($curl_response, true);
		
	}
		
	/** Using share and sell */
	function airtimeTransfer($amount, $depositorsPhoneNo, $network, $more = []) {

		$curl_post_data = array
		(
				'amount' => $amount,
				'apiKey' => self::$apiKey,
				'merchantId' => self::$merchantId,
		        'depositors_mobile_number' =>  $this->formatPhoneNo($depositorsPhoneNo),
				'object' => 'airtimeTransfer',
				'network' => $network,
		);
		
		$curl_post_data = array_merge($curl_post_data, $more);
		
		$curl_response = $this->execute_curl($curl_post_data);
		
		return json_decode($curl_response, true);
		
	}
	 
	function egoDeposit($egoPin, $amount, $depositorsPhoneNo, 
	           $network, $verificationCode, $more = []) {
	    
	    $curl_post_data = array
	    (
	        'amount' => $amount,
	        'apiKey' => self::$apiKey,
	        'merchantId' => self::$merchantId,
	        'depositors_mobile_number' =>  $this->formatPhoneNo($depositorsPhoneNo),
	        'airtime_pin' => $egoPin,
	        'object' => 'pinDeposit',
	        'network' => $network,
	        'verification_code' => $verificationCode,
	    );
	    
	    $curl_post_data = array_merge($curl_post_data, $more);
	    
	    $curl_response = $this->execute_curl($curl_post_data);
	    
	    return json_decode($curl_response, true);
	}
	 
	function purchaseEgo($amount, $depositorsPhoneNo, $more = []) {
	    
	    $curl_post_data = array
	    (
	        'amount' => $amount,
	        'apiKey' => self::$apiKey,
	        'merchantId' => self::$merchantId,
	        'depositors_mobile_number' =>  $this->formatPhoneNo($depositorsPhoneNo),
	        'object' => 'getEgo',
	        'network' => 1,
	        'description' => 'Purchase Ego',
	    );
	    
	    $curl_post_data = array_merge($curl_post_data, $more);
	    
	    $curl_response = $this->execute_curl($curl_post_data);
	    
	    return json_decode($curl_response, true);
	}
	
	/**
	 * Checks if a previously made airtime deposit has been credited 
	 * @param unknown $reference_id The reference number return when the deposit was made
	 * @return mixed|string|array
	 */
	function statusRequest($reference_id, $more = []) {
	
		$curl_post_data = array
		(
				'apiKey' => self::$apiKey,
				'merchantId' => self::$merchantId,
				'object' => 'statusRequest',
				'reference' => $reference_id,
		);
	
		$curl_post_data = array_merge($curl_post_data, $more);
		
		$curl_response = $this->execute_curl($curl_post_data);
		
		return json_decode($curl_response, true);
	
	}
	
	/**
	 * Helper function to create and execute curl requests
	 * @param unknown $curl_post_data
	 * @return mixed
	 */
	private function execute_curl($curl_post_data) {
		
		$curl = curl_init(self::$service_url);
		
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_POST, true);
		curl_setopt($curl, CURLOPT_POSTFIELDS, $curl_post_data);
		
		$curl_response = curl_exec($curl);
		
		$httpErrorCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);

		$error = curl_error($curl);
		curl_close($curl);
		
		if($error)
		{
		    return json_encode(['result_code' => $httpErrorCode, 'message' => $error]);
		}
		
		if(empty($curl_response) && $httpErrorCode != 200)
		{
		    return json_encode(['result_code' => $httpErrorCode, 
		        'message' => "Possibe error from server with status $httpErrorCode, try again later"]);
		}
		
		return $curl_response;
	}
	
	
}







