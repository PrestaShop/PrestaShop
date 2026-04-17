{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{function name=mysql_version}
  {if version_compare($version, '5.6') < 0}
    <span class="danger">{$data} (Upgrade strongly recommended)</span>
  {elseif version_compare($version, '5.7') < 0}
    <span class="warning">{$data} (Consider upgrading)</span>
  {else}
    <span class="success">{$data} (OK)</span>
  {/if}
{/function}
