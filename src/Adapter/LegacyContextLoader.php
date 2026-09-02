<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShop\PrestaShop\Adapter;

use Context;
use Currency;
use DummyAdminController;
use Employee;
use PrestaShop\PrestaShop\Core\Domain\Currency\ValueObject\Precision;
use RuntimeException;
use Shop;

/**
 * Helps loading specific context, for example in CLI context
 */
class LegacyContextLoader
{
    /**
     * @var Context
     */
    private $context;

    /**
     * @param Context $context
     */
    public function __construct(Context $context)
    {
        $this->context = $context;
    }

    /**
     * @param string|null $controllerClassName
     * @param int|null $currencyId
     * @param int|null $employeeId
     * @param int|null $shopId
     * @param int|null $shopGroupId
     *
     * @return self
     */
    public function loadGenericContext(
        ?string $controllerClassName = null,
        ?int $currencyId = null,
        ?int $employeeId = null,
        ?int $shopId = null,
        ?int $shopGroupId = null
    ): self {
        $this->loadCurrencyContext($currencyId);
        $this->loadEmployeeContext($employeeId);
        $this->loadControllerContext($controllerClassName);

        if (null !== $shopId) {
            $this->loadShopContext($shopId);
        }
        if (null !== $shopGroupId) {
            $this->loadShopGroupId($shopGroupId);
        }

        return $this;
    }

    /**
     * @param string|null $controllerClassName
     *
     * @return self
     */
    public function loadControllerContext(?string $controllerClassName = null): self
    {
        if (null === $controllerClassName) {
            $this->context->controller = new DummyAdminController();

            return $this;
        }

        if (!class_exists($controllerClassName)) {
            throw new RuntimeException(
                sprintf(
                    'Cannot load controller context for classname %s',
                    $controllerClassName
                )
            );
        }

        $this->context->controller = new $controllerClassName();

        return $this;
    }

    /**
     * @param int|null $currencyId
     *
     * @return self
     */
    public function loadCurrencyContext(?int $currencyId = null): self
    {
        if (null === $currencyId) {
            $currency = new Currency(Currency::getDefaultCurrencyId());
            $currency->precision = Precision::DEFAULT_PRECISION;
        } else {
            $currency = new Currency($currencyId);
        }

        $this->context->currency = $currency;

        return $this;
    }

    /**
     * @param int|null $employeeId
     *
     * @return self
     */
    public function loadEmployeeContext(?int $employeeId = null): self
    {
        $this->context->employee = new Employee($employeeId);

        return $this;
    }

    /**
     * @param int $shopId
     *
     * @return self
     */
    public function loadShopContext(int $shopId = 1): self
    {
        $this->context->shop = new Shop($shopId);
        Shop::setContext(Shop::CONTEXT_SHOP, $shopId);

        return $this;
    }

    /**
     * @param int $shopGroupId
     *
     * @return self
     */
    public function loadShopGroupId(int $shopGroupId): self
    {
        Shop::setContext(Shop::CONTEXT_GROUP, $shopGroupId);

        return $this;
    }
}
