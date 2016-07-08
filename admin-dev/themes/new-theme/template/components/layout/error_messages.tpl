{if count($errors) && current($errors) != '' && (!isset($disableDefaultErrorOutPut) || $disableDefaultErrorOutPut == false)}
  <div class="bootstrap">
    <div class="alert alert-danger">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {if count($errors) == 1}
        {reset($errors)}
      {else }
        {l s='%d errors' sprintf=[$errors|count]}
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
