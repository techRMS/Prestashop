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
*  @author     Paul Lashbrook <support@cardstream.com>
*  @copyright  2015 Cardstream Ltd
*  @license    http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
*}

{capture name=path}
<a href="{$link->getPageLink('order', true, NULL, " step=3")|escape:'html':'UTF-8'}"
   title="{l s='Go back to the Checkout' mod='cardstream'}">{l s='Checkout' mod='cardstream'}</a><span
    class="navigation-pipe">{$navigationPipe|escape:'html':'UTF-8'}</span>{l s= $frontend|cat:' payment' mod='cardstream'}
{/capture}


<h1 class="page-heading">{l s='Payment Confirmation' mod='cardstream'}<p class="cart_navigation clearfix pull-right"
                                                                         id="cart_navigation" style="margin:unset;">
        <a class="button-exclusive btn btn-default"
           href="{$link->getPageLink('order', true, NULL, ' step=3')|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='cardstream'}
        </a>
    </p></h1>


{assign var='current_step' value='payment'}
{include file="$tpl_dir./order-steps.tpl"}
<!--target="cardstreamFrame" -->
<form id="cardstreamform" action="https://gateway.cardstream.com/hosted/" method="post">

    <div class="box cheque-box">
        <h3 class="page-subheading">
            {l s= $frontend|cat:' Payment' mod='cardstream'}
        </h3>

        <p class="cheque-indent">
            <strong class="dark">
                {l s='Clicking "I confirm my order" will take you to the Cardstream secure payment website'
                mod='cardstream'}
            </strong>
        </p>

        <!-- <p class="cheque-indent">
             <iFrame
                 style="background-color:#ffffff; text-align: center;height: 1050px; display: block; width: 100%; border: 0; margin: 20px auto 0;"
                 src="" name="cardstreamFrame" id="cardstreamFrame"
                 onload="scroll(0,0);"></iFrame>

         </p>-->
    </div>

    {foreach from=$p key=k item=v}
    {if $k == 'customerAddress'}
    <textarea style="display:none;" name="{$k|escape:'html':'UTF-8'}">{$v|escape:'html':'UTF-8'}</textarea>
    {else}
    <input type="hidden" name="{$k|escape:'html':'UTF-8'}" value="{$v|escape:'html':'UTF-8'}"/>
    {/if}
    {/foreach}

    <p class="cart_navigation clearfix" id="cart_navigation">
        <a class="button-exclusive btn btn-default" href="{$link->getPageLink('order', true, NULL, "
           step=3")|escape:'html':'UTF-8'}">
            <i class="icon-chevron-left"></i>{l s='Other payment methods' mod='cardstream'}
        </a>
        <button class="button btn btn-default button-medium" type="submit">
            <span>{l s='I confirm my order' mod='cardstream'}<i class="icon-chevron-right right"></i></span>
        </button>
    </p>
</form>

<!--
<script>
    window.onload = function () {
        document.getElementById('cardstreamform').submit();
    }
</script>-->

