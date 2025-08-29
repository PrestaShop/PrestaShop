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

namespace PrestaShop\PrestaShop\Adapter\Smarty;

use PrestaShop\PrestaShop\Adapter\Configuration;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class will manage Smarty configuration for a Shop.
 */
class SmartyCacheConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    public function __construct(Configuration $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function getConfiguration()
    {
        return [
            'template_compilation' => $this->configuration->get('PS_SMARTY_FORCE_COMPILE'),
            'cache' => $this->configuration->getBoolean('PS_SMARTY_CACHE'),
            'multi_front_optimization' => $this->configuration->getBoolean('PS_SMARTY_LOCAL'),
            'clear_cache' => $this->configuration->get('PS_SMARTY_CLEAR_CACHE'),
            'smarty_console' => $this->configuration->get('PS_SMARTY_CONSOLE'),
            'smarty_console_key' => $this->configuration->get('PS_SMARTY_CONSOLE_KEY'),
        ];
    }

    /**
     * {@inheritdoc}
     *
     * Note: 'smarty_console' and 'smarty_console_key' keys are not allowed for update.
     */
    public function updateConfiguration(array $configuration)
    {
        $errors = [];
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_SMARTY_FORCE_COMPILE', $configuration['template_compilation']);
            $this->configuration->set('PS_SMARTY_CACHE', $configuration['cache']);
            $this->configuration->set('PS_SMARTY_LOCAL', $configuration['multi_front_optimization']);
            $this->configuration->set('PS_SMARTY_CLEAR_CACHE', $configuration['clear_cache']);
        }

        return $errors;
    }

    /**
     * {@inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        return isset(
            $configuration['template_compilation'],
            $configuration['cache'],
            $configuration['multi_front_optimization'],
            $configuration['clear_cache'],
            $configuration['smarty_console'],
            $configuration['smarty_console_key']
        );
    }
}
