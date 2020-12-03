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
 $this->displayTemplate('header') ?>

<script type="text/javascript">
<!--
var install_is_done = '<?php echo addslashes($this->translator->trans('Done!', array(), 'Install')) ?>';
var process_steps = <?php echo json_encode($this->process_steps) ?>;
var admin = '<?php echo file_exists('../admin-dev') ? '../admin-dev' : '../admin' ?>';
-->
</script>

<div id="install_process_form">
	<div id="progress_bar">
		<div class="installing"></div>

		<div class="total">
			<div class="progress"></div>
			<span>0%</span>
		</div>

		<ol class="process_list">
			<?php foreach ($this->process_steps as $item): ?>
				<li id="process_step_<?php echo $item['key'] ?>" class="process_step">
					<?php echo $item['lang'] ?>
				</li>
			<?php endforeach; ?>
		</ol>

		<div id="error_process">
			<h3><?php echo $this->translator->trans('An error occurred during installation...', array(), 'Install'); ?></h3>
			<p><?php echo $this->translator->trans('You can use the links on the left column to go back to the previous steps, or restart the installation process by <a href="%link%">clicking here</a>.', array('%link%' => 'index.php?restart=true'), 'Install'); ?></p>
		</div>
	</div>
</div>

<div id="install_process_success">
	<div class="clearfix">
		<h2><?php echo $this->translator->trans('Your installation is finished!', array(), 'Install'); ?></h2>
		<p><?php echo $this->translator->trans('You have just finished installing your shop. Thank you for using PrestaShop!', array(), 'Install'); ?></p>
		<p><?php echo $this->translator->trans('Please remember your login information:', array(), 'Install'); ?></p>
		<table cellpadding="0" cellspacing="0" border="0" id="resultInstall" width="620">
			<tr class="odd">
				<td class="label"><?php echo $this->translator->trans('E-mail', array(), 'Install') ?></td>
				<td class="resultEnd"><?php echo htmlspecialchars($this->session->admin_email) ?></td>
				<td rowspan="2" class="print" onclick="$('#password_content').text('<?php echo htmlspecialchars(addslashes($this->session->admin_password)) ?>'); $('#password_display').hide(); window.print();">
					<img src="theme/img/print.png" alt="" style="vertical-align:top">
					<?php echo $this->translator->trans('Print my login information', array(), 'Install') ?>
				</td>
			</tr>
			<tr>
				<td class="label"><?php echo $this->translator->trans('Password', array(), 'Install'); ?></td>
				<td class="resultEnd">
					<span id="password_content"><?php echo preg_replace('#.#', '*', $this->session->admin_password) ?></span>
					<span id="password_display">
						(<a href="#" onclick="$('#password_content').text('<?php echo htmlspecialchars(addslashes($this->session->admin_password)) ?>'); $('#password_display').hide(); return false"><?php echo $this->translator->trans('Display', array(), 'Install'); ?></a>)
					</span>
				</td>
			</tr>
		</table>

		<h3 class="infosBlock">
			<?php echo $this->translator->trans('For security purposes, you must delete the "install" folder.', array(), 'Install'); ?>
			<a href="<?php echo $this->translator->trans('http://doc.prestashop.com/display/PS17/Installing+PrestaShop#InstallingPrestaShop-Completingtheinstallation', array(), 'Install') ?>" target="_blank"><img src="theme/img/help.png" /></a>
		</h3>

		<div id="boBlock" class="blockInfoEnd clearfix" onclick="window.open(admin)">
			<img src="theme/img/visu_boBlock.png" alt="" />
			<h3><?php echo $this->translator->trans('Back Office', array(), 'Install'); ?></h3>
			<p class="description"><?php echo $this->translator->trans('Manage your store using your Back Office. Manage your orders and customers, add modules, change themes, etc.', array(), 'Install'); ?></p>
			<p>
				<a class="BO" target="_blank"><span><?php echo $this->translator->trans('Manage your store', array(), 'Install'); ?></span></a>
			</p>
		</div>
		<div id="foBlock" class="blockInfoEnd last clearfix" onclick="window.open('../')" />
			<img src="theme/img/visu_foBlock.png" alt="" />
			<h3><?php echo $this->translator->trans('Front Office', array(), 'Install'); ?></h3>
			<p class="description"><?php echo $this->translator->trans('Discover your store as your future customers will see it!', array(), 'Install') ?></p>
			<p>
				<a class="FO" target="_blank"><span><?php echo $this->translator->trans('Discover your store', array(), 'Install') ?></span></a>
			</p>
		</div>
	</div>

	<div class="sharing">
		<p><?php echo $this->translator->trans('Share your experience with your friends!', array(), 'Install'); ?></p>
		<button type="button" class="btn-twitter" onclick="psinstall_twitter_click('<?php echo $this->translator->trans('I just built an online store with PrestaShop!', array(), 'Install'); ?> <?php echo $this->translator->trans('Watch this exhilarating experience: http://vimeo.com/89298199', array(), 'Install'); ?>');">
			<i></i> <?php echo $this->translator->trans('Tweet', array(), 'Install'); ?>
		</button>
		<button type="button" class="btn-facebook" onclick="psinstall_facebook_click();">
			<i></i> <?php echo $this->translator->trans('Share', array(), 'Install'); ?>
		</button>
		<button type="button" class="btn-google-plus" onclick="psinstall_google_click();">
			<i></i> <?php echo $this->translator->trans('Google+', array(), 'Install'); ?>
		</button>
		<button type="button" class="btn-pinterest" onclick="psinstall_pinterest_click();">
			<i></i> <?php echo $this->translator->trans('Pinterest', array(), 'Install'); ?>
		</button>
		<button type="button" class="btn-linkedin" onclick="psinstall_linkedin_click();">
			<i></i> <?php echo $this->translator->trans('LinkedIn', array(), 'Install'); ?>
		</button>
	</div>
</div>

<?php if (@fsockopen('addons.prestashop.com', 80, $errno, $errst, 3)): ?>
	<iframe src="https://addons.prestashop.com/psinstall1541.php?version=2&lang=<?php echo $this->language->getLanguageIso() ?>&activity=<?php echo $this->session->shop_activity ?>&country=<?php echo $this->session->shop_country ?>" scrolling="no" id="prestastore">
		<p><a href="https://addons.prestashop.com/" target="_blank"><?php echo $this->translator->trans('Check out PrestaShop Addons to add that little something extra to your store!', array(), 'Install'); ?></a></p>
	</iframe>
<?php endif; ?>

<?php $this->displayTemplate('footer') ?>
