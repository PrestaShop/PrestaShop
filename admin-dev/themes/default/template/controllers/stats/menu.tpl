{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<div id="container" class="row">
	<div class="sidebar navigation col-md-3">
		<nav class="list-group categorieList">
		{if count($modules)}
			{foreach $modules as $module}
				{if $module_instance[$module.name]}
					<a class="list-group-item{if ($current_module_name && $current_module_name == $module.name)} active{/if}" href="{$current|escape:'html':'UTF-8'}&amp;token={$token|escape:'html':'UTF-8'}&amp;module={$module.name}">{$module_instance[$module.name]->displayName}</a>
				{/if}
			{/foreach}
		{else}
			{l s='No module has been installed.' d='Admin.Notifications.Warning'}
		{/if}
		</nav>
	</div>
