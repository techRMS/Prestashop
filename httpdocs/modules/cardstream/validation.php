<?php

include(dirname(__FILE__). '/../../config/config.inc.php');
include(dirname(__FILE__). '/../../init.php');
include(dirname(__FILE__). '/cardstream.php');
global $smarty;
global $cart;

$cart = new Cart($_POST['orderRef']);
        if (!Validate::isLoadedObject($cart))
        {
                exit;
        }
$cart = new Cart($_POST['orderRef']);

$customer = new Customer($cart->id_customer);


if (isset($_POST['signature'])) {
    $check = $_POST;
    unset($check['signature']);
    $sig_check = ($_POST['signature'] == createSignature($check, Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')));
}else{
    $sig_check = true;
}

if ($_POST['responseCode'] != 0 || !$sig_check){

	$cardstream = new cardstream();
	$cardstream->validateOrder((int)$cart->id, _PS_OS_ERROR_, $_POST['transactionUnique'], $cardstream->displayName, $message);
	
	Tools::redirect('order-confirmation.php?id_module='.(int)$cardstream->id.'&id_cart='.(int)$cart->id.'&key='.$customer->secure_key);
}else{
	
	$cardstream = new cardstream();

	if($_POST['amountReceived'] == number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '', '')){

		$cardstream->validateOrder((int)$cart->id, _PS_OS_PAYMENT_, ($_POST['amountReceived']/100), $cardstream->displayName, null, array(), NULL, false, $cart->secure_key);
	}else{

		$cardstream->validateOrder((int)$cart->id, _PS_OS_ERROR_, $_POST['transactionUnique'], $cardstream->displayName, $message);
	}


	Tools::redirect('order-confirmation.php?id_module='.(int)$cardstream->id.'&id_cart='.(int)$cart->id.'&key='.$customer->secure_key);
}


function createSignature(array $data, $key, $algo = null) {

	if ($algo === null) {
		$algo = 'SHA512';
	}
	
	ksort($data);

	// Create the URL encoded signature string
	$ret = http_build_query($data, '', '&');

	// Normalise all line endings (CRNL|NLCR|NL|CR) to just NL (%0A)
	$ret = preg_replace('/%0D%0A|%0A%0D|%0A|%0D/i', '%0A', $ret);
	
	// Hash the signature string and the key together
	$ret = hash($algo, $ret . $key);

	// Prefix the algorithm if not the default
	if ($algo !== 'SHA512') {
		$ret = '{' . $algo . '}' . $ret;
	}

	return $ret;	
}
