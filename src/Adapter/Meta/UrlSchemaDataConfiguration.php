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

use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;
use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class UrlSchemaDataConfiguration is responsible for validating, updating and retrieving data used in
 * Shop parameters -> Traffix & Seo -> Seo & Urls -> Set Shop URL form field.
 */
final class UrlSchemaDataConfiguration implements DataConfigurationInterface
{
    /**
     * @var array
     */
    private $rules;

    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * UrlSchemaDataConfiguration constructor.
     *
     * @param ConfigurationInterface $configuration
     * @param array $rules
     */
    public function __construct(ConfigurationInterface $configuration, array $rules)
    {
        $this->rules = $rules;
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        $configResult = [];
        foreach ($this->rules as $routeId => $defaultRule) {
            $result = $this->getConfigurationValue($routeId) ?: $defaultRule;
            $configResult[$routeId] = $result;
        }

        return $configResult;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            foreach ($configuration as $routeId => $value) {
                $this->updateConfigurationValue($routeId, $value);
            }
        }

        return [];
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $configurationExists = true;
        foreach (array_keys($configuration) as $routeId) {
            $configurationExists &= isset($this->rules[$routeId]);
        }

        return $configurationExists;
    }

    /**
     * Gets configuration from configuration table.
     *
     * @param string $routeId
     *
     * @return string
     */
    private function getConfigurationValue($routeId)
    {
        return $this->configuration->get($this->getConfigurationKey($routeId));
    }

    /**
     * Updates configuration data.
     *
     * @param string $routeId
     * @param string $rule
     *
     * @return mixed
     */
    private function updateConfigurationValue($routeId, $rule)
    {
        return $this->configuration->set($this->getConfigurationKey($routeId), $rule);
    }

    /**
     * Gets key which is used to retrieve data from configuration table.
     *
     * @param string $routeId
     *
     * @return string
     */
    private function getConfigurationKey($routeId)
    {
        return sprintf('PS_ROUTE_%s', $routeId);
    }
}
