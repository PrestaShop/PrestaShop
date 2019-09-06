<?php
/**
 * 2007-2019 PrestaShop SA and Contributors
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Open Software License (OSL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
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
 * needs please refer to https://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2019 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

/**
 * Step 1 : display language form
 */
class InstallControllerHttpWelcome extends InstallControllerHttp implements HttpConfigureInterface
{
    public function processNextStep()
    {
    }

    public function validate()
    {
        return true;
    }

    /**
     * Change language
     */
    public function process()
    {
        if (Tools::getValue('language')) {
            $this->session->lang = Tools::getValue('language');
        }

        $locale = $this->language->getLanguage($this->session->lang)->locale;
        if (!empty($this->session->lang) && !is_file(_PS_ROOT_DIR_ . '/app/Resources/translations/' . $locale . '/Install.' . $locale . '.xlf')) {
            Language::downloadLanguagePack($this->session->lang, _PS_VERSION_);
            Language::installSfLanguagePack($locale);
            $this->clearCache();
        }
        if (Tools::getIsset('language') && is_dir(_PS_ROOT_DIR_ . '/app/Resources/translations/' . $locale)) {
            $this->redirect('welcome');
        }
    }

    /**
     * Display welcome step
     */
    public function display()
    {
        $this->can_upgrade = false;
        if (file_exists(_PS_ROOT_DIR_.'/config/settings.inc.php')) {
            if (version_compare(_PS_VERSION_, _PS_INSTALL_VERSION_, '<')) {
                $this->can_upgrade = true;
                $this->ps_version = _PS_VERSION_;
            }
        }

        $this->displayTemplate('welcome');
    }

    private function clearCache()
    {
        $fs = new \Symfony\Component\Filesystem\Filesystem();
        $fs->remove(_PS_ROOT_DIR_ . '/var/cache/' . _PS_ENV_);
    }
}
