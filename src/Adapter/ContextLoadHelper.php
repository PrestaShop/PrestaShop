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

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Context;
use Currency;
use DummyController;
use Employee;
use Shop;

/**
 * Helps loading specific context
 */
class ContextLoadHelper
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param LegacyContext $context
     */
    public function __construct(LegacyContext $context)
    {
        $this->context = $context->getContext();
    }

    public function loadGenericContext(
        ?string $controllerClassName = null,
        ?int $currencyId = null,
        ?int $employeeId = null,
        ?int $shopId = null,
        ?int $shopGroupId = null)
    {
        $this->loadCurrencyContext($currencyId);
        $this->loadControllerContext($controllerClassName);
        $this->loadEmployeeContext($employeeId);

        if (null !== $shopId) {
            $this->loadShopContext($shopId);
        }
        if (null !== $shopGroupId) {
            $this->loadShopGroupId($shopGroupId);
        }
    }

    public function loadControllerContext(?string $controllerClassName = null)
    {
        if (null === $controllerClassName) {
            $this->context->controller = new DummyController();

            return;
        }

        if (!class_exists($controllerClassName)) {
            throw new \RuntimeException(
                sprintf(
                    'Cannot load controller context for classname %s',
                    $controllerClassName
                )
            );
        }

        $this->context->controller = new $controllerClassName();
    }

    public function loadCurrencyContext(?int $currencyId = null)
    {
        $currency = new Currency($currencyId);
        if (null === $currencyId) {
            $currency->precision = 2;
        }

        $this->context->currency = $currency;
    }

    public function loadEmployeeContext(?int $employeeId = null)
    {
        $this->context->employee = new Employee($employeeId);
    }

    public function loadShopContext(int $shopId = 1)
    {
        $this->context->shop = new Shop($shopId);
        Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
    }

    public function loadShopGroupId(int $shopGroupId)
    {
        Shop::setContext(Shop::CONTEXT_GROUP, $shopGroupId);
    }

    public function loadAdminDirectoryContext(string $directory)
    {
        define('_PS_ADMIN_DIR_', $directory);
    }
}
