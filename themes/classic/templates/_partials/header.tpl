{block name='header_nav'}
  <nav class="header-nav row">
    <div class="container">
        <div class="row">
          <div class="col-md-6">
            {hook h='displayNav1'}
          </div>
          <div class="col-md-6 right-nav">
              {hook h='displayNav2'}
          </div>
        </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
    <div class="container">
      <div class="header-top row">
        <div class="col-md-2">
          <a href="{$urls.base_url}" title="{l s='Homepage'}" >
            <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name} {l s='logo'}" />
          </a>
        </div>
        <div class="col-md-10">
          {hook h='displayTop'}
        </div>
      </div>
    </div>
{/block}
