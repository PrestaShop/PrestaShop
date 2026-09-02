{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
 <a href="#"
 title="{$action|escape:'html':'UTF-8'}"
 class="delete"
 onclick="
  {if $confirm}
    var modalConfirmDeleteType = $('#modalConfirmDeleteType');
    $('.btn-confirm-delete-images-type', modalConfirmDeleteType).attr('data-confirm-url', '{$href|escape:'html':'UTF-8'}');
    modalConfirmDeleteType.modal('show');
  {else}
    event.stopPropagation();event.preventDefault()
  {/if}
">
<i class="icon-trash"></i> {$action|escape:'html':'UTF-8'}
</a>
