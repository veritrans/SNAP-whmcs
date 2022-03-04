<?php

/**
 * WHMCS Midtrans SNAP Payment Gateway Module
 *
 * Midtrans SNAP Payment Gateway Module allow you to integrate Midtrans SNAP with the
 * WHMCS platform.
 *
 * For more information, please refer to the online documentation.
 * @see http://docs.midtrans.com
 *
 * Module developed based on official WHMCS Sample Payment Gateway Module
 * https://github.com/WHMCS/sample-merchant-gateway
 *
 * @author rizda.prasetya@midtrans.com
 */

// TODO
//  [v] Update library to SNAP
//  [ ] Update Veritrans String to Midtrans

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

require_once(dirname(__FILE__) . '/veritrans-lib/Veritrans.php');

/**
 * Define module related meta data.
 *
 * Values returned here are used to determine module related capabilities and
 * settings.
 *
 * @see http://docs.whmcs.com/Gateway_Module_Meta_Data_Parameters
 *
 * @return array
 */
function veritrans_MetaData()
{
    return array(
        'DisplayName' => 'Midtrans Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => true,
    );
}

/**
 * Define gateway configuration options.
 *
 * The fields you define here determine the configuration options that are
 * presented to administrator users when activating and configuring your
 * payment gateway module for use.
 *
 * Supported field types include:
 * * text
 * * password
 * * yesno
 * * dropdown
 * * radio
 * * textarea
 *
 * Examples of each field type and their possible configuration parameters are
 * provided in the sample function below.
 *
 * @return array
 */
function veritrans_config()
{
    return array(
        // the friendly display name for a payment gateway should be
        // defined here for backwards compatibility
        'FriendlyName' => array(
            'Type' => 'System',
            'Value' => 'Midtrans',
        ),
        // a text field type allows for single line text input
        'merchantid' => array(
            'FriendlyName' => 'Midtrans Merchant ID',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => '<br>Input your Merchant ID. Get it at dashboard.midtrans.com',
        ),
        // a text field type allows for single line text input
        'clientkey' => array(
            'FriendlyName' => 'Midtrans Client Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => '<br>Input your Client Server Key. Get it at dashboard.midtrans.com',
        ),
        // a text field type allows for single line text input
        'serverkey' => array(
            'FriendlyName' => 'Midtrans Server Key',
            'Type' => 'text',
            'Size' => '50',
            'Default' => '',
            'Description' => '<br>Input your Midtrans Server Key. Get it at dashboard.midtrans.com',
        ),
        // the dropdown field type renders a select menu of options
        'environment' => array(
            'FriendlyName' => 'Production Mode',
            'Type' => 'yesno',
            'Description' => 'Tick to allow real transaction, untick for testing transaction in sandbox mode',
        ),
        // the yesno field type displays a single checkbox option
        'enable3ds' => array(
            'FriendlyName' => 'Credit Card 3DS',
            'Type' => 'yesno',
            'Default' => 'on',
            'Description' => 'Tick to enable 3DS for Credit Card payment (recommended to set it: on)',
        ),
        'enableInstallment' => array(
            'FriendlyName' => 'Allow Credit Card Online Installment Payment',
            'Type' => 'yesno',
            'Description' => 'Tick to allow payment using Credit Card Online Installment (Please make sure you have active Installment feature on Midtrans, otherwise set this to: off)',
        ),
        'enableInstallmentOffline' => array(
            'FriendlyName' => 'Allow Credit Card Offline Installment Payment',
            'Type' => 'yesno',
            'Description' => 'Tick to allow payment using Credit Card Offline Installment (Please make sure you have active Installment feature on Midtrans, otherwise set this to: off)',
        ),
        'minimumInstallmentAmount' => array(
            'FriendlyName' => 'Minimum Amount For Installment',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '500000',
            'Description' => '<br>Minimum allowed amount for installment payment, amount below this value will not be eligible for installment',
        ),
        'installmentTerms' => array(
            'FriendlyName' => 'Installment terms for Credit Card',
            'Type' => 'text',
            'Size' => '64',
            'Default' => '3,6,12',
            'Description' => '<br>Installment terms separated by coma e.g: 3,6,12 (leave it default if you are not sure)',
        ),
        'whitelistBins' => array(
            'FriendlyName' => 'Whitelisted Bins for Credit Card',
            'Type' => 'text',
            'Size' => '256',
            'Default' => '',
            'Description' => '<br>Only allow customer pay with the whitelisted Bins only, input Bins separated by coma e.g: 481234,521117 (leave it default if you are not sure)',
        ),
        'customExpiry' => array(
            'FriendlyName' => 'Custom Expiry',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => '<br>This will allow you to set custom duration on how long the transaction available to be paid. e.g: 24 hours (leave it default if you are not sure)',
        ),
        'enabledPayments' => array(
            'FriendlyName' => 'Enabled Payments',
            'Type' => 'text',
            'Size' => '25',
            'Default' => '',
            'Description' => '<br>This will allow you to set custom activated payment method in Midtrans payment page. Separate payment method code with coma e.g: bank_transfer,credit_card (leave it default if you are not sure)',
        ),
        'enableSaveCard' => array(
            'FriendlyName' => 'Allow Customer to Save Card for Next Payment',
            'Type' => 'yesno',
            'Description' => 'Tick to allow Credit Card to be saved on Midtrans payment page, to be used for next payment',
        ),
        'enableSnapRedirect' => array(
            'FriendlyName' => 'Payment Redirect To Midtrans',
            'Type' => 'yesno',
            'Description' => 'Tick to make payment page redirect to Midtrans, instead of popup (recommended to set it: off)',
        ),
        'useInvoiceAmountAsPaid' => array(
            'FriendlyName' => 'Use Invoice amount as paid amount',
            'Type' => 'yesno',
            'Description' => 'Only use this IF YOU USE OTHER THAN IDR currency, and encounter amount mismatch issue on paid invoice, this will use invoice amount as paid amount (recommended to set it: off)',
        ),
        'tryToConvertCurrencyBack' => array(
            'FriendlyName' => 'Try convert back paid amount currency',
            'Type' => 'yesno',
            'Description' => 'Only use this IF YOU USE OTHER THAN IDR currency, and encounter amount mismatch issue on paid invoice, this will try to convert back to original invoice currency amount (recommended to set it: off)',
        ),
    );
}

/**
 * Payment link.
 *
 * Required by third party payment gateway modules only.
 *
 * Defines the HTML output displayed on an invoice. Typically consists of an
 * HTML form that will take the user to the payment gateway endpoint.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return string
 */
function veritrans_link($params)
{
    // @TODO: Find proper versioning method
    // Hardcoded version.
    $pluginVersion = '1.2.2';

    // Gateway Configuration Parameters
    $merchantid = $params['merchantid'];
    $clientkey = $params['clientkey'];
    $serverkey = $params['serverkey'];
    $environment = $params['environment'];
    $enable3ds = $params['enable3ds'];
    $enableInstallment = $params['enableInstallment'];
    $enableInstallmentOffline = $params['enableInstallmentOffline'];
    $installmentTerms = $params['installmentTerms'];
    $enableSaveCard = $params['enableSaveCard'];
    $whitelistBins = $params['whitelistBins'];
    $customExpiry = $params['customExpiry'];
    $enabledPayments = $params['enabledPayments'];
    $minimumInstallmentAmount = $params['minimumInstallmentAmount'];
    $enableSnapRedirect = $params['enableSnapRedirect'];
    $useInvoiceAmountAsPaid = $params['useInvoiceAmountAsPaid'];
    $tryToConvertCurrencyBack = $params['tryToConvertCurrencyBack'];

    // Invoice Parameters
    $invoiceId = $params['invoiceid'];
    $description = $params["description"];
    $amount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $returnUrl = $params['returnurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // Set VT config
    Veritrans_Config::$isProduction = ($environment == 'on') ? true : false;
    Veritrans_Config::$serverKey = $serverkey;
    // error_log($enable3ds); //debugan
    Veritrans_Config::$is3ds = ($enable3ds == 'on') ? true : false;
    Veritrans_Config::$isSanitized = true;

    // Build basic param
    $params = array(
        'transaction_details' => array(
            'order_id' => $invoiceId,
            'gross_amount' => ceil($amount),
      )
    );

    // Build customer details param
    $customer_details = array();
    $customer_details['first_name'] = $firstname;
    $customer_details['last_name'] = $lastname;
    $customer_details['email'] = $email;
    $customer_details['phone'] = $phone;

    $billing_address = array();
    $billing_address['first_name'] = $firstname;
    $billing_address['last_name'] = $lastname;
    $billing_address['address'] = $address1.$address2;
    $billing_address['city'] = $city;
    $billing_address['postal_code'] = $postcode;
    $billing_address['phone'] = $phone;
    // $billing_address['country_code'] = (strlen($this->convert_country_code($order->billing_country) != 3 ) ? 'IDN' : $this->convert_country_code($order->billing_country) );
    // error_log("===== country :".$country); //debugan
    $billing_address['country_code'] = (strlen($country) != 3 ) ? 'IDN' : $country;

    // Insert array to param
    $customer_details['billing_address'] = $billing_address;
    $params['customer_details'] = $customer_details;

    // build item details, there's only one item we can get from the WHMCS
    $item1 = array(
        'id' => 'a1',
        'price' => ceil($amount),
        'quantity' => 1,
        'name' => $description
    );
    $item_details = array ($item1);

    // Insert array to param
    $params['item_details'] = $item_details;
    // error_log("===== params :"); //debugan
    // error_log(print_r($params,true)); //debugan

    $params['callbacks'] = array('finish' => $returnUrl );

    // Build Installment param
    if($enableInstallment == 'on' && $amount >=  (int)$minimumInstallmentAmount){
        $terms = explode(',', $installmentTerms);
        $terms = array_map(function($e){return (int)$e;}, $terms);
        // Add installment param
        $params['credit_card']['installment']['required'] = false;
        $params['credit_card']['installment']['terms'] =
        array(
          'bri' => $terms,
          'danamon' => $terms,
          'maybank' => $terms,
          'bni' => $terms,
          'mandiri' => $terms,
          'bca' => $terms,
          'cimb' => $terms
        );
    }

    if ($enableInstallmentOffline == 'on' && $amount >= (int)$minimumInstallmentAmount) {
        $terms = explode(',', $installmentTerms);
        $terms = array_map(function($e){return (int)$e;}, $terms);
        // Add installment param
        $params['credit_card']['installment']['required'] = true;
        $params['credit_card']['installment']['terms'] = array( 'offline' => $terms );
    }

    if(strlen($whitelistBins)>1){
        $params['credit_card']['whitelist_bins'] = explode(',', $whitelistBins);
    }

    if(strlen($customExpiry)>1){
        $customExpiryParams = explode(" ",$customExpiry);
        $time = time();
        $time += 30; // add 30 seconds to allow margin of error
        $params['expiry'] = array(
            'start_time' => date("Y-m-d H:i:s O",$time),
            'unit' => $customExpiryParams[1],
            'duration'  => (int)$customExpiryParams[0],
        );
    }

    if(strlen($enabledPayments)>1){
        $enabledPaymentsParams = explode(",",$enabledPayments);
        $params['enabled_payments'] = $enabledPaymentsParams;
    }

    // Build one click / two click param
    if($enableSaveCard == 'on'){
        $params['user_id'] = crypt( $email.$phone , Veritrans_Config::$serverKey );
        $params['credit_card']['save_card'] = true;
    }
    
    // prefix-suffix string used for invoice.notes read/write
    // using a explicit prefix string, to prevent customer confusion, as the notes will also be visible even if other PG is selected, on invoice page.
    $midtransNotesSeparatorBegin = "#midtransPayUrl: ";
    $midtransNotesSeparatorEnd = " ###";
    // Regexp to extract value between Midtrans invoice notes begin & end separator
    $midtransNotesRegexPattern = "/$midtransNotesSeparatorBegin(.*?)$midtransNotesSeparatorEnd/";
        
    // Read invoice notes
    try {
        $whmcsApiCommand = 'GetInvoice';
        $whmcsApiPostParams = array(
            'invoiceid' => $invoiceId
        );
        $invoice = localAPI($whmcsApiCommand, $whmcsApiPostParams);
        $existingInvoiceNotes = $invoice["notes"];
    } catch (Exception $e) {
        $existingInvoiceNotes = "";
    }

    // Check if Snap URL found in invoices notes
    $redirectUrlFromNotes = null;
    if (strpos($existingInvoiceNotes, $midtransNotesSeparatorBegin) !== false) {
        // use Regexp to extract existing Snap Url value
        if ( preg_match($midtransNotesRegexPattern, $existingInvoiceNotes, $matches) ) {
            $redirectUrlFromNotes = $matches[1];
        }
    }

    /**
    * Currently Snap token allowed to be created each time invoice page triggers payment creation.
    * Useful in case merchant send invoice few days early, so that then payment UI will not be limited to 1 day Snap token expiry relative to when customer access invoice the 1st time. 
    * e.g. Customer can re-open invoice 7 days later, and new Snap token will be generated with renewed expiry (assuming the previous Snap token was not proceeded to `pending` state).
    * Though if Snap token was proceeded to `pending` state, the payment expiry will follow the pending expiry period. 
    * So, it is advisable for Merchant to extend their payment methods expiry config via Snap Preferences in Dashboard.
    **/
    
    // create snap token
    try {
        
        $snap_transaction = Veritrans_Snap::createTransaction($params);
        $snapToken = $snap_transaction->token;
        $redirect_url = $snap_transaction->redirect_url;

        // save the latest created snap redirect url to invoice notes
        try {
            $midtransNotesToWrite = $midtransNotesSeparatorBegin . $redirect_url . $midtransNotesSeparatorEnd;
            // if there's already midtrans notes
            if(isset($redirectUrlFromNotes)){
                // use regexp to replace the existing notes instead.
                $invoiceNotesToWrite = preg_replace($midtransNotesRegexPattern, $midtransNotesToWrite, $existingInvoiceNotes);
            } else {
                // no existing midtrans notes? append new midtrans notes at the end.
                $invoiceNotesToWrite = $existingInvoiceNotes . $midtransNotesToWrite;
            }
            
            $whmcsApiCommand = 'UpdateInvoice';
            $whmcsApiPostParams = array(
                'invoiceid' => $invoiceId,
                'notes' => $invoiceNotesToWrite
            );
            localAPI($whmcsApiCommand, $whmcsApiPostParams);
        } catch (Exception $e) {
            echo "error: unable to update invoice notes"; // @TODO: fix this line, this is not a propper way to display error
        }

    } catch (Exception $e) {
        // if Snap API return "Duplicated Order ID", it means the previous Snap token was proceeded to `pending` state. We can't re-create Snap token.
        if (preg_match('/utilized|digunakan/', $e->getMessage())) {
            // If there's already Snap URL in invoice notes
            if (isset($redirectUrlFromNotes)){
                // Display the Snap URL as instructions.
                return 'Invoice ID has been created on Midtrans previously. <a href="'. $redirectUrlFromNotes. '">Click here to visit the payment URL</a>, or try to check your email for the previous payment instruction details.';
            } else {
                // No Snap URL? Display generic payment instruction advise, prevent payment button from being shown.
                return 'Invoice ID has been created on Midtrans previously. Please try to check your email for the previous payment instruction details.';
            }
        } else {
            return "Midtrans payment module encountered unexpected error when requesting to Snap API. Error message:" . $e->getMessage();
        }
    }



    // ====================================== Html output for VT Web =======================
    $htmlOutput = '<form method="get" action="' . $redirect_url . '">';
    foreach ($postfields as $k => $v) {
        $htmlOutput .= '<input type="hidden" name="' . $k . '" value="' . urlencode($v) . '" />';
    }
    $htmlOutput .= '<input type="submit" value="' . $langPayNow . '" />';
    $htmlOutput .= '</form>';
    // =============================================== End of VT Web =======================

    if ($enableSnapRedirect == 'on'){
        return $htmlOutput;
    }


    // ====================================== Html output for SNAP ====================

    $htmlOutput1 = '';
    // JS script

    // Bogus form to disable auto submit / redirect
    $htmlOutput1 = '<form onsubmit="return false"></form>';

    // $htmlOutput1 .='
    // <script>
    // try {
    //     document.getElementById("frmPayment").setAttribute("id", "frmPayment-out");
    // } catch (e){
    //     console.log("failed to stop auto timer for WHMCS 6");
    // }
    // try {
    //     document.getElementById("submitfrm").setAttribute("id", "submitfrm-out");
    // } catch (e){
    //     console.log("failed to stop auto timer for WHMCS 5");
    // }
    // </script>
    // ';  // disable form auto submit

    // Change default WHMCS messaging and hide loading image.
    $htmlOutput1 .='
    <script>
    try {
        document.querySelector("[class*=\"alert alert-info text-center\"]").innerText = "Please Complete Your Payment";
    } catch (e){
        console.log("failed to change text for WHMCS 6");
    }
    try {
        document.querySelector("[class*=\"alert alert-block alert-warn textcenter\"]").innerText = "Please Complete Your Credit Card Payment :";
    } catch (e){
        console.log("failed to change text for WHMCS 5");
    }
    try{
        document.querySelector(\'[alt*="Loading"]\').style.display = "none";
    } catch(e){}
    </script>
    ';  // disable form auto submit


    $amount = ceil($amount);
    $environmenturl = Veritrans_Config::$isProduction ? "https://app.midtrans.com/snap/snap.js" : "https://app.sandbox.midtrans.com/snap/snap.js";
    $mixpanelkey = Veritrans_Config::$isProduction ? "17253088ed3a39b1e2bd2cbcfeca939a" : "9dcba9b440c831d517e8ff1beff40bd9";

    $htmlOutput1 .=  '
        <button class="submit-button" id="snap-pay">Proceed To Payment</button>
        <button class="submit-button" id="snap-instruction" style="display:none;">
            <a  target="_blank" href="#" id="instruction-button" title="View Payment Instruction">View Payment Instruction</a>
        </button>

        <!-- start Mixpanel -->
        <script data-cfasync="false" type="text/javascript">(function(e,a){if(!a.__SV){var b=window;try{var c,l,i,j=b.location,g=j.hash;c=function(a,b){return(l=a.match(RegExp(b+"=([^&]*)")))?l[1]:null};g&&c(g,"state")&&(i=JSON.parse(decodeURIComponent(c(g,"state"))),"mpeditor"===i.action&&(b.sessionStorage.setItem("_mpcehash",g),history.replaceState(i.desiredHash||"",e.title,j.pathname+j.search)))}catch(m){}var k,h;window.mixpanel=a;a._i=[];a.init=function(b,c,f){function e(b,a){var c=a.split(".");2==c.length&&(b=b[c[0]],a=c[1]);b[a]=function(){b.push([a].concat(Array.prototype.slice.call(arguments,0)))}}var d=a;"undefined"!==typeof f?d=a[f]=[]:f="mixpanel";d.people=d.people||[];d.toString=function(b){var a="mixpanel";"mixpanel"!==f&&(a+="."+f);b||(a+=" (stub)");return a};d.people.toString=function(){return d.toString(1)+".people (stub)"};k="disable time_event track track_pageview track_links track_forms register register_once alias unregister identify name_tag set_config reset people.set people.set_once people.increment people.append people.union people.track_charge people.clear_charges people.delete_user".split(" ");for(h=0;h<k.length;h++)e(d,k[h]);a._i.push([b,c,f])};a.__SV=1.2;b=e.createElement("script");b.type="text/javascript";b.async=!0;b.src="undefined"!==typeof MIXPANEL_CUSTOM_LIB_URL?MIXPANEL_CUSTOM_LIB_URL:"file:"===e.location.protocol&&"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js".match(/^\/\//)?"https://cdn.mxpnl.com/libs/mixpanel-2-latest.min.js":"//cdn.mxpnl.com/libs/mixpanel-2-latest.min.js";c=e.getElementsByTagName("script")[0];c.parentNode.insertBefore(b,c)}})(document,window.mixpanel||[]);mixpanel.init("'.$mixpanelkey.'");</script>
        <!-- end Mixpanel -->

        <script data-cfasync="false" src="'.$environmenturl.'" data-client-key="'.$clientkey.'"></script>
        <script data-cfasync="false" type="text/javascript">
        document.addEventListener("DOMContentLoaded", function(event) {
            function MixpanelTrackResult(token, merchant_id, cms_name, cms_version, plugin_name, plugin_version, status, result) {
                var eventNames = {
                    success: "pg-success",
                    pending: "pg-pending",
                    error: "pg-error",
                    close: "pg-close"
                };
                mixpanel.track(
                    eventNames[status], 
                    {
                        merchant_id: merchant_id,
                        cms_name: cms_name,
                        cms_version: cms_version,
                        plugin_name: plugin_name,
                        plugin_version: plugin_version,
                        snap_token: token,
                        payment_type: result ? result.payment_type: null,
                        order_id: result ? result.order_id: null,
                        status_code: result ? result.status_code: null,
                        gross_amount: result && result.gross_amount ? Number(result.gross_amount) : null,
                    }
                );
            }

            var SNAP_TOKEN = "'.$snapToken.'";
            var MERCHANT_ID = "'.$merchantid.'";
            var CMS_NAME = "whmcs";
            var CMS_VERSION = "'.$whmcsVersion.'";
            var PLUGIN_NAME = "whmcs"; 
            var PLUGIN_VERSION = "'.$pluginVersion.'"; 

            function fireSnap(){
                // record pay event to Mixpanel
                mixpanel.track(
                    "pg-pay", {
                        merchant_id: MERCHANT_ID,
                        cms_name: CMS_NAME,
                        cms_version: CMS_VERSION,
                        plugin_name: PLUGIN_NAME,
                        plugin_version: PLUGIN_VERSION,
                        snap_token: SNAP_TOKEN
                    }
                );
                snap.pay("'.$snapToken.'", {
                    onSuccess: function(result){
                        MixpanelTrackResult(SNAP_TOKEN, MERCHANT_ID, CMS_NAME, CMS_VERSION, PLUGIN_NAME, PLUGIN_VERSION, "success", result);
                        try{
                            document.getElementsByClassName("unpaid")[0].innerHTML = "Payment Completed!";
                        } catch (e){}
                        setTimeout(function(){
                            window.location = "'.$returnUrl.'";
                        },2000); 
                    },
                    onPending: function(result){
                        // window.location = "'.$returnUrl.'";
                        MixpanelTrackResult(SNAP_TOKEN, MERCHANT_ID, CMS_NAME, CMS_VERSION, PLUGIN_NAME, PLUGIN_VERSION, "pending", result);
                        try{
                            if(result.pdf_url){
                                document.getElementById("instruction-button").href = result.pdf_url;
                                document.getElementById("snap-instruction").style.display = "block";
                            } else {
                                // some payment doesnt have pdf url, put no link.
                            }
                            document.getElementById("snap-pay").style.display = "none";
                            document.getElementsByClassName("unpaid")[0].innerHTML = "Awaiting Payment";
                        } catch (e){}
                    },
                    onError: function(result){
                        MixpanelTrackResult(SNAP_TOKEN, MERCHANT_ID, CMS_NAME, CMS_VERSION, PLUGIN_NAME, PLUGIN_VERSION, "error", result);
                        window.location = "'.$returnUrl.'";
                    },
                    onClose: function(){
                        MixpanelTrackResult(SNAP_TOKEN, MERCHANT_ID, CMS_NAME, CMS_VERSION, PLUGIN_NAME, PLUGIN_VERSION, "close", null);
                    }
                });
            }; 
            
            document.getElementById("snap-pay").onclick = function(){
                fireSnap();
            };

            // Auto trigger SNAP
            setTimeout(function(){
                fireSnap();
            },500); 
        });
        </script>

    ';

    $htmlOutput1 .= '';

    return $htmlOutput1;
}

/**
 * Refund transaction.
 *
 * Called when a refund is requested for a previously successful transaction.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return array Transaction response status
 */

/** ## Method not supported on Veritrans
function veritrans_refund($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Transaction Parameters
    $transactionIdToRefund = $params['transid'];
    $refundAmount = $params['amount'];
    $currencyCode = $params['currency'];

    // Client Parameters
    $firstname = $params['clientdetails']['firstname'];
    $lastname = $params['clientdetails']['lastname'];
    $email = $params['clientdetails']['email'];
    $address1 = $params['clientdetails']['address1'];
    $address2 = $params['clientdetails']['address2'];
    $city = $params['clientdetails']['city'];
    $state = $params['clientdetails']['state'];
    $postcode = $params['clientdetails']['postcode'];
    $country = $params['clientdetails']['country'];
    $phone = $params['clientdetails']['phonenumber'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to initiate refund and interpret result

    return array(
        // 'success' if successful, otherwise 'declined', 'error' for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
        // Unique Transaction ID for the refund transaction
        'transid' => $refundTransactionId,
        // Optional fee amount for the fee value refunded
        'fees' => $feeAmount,
    );
}
*/

/**
 * Cancel subscription.
 *
 * If the payment gateway creates subscriptions and stores the subscription
 * ID in tblhosting.subscriptionid, this function is called upon cancellation
 * or request by an admin user.
 *
 * @param array $params Payment Gateway Module Parameters
 *
 * @see http://docs.whmcs.com/Payment_Gateway_Module_Parameters
 *
 * @return array Transaction response status
 */

/** ## Method
function veritrans_cancelSubscription($params)
{
    // Gateway Configuration Parameters
    $accountId = $params['accountID'];
    $secretKey = $params['secretKey'];
    $testMode = $params['testMode'];
    $dropdownField = $params['dropdownField'];
    $radioField = $params['radioField'];
    $textareaField = $params['textareaField'];

    // Subscription Parameters
    $subscriptionIdToCancel = $params['subscriptionID'];

    // System Parameters
    $companyName = $params['companyname'];
    $systemUrl = $params['systemurl'];
    $langPayNow = $params['langpaynow'];
    $moduleDisplayName = $params['name'];
    $moduleName = $params['paymentmethod'];
    $whmcsVersion = $params['whmcsVersion'];

    // perform API call to cancel subscription and interpret result

    return array(
        // 'success' if successful, any other value for failure
        'status' => 'success',
        // Data to be recorded in the gateway log - can be a string or array
        'rawdata' => $responseData,
    );
}
*/
