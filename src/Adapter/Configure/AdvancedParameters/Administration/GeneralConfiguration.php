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

namespace PrestaShop\PrestaShop\Adapter\Configure\AdvancedParameters\Administration;

use Cookie;
use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use PrestaShop\PrestaShop\Core\Feature\FeatureInterface;
use PrestaShopBundle\Form\Admin\Configure\AdvancedParameters\Administration\GeneralType;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * Manages the configuration data about general options.
 */
class GeneralConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = [
        GeneralType::FIELD_CHECK_MODULES_UPDATE,
        GeneralType::FIELD_CHECK_IP_ADDRESS,
        GeneralType::FIELD_FRONT_COOKIE_LIFETIME,
        GeneralType::FIELD_BACK_COOKIE_LIFETIME,
        GeneralType::FIELD_COOKIE_SAMESITE,
    ];

    /**
     * @var Cookie
     */
    private $cookie;

    /**
     * @param Configuration $configuration
     * @param Context $shopContext
     * @param FeatureInterface $multistoreFeature
     * @param Cookie $cookie
     */
    public function __construct(
        Configuration $configuration,
        Context $shopContext,
        FeatureInterface $multistoreFeature,
        Cookie $cookie
    ) {
        parent::__construct($configuration, $shopContext, $multistoreFeature);
        $this->cookie = $cookie;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            GeneralType::FIELD_CHECK_MODULES_UPDATE => (bool) $this->configuration->get('PRESTASTORE_LIVE', null, $shopConstraint),
            GeneralType::FIELD_CHECK_IP_ADDRESS => (bool) $this->configuration->get('PS_COOKIE_CHECKIP', null, $shopConstraint),
            GeneralType::FIELD_FRONT_COOKIE_LIFETIME => (int) $this->configuration->get('PS_COOKIE_LIFETIME_FO', null, $shopConstraint),
            GeneralType::FIELD_BACK_COOKIE_LIFETIME => (int) $this->configuration->get('PS_COOKIE_LIFETIME_BO', null, $shopConstraint),
            GeneralType::FIELD_COOKIE_SAMESITE => $this->configuration->get('PS_COOKIE_SAMESITE', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        if ($this->validateConfiguration($configuration)) {
            if (!$this->validateSameSite($configuration[GeneralType::FIELD_COOKIE_SAMESITE])) {
                $errors[] = [
                    'key' => 'The SameSite=None is only available in secure mode.',
                    'domain' => 'Admin.Advparameters.Notification',
                    'parameters' => [],
                ];
            } else {
                $shopConstraint = $this->getShopConstraint();

                $this->updateConfigurationValue('PRESTASTORE_LIVE', GeneralType::FIELD_CHECK_MODULES_UPDATE, $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_CHECKIP', GeneralType::FIELD_CHECK_IP_ADDRESS, $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_LIFETIME_FO', GeneralType::FIELD_FRONT_COOKIE_LIFETIME, $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_LIFETIME_BO', GeneralType::FIELD_BACK_COOKIE_LIFETIME, $configuration, $shopConstraint);
                $this->updateConfigurationValue('PS_COOKIE_SAMESITE', GeneralType::FIELD_COOKIE_SAMESITE, $configuration, $shopConstraint);

                // Clear checksum to force the refresh
                $this->cookie->checksum = '';
                $this->cookie->write();
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
            ->setAllowedTypes(GeneralType::FIELD_CHECK_MODULES_UPDATE, 'bool')
            ->setAllowedTypes(GeneralType::FIELD_CHECK_IP_ADDRESS, 'bool')
            ->setAllowedTypes(GeneralType::FIELD_FRONT_COOKIE_LIFETIME, 'int')
            ->setAllowedTypes(GeneralType::FIELD_BACK_COOKIE_LIFETIME, 'int')
            ->setAllowedValues(GeneralType::FIELD_COOKIE_SAMESITE, Cookie::SAMESITE_AVAILABLE_VALUES)
        ;

        return $resolver;
    }

    /**
     * Validate SameSite.
     * The SameSite=None is only working when Secure is settled
     *
     * @param string $sameSite
     *
     * @return bool
     */
    protected function validateSameSite(string $sameSite): bool
    {
        $forceSsl = $this->configuration->get('PS_SSL_ENABLED') && $this->configuration->get('PS_SSL_ENABLED_EVERYWHERE');
        if ($sameSite === Cookie::SAMESITE_NONE) {
            return $forceSsl;
        }

        return true;
    }
}
