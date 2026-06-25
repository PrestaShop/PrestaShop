{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
<select id="zone_to_affect" name="zone_to_affect">
    {foreach $zones as $z}
        <option value="{$z['id_zone']}">{$z['name']|escape:'html':'UTF-8'}</option>
    {/foreach}
</select>
