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
 * needs please refer to https://devdocs.prestashop-project.org/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */
?>

<script type="text/javascript">
  <!--
  var install_is_done = '<?php echo addslashes($this->translator->trans('Done!', [], 'Install')); ?>';
  var process_steps = <?php echo json_encode($this->process_steps); ?>;
  var admin = '<?php echo file_exists('../admin-dev') ? '../admin-dev' : '../admin'; ?>';
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
        <li id="process_step_<?php echo $item['key']; ?>" class="process_step">
          <?php echo $item['lang']; ?>
        </li>
      <?php endforeach; ?>
    </ol>

    <div id="error_process">
      <h3><?php echo $this->translator->trans('An error occurred during installation...', [], 'Install'); ?></h3>
      <p><?php echo $this->translator->trans('You can use the links on the left column to go back to the previous steps, or restart the installation process by <a href="%link%">clicking here</a>.', ['%link%' => 'index.php?restart=true'], 'Install'); ?></p>
    </div>
  </div>
</div>

<div id="install_process_success">
  <div class="clearfix">
    <h2><?php echo $this->translator->trans('Your installation is finished!', [], 'Install'); ?></h2>
    <p><?php echo $this->translator->trans('You have just finished installing your shop. Thank you for using PrestaShop!', [], 'Install'); ?></p>
    <p><?php echo $this->translator->trans('Please remember your login information:', [], 'Install'); ?></p>
    <table cellpadding="0" cellspacing="0" border="0" id="resultInstall" width="620">
      <tr class="odd">
        <td class="label"><?php echo $this->translator->trans('E-mail', [], 'Install'); ?></td>
        <td class="resultEnd"><?php echo htmlspecialchars($this->session->admin_email); ?></td>
        <td rowspan="2" class="print" onclick="$('#password_content').text('<?php echo htmlspecialchars(addslashes($this->session->admin_password)); ?>'); $('#password_display').hide(); window.print();">
          <img src="theme/img/print.png" alt="" style="vertical-align:top">
          <?php echo $this->translator->trans('Print my login information', [], 'Install'); ?>
        </td>
      </tr>
      <tr>
        <td class="label"><?php echo $this->translator->trans('Password', [], 'Install'); ?></td>
        <td class="resultEnd">
          <span id="password_content"><?php echo preg_replace('#.#', '*', $this->session->admin_password); ?></span>
          <span id="password_display">
            (<a href="#" onclick="$('#password_content').text('<?php echo htmlspecialchars(addslashes($this->session->admin_password)); ?>'); $('#password_display').hide(); return false"><?php echo $this->translator->trans('Display', [], 'Install'); ?></a>)
          </span>
        </td>
      </tr>
    </table>

    <h3 class="infosBlock">
      <?php echo $this->translator->trans('For security purposes, you must delete the "install" folder.', [], 'Install'); ?>
    </h3>

    <div id="boBlock" class="blockInfoEnd clearfix" onclick="window.open(admin)">
      <img src="theme/img/visu_boBlock.png" alt="" />
      <h3><?php echo $this->translator->trans('Back Office', [], 'Install'); ?></h3>
      <p class="description"><?php echo $this->translator->trans('Manage your store using your Back Office. Manage your orders and customers, add modules, change themes, etc.', [], 'Install'); ?></p>
      <p>
        <a class="BO" target="_blank"><span><?php echo $this->translator->trans('Manage your store', [], 'Install'); ?></span></a>
      </p>
    </div>

    <div id="foBlock" class="blockInfoEnd last clearfix" onclick="window.open('../')" />
      <img src="theme/img/visu_foBlock.png" alt="" />
      <h3><?php echo $this->translator->trans('Front Office', [], 'Install'); ?></h3>
      <p class="description"><?php echo $this->translator->trans('Discover your store as your future customers will see it!', [], 'Install'); ?></p>
      <p>
        <a class="FO" target="_blank"><span><?php echo $this->translator->trans('Discover your store', [], 'Install'); ?></span></a>
      </p>
    </div>
  </div>
</div>

<?php echo $this->getHook('install-finished'); ?>
