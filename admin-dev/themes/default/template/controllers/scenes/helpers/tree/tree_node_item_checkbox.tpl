{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<li class="tree-item{if isset($node['disabled']) && $node['disabled'] == true} tree-item-disable{/if}">
	<span class="tree-item-name">
		<input type="checkbox" name="categories[]" value="{$node['id_category']}"{if isset($node['disabled']) && $node['disabled'] == true} disabled="disabled"{/if} />
		<i class="tree-dot"></i>
		<label class="tree-toggler">{$node['name']}</label>
	</span>
</li>
