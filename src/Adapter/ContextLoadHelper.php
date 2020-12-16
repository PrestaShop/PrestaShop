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

namespace CommandLineUtils\Controller {
    use PrestaShop\PrestaShop\Adapter\SymfonyContainer;

    class DummyControllerCore extends \ControllerCore
    {
        public function __construct()
        {
            parent::__construct();

            $this->id = 0;
            $this->controller_type = 'dummy';
        }

        public function checkAccess()
        {
            return true;
        }

        public function viewAccess()
        {
            return true;
        }

        public function postProcess()
        {
            return null;
        }

        public function display()
        {
            return '';
        }

        public function setMedia()
        {
            return null;
        }

        public function initHeader()
        {
            return '';
        }

        public function initContent()
        {
            return '';
        }

        public function initCursedPage()
        {
            return '';
        }

        public function initFooter()
        {
            return '';
        }

        protected function redirect()
        {
            return '';
        }

        protected function buildContainer()
        {
            return SymfonyContainer::getInstance();
        }
    }
}

namespace PrestaShop\PrestaShop\Adapter {
    use CommandLineUtils\Controller\DummyControllerCore;
    use Context;
    use Currency;
    use Employee;
    use Shop;

    /**
     * Helps loading specific context, for example in CLI context
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

        /**
         * @param string|null $controllerClassName
         * @param int|null $currencyId
         * @param int|null $employeeId
         * @param int|null $shopId
         * @param int|null $shopGroupId
         */
        public function loadGenericContext(
            ?string $controllerClassName = null,
            ?int $currencyId = null,
            ?int $employeeId = null,
            ?int $shopId = null,
            ?int $shopGroupId = null
        ) {
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

        /**
         * @param string|null $controllerClassName
         */
        public function loadControllerContext(?string $controllerClassName = null)
        {
            if (null === $controllerClassName) {
                $this->context->controller = new DummyControllerCore();

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

        /**
         * @param int|null $currencyId
         */
        public function loadCurrencyContext(?int $currencyId = null)
        {
            $currency = new Currency($currencyId);
            if (null === $currencyId) {
                $currency->precision = 2;
            }

            $this->context->currency = $currency;
        }

        /**
         * @param int|null $employeeId
         */
        public function loadEmployeeContext(?int $employeeId = null)
        {
            $this->context->employee = new Employee($employeeId);
        }

        /**
         * @param int $shopId
         */
        public function loadShopContext(int $shopId = 1)
        {
            $this->context->shop = new Shop($shopId);
            Shop::setContext(Shop::CONTEXT_SHOP, $shopId);
        }

        /**
         * @param int $shopGroupId
         */
        public function loadShopGroupId(int $shopGroupId)
        {
            Shop::setContext(Shop::CONTEXT_GROUP, $shopGroupId);
        }
    }
}
