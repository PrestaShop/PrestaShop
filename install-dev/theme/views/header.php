<!-- Header -->
<div id="header" class="clearfix">
  <ul id="headerLinks">
    <?php if (is_array($this->getConfig('header.links'))): ?>
      <?php foreach($this->getConfig('header.links') as $link => $label): ?>
        <li>
          <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer">
            <?php echo $label; ?>
          </a>
        </li>
      <?php endforeach ?>
    <?php endif; ?>
  </ul>

  <div id="PrestaShopLogo">PrestaShop</div>
</div>
