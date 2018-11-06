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

if (!defined("WHMCS")) {
    die("This file cannot be accessed directly");
}

function veritranspromo_MetaData()
{
    return array(
        'DisplayName' => 'Midtrans Promo Additional Payment Gateway Module',
        'APIVersion' => '1.1', // Use API Version 1.1
        'DisableLocalCredtCardInput' => true,
        'TokenisedStorage' => true,
    );
}
function veritranspromo_config()
{
    // Just copy things from veritrans.php and change the name
    require_once(dirname(__FILE__) . '/veritrans.php');
    $config = veritrans_config();
    $config['FriendlyName']['Value']='Midtrans Additional Payment';
    return $config;
}
function veritranspromo_link($params)
{
    require_once(dirname(__FILE__) . '/veritrans.php');
    return veritrans_link($params);
}
