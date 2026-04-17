{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{function name=load_time}
  {if $data > 1.6}
    <span class="danger">{round($data * 1000)}</span>
  {elseif $data > 0.8}
    <span class="warning">{round($data * 1000)}</span>
  {else}
    <span class="success">{sprintf('%01.3f', $data * 1000)}</span>
  {/if}
  ms
{/function}
