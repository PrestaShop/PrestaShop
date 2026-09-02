{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<li class="tree-folder">
	<span class="tree-folder-name{if isset($node['disabled']) && $node['disabled'] == true} tree-folder-name-disable{/if}">
		<input type="checkbox" name="checkBoxShopGroupAsso_{$table}[{$node['id']}]" value="{$node['id']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if} />
		<i class="icon-folder-close"></i>
		<label class="tree-toggler">{l s='Group: %s' sprintf=[$node['name']|escape:'html':'UTF-8']}</label>
	</span>
	<ul class="tree">
		{$children}
	</ul>
</li>
