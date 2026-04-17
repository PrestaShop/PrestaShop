<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
?>

<script type="text/javascript">
    <!--
    var install_is_done = '<?php echo addslashes($this->translator->trans('Done!', [], 'Install')); ?>';
    var process_steps = <?php echo json_encode($this->process_steps); ?>;
    -->
</script>

<div id="install_process_form">
    <div id="progress_bar">
        <h2 class="installing"></h2>

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

<div id="warning_process">
    <h3><?php echo $this->translator->trans('A warning was triggered during installation', [], 'Install'); ?></h3>
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

        <button class="button" rowspan="2" class="print" onclick="$('#password_content').text('<?php echo htmlspecialchars(addslashes($this->session->admin_password)); ?>'); $('#password_display').hide(); window.print(); return false">
            <?php echo $this->translator->trans('Print my login information', [], 'Install'); ?>
        </button>

        <hr />

        <h3 class="messagesBlock">
            <?php echo $this->translator->trans('For security purposes, you must delete the "install" folder.', [], 'Install'); ?>
        </h3>

        <div class="fo-bo-container">
          <div id="boBlock" class="blockInfoEnd clearfix" onclick="window.open('<?php echo '../' . $this->session->adminFolderName; ?>')">
              <img src="theme/img/visu_boBlock.png" alt="" />
              <div class="bo-infos">
                <h3><?php echo $this->translator->trans('Back Office', [], 'Install'); ?></h3>
                <p class="description"><?php echo $this->translator->trans('Manage your store using your Back Office. Manage your orders and customers, add modules, change themes, etc.', [], 'Install'); ?></p>
                <a class="button button--small" target="_blank">
                  <?php echo $this->translator->trans('Manage your store', [], 'Install'); ?>
                </a>
              </div>
          </div>

          <div id="foBlock" class="blockInfoEnd last clearfix" onclick="window.open('../')">
              <img src="theme/img/visu_foBlock.png" alt="" />
              <div class="bo-infos">
                <h3><?php echo $this->translator->trans('Front Office', [], 'Install'); ?></h3>
                <p class="description"><?php echo $this->translator->trans('Discover your store as your future customers will see it!', [], 'Install'); ?></p>
                <a class="button button--small" target="_blank">
                  <?php echo $this->translator->trans('Discover your store', [], 'Install'); ?>
                </a>
              </div>
          </div>
        </div>
    </div>
</div>

<?php echo $this->getHook('install-finished'); ?>
