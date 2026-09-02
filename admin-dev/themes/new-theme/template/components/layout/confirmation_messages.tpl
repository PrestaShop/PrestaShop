{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if isset($confirmations) && count($confirmations) && $confirmations}
  <div class="bootstrap">
    <div class="alert alert-success" style="display:block;">
      {foreach $confirmations as $conf}
        {$conf}
      {/foreach}
    </div>
  </div>
{/if}
