<?php
/**
 * Copyright since 2007 PrestaShop SA and Contributors
 * PrestaShop is an International Registered Trademark & Property of PrestaShop SA
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.md.
 * It is also available through the world-wide-web at this URL:
 * https://opensource.org/licenses/OSL-3.0
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
?>

<?php if ($this->can_upgrade): ?>
  <div class="warnBlock">
    <img src="theme/img/pict_error.png" alt="" style="vertical-align: middle;" /> &nbsp;
    <?php echo $this->translator->trans(
      '<b>Warning: You cannot use this tool to upgrade your store anymore.</b><br /><br />You already have <b>PrestaShop version %version% installed</b>.<br /><br />If you want to upgrade to the latest version, please use the 1-Click Upgrade module and follow its instructions.',
        ['%version%' => $this->ps_version],
        'Install'
      ); ?>
  </div>
<?php endif; ?>

<h2><?php echo $this->translator->trans('Welcome to the PrestaShop %version% Installer', ['%version%' => _PS_INSTALL_VERSION_], 'Install'); ?></h2>

<p>
  <?php echo $this->translator->trans(
    'Installing PrestaShop is quick and easy. In just a few moments, you will become part of a community consisting of more than %numMerchants% merchants. You are on the way to creating your own unique online store that you can manage easily every day.',
      ['%numMerchants%' => '300,000'],
      'Install'
    ); ?>
</p>

<?php echo $this->getHook('welcome-message') ?>

<?php if (count($this->language->getIsoList()) > 1): ?>
  <h3><?php echo $this->translator->trans('Continue the installation in:', [], 'Install'); ?></h3>
  <select id="langList" name="language">
    <?php foreach ($this->language->getIsoList() as $iso): ?>
      <option
        value="<?php echo $iso; ?>"
        <?php if ($iso == $this->language->getLanguageIso()): ?>
          selected="selected"
        <?php endif; ?>
      >

        <?php echo $this->language->getLanguage($iso)->getName(); ?>
      </option>
    <?php endforeach; ?>
  </select>
<?php endif; ?>

<p>
  <?php echo $this->translator->trans(
    'The language selection above only applies to the Installation Assistant. Once your store is installed, you can choose the language of your store from over %d% translations, all for free!',
      ['%d%' => 60],
      'Install'
    );
  ?>
</p>
