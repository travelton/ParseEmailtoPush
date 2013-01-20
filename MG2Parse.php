<?PHP

/*

  File Name:   MG2Parse.php

  Description: This PHP application is responsible for storing a new Push notification and issuing a push notification validation request. The script receives POST data from a Mailgun "Route" webhook. The required expression is "match_recipient("push+.*@<yourdomain.com>")". The inbound data is then parsed and sent to the Parse API. Alternatively, if the script is called via GET, the push notification will validate against the Parse API and be sent to the Push Notification REST endpoint.

  Setup:       To configure. Enter your Mailgun API key, Parse APP ID, Parse API 
               key and Storage Class below. Then point your Mailgun route to post to this script.

  Created by Travis Swientek on 1/12/13.

  MG2Parse is licensed under a Creative Commons Attribution 3.0 Unported License. http://creativecommons.org/licenses/by/3.0/deed.en_US
  
  Github: https://github.com/travis06/ParseEmailtoPush

*/

// Your Mailgun API Key should be defined here. You can find this key by going to the Mailgun Control Panel then click on the "My Account" tab.

define("MGAPIKEY", "key-123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ");

// Your Parse Application ID and REST API key should be defined here. You can find these keys by going to the Parse Control Panel then click on "Select your App" in the top right. The "Overview" page should display both the Parse Application ID and Parse REST API Key. 

define("PARSEAPPID", "123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ");
define("PARSEAPIKEY", "123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ");

// The class that your push notifications will be stored should be defined here. See the README file if you're not sure what this means. 

define("STORAGECLASS", "PushNotifications");

// Define a list of valid senders here. This protects your push notification service from spammers!
$VALID_SENDERS = array("myusers@domain.com", "myusers@domain.com");

// Turn security on (true) and off (false) here. Debugging?
$ENABLE_SECURIY = false;

////////////////////////////////////////////////////////////////////////////////
// DO NOT MODIFY ANYTHING BELOW THIS LINE, UNLESS YOU KNOW WHAT YOU'RE DOING. //
////////////////////////////////////////////////////////////////////////////////

// Figure out what is happening when the script is executed.
switch($_SERVER['REQUEST_METHOD'])
{
// Mailgun issues a POST, so we'll capture the route to URL here. 
case 'POST': 
	//Include the Parser class, only if we need it.
	require("Parser.php");
	// Capture the posted headers. 
	$sender = $_POST['sender'];
	$recipient = $_POST['recipient'];
	$push = $_POST['stripped-text'];
	$timestamp = $_POST['timestamp'];
	$token = $_POST['token'];
	$signature = $_POST['signature'];
	
	// Create the new parser object. 
	$parseObj = new Parser($sender, $recipient, $push, $timestamp, $token, $signature);

	// Now do something!
	if($parseObj->validateInboundMessage()){
		// And do more stuff!
		$parseObj->postNewPushOnParse();
	}
	// Exit from switch
	break;

// The confirmation URL is linked from an email, so it'll be a GET request. 
case 'GET':
	// Include the Push class, only if we need it.
	require("Push.php");
	// Check to see if the PushID is set. If not, throw an error.
	if(isset($_GET['pushId'])){
		$pushId = $_GET['pushId'];
		$pushObj = new Push($pushId);
		// Check to see if we were able to get a good Push notification from Parse, if not, throw a nice error.
		if($pushObj->getPushDetails() == "200" && $pushObj->validatePush()){
			// Send the push notification to Parse API and set the Push to "sent". If both 200, show success.
			if($pushObj->sendPushNotification() == "200" && $pushObj->setPushAsSent() == "200")
			{
				echo '<html><head><meta http-equiv="content-type" content="text/html; harset=UTF-8"/><title>Push Confirmation</title></head><style>body{ font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif; background: #F8F8F8; font-size: 12px; } div{ background-color: white; width: 400px; padding: 30px; border: 3px solid #7e7e7e; color: #757575; margin: 0 auto; display: block; margin-top: 100px; } h2 { color: #000000; margin: 0px; margin-bottom: 10px; } </style> <body><div class="message"><h2>Push Notification Status</h2> Your push notification of "' . $pushObj->returnPushMessage() . '" has been succcessfuly sent. </div> </body></html>';
			}
		}
		else{
			echo '<html><head><meta http-equiv="content-type" content="text/html; harset=UTF-8"/><title>Push Confirmation</title></head><style>body{ font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif; background: #F8F8F8; font-size: 12px; } div{ background-color: white; width: 400px; padding: 30px; border: 3px solid #7e7e7e; color: #757575; margin: 0 auto; display: block; margin-top: 100px; } h2 { color: #000000; margin: 0px; margin-bottom: 10px; } </style><body><div class="message"><h2>Push Notification Status</h2> Sorry, this link has expired.</div><br></body></html>';
		}
	}
	else {
		echo '<html><head><meta http-equiv="content-type" content="text/html; harset=UTF-8"/><title>Push Confirmation</title></head><style>body{ font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif; background: #F8F8F8; font-size: 12px; } div{ background-color: white; width: 400px; padding: 30px; border: 3px solid #7e7e7e; color: #757575; margin: 0 auto; display: block; margin-top: 100px; } h2 { color: #000000; margin: 0px; margin-bottom: 10px; } </style><body><div class="message"><h2>Error</h2> Sorry, something did not go right.</div><br></body></html>';
	}
	break;
	
// Default error, in the event the script is called via other HTTP method. 
default:
	 echo '<html><head><meta http-equiv="content-type" content="text/html; harset=UTF-8"/><title>Push Confirmation</title></head><style>body{ font-family:"Lucida Grande", Tahoma, Arial, Verdana, sans-serif; background: #F8F8F8; font-size: 12px; } div{ background-color: white; width: 400px; padding: 30px; border: 3px solid #7e7e7e; color: #757575; margin: 0 auto; display: block; margin-top: 100px; } h2 { color: #000000; margin: 0px; margin-bottom: 10px; } </style><body><div class="message"><h2>Error</h2> Sorry, something did not go right.</div><br></body></html>';
	 break;
}










?>