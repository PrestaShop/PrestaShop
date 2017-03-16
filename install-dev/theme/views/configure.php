<?php
/**
 * 2007-2017 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/osl-3.0.php
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
 * @license   http://opensource.org/licenses/osl-3.0.php Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */
 $this->displayTemplate('header') ?>

<script type="text/javascript">
<!--
var default_iso = '<?php echo $this->session->shop_country ?>';
-->
</script>

<!-- Configuration form -->
<div id="infosShopBlock">
	<h2><?php echo $this->translator->trans('Information about your Store', array(), 'Install'); ?></h2>

	<!-- Shop name -->
	<div class="field clearfix">
		<label for="infosShop" class="aligned"><?php echo $this->translator->trans('Shop name', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input class="text required" type="text" id="infosShop" name="shop_name" value="<?php echo htmlspecialchars($this->session->shop_name) ?>" /> <sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('shop_name') ?>
	</div>

	<!-- Activity -->
	<div class="field clearfix">
		<label for="infosActivity" class="aligned"><?php echo $this->translator->trans('Main activity', array(), 'Install'); ?></label>
		<div class="contentinput">
			<select id="infosActivity" name="shop_activity" class="chosen">
				<option value="0" style="font-weight: bold" <?php if (!$this->session->shop_activity): ?>selected="selected"<?php endif; ?>><?php echo $this->translator->trans('Please choose your main activity', array(), 'Install'); ?></option>
				<?php foreach ($this->list_activities as $i => $activity): ?>
					<option value="<?php echo $i ?>" <?php if (isset($this->session->shop_activity) && $this->session->shop_activity == $i): ?>selected="selected"<?php endif; ?>><?php echo $activity ?></option>
				<?php endforeach; ?>
				<option value="0"><?php echo $this->translator->trans('Other activity...', array(), 'Install'); ?></option>
			</select>
		</div>
		<p class="userInfos aligned"><?php echo $this->translator->trans('Help us learn more about your store so we can offer you optimal guidance and the best features for your business!', array(), 'Install') ?></p>
	</div>

	<?php if (_PS_MODE_DEV_): ?>
	<!-- Install type (with fixtures or not) -->
	<div class="field clearfix">
		<label class="aligned"><?php echo $this->translator->trans('Install demo products', array(), 'Install'); ?></label>
		<div class="contentinput">
			<label>
				<input value="full" type="radio" name="db_mode" style="vertical-align: middle;" <?php if ($this->install_type == 'full'): ?>checked="checked"<?php endif; ?> autocomplete="off" />
				<?php echo $this->translator->trans('Yes', array(), 'Install'); ?>
			</label>
			<label>
				<input value="lite" type="radio" name="db_mode" style="vertical-align: middle;" <?php if ($this->install_type == 'lite'): ?>checked="checked"<?php endif; ?> autocomplete="off" />
				<?php echo $this->translator->trans('No', array(), 'Install');; ?>
			</label>
		</div>
		<p class="userInfos aligned"><?php echo $this->translator->trans('Demo products are a good way to learn how to use PrestaShop. You should install them if you are not familiar with it.', array(), 'Install'); ?></p>
	</div>
	<?php else: ?>
		<input value="full" name="db_mode" type="hidden" />
	<?php endif; ?>

	<!-- Country list -->
	<div class="field clearfix">
		<label for="infosCountry" class="aligned"><?php echo $this->translator->trans('Country', array(), 'Install'); ?></label>
		<div class="contentinput">
			<select name="shop_country" id="infosCountry" class="chosen">
				<option value="0" style="font-weight: bold"><?php echo $this->translator->trans('Select your country', array(), 'Install'); ?></option>
				<?php foreach ($this->list_countries as $country): ?>
					<option value="<?php echo (isset($country['iso'])) ? $country['iso'] : '' ?>" <?php if ($this->session->shop_country && isset($country['iso']) && $this->session->shop_country === $country['iso']): ?>selected="selected"<?php endif; ?>><?php echo $country['name'] ?></option>
				<?php endforeach; ?>
			</select>
			<sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('shop_country') ?>
	</div>

	<!-- Timezone list -->
	<div id="timezone_div" class="field clearfix" <?php if (!in_array($this->session->shop_timezone, array('us','ca','au','ru','me','id'))) echo 'style="display:none"'; ?>>
		<label for="infosTimezone" class="aligned"><?php echo $this->translator->trans('Shop timezone', array(), 'Install'); ?></label>
		<div class="contentinput">
			<select name="shop_timezone" id="infosTimezone" class="chosen no-chosen">
				<option value="0" style="font-weight: bold"><?php echo $this->translator->trans('Select your timezone', array(), 'Install'); ?></option>
				<?php foreach ($this->getTimezones() as $timezone): ?>
					<option value="<?php echo $timezone ?>" <?php if ($this->session->shop_timezone == $timezone): ?>selected="selected"<?php endif; ?>><?php echo $timezone ?></option>
				<?php endforeach; ?>
			</select>
			<sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('shop_timezone') ?>
	</div>

	<!-- Shop logo
	<div class="field clearfix">
		<label for="uploadedImage" class="aligned logo"><?php echo $this->translator->trans('Shop logo', array(), 'Install'); ?></label>
		<div class="contentinput">
			<p id="alignedLogo"><img id="uploadedImage" src="../img/logo.jpg?t=<?php echo time() ?>" alt="Logo" /></p>
		</div>
		<p class="userInfos aligned"><?php echo $this->translator->trans('Optional - You can add you logo at a later time.', array(), 'Install'); ?></p>

		<div id="inputFileLogo" class="contentinput" style="top:-20px;position:relative">
			<input type="file" name="fileToUpload" id="fileToUpload"/>
		</div>
		<span id="resultInfosLogo" class="result"></span>
	</div>
	 -->

	<h2 style="margin-top:20px"><?php echo $this->translator->trans('Your Account', array(), 'Install'); ?></h2>

	<!-- Admin firstname -->
	<div class="field clearfix">
		<label for="infosFirstname" class="aligned"><?php echo $this->translator->trans('First name', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input class="text required" type="text" id="infosFirstname" name="admin_firstname" value="<?php echo htmlspecialchars($this->session->admin_firstname) ?>" />
			<sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('admin_firstname') ?>
	</div>

	<!-- Admin lastname -->
	<div class="field clearfix">
		<label for="infosName" class="aligned"><?php echo $this->translator->trans('Last name', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input class="text required" type="text" id="infosName" name="admin_lastname" value="<?php echo htmlspecialchars($this->session->admin_lastname) ?>" />
			<sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('admin_lastname') ?>
	</div>

	<!-- Admin email -->
	<div class="field clearfix">
		<label for="infosEmail" class="aligned"><?php echo $this->translator->trans('E-mail address', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input type="text" class="text required" id="infosEmail" name="admin_email" value="<?php echo htmlspecialchars($this->session->admin_email) ?>" />
			<sup class="required">*</sup>
		</div>
		<p class="userInfos aligned"><?php echo $this->translator->trans('This email address will be your username to access your store\'s back office.', array(), 'Install') ?></p>
		<?php echo $this->displayError('admin_email') ?>
	</div>

	<!-- Admin password -->
	<div class="field clearfix">
		<label for="infosPassword" class="aligned"><?php echo $this->translator->trans('Shop password', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input autocomplete="off" type="password" class="text required" id="infosPassword" name="admin_password" value="<?php echo htmlspecialchars($this->session->admin_password) ?>" />
			<sup class="required">*</sup>
		</div>
		<?php if ($this->displayError('admin_password')): ?>
			<?php echo $this->displayError('admin_password') ?>
		<?php else: ?>
			<p class="userInfos aligned"><?php echo $this->translator->trans('Must be at least 8 characters', array(), 'Install'); ?></p>
		<?php endif; ?>
	</div>

	<!-- Admin password confirm -->
	<div class="field clearfix">
		<label class="aligned" for="infosPasswordRepeat"><?php echo $this->translator->trans('Re-type to confirm', array(), 'Install'); ?> </label>
		<div class="contentinput">
			<input type="password" autocomplete="off" class="text required" id="infosPasswordRepeat" name="admin_password_confirm" value="<?php echo htmlspecialchars($this->session->admin_password_confirm) ?>" />
			<sup class="required">*</sup>
		</div>
		<?php echo $this->displayError('admin_password_confirm') ?>
	</div>
	<br />
	<span><small><?php echo sprintf($this->translator->trans('All information you give us is collected by us and is subject to data processing and statistics, it is necessary for the members of the PrestaShop company in order to respond to your requests. Your personal data may be communicated to service providers and partners as part of partner relationships. Under the current "Act on Data Processing, Data Files and Individual Liberties" you have the right to access, rectify and oppose to the processing of your personal data through this <a href="%s" onclick="return !window.open(this.href)">link</a>.', array(), 'Install'), 'mailto:legal@prestashop.com'); ?></small></span>
</div>

<!-- Partners form -->
<div id="benefitsBlock" style="display:none"></div>

<?php $this->displayTemplate('footer') ?>
