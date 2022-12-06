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
 * needs please refer to https://devdocs.prestashop.com/ for more information.
 *
 * @author    PrestaShop SA and Contributors <contact@prestashop.com>
 * @copyright Since 2007 PrestaShop SA and Contributors
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 */

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * Class SetUpUrlsDataConfiguration is responsible for saving, validating and getting configurations related with urls
 * configuration located in Shop parameters -> Traffic & Seo -> Seo & Urls.
 */
final class SetUpUrlsDataConfiguration extends AbstractMultistoreConfiguration
{
    /**
     * @var array<int, string>
     */
    private const CONFIGURATION_FIELDS = [
        'friendly_url',
        'accented_url',
        'canonical_url_redirection',
        'disable_apache_multiview',
        'disable_apache_mod_security',
    ];

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
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param HtaccessFileGenerator $htaccessFileGenerator
     * @param TranslatorInterface $translator
     */
    public function __construct(Configuration $configuration, Context $shopContext, FeatureInterface $multistoreFeature, HtaccessFileGenerator $htaccessFileGenerator, TranslatorInterface $translator)
    {
        parent::__construct($configuration, $shopContext, $multistoreFeature);

        $this->htaccessFileGenerator = $htaccessFileGenerator;
        $this->translator = $translator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'friendly_url' => (bool) $this->configuration->get('PS_REWRITING_SETTINGS', false, $shopConstraint),
            'accented_url' => (bool) $this->configuration->get('PS_ALLOW_ACCENTED_CHARS_URL', false, $shopConstraint),
            'canonical_url_redirection' => (int) $this->configuration->get('PS_CANONICAL_REDIRECT', 0, $shopConstraint),
            'disable_apache_multiview' => (bool) $this->configuration->get('PS_HTACCESS_DISABLE_MULTIVIEWS', false, $shopConstraint),
            'disable_apache_mod_security' => (bool) $this->configuration->get('PS_HTACCESS_DISABLE_MODSEC', false, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();

            $this->updateConfigurationValue('PS_REWRITING_SETTINGS', 'friendly_url', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_ALLOW_ACCENTED_CHARS_URL', 'accented_url', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_CANONICAL_REDIRECT', 'canonical_url_redirection', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_HTACCESS_DISABLE_MULTIVIEWS', 'disable_apache_multiview', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_HTACCESS_DISABLE_MODSEC', 'disable_apache_mod_security', $configuration, $shopConstraint);

            if (!$this->htaccessFileGenerator->generateFile($configuration['disable_apache_multiview'])) {
                $this->updateConfigurationValue('PS_REWRITING_SETTINGS', 'friendly_url', ['friendly_url' => false], $shopConstraint);

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
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $resolver = (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('friendly_url', 'bool')
            ->setAllowedTypes('accented_url', 'bool')
            ->setAllowedTypes('canonical_url_redirection', 'int')
            ->setAllowedTypes('disable_apache_multiview', 'bool')
            ->setAllowedTypes('disable_apache_mod_security', 'bool');

        return $resolver;
    }
}
