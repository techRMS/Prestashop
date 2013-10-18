
<p class="payment_module" >

	<form id="aut" name="charityclearform" action="https://gateway.cardstream.com/hosted/" method="post">
		<span style="display: block;padding: 0.6em;text-decoration: none;margin-left: 0.7em;">

						<div id="aut2">
                            <img src="{$this_path_cardstream}logo.gif" />
				{foreach from=$p key=k item=v}
					<input type="hidden" name="{$k}" value="{$v}" />
				{/foreach}

				<a href="javascript:document.charityclearform.submit();">{$frontend}</p>
			</div>
		</span>
	</form>
</p>


