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

<div id="contentInfosBlock">
  <h2><?php echo $this->translator->trans('Content of your store', [], 'Install'); ?></h2>

  <?php if (count($this->themes) === 1): ?>
    <input type="hidden" value="<?php echo current($this->themes)->get('name'); ?>" name="theme" />
  <?php else: ?>
    <div class="field clearfix">
      <label class="aligned"><?php echo $this->translator->trans('Installation of theme', [], 'Install'); ?></label>

      <div class="contentinput">
        <div class="themes-container">
          <?php foreach ($this->themes as $theme): ?>
            <div class="theme-card">
              <label>
                <input value="<?php echo $theme->get('name'); ?>" type="radio" name="theme" style="vertical-align: middle;" <?php if ($this->session->content_theme === $theme->get('name')): ?>checked="checked"<?php endif ?> autocomplete="off" />
                <?php echo $theme->get('display_name'); ?>
                <div class="image-block">
                  <img src="../<?php echo $theme->get('preview'); ?>" alt="<?php echo $theme->get('display_name'); ?>">
                </div>

                <p class="theme-version">
                  <strong><?php echo $this->translator->trans('Version:', [], 'Install'); ?></strong> <?php echo $theme->get('version'); ?>
                </p>
              </label>
            </div>
          <?php endforeach; ?>
        </div>
      </div>

      <p class="userInfos aligned"><?php echo $this->translator->trans('Select the theme to install', [], 'Install'); ?></p>
    </div>
  <?php endif; ?>

  <div class="field clearfix">
    <label class="aligned"><?php echo $this->translator->trans('Installation of demo products', [], 'Install'); ?></label>
    <div class="contentinput">
      <label>
        <input value="1" type="radio" name="install-fixtures" style="vertical-align: middle;" <?php if ($this->session->content_install_fixtures): ?>checked="checked"<?php endif; ?> autocomplete="off" />
        <?php echo $this->translator->trans('Yes', [], 'Install'); ?>
      </label>
      <label>
        <input value="0" type="radio" name="install-fixtures" style="vertical-align: middle;" <?php if (!$this->session->content_install_fixtures): ?>checked="checked"<?php endif; ?> autocomplete="off" />
        <?php echo $this->translator->trans('No', [], 'Install'); ?>
      </label>
    </div>
    <p class="userInfos aligned"><?php echo $this->translator->trans('Demo products are a good way to learn how to use PrestaShop. You should install them if you are not familiar with it.', [], 'Install'); ?></p>
  </div>

  <div class="field clearfix">
    <label class="aligned"><?php echo $this->translator->trans('Installation of modules', [], 'Install'); ?></label>
    <div class="contentinput">
      <ul class="modules-select-type">
        <li>
          <label>
            <input value="<?php echo static::MODULES_ALL; ?>" name="module-action" type="radio" autocomplete="off"<?php if ($this->moduleAction === static::MODULES_ALL): ?> checked="checked"<?php endif; ?>/>
            <?php echo $this->translator->trans('Install all modules (recommended)', [], 'Install') ?>
          </label>
        </li>
        <li>
          <label>
            <input value="<?php echo static::MODULES_SELECTED; ?>" name="module-action" type="radio" autocomplete="off" <?php if ($this->moduleAction === static::MODULES_SELECTED): ?> checked="checked"<?php endif; ?>/>
            <?php echo $this->translator->trans('Select the modules to install', [], 'Install') ?>
          </label>
        </li>
      </ul>
    </div>

    <p class="userInfos aligned"><?php echo $this->translator->trans('If you are using PrestaShop for the first time, you should install all modules now and uninstall the ones you don\'t need later.', [], 'Install'); ?></p>

    <div id="modules-container">
      <div>
        <input type="text" name="search" id="search-for-module" placeholder="<?php echo $this->translator->trans('Search', [], 'Install'); ?>">
      </div>

      <div>
        <label>
          <input type="checkbox" name="select-all"<?php if ($this->selectAllButton): ?> checked="checked"<?php endif; ?>>
          <?php echo $this->translator->trans('Select all', [], 'Install'); ?>
        </label>
      </div>

      <dl>
        <?php foreach ($this->getModulesPerCategories() as $category): ?>
          <?php if (empty($category->modules)): ?>
            <?php continue; ?>
          <?php endif; ?>

          <dt><?php echo $this->translator->trans($category->name, [], 'Install'); ?></dt>

          <?php foreach ($category->modules as $module): ?>
            <dd>
              <label>
                <input value="<?php echo $module->get('name') ?>" type="checkbox" name="modules[]" <?php if ($this->session->content_modules === null || is_array($this->session->content_modules) && in_array($module->get('name'), $this->session->content_modules)): ?>checked="checked"<?php endif; ?> autocomplete="off" />
                <?php echo $module->get('displayName') ?>
              </label>
            </dd>
          <?php endforeach; ?>
        <?php endforeach; ?>
      </dl>
    </div>
  </div>
</div>
