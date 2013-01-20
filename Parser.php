<?PHP

/*

  File Name:   Parser.php

  Description: There should be nothing in here to modify. FYI, this is used to parse a message posted by Mailgun. It then stores the message in the defined Parse class. 
  
  Created by Travis Swientek on 1/12/13.

  MG2Parse is licensed under a Creative Commons Attribution 3.0 Unported License. http://creativecommons.org/licenses/by/3.0/deed.en_US
  
  Github: https://github.com/travis06/ParseEmailtoPush

*/

class Parser{

	// Default construct
	public function __construct($sender, $recipient, $push, $timestamp, $token, $signature)
	{
		global $VALID_SENDERS;
		$this->validSenders = $VALID_SENDERS;
		$this->MGAPIKEY = constant('MGAPIKEY');
		$this->PARSEAPPID = constant('PARSEAPPID');
		$this->PARSEAPIKEY = constant('PARSEAPIKEY');
		$this->STORAGECLASS = constant('STORAGECLASS');
		$this->sender = $sender;
		$this->recipient = $recipient;
		$this->push = $push;
		$this->timestamp = $timestamp;
		$this->token = $token;
		$this->signature = $signature;

		}
	
	// Function used to validate Mailgun webhook authenticity. See "Securing Webhooks". http://documentation.mailgun.net/user_manual.html#manual-webhooks
	public function validateInboundMessage(){
		$hashData = $this->timestamp . "" . $this->token;
		$computedSignature = hash_hmac("sha256",$hashData , $this->MGAPIKEY);
		$providedSignature = $this->signature;
		if ($computedSignature == $providedSignature){
			if($this->validateSender()){
				return true;
			}
		}
		return false;
	}
	
	// Function to validate sender is permitted to use this service. You could hook this up to a database. Defined within code to KISS. 
	private function validateSender(){
		global $ENABLE_SECURITY;
		if(in_array($this->sender, $this->validSenders) || $ENABLE_SECURITY == false){
			return true;
		}
		return false;
	}
	
	// Function to store the inbound Push Notification as a Parse Object within a class called "PushNotifications". 
	public function postNewPushOnParse(){
		$this->generateChannel();
		$ch = curl_init();
		$headers = array( "X-Parse-Application-Id: $this->PARSEAPPID",
                    "X-Parse-REST-API-Key: $this->PARSEAPIKEY",
                    "Content-Type: application/json");
        $url = "https://api.parse.com/1/classes/" . $this->STORAGECLASS;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("sender" => $this->sender, "message" => $this->push, "channel" => $this->channel, "sent" => 0)));
		$result = curl_exec($ch);
		curl_close($ch);
  		return $result;
	}
	
	// Function to abstract the channel from the inbound email.
	public function generateChannel(){
		$tempArray = array();
		preg_match('/\+(.*?)@/', $this->recipient, $tempArray);
		$this->channel = $tempArray[1];
	}
}
?>