{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}

		<div class="panel">
			{if $module_name}
				{if $module_instance && $module_instance->active}
					{$hook}
				{else}
					{l s='Module not found' d='Admin.Stats.Notification'}
				{/if}
			{else}
				<h3 class="space">{l s='Please select a module from the left column.' d='Admin.Stats.Notification'}</h3>
			{/if}
		</div>
	</div>
</div>
