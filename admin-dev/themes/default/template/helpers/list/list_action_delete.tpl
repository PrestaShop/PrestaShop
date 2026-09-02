{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<a href="#"
   title="{$action|escape:'html':'UTF-8'}"
   class="delete"
   onclick="{if $confirm}confirm_link('', '{$confirm|escape:'html':'UTF-8'}', '{l s='Yes' d='Admin.Global'}', '{l s='No' d='Admin.Global'}', '{$href|escape:'html':'UTF-8'}', '#'){else}event.stopPropagation();event.preventDefault(){/if}">
  <i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>
