{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div class="panel">
	{if isset($header)}{$header}{/if}
	{if isset($nodes)}
	<ul id="{$id|escape:'html':'UTF-8'}" class="tree">
		{$nodes}
	</ul>
	{/if}
</div>
