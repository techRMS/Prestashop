<?php
/**
 * 2015 Cardstream
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 *  @author    Paul Lashbrook <support@cardstream.com>
 *  @copyright 2015 Cardstream Ltd
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 */

class Cardstream extends PaymentModule
{
    public function __construct()
    {
        $this->bootstrap   = true;
        $this->name        = 'cardstream';
        $this->tab         = 'payments_gateways';
        $this->version     = '2.0.0';
        $this->author      = 'CardStream';
        $this->controllers = array( 'payment', 'validation' );

        parent::__construct();

        $this->displayName            = 'CardStream Hosted Form';
        $this->description            = $this->l('Process payments with CardStream');
        $this->ps_versions_compliancy = array( 'min' => '1.5', 'max' => _PS_VERSION_ );
    }

    /*
     * install registers hooks now
     */
    public function install()
    {
        // if prestashop multi store is active, change context to global
        if (Shop::isFeatureActive()) {
            Shop::setContext(Shop::CONTEXT_ALL);
        }

        if (!parent::install() || !$this->registerHook('displayPayment') ||
            !$this->registerHook('displayPaymentReturn') || !$this->registerHook('header')
        ) {
            return false;
        }

        return true;

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


        if ($params['objOrder']->module != $this->name) {
            return "";
        }

        if ($params['objOrder']->getCurrentState() != _PS_OS_ERROR_) {
            $this->smarty->assign(
                array(
                    'status'   => 'ok',
                    'id_order' => (int)$params['objOrder']->id
                )
            );
        } else {
            $this->smarty->assign('status', 'failed');
        }

        return $this->display(__FILE__, 'hookorderconfirmation.tpl');
    }

    public function getContent()
    {

        $output = null;

        if (Tools::isSubmit('submit' . $this->name)) {
            Configuration::updateValue('CARDSTREAM_MERCHANT_ID', Tools::getvalue('cardstream_merchant_id'));
            Configuration::updateValue('CARDSTREAM_CURRENCY_ID', Tools::getvalue('cardstream_currency_id'));
            Configuration::updateValue('CARDSTREAM_COUNTRY_ID', Tools::getvalue('cardstream_country_id'));
            Configuration::updateValue('CARDSTREAM_FRONTEND', Tools::getvalue('cardstream_frontend'));
            Configuration::updateValue('CARDSTREAM_MERCHANT_PASSPHRASE', Tools::getvalue('cardstream_passphrase'));
            $output .= $this->displayConfirmation($this->l('Settings updated'));

        }

        return $output . $this->displayForm();
    }

    public function displayForm()
    {
        // Get default language
        $default_lang = (int)Configuration::get('PS_LANG_DEFAULT');
        $fields_form  = array();
        // Init Fields form array
        $fields_form[0]['form'] = array(
            'legend' => array(
                'title' => $this->l('Cardstream Settings'),
            ),
            'input'  => array(
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Merchant ID'),
                    'name'     => 'cardstream_merchant_id',
                    'class'    => 'fixed-width-md',
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Currency Code'),
                    'name'     => 'cardstream_currency_id',
                    'class'    => 'fixed-width-xs',
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Country Code'),
                    'name'     => 'cardstream_country_id',
                    'class'    => 'fixed-width-xs',
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Passphrase / Shared Secret'),
                    'name'     => 'cardstream_passphrase',
                    'class'    => 'fixed-width-xl',
                    'required' => true
                ),
                array(
                    'type'     => 'text',
                    'label'    => $this->l('Frontend Text'),
                    'name'     => 'cardstream_frontend',
                    'class'    => 'fixed-width-xl',
                    'required' => true
                )
            ),
            'submit' => array(
                'title' => $this->l('Save'),
                'class' => 'button'
            )
        );


        $helper = new HelperFormCore();

        // Module, token and currentIndex
        $helper->module          = $this;
        $helper->name_controller = $this->name;
        $helper->token           = Tools::getAdminTokenLite('AdminModules');
        $helper->currentIndex    = AdminController::$currentIndex . '&configure=' . $this->name;

        // Language
        $helper->default_form_language    = $default_lang;
        $helper->allow_employee_form_lang = $default_lang;

        // Title and toolbar
        $helper->title          = $this->displayName;
        $helper->show_toolbar   = true; // false -> remove toolbar
        $helper->toolbar_scroll = true; // yes - > Toolbar is always visible on the top of the screen.
        $helper->submit_action  = 'submit' . $this->name;
        $helper->toolbar_btn    = array(
            'save' =>
                array(
                    'desc' => $this->l('Save'),
                    'href' => AdminController::$currentIndex . '&configure=' . $this->name . '&save' . $this->name .
                              '&token=' . Tools::getAdminTokenLite('AdminModules'),
                ),
            'back' => array(
                'href' => AdminController::$currentIndex . '&token=' . Tools::getAdminTokenLite('AdminModules'),
                'desc' => $this->l('Back to list')
            )
        );

        // Load current values
        $helper->fields_value['cardstream_merchant_id'] = Configuration::get('CARDSTREAM_MERCHANT_ID');
        $helper->fields_value['cardstream_currency_id'] = Configuration::get('CARDSTREAM_CURRENCY_ID');
        $helper->fields_value['cardstream_country_id']  = Configuration::get('CARDSTREAM_COUNTRY_ID');
        $helper->fields_value['cardstream_passphrase']  = Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE');
        $helper->fields_value['cardstream_frontend']    = Configuration::get('CARDSTREAM_FRONTEND');

        return $helper->generateForm($fields_form);
    }

    public function hookPayment($params)
    {

        if (!$this->active) {
            return;
        }

        $this->smarty->assign(
            array(
                'frontend'      => Configuration::get('CARDSTREAM_FRONTEND'),
                'this_path'     => $this->_path,
                'this_path_bw'  => $this->_path,
                'this_path_ssl' => Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->name .
                                   '/'
            )
        );

        return $this->display(__FILE__, 'payment.tpl');

    }

    public function hookPaymentReturn($params)
    {

        if (!$this->active) {
            return;
        }

        if ($params['objOrder']->module != $this->name) {
            return "";
        }

        if ($params['objOrder']->getCurrentState() != _PS_OS_ERROR_) {
            $this->smarty->assign(
                array(
                    'status'   => 'ok',
                    'id_order' => (int)$params['objOrder']->id
                )
            );
        } else {
            $this->smarty->assign('status', 'failed');
        }

        return $this->display(__FILE__, 'payment_confirmation.tpl');
    }

    /**
     * adds our css file to the header
     */
    public function hookHeader()
    {
        if (Configuration::get('PS_CATALOG_MODE')) {
            return;
        }

        $this->context->controller->addCSS(( $this->_path ) . 'views/css/cardstream.css', 'all');

    }

    public function createCardstreamSignature(array $data, $key, $algo = null)
    {

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
}
