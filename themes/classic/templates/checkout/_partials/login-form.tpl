{extends file='customer/_partials/login-form.tpl'}

{block name='form_buttons'}
  <button
    class="continue btn btn-primary pull-xs-right"
    name="continue"
    data-link-action="sign-in"
    type="submit"
    value="1"
  >
    {l s='Continue'}
  </button>
{/block}
