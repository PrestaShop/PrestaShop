{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<li class="tree-folder">
	<span class="tree-folder-name{if isset($node['disabled']) && $node['disabled'] == true} tree-folder-name-disable{/if}">
		{if isset($node['id_category']) && $node['id_category'] != $root_category}
		<input type="checkbox" name="{$input_name}[]" value="{$node['id_category']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if} />
		{/if}
		<i class="icon-folder-close"></i>
		<label class="tree-toggler">{if isset($node['name'])}{$node['name']|escape:'html':'UTF-8'}{/if}{if isset($node['selected_childs']) && (int)$node['selected_childs'] > 0} {l s='(%s selected)' sprintf=[$node['selected_childs']]}{/if}</label>
	</span>
	<ul class="tree">
		{$children}
	</ul>
</li>
