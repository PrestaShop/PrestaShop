{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

<!-- <label for="node-search">{l s=$label}</label> -->
<div class="pull-right">
	<input type="text"
		{if isset($id)}id="{$id|escape:'html':'UTF-8'}"{/if}
		{if isset($name)}name="{$name|escape:'html':'UTF-8'}"{/if}
		class="search-field{if isset($class)} {$class|escape:'html':'UTF-8'}{/if}"
		placeholder="{l s='search...'}" />
</div>

{if isset($typeahead_source) && isset($id) && isset($name)}

<script type="text/javascript">
	$(
		function()
		{
			$("#{$id|escape:'html':'UTF-8'}").typeahead(
			{
				name: "{$name|escape:'html':'UTF-8'}",
				valueKey: 'name',
				local: [{$typeahead_source}]
			});

			$("#{$id|escape:'html':'UTF-8'}").on('keypress', function( event ) {
				if ( event.which == 13 ) {
					event.stopPropagation();
				}
			});
		}
	);
</script>
{/if}
