<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
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
        if (file_exists(_PS_ROOT_DIR_ . '/app/config/parameters.php')) {
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
