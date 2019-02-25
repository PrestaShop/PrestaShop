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

namespace PrestaShop\PrestaShop\Core\Localization\RTL;

use PrestaShop\PrestaShop\Core\ConfigurationInterface;

/**
 * Class StyleSheetProcessorFactory
 */
final class StyleSheetProcessorFactory implements StyleSheetProcessorFactoryInterface
{
    /**
     * @var ConfigurationInterface
     */
    private $configuration;

    /**
     * @param ConfigurationInterface $configuration
     */
    public function __construct(ConfigurationInterface $configuration)
    {
        $this->configuration = $configuration;
    }

    /**
     * {@inheritdoc}
     */
    public function create()
    {
        $rootDir = $this->configuration->get('_PS_ROOT_DIR_');
        $moduleDir = $this->configuration->get('_PS_MODULE_DIR_');

        if (null === $adminDir = $this->configuration->get('_PS_ADMIN_DIR_')) {
            $adminDir = $rootDir . DIRECTORY_SEPARATOR . 'admin';
            $adminDir = is_dir($adminDir) ? $adminDir : ($adminDir . '-dev');
        }

        $themesDir = $this->configuration->get('_PS_ROOT_DIR_') . DIRECTORY_SEPARATOR . 'themes';

        // @todo: improve modules configuration
        // see: https://github.com/PrestaShop/PrestaShop/pull/11169#discussion_r231824489
        $modulesToProcess = [
            $moduleDir . 'gamification',
            $moduleDir . 'welcome',
            $moduleDir . 'cronjobs',
        ];

        return new Processor(
            $adminDir,
            $themesDir,
            $modulesToProcess
        );
    }
}
