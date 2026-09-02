<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */
?>

<!-- License agreement -->
<h2 id="licenses-agreement"><?php echo $this->translator->trans('License Agreements', [], 'Install'); ?></h2>
<p><strong><?php echo $this->translator->trans('To enjoy the many features that are offered for free by PrestaShop, please read the license terms below. PrestaShop core is licensed under OSL 3.0, while the modules and themes are licensed under AFL 3.0.', [], 'Install'); ?></strong></p>
<div class="infosLicense">
    <?php echo $this->getTemplate('license_content'); ?>
</div>

<div class="checkLicense">
    <input type="checkbox" id="set_license" class="required" name="licence_agrement" value="1" style="margin-top: 2px;" <?php if ($this->session->licence_agrement) { ?>checked="checked"<?php } ?> />
    <div>
        <label for="set_license">
            <strong><?php echo $this->translator->trans('I agree to the above terms and conditions.', [], 'Install'); ?></strong>
        </label>
    </div>
</div>
<div class="infosBlock">
    <h4><?php echo $this->translator->trans('Privacy note', [], 'Install'); ?></h4>
    <p>
        <?php echo $this->translator->trans(
            'Some project modules may submit public and technical information about your store to the PrestaShop Project for analytics purposes. To learn more and make an informed choice, read %link%.',
            [
                '%link%' => '<a href="https://www.prestashop-project.org/data-transparency/" target="_blank">' .
                    $this->translator->trans('this article', [], 'Install')
                    . '</a>'
            ],
            'Install'
        ); ?>
    </p>
</div>
