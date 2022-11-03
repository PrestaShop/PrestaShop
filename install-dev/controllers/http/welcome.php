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

/**
 * Step 1 : display language form
 */
class InstallControllerHttpWelcome extends InstallControllerHttp implements HttpConfigureInterface
{
    /**
     * @var bool
     */
    public $can_upgrade;
    /**
     * @var string
     */
    public $ps_version;

    /**
     * {@inheritdoc}
     */
    public function process(): void
    {
        if (Tools::getValue('language')) {
            $this->session->lang = Tools::getValue('language');
        }

        $locale = $this->language->getLanguage($this->session->lang)->locale;
        if (!empty($this->session->lang) && !is_file(_PS_ROOT_DIR_ . '/translations/' . $locale . '/Install.' . $locale . '.xlf')) {
            Language::downloadLanguagePack($this->session->lang, _PS_VERSION_);
            Language::installSfLanguagePack($locale);
            $this->clearCache();
        }
        if (Tools::getIsset('language') && is_dir(_PS_ROOT_DIR_ . '/translations/' . $locale)) {
            $this->redirect('welcome');
        }
    }

    /**
     * {@inheritdoc}
     */
    public function display(): void
    {
        $this->can_upgrade = false;
        if (file_exists(_PS_ROOT_DIR_ . '/config/settings.inc.php')) {
            if (version_compare(_PS_VERSION_, _PS_INSTALL_VERSION_, '<')) {
                $this->can_upgrade = true;
                $this->ps_version = _PS_VERSION_;
            }
        }

        $this->displayContent('welcome');
    }

    private function clearCache()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->remove(_PS_CACHE_DIR_);
    }
}
