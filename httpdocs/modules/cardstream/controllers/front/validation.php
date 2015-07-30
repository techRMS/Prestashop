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

/**
 * @since 1.5.0
 */
class CardstreamValidationModuleFrontController extends ModuleFrontController
{

    private $return_fields = array(
        'merchantID'               => null,
        'transactionID'            => null,
        'previousID'               => null,
        'responseCode'             => null,
        'responseMessage'          => null,
        'responseStatus'           => null,
        'xref'                     => null,
        'state'                    => null,
        'timestamp'                => null,
        'amountReceived'           => null,
        'amountRefunded'           => null,
        'authorisationCode'        => null,
        'referralPhone'            => null,
        'avscv2CheckEnabled'       => null,
        'avscv2CheckRequired'      => null,
        'avscv2ResponseCode'       => null,
        'avscv2ResponseMessage'    => null,
        'avscv2AuthEntity'         => null,
        'cv2Check'                 => null,
        'cv2CheckPref'             => null,
        'addressCheck'             => null,
        'addressCheckPref'         => null,
        'postcodeCheck'            => null,
        'postcodeCheckPref'        => null,
        'cardCVVMandatory'         => null,
        'cardNumberMask'           => null,
        'cardType'                 => null,
        'cardTypeCode'             => null,
        'cardScheme'               => null,
        'cardSchemeCode'           => null,
        'cardIssuer'               => null,
        'cardIssuerCountry'        => null,
        'cardIssuerCountryCode'    => null,
        'threeDSResponseCode'      => null,
        'threeDSResponseMessage'   => null,
        'threeDSEnabled'           => null,
        'threeDSRequired'          => null,
        'threeDSEnrolled'          => null,
        'threeDSAuthenticated'     => null,
        'threeDSPaReq'             => null,
        'threeDSACSURL'            => null,
        'threeDSECI'               => null,
        'threeDSCAVV'              => null,
        'threeDSCAVVAlgorithm'     => null,
        'threeDSXID'               => null,
        'threeDSMerchantPref'      => null,
        'threeDSVETimestamp'       => null,
        'threeDSCATimestamp'       => null,
        'threeDSCheck'             => null,
        'threeDSCheckPref'         => null,
        'remoteAddress'            => null,
        'notifyEmail'              => null,
        'customerReceiptsRequired' => null,
        // eReceipt details fields
        'eReceiptsEnabled'         => null,
        'eReceiptsRequired'        => null,
        'eReceiptsStoreID'         => null,
        'eReceiptsCustomerRef'     => null,
        'eReceiptsReceiptRef'      => null,
        'eReceiptsReceiptData'     => null,
        'eReceiptsResponseCode'    => null,
        'eReceiptsResponseMessage' => null,
        // Merchant ID routing
        'requestMerchantID'        => null,
        'processMerchantID'        => null,


    );

    public function postProcess()
    {


        $cart = new Cart((int)Tools::getValue('orderRef'));
        if (!Validate::isLoadedObject($cart)) {
            exit;
        }
        $check = $_POST;

        unset( $check['signature'] );
        $sig_check =
            ( Tools::getValue('signature') ==
              $this->module->createCardstreamSignature($check, Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')) );

        if (!$sig_check) {
            $this->module->validateOrder(
                (int)$cart->id,
                _PS_OS_ERROR_,
                Tools::getValue('transactionUnique'),
                $this->module->displayName,
                $this->module->l('Payment Signature')
            );
            Tools::redirectLink(Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ .'index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
        }

        if (Tools::getValue('responseCode') != 0) {

            $this->module->validateOrder(
                (int)$cart->id,
                _PS_OS_ERROR_,
                Tools::getValue('transactionUnique'),
                $this->module->displayName,
                $this->module->l('Payment Error')
            );
            Tools::redirectLink(Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ .'index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);

        } else {


            if (Tools::getValue('amountReceived') == number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '', '')) {

                $this->module->validateOrder(
                    (int)$cart->id,
                    _PS_OS_PAYMENT_,
                    ( Tools::getValue('amountReceived') / 100 ),
                    $this->module->displayName,
                    $this->module->l('Payment Accepted, signature matched processed via callback '),
                    array(),
                    null,
                    false,
                    $cart->secure_key
                );
                Tools::redirectLink(Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ .'index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
            } else {

                $this->module->validateOrder(
                    (int)$cart->id,
                    _PS_OS_ERROR_,
                    Tools::getValue('transactionUnique'),
                    $this->module->displayName,
                    $this->module->l('Payment amount miss match')
                );
                Tools::redirectLink(Tools::getShopDomainSsl(true, true) . __PS_BASE_URI__ .'index.php?controller=order-confirmation&id_cart='.$this->context->cart->id.'&id_module='.$this->module->id.'&id_order='.$this->module->currentOrder.'&key='.$customer->secure_key);
            }

        }

        die();

    }

    public function createSignature(array $data, $key, $algo = null)
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
