{if isset($informations) && count($informations) && $informations}
  <div class="bootstrap">
    <div class="alert alert-info">
      <button type="button" class="close" data-dismiss="alert">&times;</button>
      <ul id="infos_block" class="list-unstyled">
        {foreach $informations as $info}
          <li>{$info}</li>
        {/foreach}
      </ul>
    </div>
  </div>
{/if}
