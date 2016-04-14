{if count($warnings)}
  <div class="bootstrap">
    <div class="alert alert-warning">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      {if count($warnings) > 1}
        <h4>{l s='There are %d warnings:' sprintf=count($warnings)}</h4>
      {/if}
      <ul class="list-unstyled">
        {foreach $warnings as $warning}
          <li>{$warning}</li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}
