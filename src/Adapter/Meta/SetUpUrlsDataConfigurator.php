<?php
/**
 * 2007-2018 PrestaShop.
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
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShop\PrestaShop\Core\File\FileFinderInterface;
use PrestaShop\PrestaShop\Core\Util\Url\UrlFileCheckerInterface;

/**
 * Class SetUpUrlsDataConfigurator is responsible for saving, validating and getting configurations related with urls
 * configuration located in Shop parameters -> Traffic & Seo -> Seo & Urls.
 */
final class SetUpUrlsDataConfigurator implements DataConfigurationInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @var FileFinderInterface
     */
    private $htaccessFinder;

    /**
     * @var UrlFileCheckerInterface
     */
    private $htaccessFileChecker;

    /**
     * @var bool
     */
    private $isHostMode;

    /**
     * SetUpUrlsDataConfigurator constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param FileFinderInterface $htaccessFinder
     * @param UrlFileCheckerInterface $htaccessFileChecker
     * @param bool $isHostMode
     */
    public function __construct(
        ConfigurationInterface $configuration,
        FileFinderInterface $htaccessFinder,
        UrlFileCheckerInterface $htaccessFileChecker,
        $isHostMode
    )
    {
        $this->configuration = $configuration;
        $this->htaccessFinder = $htaccessFinder;
        $this->htaccessFileChecker = $htaccessFileChecker;
        $this->isHostMode = $isHostMode;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'friendly_url' => (bool) $this->configuration->get('PS_REWRITING_SETTINGS'),
            'accented_url' => (bool) $this->configuration->get('PS_ALLOW_ACCENTED_CHARS_URL'),
            'canonical_url_redirection' => $this->configuration->get('PS_CANONICAL_REDIRECT'),
            'disable_apache_multiview' => (bool) $this->configuration->get('PS_HTACCESS_DISABLE_MULTIVIEWS'),
            'disable_apache_mod_security' => (bool) $this->configuration->get('PS_HTACCESS_DISABLE_MODSEC'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_REWRITING_SETTINGS', $configuration['friendly_url']);
            $this->configuration->set('PS_ALLOW_ACCENTED_CHARS_URL', $configuration['accented_url']);
            $this->configuration->set('PS_CANONICAL_REDIRECT', $configuration['canonical_url_redirection']);

            if (!$this->isHostMode && $this->isHtaccessFileValid()) {
                $this->configuration->set('PS_HTACCESS_DISABLE_MULTIVIEWS', $configuration['disable_apache_multiview']);
                $this->configuration->set('PS_HTACCESS_DISABLE_MODSEC', $configuration['disable_apache_mod_security']);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {

        $isApacheConfigsValid = true;

        if (!$this->isHostMode && $this->isHtaccessFileValid()) {
            $isApacheConfigsValid = isset(
                $configuration['disable_apache_multiview'],
                $configuration['disable_apache_mod_security']
            );
        }

        return isset(
            $configuration['friendly_url'],
            $configuration['accented_url'],
            $configuration['canonical_url_redirection']
        ) && $isApacheConfigsValid;
    }

    /**
     * Checks whenever the htaccess file is valid.
     *
     * @return bool
     */
    private function isHtaccessFileValid()
    {
        list($htaccessFile) = $this->htaccessFinder->find();
        return $this->htaccessFileChecker->isValidFile($htaccessFile);
    }
}
