<h1>{l s='Page not available'}</h1>
<p class="error404">
	<img src="{$img_dir}icon/error.png" alt="{l s='Error'}" class="middle" />
		{l s='We\'re sorry, but the Web address you entered is no longer available'}
	</p>
	<h3>{l s='To find a product, please type its name in the field below'}</h3>
	<form action="{$link->getPageLink('search.php')}" method="post" class="std">
		<fieldset>
		<p class="text">
				<label for="search">{l s='Search our product catalog:'}</label>
				<input id="search_query" name="search_query" type="text" />
                </p>
                	<p class="submit">
				<input type="submit" name="Submit" value="OK" class="page404_input button_small" />
			</p>
		</fieldset>
	</form>
<ul class="footer_links">
	<li><a href="{$base_dir}" title="{l s='Home'}"><img src="{$img_dir}icon/home.png" alt="{l s='Home'}" class="icon" /></a><a href="{$base_dir}" title="{l s='Home'}">{l s='Home'}</a></li>
</ul>