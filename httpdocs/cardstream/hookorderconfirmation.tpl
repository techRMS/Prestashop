<h2>Test</h2>
{if $status == 'ok'}
	<p>{l s='Your order on' mod='cardstream'} <span class="bold">{$shop_name}</span> {l s='is complete.' mod='cardstream'}
		<br /><br /><span class="bold">{l s='Your order will be sent as soon as possible.' mod='cardstream'}</span>
		<br /><br />{l s='For any questions or for further information, please contact our' mod='cardstream'} <a href="{$base_dir_ssl}contact-form.php">{l s='customer support' mod='cardstream'}</a>.
	</p>
{else}
	<p class="warning">
		Unfortunately payment has failed for your order. Please recomplete the checkout process.
	</p>
{/if}
