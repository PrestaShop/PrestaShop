<ul id="footer">
  <?php if (is_array($this->getConfig('footer.links'))): ?>
    <?php foreach($this->getConfig('footer.links') as $link => $label): ?>
      <li>
        <a href="<?php echo $link ?>" target="_blank" rel="noopener noreferrer">
          <?php echo $label; ?>
        </a>
        |
      </li>
    <?php endforeach ?>
  <?php endif; ?>

  <li>&copy; 2007-<?php echo date('Y'); ?></li>
</ul>
