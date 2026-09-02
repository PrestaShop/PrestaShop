{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/view/view.tpl"}
{block name="override_tpl"}
	{if !$shop_context}
		<div class="alert alert-warning">{l s='You have more than one shop and must select one to configure payment.' d='Admin.Payment.Notification'}</div>
	{else}
		{if isset($modules_list)}
			{$modules_list}
		{/if}
	{/if}
{/block}
