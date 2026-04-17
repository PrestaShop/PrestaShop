{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<a href="{$link}"{if isset($action)} onclick="{$action}"{/if}{if isset($id)} id="{$id|escape:'html':'UTF-8'}"{/if} class="btn btn-default">
	{if isset($icon_class)}<i class="{$icon_class}"></i>{/if}
	{l s=$label}
</a>
