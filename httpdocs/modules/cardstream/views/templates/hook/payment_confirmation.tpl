{if $status == 'ok'}
    <h2>Payment Success</h2>
    <p>{l s='Your order on' mod='cardstream'} <span class="bold">{$shop_name|escape:'htmlall':'UTF-8'}</span> {l s='is complete.' mod='cardstream'}
        <br/><br/><span class="bold">{l s='Your order will be sent as soon as possible.' mod='cardstream'}</span>
        <br/><br/>{l s='For any questions or for further information, please contact our' mod='cardstream'}
        {l s='customer support' mod='cardstream'}.
    </p>
{else}
    <h2>Payment Error</h2>
    <p class="warning">
        {l s='Unfortunately payment has failed for your order. Please recomplete the checkout process.' mod='cardstream'}
    </p>
{/if}
