{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{function name=total_queries}
  {if $data >= 100}
    <span class="danger">{$data}</span>
  {elseif $data >= 50}
    <span class="warning">{$data}</span>
  {else}
    <span class="success">{$data}</span>
  {/if}
{/function}
