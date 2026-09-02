{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

{extends file="helpers/list/list_content.tpl"}

{block name="td_content"}
	{if $key == 'url'}
		<a href="{$tr.$key}" onmouseover="$(this).css('text-decoration', 'underline')" onmouseout="$(this).css('text-decoration', 'none')" target="_blank" rel="noopener noreferrer nofollow">{$tr.$key}</a>
	{else}
		{$smarty.block.parent}
	{/if}
{/block}
