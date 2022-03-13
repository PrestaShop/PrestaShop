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

namespace PrestaShop\PrestaShop\Adapter\Media;

use PrestaShop\PrestaShop\Core\Configuration\AbstractMultistoreConfiguration;
use Symfony\Component\OptionsResolver\OptionsResolver;

/**
 * This class will provide Media servers configuration for a Shop.
 */
class MediaServerConfiguration extends AbstractMultistoreConfiguration
{
    private const CONFIGURATION_FIELDS = ['media_server_one', 'media_server_two', 'media_server_three'];

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $shopConstraint = $this->getShopConstraint();

        return [
            'media_server_one' => $this->configuration->get('PS_MEDIA_SERVER_1', null, $shopConstraint),
            'media_server_two' => $this->configuration->get('PS_MEDIA_SERVER_2', null, $shopConstraint),
            'media_server_three' => $this->configuration->get('PS_MEDIA_SERVER_3', null, $shopConstraint),
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $shopConstraint = $this->getShopConstraint();
            $this->updateConfigurationValue('PS_MEDIA_SERVER_1', 'media_server_one', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_MEDIA_SERVER_2', 'media_server_two', $configuration, $shopConstraint);
            $this->updateConfigurationValue('PS_MEDIA_SERVER_3', 'media_server_three', $configuration, $shopConstraint);

            $serverOne = $configuration['media_server_one'];
            $serverTwo = $configuration['media_server_two'];
            $serverThree = $configuration['media_server_three'];
            $hasMediaServer = !empty($serverOne) || !empty($serverTwo) || !empty($serverThree);
            $this->configuration->set('PS_MEDIA_SERVERS', $hasMediaServer ? 1 : 0);
        }

        return [];
    }

    /**
     * @return OptionsResolver
     */
    protected function buildResolver(): OptionsResolver
    {
        $isValidDomain = function (string $domainName): bool {
            return empty($domainName)
                || false !== filter_var($domainName, FILTER_VALIDATE_DOMAIN)
                && false !== filter_var(gethostbyname($domainName), FILTER_VALIDATE_IP);
        };

        return (new OptionsResolver())
            ->setDefined(self::CONFIGURATION_FIELDS)
            ->setAllowedTypes('media_server_one', 'string')
            ->setAllowedValues('media_server_one', $isValidDomain)
            ->setAllowedTypes('media_server_two', 'string')
            ->setAllowedValues('media_server_two', $isValidDomain)
            ->setAllowedTypes('media_server_three', 'string')
            ->setAllowedValues('media_server_three', $isValidDomain);
    }
}
