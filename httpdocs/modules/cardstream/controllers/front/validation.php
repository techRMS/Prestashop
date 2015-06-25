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
 */
class CardstreamValidationModuleFrontController extends ModuleFrontController
{


    public function postProcess()
    {


        $cart = new Cart((int)Tools::getValue('orderRef'));
        if (!Validate::isLoadedObject($cart)) {
            exit;
        }

        if (Tools::getValue('signature')) {
            $check = $_POST;

            unset( $check['signature'] );
            $sig_check =
                ( $_POST['signature'] ==
                  $this->module->createCardstreamSignature($check, Configuration::get('CARDSTREAM_MERCHANT_PASSPHRASE')) );
        } else {
            $sig_check = true;
        }

        if ($_POST['responseCode'] != 0 || !$sig_check) {


            $this->module->validateOrder(
                (int)$cart->id, _PS_OS_ERROR_, $_POST['transactionUnique'], $this->module->displayName,
                $this->module->l('Payment Error')
            );

        } else {



            if ($_POST['amountReceived'] == number_format($cart->getOrderTotal(true, Cart::BOTH), 2, '', '')) {

                $this->module->validateOrder(
                    (int)$cart->id, _PS_OS_PAYMENT_, ( $_POST['amountReceived'] / 100 ),
                    $this->module->displayName, $this->module->l('Payment Accepted, signature matched processed via callback '), array(), null, false, $cart->secure_key
                );
            } else {

                $this->module->validateOrder(
                    (int)$cart->id, _PS_OS_ERROR_, $_POST['transactionUnique'],
                    $this->module->displayName, $this->module->l('Payment amount miss match')
                );
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
