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

<div id="infosShopBlock">
  <h2><?php echo $this->translator->trans('Information about your Store', [], 'Install'); ?></h2>

  <div class="field clearfix">
	<label for="infosShop" class="aligned"><?php echo $this->translator->trans('Shop name', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input class="text required" type="text" id="infosShop" name="shop_name" value="<?php echo htmlspecialchars($this->session->shop_name); ?>" /> <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('shop_name'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosActivity" class="aligned"><?php echo $this->translator->trans('Main activity', [], 'Install'); ?></label>
	<div class="contentinput">
	  <select id="infosActivity" name="shop_activity" class="chosen">
		<option value="0" style="font-weight: bold" <?php if (!$this->session->shop_activity) { ?>selected="selected"<?php } ?>><?php echo $this->translator->trans('Please choose your main activity', [], 'Install'); ?></option>
		<?php foreach ($this->list_activities as $i => $activity) { ?>
		  <option value="<?php echo $i; ?>" <?php if (isset($this->session->shop_activity) && $this->session->shop_activity == $i) { ?>selected="selected"<?php } ?>><?php echo $activity; ?></option>
		<?php } ?>
		<option value="0"><?php echo $this->translator->trans('Other activity...', [], 'Install'); ?></option>
	  </select>
	</div>
	<p class="userInfos aligned"><?php echo $this->translator->trans('Help us learn more about your store so we can offer you optimal guidance and the best features for your business!', [], 'Install'); ?></p>
  </div>

  <div class="field clearfix">
	<label class="aligned"><?php echo $this->translator->trans('Install demonstration data', [], 'Install'); ?></label>
	<div class="contentinput">
	  <label>
		<input value="full" type="radio" name="db_mode" style="vertical-align: middle;" <?php if ($this->install_type == 'full') { ?>checked="checked"<?php } ?> autocomplete="off" />
		<?php echo $this->translator->trans('Yes', [], 'Install'); ?>
	  </label>
	  <label>
		<input value="lite" type="radio" name="db_mode" style="vertical-align: middle;" <?php if ($this->install_type == 'lite') { ?>checked="checked"<?php } ?> autocomplete="off" />
		<?php echo $this->translator->trans('No', [], 'Install'); ?>
	  </label>
	</div>
	<p class="userInfos aligned"><?php echo $this->translator->trans('Demo products are a good way to learn how to use PrestaShop. You should install them if you are not familiar with it.', [], 'Install'); ?></p>
  </div>

  <div class="field clearfix">
	<label for="infosCountry" class="aligned"><?php echo $this->translator->trans('Country', [], 'Install'); ?></label>
	<div class="contentinput">
	  <select name="shop_country" id="infosCountry" class="chosen">
		<option value="0" style="font-weight: bold"><?php echo $this->translator->trans('Select your country', [], 'Install'); ?></option>
		<?php foreach ($this->list_countries as $country) { ?>
		  <option value="<?php echo (isset($country['iso'])) ? $country['iso'] : ''; ?>" <?php if ($this->session->shop_country && isset($country['iso']) && $this->session->shop_country === $country['iso']) { ?>selected="selected"<?php } ?>><?php echo $country['name']; ?></option>
		<?php } ?>
	  </select>
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('shop_country'); ?>
  </div>

  <div id="timezone_div" class="field clearfix" <?php if (!in_array($this->session->shop_timezone, ['us', 'ca', 'au', 'ru', 'me', 'id'])) {
    echo 'style="display:none"';
} ?>>
	<label for="infosTimezone" class="aligned"><?php echo $this->translator->trans('Shop timezone', [], 'Install'); ?></label>
	<div class="contentinput">
	  <select name="shop_timezone" id="infosTimezone" class="chosen no-chosen">
		<option value="0" style="font-weight: bold"><?php echo $this->translator->trans('Select your timezone', [], 'Install'); ?></option>
		<?php foreach ($this->getTimezones() as $timezone) { ?>
		  <option value="<?php echo $timezone; ?>" <?php if ($this->session->shop_timezone == $timezone) { ?>selected="selected"<?php } ?>><?php echo $timezone; ?></option>
		<?php } ?>
	  </select>
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('shop_timezone'); ?>
  </div>

  <div class="field clearfix">
    <label class="aligned"><?php echo $this->translator->trans('Enable SSL', [], 'Install'); ?></label>
    <div class="contentinput">
      <label>
        <input value="1" type="radio" name="enable_ssl" style="vertical-align: middle;" <?php if ($this->session->enable_ssl == '1') { ?>checked="checked"<?php } ?> autocomplete="off" />
        <?php echo $this->translator->trans('Yes', [], 'Install'); ?>
      </label>
      <label>
        <input value="0" type="radio" name="enable_ssl" style="vertical-align: middle;" <?php if ($this->session->enable_ssl == '0') { ?>checked="checked"<?php } ?> autocomplete="off" />
        <?php echo $this->translator->trans('No', [], 'Install'); ?>
      </label>
    </div>
  </div>

  <h2 style="margin-top:20px"><?php echo $this->translator->trans('Your Account', [], 'Install'); ?></h2>

  <!-- Admin firstname -->
  <div class="field clearfix">
	<label for="infosFirstname" class="aligned"><?php echo $this->translator->trans('First name', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input class="text required" type="text" id="infosFirstname" name="admin_firstname" value="<?php echo htmlspecialchars($this->session->admin_firstname); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_firstname'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosName" class="aligned"><?php echo $this->translator->trans('Last name', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input class="text required" type="text" id="infosName" name="admin_lastname" value="<?php echo htmlspecialchars($this->session->admin_lastname); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_lastname'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosEmail" class="aligned"><?php echo $this->translator->trans('E-mail address', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input type="text" class="text required" id="infosEmail" name="admin_email" value="<?php echo htmlspecialchars($this->session->admin_email); ?>" />
	  <sup class="required">*</sup>
	</div>
	<p class="userInfos aligned"><?php echo $this->translator->trans('This email address will be your username to access your store\'s back office.', [], 'Install'); ?></p>
	<?php echo $this->displayError('admin_email'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosPassword" class="aligned"><?php echo $this->translator->trans('Shop password', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input autocomplete="off" type="password" class="text required" id="infosPassword" name="admin_password" value="<?php echo htmlspecialchars($this->session->admin_password); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php if ($this->displayError('admin_password')) { ?>
	  <?php echo $this->displayError('admin_password'); ?>
	<?php } else { ?>
	  <p class="userInfos aligned"><?php echo $this->translator->trans('Must be at least 8 characters', [], 'Install'); ?></p>
	<?php } ?>
  </div>

  <div class="field clearfix">
	<label class="aligned" for="infosPasswordRepeat"><?php echo $this->translator->trans('Re-type to confirm', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input type="password" autocomplete="off" class="text required" id="infosPasswordRepeat" name="admin_password_confirm" value="<?php echo htmlspecialchars($this->session->admin_password_confirm); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_password_confirm'); ?>
  </div>

  <?php echo $this->getHook('configure-footer') ?>
</div>

<!-- Partners form -->
<div id="benefitsBlock" style="display:none"></div>
