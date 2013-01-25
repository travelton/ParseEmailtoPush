var mailgun = require('mailgun');
mailgun.initialize('<yourdomainhere.com>', 'key-123456789ABCDEFGHIJKLMNOPQRSTUVWXYZ');

Parse.Cloud.afterSave("PushNotifications", function(request) {
  var sender = request.object.get("sender");
  var channel = request.object.get("channel");
  var message = request.object.get("message");
  var sent = request.object.get("sent");
  var objectId = request.object.id;
  var body = '<html><body>Hello, <br /><br />A request to send a push notification to all users on the channel, "' + channel + '".<br /><br />The message includes the following text: "' + message + '".<br /><br />If you are ready to send this message, please confirm by clicking the following link: <a href="http://<yourdomainhere.com>/MG2Parse.php?pushId='+objectId+'">Send Push Now</a><br /><br />Thank you,<br />MG2Parse';
 if(!sent){
  mailgun.sendEmail({
  to: sender,
  from: "PushValidation <pushvalidate@<yourdomainhere.com>",
  subject: "Please Confirm Your Push Notification",
  html: body}
  , {
    success: function(httpResponse) {
        console.log(httpResponse);
    },
    error: function(httpResponse) {
        console.error(httpResponse);
    }
  });
}});
