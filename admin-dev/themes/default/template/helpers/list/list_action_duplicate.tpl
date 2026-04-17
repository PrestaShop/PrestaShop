{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<a href="#" title="{$action|escape:'html':'UTF-8'}" onclick="{if $confirm}confirm_link('', '{$confirm|escape:'html':'UTF-8'}', '{l s='Yes' d='Admin.Global'}', '{l s='No' d='Admin.Global'}', '{$location_ok|escape:'html':'UTF-8'}', '{$location_ko|escape:'html':'UTF-8'}'){else}document.location = '{$location_ko|escape:'html':'UTF-8'}'{/if}">
	<i class="icon-copy"></i> {$action}
</a>
