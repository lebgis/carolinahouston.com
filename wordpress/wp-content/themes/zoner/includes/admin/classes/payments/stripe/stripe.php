<?php

// Tested on PHP 5.2, 5.3

// This snippet (and some of the curl code) due to the Facebook SDK.
if (!function_exists('curl_init')) {
  throw new Exception('stripe needs the CURL PHP extension.');
}
if (!function_exists('json_decode')) {
  throw new Exception('stripe needs the JSON PHP extension.');
}
if (!function_exists('mb_detect_encoding')) {
  throw new Exception('stripe needs the Multibyte String PHP extension.');
}


// stripe singleton
require(dirname(__FILE__) . '/stripe/Stripe.php');

// Utilities
require(dirname(__FILE__) . '/stripe/Util.php');
require(dirname(__FILE__) . '/stripe/Util/Set.php');

// Errors
require(dirname(__FILE__) . '/stripe/Error.php');
require(dirname(__FILE__) . '/stripe/ApiError.php');
require(dirname(__FILE__) . '/stripe/ApiConnectionError.php');
require(dirname(__FILE__) . '/stripe/AuthenticationError.php');
require(dirname(__FILE__) . '/stripe/CardError.php');
require(dirname(__FILE__) . '/stripe/InvalidRequestError.php');
require(dirname(__FILE__) . '/stripe/RateLimitError.php');

// Plumbing
require(dirname(__FILE__) . '/stripe/Object.php');
require(dirname(__FILE__) . '/stripe/ApiRequestor.php');
require(dirname(__FILE__) . '/stripe/ApiResource.php');
require(dirname(__FILE__) . '/stripe/SingletonApiResource.php');
require(dirname(__FILE__) . '/stripe/AttachedObject.php');
require(dirname(__FILE__) . '/stripe/List.php');

// stripe API Resources
require(dirname(__FILE__) . '/stripe/Account.php');
require(dirname(__FILE__) . '/stripe/Card.php');
require(dirname(__FILE__) . '/stripe/Balance.php');
require(dirname(__FILE__) . '/stripe/BalanceTransaction.php');
require(dirname(__FILE__) . '/stripe/Charge.php');
require(dirname(__FILE__) . '/stripe/Customer.php');
require(dirname(__FILE__) . '/stripe/FileUpload.php');
require(dirname(__FILE__) . '/stripe/Invoice.php');
require(dirname(__FILE__) . '/stripe/InvoiceItem.php');
require(dirname(__FILE__) . '/stripe/Plan.php');
require(dirname(__FILE__) . '/stripe/Subscription.php');
require(dirname(__FILE__) . '/stripe/Token.php');
require(dirname(__FILE__) . '/stripe/Coupon.php');
require(dirname(__FILE__) . '/stripe/Event.php');
require(dirname(__FILE__) . '/stripe/Transfer.php');
require(dirname(__FILE__) . '/stripe/Recipient.php');
require(dirname(__FILE__) . '/stripe/Refund.php');
require(dirname(__FILE__) . '/stripe/ApplicationFee.php');
require(dirname(__FILE__) . '/stripe/ApplicationFeeRefund.php');
