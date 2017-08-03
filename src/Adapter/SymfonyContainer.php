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

namespace PrestaShop\PrestaShop\Adapter;

use Symfony\Component\HttpKernel\KernelInterface;

/**
 * Class SymfonyContainer
 *
 * This is a TEMPORARY class for quick access to the Symfony Container
 */
final class SymfonyContainer
{
    /** @var self */
    private static $instance = null;

    /**
     * Get a singleton instance of SymfonyContainer
     *
     * @return \Symfony\Component\DependencyInjection\ContainerInterface;
     */
    public static function getInstance()
    {
        if (!isset(self::$instance)) {

            global $kernel;

            if (!is_null($kernel) && $kernel instanceof KernelInterface) {
                self::$instance = $kernel->getContainer();
            }
        }

        return self::$instance;
    }
}
