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

/**
 * Class UrlSchemaDataConfiguration is responsible for validating, updating and retrieving data used in
 * Shop parameters -> Traffix & Seo -> Seo & Urls -> Set Shop URL form field.
 */
class UrlSchemaDataConfiguration implements DataConfigurationInterface
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
            $result = $this->getConfigurationValue($routeId) ? $this->getConfigurationValue($routeId) : $defaultRule;
            $configResult[$routeId] = $result;
        }

        return $configResult;
    }

    /**
     * {@inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        // TODO: Implement updateConfiguration() method.
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        // TODO: Implement validateConfiguration() method.
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
        return $this->configuration->get(sprintf('PS_ROUTE_%s', $routeId));
    }
}
