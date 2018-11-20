<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
 $this->displayTemplate('header') ?>

<?php if (Tools::getMemoryLimit() < Tools::getOctets('32M')): ?>
	<div class="warnBlock"><?php echo $this->translator->trans('PrestaShop requires at least 32 MB of memory to run: please check the memory_limit directive in your php.ini file or contact your host provider about this.', array(), 'Install'); ?></div>
<?php endif; ?>

<?php if ($this->can_upgrade): ?>
	<div class="warnBlock">
		<img src="theme/img/pict_error.png" alt="" style="vertical-align: middle;" /> &nbsp;
		<?php echo $this->translator->trans(
			'<b>Warning: You cannot use this tool to upgrade your store anymore.</b><br /><br />You already have <b>PrestaShop version %version% installed</b>.<br /><br />If you want to upgrade to the latest version, please read our documentation: <a href="%doc%">%doc%</a>',
			array('version' => $this->ps_version, '%doc%' => $this->getDocumentationUpgradeLink()), 'Install'); ?></div>
<?php endif; ?>

<h2><?php echo $this->translator->trans('Welcome to the PrestaShop %version% Installer', array('%version%' => _PS_INSTALL_VERSION_), 'Install') ?></h2>
<p><?php echo $this->translator->trans('Installing PrestaShop is quick and easy. In just a few moments, you will become part of a community consisting of more than 250,000 merchants. You are on the way to creating your own unique online store that you can manage easily every day.', array(), 'Install'); ?></p>
<p><?php echo $this->translator->trans('If you need help, do not hesitate to <a href="%tutoriellink%" target="_blank">watch this short tutorial</a>, or check <a href="%linkdoc%" target="_blank">our documentation</a>.', array('%tutoriellink%' => $this->getTutorialLink(), '%linkdoc%' => $this->getDocumentationLink()), 'Install') ?></p>

<!-- List of languages -->
<?php if (count($this->language->getIsoList()) > 1): ?>
	<h3><?php echo $this->translator->trans('Continue the installation in:', array(), 'Install') ?></h3>
	<select id="langList" name="language">
	<?php foreach ($this->language->getIsoList() as $iso): ?>
		<option value="<?php echo $iso ?>" <?php if ($iso == $this->language->getLanguageIso()) echo 'selected="selected"' ?>>
			<?php echo $this->language->getLanguage($iso)->getName() ?>
		</option>
	<?php endforeach; ?>
	</select>
<?php endif; ?>

<p><?php echo $this->translator->trans('The language selection above only applies to the Installation Assistant. Once your store is installed, you can choose the language of your store from over %d% translations, all for free!', array('%d%' => 60), 'Install'); ?> </p>

<?php $this->displayTemplate('footer') ?>
