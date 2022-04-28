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
	  <input class="text required" type="text" id="infosShop" name="shop_name" value="<?php echo htmlspecialchars($this->session->shop_name ?? ''); ?>" /> <sup class="required">*</sup>
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
        <input value="1" type="radio" name="enable_ssl" style="vertical-align: middle;" <?php if ($this->session->enable_ssl) { ?>checked="checked"<?php } ?> autocomplete="off" />
        <?php echo $this->translator->trans('Yes', [], 'Install'); ?>
      </label>
      <label>
        <input value="0" type="radio" name="enable_ssl" style="vertical-align: middle;" <?php if (!$this->session->enable_ssl) { ?>checked="checked"<?php } ?> autocomplete="off" />
        <?php echo $this->translator->trans('No', [], 'Install'); ?>
      </label>
    </div>
  </div>

  <h2 style="margin-top:20px"><?php echo $this->translator->trans('Your Account', [], 'Install'); ?></h2>

  <!-- Admin firstname -->
  <div class="field clearfix">
	<label for="infosFirstname" class="aligned"><?php echo $this->translator->trans('First name', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input class="text required" type="text" id="infosFirstname" name="admin_firstname" value="<?php echo htmlspecialchars($this->session->admin_firstname ?? ''); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_firstname'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosName" class="aligned"><?php echo $this->translator->trans('Last name', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input class="text required" type="text" id="infosName" name="admin_lastname" value="<?php echo htmlspecialchars($this->session->admin_lastname ?? ''); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_lastname'); ?>
  </div>

  <div class="field clearfix">
	<label for="infosEmail" class="aligned"><?php echo $this->translator->trans('E-mail address', [], 'Install'); ?> </label>
	<div class="contentinput">
	  <input type="text" class="text required" id="infosEmail" name="admin_email" value="<?php echo htmlspecialchars($this->session->admin_email ?? ''); ?>" />
	  <sup class="required">*</sup>
	</div>
	<p class="userInfos aligned"><?php echo $this->translator->trans('This email address will be your username to access your store\'s back office.', [], 'Install'); ?></p>
	<?php echo $this->displayError('admin_email'); ?>
  </div>

  <div class="field field-password clearfix">
	<label for="infosPassword" class="aligned"><?php echo $this->translator->trans('Shop password', [], 'Install'); ?> </label>
	<div class="contentinput">
      <div class="popover fade bs-popover-top d-none" role="tooltip" x-placement="top">
        <div class="arrow"></div>
        <h3 class="popover-header"></h3>
        <div class="popover-body"></div>
      </div>

	  <input autocomplete="off" type="password" data-minlength="8" data-maxlength="72" data-minscore="3" class="text required" id="infosPassword" name="admin_password" value="<?php echo htmlspecialchars($this->session->admin_password ?? ''); ?>" />
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
	  <input type="password" autocomplete="off" class="text required" id="infosPasswordRepeat" name="admin_password_confirm" value="<?php echo htmlspecialchars($this->session->admin_password_confirm ?? ''); ?>" />
	  <sup class="required">*</sup>
	</div>
	<?php echo $this->displayError('admin_password_confirm'); ?>
  </div>

  <?php echo $this->getHook('configure-footer') ?>
</div>

<!-- Partners form -->
<div id="benefitsBlock" style="display:none"></div>

<template id="password-feedback">
  <div
    class="password-strength-feedback"
    data-translations="<?php echo htmlspecialchars($this->translatedStrings); ?>"
  >
    <div class="progress-container">
      <div class="progress-bar">
        <div></div>
      </div>
    </div>
    <div class="password-strength-text"></div>
    <div class="password-requirements">
      <p class="password-requirements-length" data-translation="<?php echo htmlspecialchars($this->translator->trans('Enter a password between %d and %d characters', [], 'Install')); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span></span>
      </p>
      <p class="password-requirements-score" data-translation="<?php echo htmlspecialchars($this->translator->trans('The minimum score must be: %s', [], 'Install')); ?>">
        <svg xmlns="http://www.w3.org/2000/svg" height="24px" viewBox="0 0 24 24" width="24px"><path d="M0 0h24v24H0z" fill="none"/><path fill="currentColor" d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-2 15l-5-5 1.41-1.41L10 14.17l7.59-7.59L19 8l-9 9z"/></svg>
        <span></span>
      </p>
    </div>
  </div>
</template>
