{**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 *}
{if count($errors) && current($errors) != '' && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
  <div class="bootstrap">
    <div class="alert alert-danger">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {if count($errors) == 1}
        {reset($errors)}
      {else }
        {l s='There are %d errors.' sprintf=[$errors|count] d='Admin.Notifications.Error'}
        <br/>
        <ol>
          {foreach $errors as $error}
            <li>{$error}</li>
          {/foreach}
        </ol>
      {/if}
    </div>
  </div>
{/if}
