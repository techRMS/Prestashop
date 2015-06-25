<?php
/**
* 2007-2014 PrestaShop
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
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
*  @author PrestaShop SA <contact@prestashop.com>
*  @copyright  2007-2014 PrestaShop SA
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*  International Registered Trademark & Property of PrestaShop SA
*/

/**
 * @since 1.5.0
 * @uses  ModuleFrontControllerCore
 */

class CardstreamPaymentModuleFrontController extends ModuleFrontController
{
    public $ssl = true;

    /**
     * @see FrontController::initContent()
     */
    public function initContent()
    {
        $this->display_column_right = false;
        $this->display_column_left = false;

        parent::initContent();

        $invoiceAddress = new Address((int)$this->context->cart->id_address_invoice);

        $currency = new Currency((int)( $this->context->cart->id_currency ));

        $customer = new Customer($this->context->cart->id_customer);

        $form = array(
            'merchantID'        => Configuration::get('CARDSTREAM_MERCHANT_ID'),
            'currencyCode'      => is_numeric(Configuration::get('CARDSTREAM_CURRENCY_ID')) ? Configuration::get(
                'CARDSTREAM_CURRENCY_ID'
            ) : $currency->iso_code_num,
            'countryCode'       => Configuration::get('CARDSTREAM_COUNTRY_ID'),
            'action'            => "SALE",
            'type'              => 1,
            'orderRef'          => $this->context->cart->id,
            'transactionUnique' => (int)( $this->context->cart->id ) . '_' . date('YmdHis') . '_' .
                                   $this->context->cart->secure_key,
            'amount'            => number_format($this->context->cart->getOrderTotal(), 2, '', ''),
           /* 'redirectURL'       =>
                Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'module/'.$this->module->name . '/return',*/
            'callbackURL'       =>
                Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . "module/" . $this->module->name .
                "/validation",
            'redirectURL'       =>
                Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key,
            'customerName'      => $invoiceAddress->firstname . ' ' . $invoiceAddress->lastname,
            'customerAddress'   => trim($invoiceAddress->address1) . "\n" . trim($invoiceAddress->address2) . "\n" .
                                   trim($invoiceAddress->city),
            'customerPostcode'  => $invoiceAddress->postcode,
            'merchantData'      => "PrestaShop " . $this->module->name . ' ' . $this->module->version,
            'customerPhone'     => empty( $invoiceAddress->phone ) ? $invoiceAddress->phone_mobile : $invoiceAddress->phone
        );

        // fix for prestashop CCC
        foreach ($form as &$value) {
            $value = trim($value);
        }

        if (Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')) {
            $form['signature'] = $this->module->createCardstreamSignature(
                $form,
                Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')
            );
        }


        $this->context->smarty->assign(
            array(
                'p'                    => $form,
                'frontend'             => Configuration::get('CARDSTREAM_FRONTEND'),
                'this_path'            => $this->module->getPathUri(),
                'this_path_cardstream' => $this->module->getPathUri(),
                'this_path_ssl'        =>
                    Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ . 'modules/' . $this->module->name . '/'
            )
        );

        $this->setTemplate('form_render.tpl');
    }
}
