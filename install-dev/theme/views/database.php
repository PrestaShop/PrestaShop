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

<!-- Database configuration -->
<div id="dbPart">
	<h2><?php echo $this->translator->trans('Configure your database by filling out the following fields', array(), 'Install'); ?></h2>
	<p>
		<?php echo $this->translator->trans('To use PrestaShop, you must <a href="http://doc.prestashop.com/display/PS16/Installing+PrestaShop#InstallingPrestaShop-Creatingadatabaseforyourshop" target="_blank">create a database</a> to collect all of your store\'s data-related activities.', array(), 'Install'); ?>
		<br />
		<?php echo $this->translator->trans('Please complete the fields below in order for PrestaShop to connect to your database.', array(), 'Install'); ?>
	</p>

	<div id="formCheckSQL">
		<p class="first" style="margin-top: 15px;">
			<label for="dbServer"><?php echo $this->translator->trans('Database server address', array(), 'Install'); ?> </label>
			<input size="25" class="text" type="text" id="dbServer" name="dbServer" value="<?php echo htmlspecialchars($this->database_server) ?>" />
			<span class="userInfos aligned"><?php echo $this->translator->trans('The default port is 3306. To use a different port, add the port number at the end of your server\'s address i.e ":4242".', array(), 'Install'); ?></span>
		</p>
		<p>
			<label for="dbName"><?php echo $this->translator->trans('Database name', array(), 'Install'); ?> </label>
			<input size="10" class="text" type="text" id="dbName" name="dbName" value="<?php echo htmlspecialchars($this->database_name) ?>" />
		</p>
		<p>
			<label for="dbLogin"><?php echo $this->translator->trans('Database login', array(), 'Install'); ?> </label>
			<input class="text" size="10" type="text" id="dbLogin" name="dbLogin" value="<?php echo htmlspecialchars($this->database_login) ?>" />
		</p>
		<p>
			<label for="dbPassword"><?php echo $this->translator->trans('Database password', array(), 'Install'); ?> </label>
			<input class="text" size="10" type="password" id="dbPassword" name="dbPassword" value="<?php echo htmlspecialchars($this->database_password) ?>" />
		</p>
		<!--
		<p>
			<label for="dbEngine"><?php echo $this->translator->trans('Database Engine', array(), 'Install'); ?></label>
			<select id="dbEngine" name="dbEngine">
				<option value="InnoDB" <?php if ($this->database_engine == 'InnoDB'): ?>selected="selected"<?php endif; ?>>InnoDB</option>
				<option value="MyISAM" <?php if ($this->database_engine == 'MyISAM'): ?>selected="selected"<?php endif; ?>>MyISAM</option>
			</select>
		</p>-->
		<p>
			<label for="db_prefix"><?php echo $this->translator->trans('Tables prefix', array(), 'Install'); ?></label>
			<input class="text" type="text" id="db_prefix" name="db_prefix" value="<?php echo htmlspecialchars($this->database_prefix) ?>" />
		</p>
		<?php if (_PS_MODE_DEV_): ?>
			<p>
				<label for="db_clear"><?php echo $this->translator->trans('Drop existing tables (mode dev)', array(), 'Install'); ?></label>
				<input type="checkbox" name="database_clear" id="db_clear" value="1" <?php if ($this->database_clear): ?>checked="checked"<?php endif; ?> />
			</p>
		<?php endif; ?>
		<p class="aligned last">
			<input id="btTestDB" class="button" type="button" value="<?php echo $this->translator->trans('Test your database connection now!', array(), 'Install'); ?>"/>
		</p>

		<input class="text" type="hidden" id="rewrite_engine" name="rewrite_engine" value="0" />

		<?php if ($this->errors): ?>
			<p id="dbResultCheck" class="errorBlock"><?php echo implode('<br />', $this->errors) ?></p>
		<?php else: ?>
			<p id="dbResultCheck" style="display: none;"></p>
		<?php endif; ?>
	</div>
</div>

<?php $this->displayTemplate('footer') ?>
