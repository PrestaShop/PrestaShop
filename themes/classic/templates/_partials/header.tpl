{block name='header_nav'}
  <nav class="header-nav row">
    <div class="container">
        <div class="row">
          {hook h='displayNav'}
        </div>
    </div>
  </nav>
{/block}

{block name='header_top'}
    <div class="container">
      <div class="header-top row _margin-bottom-medium">
        <div class="col-md-2">
          <a href="{$urls.base_url}" title="{l s='Homepage'}" >
            <img class="logo img-responsive" src="{$shop.logo}" alt="logo" />
          </a>
        </div>
        <div class="col-md-10">
          {hook h="displayTop"}
        </div>
      </div>
    </div>
{/block}
