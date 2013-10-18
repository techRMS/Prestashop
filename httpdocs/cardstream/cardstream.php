<?php

class cardstream extends PaymentModule
{
    public function __construct()
    {
        $this->name = 'cardstream';
        $this->tab = 'payments_gateways';
        $this->version = '1.0';
        $this->author = 'CardStream';

        parent::__construct();

        $this->displayName = 'CardStream Hosted Form';
        $this->description = $this->l('Process payments with CardStream');
    }

    public function install()
    {
        return (parent::install());
    }

    public function uninstall()
    {
        Configuration::deleteByName('CARDSTREAM_MERCHANT_ID');
        Configuration::deleteByName('CARDSTREAM_CURRENCY_ID');
        Configuration::deleteByName('CARDSTREAM_COUNTRY_ID');
        Configuration::deleteByName('CARDSTREAM_FRONTEND');
        Configuration::deleteByName('CARDSTREAM_MERCHANT_PASSPHRASE');

        return parent::uninstall();
    }

    public function hookOrderConfirmation($params)
    {
        global $smarty;

        if ($params['objOrder']->module != $this->name)
            return "";

        if ($params['objOrder']->getCurrentState() != _PS_OS_ERROR_)
            $smarty->assign(array('status' => 'ok', 'id_order' => intval($params['objOrder']->id)));
        else
            $smarty->assign('status', 'failed');

        return $this->display(__FILE__, 'hookorderconfirmation.tpl');
    }

    public function getContent()
    {
        if (Tools::isSubmit('submitModule')) {
            Configuration::updateValue('CARDSTREAM_MERCHANT_ID', Tools::getvalue('cardstream_merchant_id'));
            Configuration::updateValue('CARDSTREAM_CURRENCY_ID', Tools::getvalue('cardstream_currency_id'));
            Configuration::updateValue('CARDSTREAM_COUNTRY_ID', Tools::getvalue('cardstream_country_id'));
            Configuration::updateValue('CARDSTREAM_FRONTEND', Tools::getvalue('cardstream_frontend'));
            Configuration::updateValue('CARDSTREAM_MERCHANT_PASSPHRASE', Tools::getvalue('cardstream_passphrase'));

            echo $this->displayConfirmation($this->l('Configuration updated'));
        }

        return '
		<h2>' . $this->displayName . '</h2>
		<form action="' . Tools::htmlentitiesutf8($_SERVER['REQUEST_URI']) . '" method="post">
			<fieldset class="width2">
				<legend><img src="../img/admin/contact.gif" alt="" />' . $this->l('Settings') . '</legend>
				<label for="cardstream_merchant_id">' . $this->l('Merchant ID') . '</label>
				<div class="margin-form"><input type="text" size="20" id="cardstream_merchant_id" name="cardstream_merchant_id" value="' . Configuration::get('CARDSTREAM_MERCHANT_ID') . '" /></div>
				<label for="cardstream_currency_id">' . $this->l('Currency Code') . '</label>
				<div class="margin-form"><input type="text" size="20" id="cardstream_currency_id" name="cardstream_currency_id" value="' . Configuration::get('CARDSTREAM_CURRENCY_ID') . '" /></div>
				<label for="cardstream_country_id">' . $this->l('Country ID') . '</label>
                                <div class="margin-form"><input type="text" size="20" id="cardstream_country_id" name="cardstream_country_id" value="' . Configuration::get('CARDSTREAM_COUNTRY_ID') . '" /></div>
				<label for="cardstream_passphrase">' . $this->l('Passphrase') . '</label>
                                <div class="margin-form"><input type="text" size="20" id="cardstream_passphrase" name="cardstream_passphrase" value="' . Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE') . '" /></div>
				<label for="cardstream_frontend">' . $this->l('Frontend') . '</label>
                                <div class="margin-form"><input type="text" size="20" id="cardstream_frontend" name="cardstream_frontend" value="' . Configuration::get('CARDSTREAM_FRONTEND') . '" /></div>
				<br /><center><input type="submit" name="submitModule" value="' . $this->l('Update settings') . '" class="button" /></center>
			</fieldset>
		</form>';
    }

    public function hookPayment($params)
    {
        global $smarty;

        $invoiceAddress = new Address((int)$params['cart']->id_address_invoice);


        $cardstreamparams = array();
        $cardstreamparams['merchantID'] = Configuration::get('CARDSTREAM_MERCHANT_ID');
        $cardstreamparams['merchantPwd'] = Configuration::get('CARDSTREAM_MERCHANT_PWD');
        $cardstreamparams['currencyCode'] = Configuration::get('CARDSTREAM_CURRENCY_ID');
        $cardstreamparams['countryCode'] = Configuration::get('CARDSTREAM_COUNTRY_ID');
        $cardstreamparams['action'] = "SALE";
        $cardstreamparams['type'] = 1;
        $cardstreamparams['orderRef'] = $params['cart']->id;
        $cardstreamparams['transactionUnique'] = (int)($params['cart']->id) . '_' . date('YmdHis') . '_' . $params['cart']->secure_key;
        $cardstreamparams['amount'] = number_format($params['cart']->getOrderTotal(), 2, '', '');

        $cardstreamparams['redirectURL'] = Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . "modules/" . $this->name . "/validation.php";
        $cardstreamparams['customerName'] = $invoiceAddress->firstname . ' ' . $invoiceAddress->lastname;
        $cardstreamparams['customerAddress'] = $invoiceAddress->address1 . "\n" . $invoiceAddress->address2 . "\n" . $invoiceAddress->city;
        $cardstreamparams['customerPostCode'] = $invoiceAddress->postcode;
        $cardstreamparams['merchantData'] = "PrestaShop " . $this->name . ' ' . $this->version;
        $cardstreamparams['customerPhone'] = empty($invoiceAddress->phone) ? $invoiceAddress->phone_mobile : $invoiceAddress->phone;

        if (Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')) {
            $sig_fields = http_build_query($cardstreamparams) . Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE');
            $cardstreamparams['signature'] = hash('SHA512', $sig_fields);
        }

        $smarty->assign('p', $cardstreamparams);
       // $smarty->assign('isFailed', $isFailed);
        $smarty->assign('frontend', Configuration::get('CARDSTREAM_FRONTEND'));

        $this->smarty->assign(array(
            'this_path' => $this->_path,
            'this_path_cardstream' => $this->_path,
            'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name . '/'));

        return $this->display(__FILE__, 'cardstream.tpl');
    }

    public function hookPaymentReturn($params)
    {
        if (!$this->active)
            return;

        global $smarty;

        if ($params['objOrder']->module != $this->name) {
            return "";
        }

        if ($params['objOrder']->getCurrentState() != _PS_OS_ERROR_) {
            $smarty->assign(array('status' => 'ok', 'id_order' => intval($params['objOrder']->id)));
        } else {
            $smarty->assign('status', 'failed');
        }
        return $this->display(__FILE__, 'hookorderconfirmation.tpl');
    }

}

?>
