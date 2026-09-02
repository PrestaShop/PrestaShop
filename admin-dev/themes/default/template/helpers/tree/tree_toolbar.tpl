{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="tree-actions pull-right">
	{if isset($actions)}
	{foreach from=$actions item=action}
		{$action->render()}
	{/foreach}
	{/if}
</div>
