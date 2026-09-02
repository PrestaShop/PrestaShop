{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{* Workaround to add compatibility for enable/disable actions to be able to use symfony endpoints *}
{if isset($migrated_url_enable)}
  {assign var="url_enable" value=$migrated_url_enable}
{/if}

<a class="list-action-enable{if isset($ajax) && $ajax} ajax_table_link{/if}{if $enabled} action-enabled{else} action-disabled{/if}" href="{$url_enable|escape:'html':'UTF-8'}"{if isset($confirm)} onclick="return confirm('{$confirm}');"{/if} title="{if $enabled}{l s='Enabled' d='Admin.Global'}{else}{l s='Disabled' d='Admin.Global'}{/if}">
	<i class="icon-check{if !$enabled} hidden{/if}"></i>
	<i class="icon-remove{if $enabled} hidden{/if}"></i>
</a>
