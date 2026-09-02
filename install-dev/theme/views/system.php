<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
?>

<h2><?php echo $this->translator->trans('We are currently checking PrestaShop compatibility with your system environment', [], 'Install'); ?></h2>

<?php if ($this->tests['required']['success']) { ?>
  <h3 class="okBlock">
    <?php echo $this->translator->trans('PrestaShop compatibility with your system environment has been verified!', [], 'Install'); ?>
  </h3>
<?php } else { ?>
  <h3 class="errorBlock">
    <?php echo $this->translator->trans('Oops! Please correct the item(s) below, and then click "%refresh_label%" to test the compatibility of your new system.', ['%refresh_label%' => $this->translator->trans('Refresh information', [], 'Install')], 'Install'); ?>
  </h3>
<?php } ?>

<hr />

<!-- Display tests results -->
<?php foreach ($this->tests_render as $type => $categories): ?>
  <ul id="<?php echo $type; ?>">
    <?php foreach ($categories as $category): ?>
      <li class="title <?php if ($category['success'] == 1): ?>ok<?php endif; ?>"><?php echo $category['title']; ?></li>
      <?php $i = 0; ?>
      <?php foreach ($category['checks'] as $id => $lang): ?>
        <li class="required <?php if ($i == 0): ?>first<?php endif; ?> <?php echo isset($this->tests[$type]['checks'][$id]) ? $this->tests[$type]['checks'][$id] : 'fail'; ?>">
          <?php echo $lang; ?>
        </li>
        <?php ++$i; ?>
      <?php endforeach; ?>
    <?php endforeach; ?>
  </ul>
<?php endforeach; ?>

<hr />

<p><input class="button button--secondary" value="<?php echo $this->translator->trans('Refresh information', [], 'Install'); ?>" type="submit" id="req_bt_refresh" /></p>
