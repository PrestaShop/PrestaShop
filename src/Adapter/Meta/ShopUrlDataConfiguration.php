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

namespace PrestaShop\PrestaShop\Adapter\Meta;

use PrestaShop\PrestaShop\Adapter\File\HtaccessFileGenerator;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;
use PrestaShopException;
use ShopUrl;

/**
 * Class ShopUrlDataConfiguration is responsible for updating and getting data from shop_url table.
 */
final class ShopUrlDataConfiguration implements DataConfigurationInterface
{
    /**
     * @var ShopUrl
     */
    private $mainShopUrl;

    /**
     * @var HtaccessFileGenerator
     */
    private $htaccessFileGenerator;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * ShopUrlDataConfiguration constructor.
     *
     * @param ShopUrl $mainShopUrl
     * @param ConfigurationInterface $configuration
     */
    public function __construct(
        ShopUrl $mainShopUrl,
        ConfigurationInterface $configuration,
        HtaccessFileGenerator $htaccessFileGenerator
    ) {
        $this->mainShopUrl = $mainShopUrl;
        $this->configuration = $configuration;
        $this->htaccessFileGenerator = $htaccessFileGenerator;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'domain' => $this->mainShopUrl->domain,
            'domain_ssl' => $this->mainShopUrl->domain_ssl,
            'physical_uri' => $this->mainShopUrl->physical_uri,
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];

        try {
            if ($this->validateConfiguration($configuration)) {
                if (!$this->isValidUri($configuration['physical_uri'])) {
                    return [
                        [
                            'key' => 'The Base URI is not valid.',
                            'domain' => 'Admin.Shopparameters.Notification',
                            'parameters' => [],
                        ],
                    ];
                }
                $this->mainShopUrl->domain = $configuration['domain'];
                $this->mainShopUrl->domain_ssl = $configuration['domain_ssl'];
                $this->mainShopUrl->physical_uri = $configuration['physical_uri'];

                $this->mainShopUrl->update();

                $this->configuration->set('PS_SHOP_DOMAIN', $configuration['domain']);
                $this->configuration->set('PS_SHOP_DOMAIN_SSL', $configuration['domain_ssl']);
                $this->htaccessFileGenerator->generateFile();
            }
        } catch (PrestaShopException $exception) {
            $errors[] = $exception->getMessage();
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['domain'],
            $configuration['domain_ssl'],
            $configuration['physical_uri']
        );
    }

    /**
     * Check if it's a valid URI.
     *
     * @param string $uri
     *
     * @return bool
     */
    private function isValidUri($uri)
    {
        return is_string($uri) && preg_match('#^(?:[~\-_\/&\.\+]|\w|%\d+|\s)+$#', $uri);
    }
}
