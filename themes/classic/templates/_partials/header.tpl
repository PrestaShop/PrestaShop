{block name='header_nav'}
  <nav class="header-nav row">
    <div class="container">
      <div class="col-lg-8 col-lg-offset-7 col-md-10 col-md-offset-4">
        <div class="row">
          {hook h='displayNav'}
        </div>
      </div>
    </div>
  </nav>
{/block}

{block name='header_logo'}
<div class="row">
  <div class="container">
    <div class="col-lg-8 col-md-6">
      <img class="logo img-responsive" src="{$shop.logo}" alt="logo" width="180" />
    </div>
    <div class="col-md-1 col-md-offset-1">{hook h="displayHeaderMiddle"}</div>
  </div>
</div>
{/block}

{block name='header_top'}
  <div class="header-top row">
    <div class="container _relative navigation">
      {hook h='displayTop'}
    </div>
  </div>
{/block}
