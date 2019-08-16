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

namespace PrestaShop\PrestaShop\Adapter\Console;

use Currency;
use Employee;
use ConsoleController;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Exception\LogicException;
use PrestaShop\PrestaShop\Adapter\Shop\Context as ShopContext;
use PrestaShop\PrestaShop\Core\Console\ContextLoaderInterface;

/**
 * PrestaShop Context in Console Application
 */
final class ContextLoader implements ContextLoaderInterface
{
    private $legacyContext;
    private $shopContext;
    private $rootDir;

    public function __construct(LegacyContext $legacyContext, ShopContext $shopContext, $rootDir)
    {
        $this->legacyContext = $legacyContext;
        $this->shopContext = $shopContext;
        $this->rootDir = $rootDir;

        require_once $rootDir . '/../config/config.inc.php';
    }

    /**
     * {@inheritdoc}
     */
    public function loadConsoleContext(InputInterface $input)
    {
        if (!defined('_PS_ADMIN_DIR_')) {
            define('_PS_ADMIN_DIR_', $this->rootDir);
        }
        $employeeId = $input->getOption('employee');
        $shopId = $input->getOption('id_shop');
        $shopGroupId = $input->getOption('id_shop_group');

        if ($shopId && $shopGroupId) {
            throw new LogicException('Do not specify an ID shop and an ID group shop at the same time.');
        }

        $this->legacyContext->getContext()->controller = new ConsoleController();

        if (!$this->legacyContext->getContext()->employee) {
            $this->legacyContext->getContext()->employee = new Employee($employeeId);
        }

        $shop = $this->legacyContext->getContext()->shop;
        $shop::setContext(1);

        if ($shopId === null) {
            $shopId = 1;
        }
        $this->shopContext->setShopContext($shopId);
        $this->legacyContext->getContext()->shop = $shop;

        if ($shopGroupId !== null) {
            $this->shopContext->setShopGroupContext($shopGroupId);
        }

        $this->legacyContext->getContext()->currency = new Currency();
    }
}
