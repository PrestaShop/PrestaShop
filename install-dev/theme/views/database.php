<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
?>

<!-- Database configuration -->
<div id="dbPart">
  <h2><?php echo $this->translator->trans('Configure your database by filling out the following fields', [], 'Install'); ?></h2>
  <p>
      <?php echo $this->translator->trans('To use PrestaShop, you must <a href="https://devdocs.prestashop-project.org/9/basics/installation/#creating-a-database-for-your-shop" target="_blank">create a database</a> to collect all of your store\'s data-related activities.', array(), 'Install'); ?>
    <br />
    <?php echo $this->translator->trans('Please complete the fields below in order for PrestaShop to connect to your database.', [], 'Install'); ?>
  </p>
  <div id="formCheckSQL">
    <div class="field">
      <label for="dbServer"><?php echo $this->translator->trans('Database server address', [], 'Install'); ?> </label>
      <input size="25" class="text" type="text" id="dbServer" name="dbServer" value="<?php echo htmlspecialchars($this->database_server ?? ''); ?>" />
      <span class="userInfos aligned"><?php echo $this->translator->trans('The default port is 3306. To use a different port, add the port number at the end of your server\'s address i.e ":4242".', [], 'Install'); ?></span>
    </div>
    <div class="field">
      <label for="dbName"><?php echo $this->translator->trans('Database name', [], 'Install'); ?> </label>
      <input size="10" class="text" type="text" id="dbName" name="dbName" value="<?php echo htmlspecialchars($this->database_name ?? ''); ?>" />
    </div>
    <div class="field">
      <label for="dbLogin"><?php echo $this->translator->trans('Database login', [], 'Install'); ?> </label>
      <input class="text" size="10" type="text" id="dbLogin" name="dbLogin" value="<?php echo htmlspecialchars($this->database_login ?? ''); ?>" />
    </div>
    <div class="field">
      <label for="dbPassword"><?php echo $this->translator->trans('Database password', [], 'Install'); ?> </label>
      <input class="text" size="10" type="password" id="dbPassword" name="dbPassword" value="<?php echo htmlspecialchars($this->database_password ?? ''); ?>" />
    </div>
    <div class="field">
      <label for="db_prefix"><?php echo $this->translator->trans('Tables prefix', [], 'Install'); ?></label>
      <input class="text" type="text" id="db_prefix" name="db_prefix" value="<?php echo htmlspecialchars($this->database_prefix ?? ''); ?>" />
    </div>
    <div class="field">
      <label for="db_clear"><?php echo $this->translator->trans('Drop existing tables', [], 'Install'); ?></label>
      <input type="checkbox" name="database_clear" id="db_clear" value="1" <?php if ($this->database_clear) { ?>checked="checked"<?php } ?> />
    </div>
    <div class="field">
      <input id="btTestDB" class="button" type="button" value="<?php echo $this->translator->trans('Test your database connection now!', [], 'Install'); ?>"/>
    </div>

    <input class="text" type="hidden" id="rewrite_engine" name="rewrite_engine" value="0" />

    <?php if ($this->errors): ?>
      <p id="dbResultCheck" class="errorBlock"><?php echo implode('<br />', $this->errors); ?></p>
    <?php else: ?>
      <p id="dbResultCheck" style="display: none;"></p>
    <?php endif; ?>
  </div>
</div>
