<div class="container">
  <div class="row">
    {hook h='displayFooterBefore'}
  </div>
</div>
<div class="footer-container">
  <div class="container">
    <div class="row">
      {hook h='displayFooter'}
    </div>
    <div class="row">
      {hook h='displayFooterAfter'}
    </div>
    <div class="row">
      <div class="col-md-12">
        <p>
          <a class="_blank" href="http://www.prestashop.com" target="_blank">
            {l s='%copyright% %year% - Ecommerce software by %prestashop%' sprintf=['%prestashop%' => 'PrestaShop™', '%year%' => 'Y'|date, '%copyright%' => '©'] d='Shop.Theme'}
          </a>
        </p>
      </div>
    </div>
  </div>
</div>
