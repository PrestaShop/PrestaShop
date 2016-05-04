{extends file='checkout/_partials/steps/checkout-step.tpl'}

{block name='step_content'}
  {if $customer.is_logged}

    <p class="identity">{l s='Connected as %1$s %2$s.' sprintf=[$customer.firstname, $customer.lastname]}</p>
    <p>{l s='Not you? [1]Log out[/1]' tags=["<a href='{$urls.actions.logout}'>"]}</p>

  {else}

    <ul class="nav nav-inline m-y-2">
      <li class="nav-item">
        <a class="nav-link {if !$show_login_form}active{/if}" data-toggle="tab" href="#checkout-guest-form" role="tab">
          {l s='Order as a guest'}
        </a>
      </li>

      <li class="nav-item">
        <span href="nav-separator"> | </span>
      </li>

      <li class="nav-item">
        <a class="nav-link {if $show_login_form}active{/if}" data-link-action="show-login-form" data-toggle="tab" href="#checkout-login-form" role="tab">
          {l s='Sign in'}
        </a>
      </li>
    </ul>

    <div class="tab-content">
      <div class="tab-pane {if !$show_login_form}active{/if}" id="checkout-guest-form" role="tabpanel">
        {render file='checkout/_partials/customer-form.tpl' ui=$register_form guest_allowed=$guest_allowed}
      </div>
      <div class="tab-pane {if $show_login_form}active{/if}" id="checkout-login-form" role="tabpanel">
        {render file='checkout/_partials/login-form.tpl' ui=$login_form}
      </div>
    </div>


  {/if}
{/block}
