ParseEmailtoPush
===========
ParseEmailtoPush is a demo application provided by Mailgun and Parse. It was written to demonstrate the capabilities of combining the Mailgun and Parse platform. The application will accept an email and convert the email to a Push Notification for mobile devices. iOS is used for demonstration purposes.

Mailgun: http://www.mailgun.com  
Parse: http://www.parse.com


Developed by: Travis Swientek, Mailgun, Inc.  
Date: January 20, 2013


Concept Flow
--------

1. User sends an email to a Mailgun "Route" via defined email address.
2. Mailgun parses the email and posts the message to route endpoint (MG2Parse.php). 
3. Endpoint parses the inbound POST and stores received data as a Parse Object.
4. The Parse Class, upon saving the Object, uses "Cloud Code" to send a confirmation email to the sender. 
5. The confirmation email contains a validation link. The end user clicks the link to validate the Push Notification. 
6. The endpoint (MG2Parse.php) is called to issue a Push Notification to the Parse REST API.
7. The push notification is delivered to the mobile clients that are subscribed to that channel.

Setup/Configuration
--------

Parse - 
1. Create a new Class titled "PushNotifications".  
2. Add the following columns (keep all default columns): channel (string), message (string), sender (string), sent (number).  
3. Deploy the "main.js" file to your Parse Cloud Code  

Mailgun - 
1. Considering your account is already setup to receive email.  
2. Go to the Routes tab and add a new Route defined as "push+.*@<yourdomain.com>". Example: push+.*@parsedemo.com  
3. Set the route to point to the script "MG2Parse.php". Note: You must host this script on a server accessible from the internet.  

MG2Parse.php - 
Note: Requires PHP version 5 or newer.  
1. Obtain your Mailgun API key, Parse Application ID, and Parse API Key.  
2. Store these values in the constants within the configuration section.  
3. Define the valid "senders" to ensure spammers don't spam your endpoint.  
4. Disable security during testing, enable when you put in production.  

iOS App - 
1. Follow the "Quick Start" guidelines to add the Parse SDK to a new or existing app. https://parse.com/apps/quickstart?app_id=mailgun-test-app#ios/blank
2. Configure your Parse account for iOS Push Notifications. https://parse.com/apps/quickstart_push
3. Once your iOS app is configured for Parse and Push Notifications, subscribe yourself to any channel by issuing "[PFPush subscribeToChannel:@"myawesomechannel" error:nil];"
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




