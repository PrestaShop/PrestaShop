{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<a href="#"
   title="{$action|escape:'html':'UTF-8'}"
   class="delete"
   onclick="{if $confirm}confirm_link('{l s='Delete selection' js=1 d='Admin.Actions'}', '{$confirm|escape:'html':'UTF-8'}', '{l s='Delete' js=1 d='Admin.Actions'}', '{l s='Cancel' js=1 d='Admin.Actions'}', '{$href|escape:'html':'UTF-8'}', '#', 'btn-danger'){else}event.stopPropagation();event.preventDefault(){/if}">
  <i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>
