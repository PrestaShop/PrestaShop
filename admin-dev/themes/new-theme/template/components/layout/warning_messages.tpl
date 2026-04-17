{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if count($warnings)}
  <div class="bootstrap">
    <div class="alert alert-warning">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {if count($warnings) > 1}
        <h4>{l s='There are %d warnings.' sprintf=[count($warnings)] d='Admin.Notifications.Error'}</h4>
      {/if}
      <ul class="list-unstyled">
        {foreach $warnings as $warning}
          <li>{$warning}</li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}
