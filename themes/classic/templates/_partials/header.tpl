{block name='header_nav'}
  <nav class="header-nav">
    <div class="container">
        <div class="row">
          <div class="col-md-4 col-xs-12">
            {hook h='displayNav1'}
          </div>
          <div class="col-md-8 right-nav">
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
          <a href="{$urls.base_url}">
            <img class="logo img-responsive" src="{$shop.logo}" alt="{$shop.name}">
          </a>
        </div>
        <div class="col-md-10 position-static">
          <div class="row">
            {hook h='displayTop'}
            <div class="clearfix"></div>
          </div>
        </div>
      </div>
    </div>
{/block}
