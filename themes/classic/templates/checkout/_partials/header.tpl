{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
      <div class="row">
        <div class="col-md-6">
          <a href="{$urls.base_url}" title="{l s='Homepage'}" >
            <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name} {l s='logo'}">
          </a>
        </div>
        <div class="col-md-6 text-xs-right">
          {hook h='displayNav1'}
        </div>
      </div>
    </div>
  </nav>
{/block}
