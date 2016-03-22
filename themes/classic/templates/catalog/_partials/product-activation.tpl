{if $adminActionDisplay}
  <div class="alert alert-warning row" role="alert">
    <div class="container">
      <div class="row">
        <div class="col-md-9">
          <i class="material-icons pull-xs-left">&#xE001;</i>
          <p class="alert-text">{l s='This product is not visible to your customers.'}</p>
        </div>
        <div class="col-md-3">
          {block name='draft_links'}
            <ul class="warning-buttons">
              {foreach from=$draftLinks item=draftLink name=draftLink}
                <li class="pull-xs-left"><a href="{$draftLink.url}" class="btn {if $smarty.foreach.draftLink.last}btn-warning{else}btn-tertiary-outline{/if} alert-link text-uppercase">{$draftLink.title}</a></li>
              {/foreach}
            </ul>
          {/block}
        </div>
      </div>
    </div>
  </div>
{/if}
