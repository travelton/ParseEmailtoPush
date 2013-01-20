<?PHP

/*

  File Name:   Push.php

  Description: There should be nothing in here to modify. FYI, this is used to validate and send a Push notification using the Parse REST API.
  
  Created by Travis Swientek on 1/12/13.

  MG2Parse is licensed under a Creative Commons Attribution 3.0 Unported License. http://creativecommons.org/licenses/by/3.0/deed.en_US
  
  Github: https://github.com/travis06/ParseEmailtoPush

*/

class Push{

	// Default construct
	public function __construct($pushId)
	{
		$this->PARSEAPPID = constant('PARSEAPPID');
		$this->PARSEAPIKEY = constant('PARSEAPIKEY');
		$this->STORAGECLASS = constant('STORAGECLASS');
		$this->pushId = $pushId;
		return;
		}
		
	// Function used to return the message that is stored in Parse Class
	public function returnPushMessage(){
		return $this->message;
	}
	
	// Function used to get the Push Notification details based on ID.
	public function getPushDetails(){
		$ch = curl_init();
		$headers = array( "X-Parse-Application-Id: $this->PARSEAPPID",
                    "X-Parse-REST-API-Key: $this->PARSEAPIKEY",
                    "Content-Type: application/json");
        $url = "https://api.parse.com/1/classes/" . $this->STORAGECLASS . "/" . $this->pushId;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'GET');
		curl_setopt($ch, CURLOPT_URL, $url);
		$result = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		$this->pushJson = $result;
		return $http_status;
	}
	
	// Function used to ensure Push notification hasn't been sent yet. Prevents duplicate Pushes.
	public function validatePush(){
		$this->sent = json_decode($this->pushJson)->{'sent'};
		if($this->sent == 0){
			return true;
		}
		return false;
	}
	
	// Function to issue Push Notification to Parse API endpoint.
	public function sendPushNotification(){
		$ch = curl_init();
		$this->channel = json_decode($this->pushJson)->{'channel'};
		$this->message = json_decode($this->pushJson)->{'message'};
		$headers = array("X-Parse-Application-Id: $this->PARSEAPPID",
                         "X-Parse-REST-API-Key: $this->PARSEAPIKEY",
                         "Content-Type: application/json");
      
    	$postFields = json_encode(array("data"=> 
    								array("alert"=> $this->message, "badge" => "1"), 
    									"where" => 
    										array("deviceType" => "ios", 
    										      "channels" => 
    										      	array('$in' => 
    										      			array($this->channel)))));											
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'POST');
		curl_setopt($ch, CURLOPT_URL, "https://api.parse.com/1/push");
		curl_setopt($ch, CURLOPT_POSTFIELDS, $postFields);
		$result = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $http_status;
	}
	
	// Funtcion to mark the Push Notification as sent. We need to prevent duplicate push notifications, because that's annoying.
	public function setPushAsSent(){
		$ch = curl_init();
		$headers = array("X-Parse-Application-Id: $this->PARSEAPPID",
                         "X-Parse-REST-API-Key: $this->PARSEAPIKEY",
                         "Content-Type: application/json");
        $url = "https://api.parse.com/1/classes/" . $this->STORAGECLASS . "/" . $this->pushId;
		curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($ch, CURLOPT_CUSTOMREQUEST, 'PUT');
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode(array("sent" => 1)));
		$result = curl_exec($ch);
		$http_status = curl_getinfo($ch, CURLINFO_HTTP_CODE);
		curl_close($ch);
		return $http_status;
	}
}
?>