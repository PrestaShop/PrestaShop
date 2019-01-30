<?php
/**
 * 2007-2019 PrestaShop and Contributors
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

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * Class SetUpUrlsDataConfiguration is responsible for saving, validating and getting configurations related with urls
 * configuration located in Shop parameters -> Traffic & Seo -> Seo & Urls.
 */
final class SetUpUrlsDataConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var HtaccessFileGenerator
     */
    private $htaccessFileGenerator;

    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * SetUpUrlsDataConfiguration constructor.
     *
     * @param Configuration $configuration
     * @param HtaccessFileGenerator $htaccessFileGenerator
     * @param TranslatorInterface $translator
     */
    public function __construct(
        Configuration $configuration,
        HtaccessFileGenerator $htaccessFileGenerator,
        TranslatorInterface $translator
    ) {
        $this->configuration = $configuration;
        $this->htaccessFileGenerator = $htaccessFileGenerator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'friendly_url' => $this->configuration->getBoolean('PS_REWRITING_SETTINGS'),
            'accented_url' => $this->configuration->getBoolean('PS_ALLOW_ACCENTED_CHARS_URL'),
            'canonical_url_redirection' => $this->configuration->get('PS_CANONICAL_REDIRECT'),
            'disable_apache_multiview' => $this->configuration->getBoolean('PS_HTACCESS_DISABLE_MULTIVIEWS'),
            'disable_apache_mod_security' => $this->configuration->getBoolean('PS_HTACCESS_DISABLE_MODSEC'),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_REWRITING_SETTINGS', $configuration['friendly_url']);
            $this->configuration->set('PS_ALLOW_ACCENTED_CHARS_URL', $configuration['accented_url']);
            $this->configuration->set('PS_CANONICAL_REDIRECT', $configuration['canonical_url_redirection']);
            $this->configuration->set('PS_HTACCESS_DISABLE_MULTIVIEWS', $configuration['disable_apache_multiview']);
            $this->configuration->set('PS_HTACCESS_DISABLE_MODSEC', $configuration['disable_apache_mod_security']);

            if (!$this->htaccessFileGenerator->generateFile($configuration['disable_apache_multiview'])) {
                $this->configuration->set('PS_REWRITING_SETTINGS', 0);

                $errorMessage = $this->translator
                    ->trans(
                        'Before being able to use this tool, you need to:',
                        [],
                        'Admin.Shopparameters.Notification'
                    );

                $errorMessage .= ' ';
                $errorMessage .= $this->translator
                    ->trans(
                        'Create a blank .htaccess in your root directory.',
                        [],
                        'Admin.Shopparameters.Notification'
                    );

                $errorMessage .= ' ';
                $errorMessage .= $this->translator
                    ->trans(
                        'Give it write permissions (CHMOD 666 on Unix system).',
                        [],
                        'Admin.Shopparameters.Notification'
                    );

                $errors[] = $errorMessage;
            }
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['friendly_url'],
            $configuration['accented_url'],
            $configuration['canonical_url_redirection'],
            $configuration['disable_apache_multiview'],
            $configuration['disable_apache_mod_security']
        );
    }
}
