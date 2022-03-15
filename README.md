
Midtrans&nbsp; WHMCS Payment Gateway Module
=====================================

Midtrans&nbsp; :heart: WHMCS!
Integrate WHMCS with Midtrans&nbsp; payment gateway, easily allows you to accept payments with Midtrans' [various payment methods](https://midtrans.com/payments).

### Description

[Midtrans&nbsp;](https://www.midtrans.com) payment gateway is an online payment gateway. We strive to make payments simple for both the merchant and customers. With this plugin you can start accepting online payment on your WHMCS using [Midtrans&nbsp;](https://www.midtrans.com) payment gateway.

Payment Method Feature:
- [Midtrans&nbsp;](https://www.midtrans.com) Snap all payment method fullpayment

### Installation Instruction

#### Minimum Requirements

* WHMCS v5.3.12 - v8.x or greater (Tested up to WHMCS v8.3.1 - running well)
* PHP version 5.4 or greater
* MySQL version 5.0 or greater

#### Installation

1. [Download](../../archive/master.zip) the modules from this repository.
2. Extract `snap-whmcs-master.zip` file you have previously downloaded.
3. Upload (or merge) `modules` folder that you have extracted into your WHMCS directory `modules` folder. 

Sample folder structure
```
[Your WHMCS installation folder, it should contains `index.php` file from the whmcs installation]
  |- [other_folder_from_whmcs]
  |- [other_folder_from_whmcs]
  |- [other_folder_from_whmcs]
  |- modules
    |- gateways
      |- veritrans-lib
      |- veritrans.php
      |- veritranspromo.php
      |- callback
        |- veritrans.php
```

Or follow this [official guide](https://developers.whmcs.com/payment-gateways/installation-activation/) from WHMCS.

#### Installation & Configuration

1. Access your WHMCS admin page.
2. Go to menu `Configuration > System Settings > Payment Gateways (Setup > Payments > Payment Gateways in WHMCS 7.10 and earlier)`.
3. Click `Midtrans` payment method, then you will be redirected to configuration page. 
4. Fill the input as instructed on the screen. Click `Save Changes`.

### Midtrans&nbsp;  MAP Configuration

1. Login to your [Midtrans&nbsp;  Account](https://dashboard.midtrans.com), select your environment (sandbox/production), go to menu `settings > configuration`
   * Payment Notification URL: `http://[your website url]/modules/gateways/callback/veritrans.php`
   * Finish Redirect URL: `http://[your website url]`
   * Unfinish Redirect URL: `http://[your website url]`
   * Error Redirect URL: `http://[your website url]`

#### Troubleshooting

If you encounter payment popup not being properly opened on invoice page, try these solution:

On menu `Setup -> Payments -> Payment Gateways`, tick option `Payment Redirect To Midtrans`. Customer will be redirected to Midtrans instead of popup. This can minimize popup issue.

If you use non IDR currency on WHMCS and encounter issue of paid amount is too big or does not match with invoice amount: 

Try to enable configuration `Use Invoice amount as paid amount` to use the invoice amount as paid amount. Note: this will also means it will not check the actual paid amount on Payment Gateway, but it can avoid currency conversion issues.

Or Try to enable `Try convert back paid amount currency` to try to convert back from paid amount in IDR to original customer currency, using builtin WHMCS converter. Note: WHMCS converter may not 100% accurate.

#### Get help

* [Midtrans&nbsp;](https://www.midtrans.com)
* [Midtrans registration](https://dashboard.midtrans.com/register)
* [SNAP documentation](http://snap-docs.midtrans.com)
* Can't find answer you looking for? email to [support@midtrans.com](mailto:support@midtrans.com)
