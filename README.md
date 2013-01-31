ParseEmailtoPush
===========
ParseEmailtoPush is a demo application provided by Mailgun and Parse. It was written to demonstrate the capabilities of combining the Mailgun and Parse platform. The application will accept an email and convert the email to a Push Notification for mobile devices. iOS is used for demonstration purposes.  

Note: This demo application assumes you understand Mailgun, Parse, and iOS programming. For beginners, be sure to follow the guides provided for each "module" on the various platforms. 

Mailgun: http://www.mailgun.com  
Parse: http://www.parse.com  

Webcast Demo: http://blog.parse.com/2013/01/15/register-for-the-webcast-using-email-to-send-push-notifications-with-parse-and-mailgun/  

Developed by: Travis Swientek, Mailgun, Inc.  
Date: January 20, 2013


Concept Flow
--------

1. User sends an email to a Mailgun "Route" via defined email address.
2. Mailgun parses the email and posts the message to route endpoint (MG2Parse.php). 
3. MG2Parse.php parses the inbound POST and stores the received data in a Parse Class.
4. The Parse Class, upon saving the inbound Object, uses "Cloud Code" to send a confirmation email to the sender. 
5. The confirmation email contains a validation link. The end user clicks the link to validate the Push Notification. 
6. MG2Parse.php is called to issue a Push Notification to the Parse REST API.
7. The push notification is delivered to the mobile clients, for the defined channel.

Setup/Configuration
--------

**Parse -**  
1. Create a new Class titled "PushNotifications" (or whatever you want, define in MG2Parse.php).  
2. Add the following columns (keeping all default columns): channel (string), message (string), sender (string), sent (number).  


**Mailgun -**  
1. Considering your account is already setup to receive email on a domain.  
2. Go to the Routes tab and add a new Route defined like "push+.*@<yourdomain.com>". Example: push+.*@parsedemo.com  
3. Set the route to point to the script "MG2Parse.php". Note: You must host this script on a server accessible from the internet.  
  
Mailgun Example -  
Filter Expression: match_recipient("push+.*@parsedemo.com")  
Actions: forward("http://parsedemo.com/MG2Parse.php")

**MG2Parse.php -**  
Note: Requires PHP version 5 or newer.  
1. Obtain your Mailgun API key, Parse Application ID, and Parse API Key.  
2. Add these values, within the constants, for the configuration section.  
3. Define valid "senders" to ensure spammers don't spam your endpoint.  
4. Disable security during testing, enable when you put in production.  

**Main.js -**  
1. Open this file and add your domain and Mailgun API key.  
2. Make modifications as you see fit. (e.g. Adjust the From, Subject and Body fields)  
3. Deploy the script using Parse Cloud Code tools. https://parse.com/docs/cloud_code_guide  

**iOS App -**  
1. Follow the "Quick Start" guidelines to add the Parse SDK to a new or existing app. https://parse.com/apps/quickstart?app_id=mailgun-test-app#ios/blank  
2. Configure your Parse account for iOS Push Notifications. https://parse.com/apps/quickstart_push  
3. Once your iOS app is configured for Parse and Push Notifications, subscribe yourself to any channel by issuing: 
   ``[PFPush subscribeToChannel:@"myawesomechannel" error:nil];``  
4. Build and run the app on a physical iOS device, as the simulator cannot handle Push Notifications.  
5. Test by sending an email to your endpoint.  

Resources
-------
Mailgun Support: support@mailgun.com  
Parse Support: https://parse.com/help

Mailgun Documentation: http://documentation.mailgun.net/  
Parse Documentation: https://parse.com/docs/

Contact Developer: Travis Swientek - travis@mailgunhq.com

License
-------
MG2Parse is licensed under a Creative Commons Attribution 3.0 Unported License.  
http://creativecommons.org/licenses/by/3.0/deed.en_US




