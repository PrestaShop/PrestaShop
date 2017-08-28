<?php
/**
 * 2007-2017 PrestaShop
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
 * @copyright 2007-2017 PrestaShop SA
 * @license   https://opensource.org/licenses/OSL-3.0 Open Software License (OSL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

namespace PrestaShop\PrestaShop\Adapter\Debug;

use PrestaShop\PrestaShop\Adapter\Configuration;
use Symfony\Component\OptionsResolver\OptionsResolver;
use PrestaShop\PrestaShop\Core\Configuration\DataConfigurationInterface;

/**
 * This class manages Debug mode configuration for a Shop
 */
class DebugModeConfiguration implements DataConfigurationInterface
{
    /**
     * @var Configuration
     */
    private $configuration;

    /**
     * @var DebugMode $debugMode Debug mode manager
     */
    private $debugMode;

    public function __construct(DebugMode $debugMode, Configuration $configuration)
    {
        $this->debugMode = $debugMode;
        $this->configuration = $configuration;
    }

    /**
     * Returns configuration used to manage Debug mode in back office
     *
     * @return array
     */
    public function getConfiguration()
    {
        return array(
            'disable_non_native_modules' => $this->configuration->get('PS_DISABLE_NON_NATIVE_MODULE'),
            'disable_overrides' => $this->configuration->get('PS_DISABLE_OVERRIDES'),
            'debug_mode' => $this->debugMode->isDebugModeEnabled(),
        );
    }

    /**
     * @{inheritdoc}
     */
    public function updateConfiguration(array $configuration)
    {
        if ($this->validateConfiguration($configuration)) {
            $this->configuration->set('PS_DISABLE_NON_NATIVE_MODULE', $configuration['disable_non_native_modules']);
            $this->configuration->set('PS_DISABLE_OVERRIDES', $configuration['disable_overrides']);

            $this->updateDebugMode($configuration['debug_mode']);
        }

        return array();
    }

    /**
     * @{inheritdoc}
     */
    public function validateConfiguration(array $configuration)
    {
        $resolver = new OptionsResolver();
        $resolver->setRequired(
            array(
                'disable_non_native_modules',
                'disable_overrides',
                'debug_mode',
            )
        );
        $resolver->resolve($configuration);

        return true;
    }

    /**
     * Change Debug mode value if needed
     *
     * @param $enableStatus
     * @return void
     */
    private function updateDebugMode($enableStatus)
    {
        $currentDebugMode = $this->debugMode->isDebugModeEnabled();

        if ($enableStatus === $currentDebugMode) {
            true === $enableStatus ? $this->debugMode->enable() : $this->debugMode->disable();
        }
    }
}
