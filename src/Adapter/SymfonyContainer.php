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

namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SymfonyContainer.
 *
 * This is a TEMPORARY class for quick access to the Symfony Container
 */
final class SymfonyContainer
{
    /** @var ContainerInterface|null */
    private static $instance = null;

    /**
     * Get a singleton instance of SymfonyContainer.
     *
     * @return ContainerInterface|null
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {
            global $kernel;

            if (null !== $kernel && $kernel instanceof KernelInterface) {
                try {
                    self::$instance = $kernel->getContainer();
                } catch (\LogicException $e) {
                    self::$instance = null;
                }
            }
        }

        return self::$instance;
    }

    public static function resetStaticCache()
    {
        self::$instance = null;
    }
}
