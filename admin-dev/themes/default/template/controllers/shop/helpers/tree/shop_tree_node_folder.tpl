{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<li class="tree-folder">
	<span class="tree-folder-name">
		<i class="icon-folder-close"></i>
		<label class="tree-toggler"><a href="{$url_shop_group|escape:'html':'UTF-8'}&amp;id_shop_group={$node['id']}">{$node['name']|escape:'html':'UTF-8'}</a></label>
	</span>
	<ul class="tree">
		{$children}
	</ul>
</li>
