{*
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
*
*
*  @author Paul Lashbrook <support@cardstream.com>
*  @copyright  2015 Cardstream Ltd
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}
<div class="row">
    <div class="col-xs-12">
        <p class="payment_module">
            <a class="cardstream" href="{$link->getModuleLink('cardstream', 'payment')|escape:'html':'UTF-8'}"
               title="{l s='Pay by '|cat:$frontend mod='cardstream'}">
                {l s='Pay by '|cat:$frontend mod='cardstream'}
            </a>
        </p>
    </div>
</div>


