# php-fcm-v1
[![Build Status](https://travis-ci.org/lkaybob/php-fcm-v1.svg?branch=master)](https://travis-ci.org/lkaybob/php-fcm-v1)
[![codecov](https://codecov.io/gh/lkaybob/php-fcm-v1/branch/master/graph/badge.svg)](https://codecov.io/gh/lkaybob/php-fcm-v1)

php-fcm-v1 is an PHP implementation of [FCM](https://firebase.google.com/docs/cloud-messaging) HTTP v1 API

### What is different compared to others FCM Libraries?
Most of other libraries are implementation of FCM's [Legacy HTTP Server Protocol](https://firebase.google.com/docs/cloud-messaging/http-server-ref). It requires a server key from Firebase console (which means you have to copy and paste in your code) ([Docs](https://firebase.google.com/docs/cloud-messaging/auth-server#authorize_legacy_protocol_send_requests))

HTTP v1 API, in contrast, leverages OAuth2 security model. You need to get an access token (which is valid for about an hour) in order to request sending notification with service account's private key file. Although 
(See the blog [post](https://firebase.googleblog.com/2017/11/whats-new-with-fcm-customizing-messages.html) about HTTP v1 API)

### References
* [google/node-gtoken](https://github.com/google/node-gtoken)
* [google/google-auth-library-nodejs](https://github.com/google/google-auth-library-nodejs) 
  : Above two libraries let me understand how HTTP v1 API works in FCM
* [guzzlehttp/guzzle](https://github.com/guzzle/guzzle) : GuzzleHttp let this library to PSR7 compatible
* [Paragraph1/php-fcm](https://github.com/Paragraph1/php-fcm) : Inspired me how FCM libraries are used in Legacy HTTP Protocol

### How to use

* Install the library with composer

  ```
  composer install lkaybob/phpFCMv1
  ```

* Import the library with *autoload.php*

  ```php
  require_once __DIR__ . '/vender/autoload.php';

  use lkaybob\phpFCMv1\Client;
  use lkaybob\phpFCMv1\Notification;
  use lkaybob\phpFCMv1\Recipient;
  ```

* Create Necessary class instances, Client, Recipient, Notification/Data

  ```php
  // Client instance should be created with path to service account key file
  $client = new Client('service_account.json');
  $recipient = new Recipient();
  // Either Notification or Data (or both) instance should be created
  $notification = new Notification();
  ```

* Setup each instances with necessary information

  ```php
  // Recipient could accept individual device token,
  // the name of topic, and conditional statement
  $recipient -> setSingleREcipient('DEVICE_TOKEN');
  // Setup Notificaition title and body
  $notification -> setNotification('NOTIFICATION_TITLE', 'NOTIFICATION_BODY');
  // Build FCM request payload
  $client -> build($recipient, $notification);
  ```

* Fire in the FCM Server!

  ```php
  $result = $client -> fire();
  // You can check the result
  // If successful, true will be returned
  // If not, error message will be returned
  echo $result;
  ```

### Further Example

* Full Simple Example

  ```php
  <?php
  require_once __DIR__ . '/vendor/autoload.php';

  use phpFCMv1\Client;
  use phpFCMv1\Notification;
  use phpFCMv1\Recipient;

  $client = new Client('service_account.json');
  $recipient = new Recipient();
  $notification = new Notification();

  $recipient -> setSingleRecipient('DEVICE_TOKEN');
  $notification -> setNotification('NOTIFICATION_TITILE', 'NOTIFICATION_BODY');
  $client -> build($recipient, $notification);
  $client -> fire();
  ```

* Using with *PRIOIRTY* option (for both Android & iOS)

  ```php
  <?php
  require_once __DIR__ . '/vendor/autoload.php';

  use phpFCMv1\Client;
  use phpFCMv1\Config;
  use phpFCMv1\Notification;
  use phpFCMv1\Recipient;

  $client = new Client('service_account.json');
  $recipient = new Recipient();
  $notification = new Notification();
  $config = new Config();

  $recipient -> setSingleRecipient('DEVICE_TOKEN');
  $notification -> setNotification('NOTIFICATION_TITLE', 'NOTIFICATION_BODY');
  $config -> setPriority(Config::PRIORITY_HIGH);
  $client -> build($recipient, $notification, null, $config);
  $result = $client -> fire();
  ```

* For independent platform (either Android or iOS)

  ```
  // Option Instance for Android
  // Use phpFCMv1\AndroidConfig Class
  $androidConfig = new Config\AndroidConfig();
  $androidConfig -> setPriority(Config\AndroidConfig::PRIORITY_HIGH);
  $client -> build($recipient, $notification, null, $androidConfig);
  
  // Option Instance for iOS (which is APNs header)
  // Use phpFCMv1\APNsCOnfig Class
  $apnsConfig = new APNsConfig();
  $apnsConfig -> setPriority(APNsConfig::PRIORITY_HIGH);
  $client -> build($recipient, $notification, null, $apnsConfig);
  ```



### Future Works

[ ] Implement simultaneous send (Currently supports single recipient or topic one at a time)

[ ] Setup Read the Docs

[ ] Add CI Test

[ ] Add CodeCov Badge