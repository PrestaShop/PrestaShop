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

<!-- Database configuration -->
<div id="dbPart">
  <h2><?php echo $this->translator->trans('Configure your database by filling out the following fields', [], 'Install'); ?></h2>
  <p>
      <?php echo $this->translator->trans('To use PrestaShop, you must <a href="https://docs.prestashop-project.org/1.7-documentation/getting-started/installing-prestashop#creating-a-database-for-your-shop" target="_blank">create a database</a> to collect all of your store\'s data-related activities.', array(), 'Install'); ?>
    <br />
    <?php echo $this->translator->trans('Please complete the fields below in order for PrestaShop to connect to your database.', [], 'Install'); ?>
  </p>
  <div id="formCheckSQL">
    <p class="first" style="margin-top: 15px;">
      <label for="dbServer"><?php echo $this->translator->trans('Database server address', [], 'Install'); ?> </label>
      <input size="25" class="text" type="text" id="dbServer" name="dbServer" value="<?php echo htmlspecialchars($this->database_server ?? ''); ?>" />
      <span class="userInfos aligned"><?php echo $this->translator->trans('The default port is 3306. To use a different port, add the port number at the end of your server\'s address i.e ":4242".', [], 'Install'); ?></span>
    </p>
    <p>
      <label for="dbName"><?php echo $this->translator->trans('Database name', [], 'Install'); ?> </label>
      <input size="10" class="text" type="text" id="dbName" name="dbName" value="<?php echo htmlspecialchars($this->database_name ?? ''); ?>" />
    </p>
    <p>
      <label for="dbLogin"><?php echo $this->translator->trans('Database login', [], 'Install'); ?> </label>
      <input class="text" size="10" type="text" id="dbLogin" name="dbLogin" value="<?php echo htmlspecialchars($this->database_login ?? ''); ?>" />
    </p>
    <p>
      <label for="dbPassword"><?php echo $this->translator->trans('Database password', [], 'Install'); ?> </label>
      <input class="text" size="10" type="password" id="dbPassword" name="dbPassword" value="<?php echo htmlspecialchars($this->database_password ?? ''); ?>" />
    </p>
    <p>
      <label for="db_prefix"><?php echo $this->translator->trans('Tables prefix', [], 'Install'); ?></label>
      <input class="text" type="text" id="db_prefix" name="db_prefix" value="<?php echo htmlspecialchars($this->database_prefix ?? ''); ?>" />
    </p>
    <p>
      <label for="db_clear"><?php echo $this->translator->trans('Drop existing tables', [], 'Install'); ?></label>
      <input type="checkbox" name="database_clear" id="db_clear" value="1" <?php if ($this->database_clear) { ?>checked="checked"<?php } ?> />
    </p>
    <p class="aligned last">
      <input id="btTestDB" class="button" type="button" value="<?php echo $this->translator->trans('Test your database connection now!', [], 'Install'); ?>"/>
    </p>

    <input class="text" type="hidden" id="rewrite_engine" name="rewrite_engine" value="0" />

    <?php if ($this->errors): ?>
      <p id="dbResultCheck" class="errorBlock"><?php echo implode('<br />', $this->errors); ?></p>
    <?php else: ?>
      <p id="dbResultCheck" style="display: none;"></p>
    <?php endif; ?>
  </div>
</div>
