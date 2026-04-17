{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<a href="{$href|escape:'html':'UTF-8'}" class="delete" title="{$action|escape:'html':'UTF-8'}"
	{if in_array($id_shop, $shops_having_dependencies)}
		onclick="jAlert('{l s='You cannot delete this shop (customer and/or order dependency)' js=1 d='Admin.Shopparameters.Notification'}'); return false;"
	{elseif isset($confirm)}
		onclick="if (confirm('{$confirm}')){ldelim}return true;{rdelim}else{ldelim}event.stopPropagation(); event.preventDefault();{rdelim};"
	{/if}>
	<i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>
