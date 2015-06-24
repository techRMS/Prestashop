<p class="payment_module">

<form id="aut" name="cardstreamform" action="https://gateway.cardstream.com/hosted/" method="post">
		<span style="display: block;padding: 0.6em;text-decoration: none;margin-left: 0.7em;">

						<div id="aut2">
							<img src="{$this_path_cardstream}logo.gif"/>
							{foreach from=$p key=k item=v}
							{if $k == 'customerAddress'}
							<textarea style="display:none;" name="{$k}">{$v}</textarea>
							{else}
							<input type="hidden" name="{$k}" value="{$v}"/>
							{/if}
							{/foreach}

							<a href="javascript:document.cardstreamform.submit();">{$frontend}</p>
</div>
</span>
</form>
</p>


