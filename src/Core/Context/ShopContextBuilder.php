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

namespace PrestaShop\PrestaShop\Core\Context;

use PrestaShop\PrestaShop\Adapter\ContextStateManager;
use PrestaShop\PrestaShop\Adapter\Shop\Repository\ShopRepository;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopConstraint;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Exception\InvalidArgumentException;
use Shop as LegacyShop;

/**
 * @experimental Depends on ADR https://github.com/PrestaShop/ADR/pull/36
 */
class ShopContextBuilder implements LegacyContextBuilderInterface
{
    private ?ShopConstraint $shopConstraint = null;
    private ?int $shopId = null;
    private ?LegacyShop $legacyShop = null;

    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ContextStateManager $contextStateManager
    ) {
    }

    public function build(): ShopContext
    {
        $this->assertArguments();
        $legacyShop = $this->getLegacyShop();

        return new ShopContext(
            shopConstraint: $this->shopConstraint,
            id: (int) $legacyShop->id,
            name: $legacyShop->name,
            shopGroupId: (int) $legacyShop->id_shop_group,
            categoryId: (int) $legacyShop->id_category,
            themeName: $legacyShop->theme_name,
            color: $legacyShop->color,
            physicalUri: $legacyShop->physical_uri,
            virtualUri: $legacyShop->virtual_uri,
            domain: $legacyShop->domain,
            domainSSL: $legacyShop->domain_ssl,
            active: (bool) $legacyShop->active
        );
    }

    public function buildLegacyContext(): void
    {
        $this->assertArguments();
        // It is very important to start by setting the shop, because the ContextStateManager forcefully sets the Context shop to single shop when setShop
        // is called. If we set it first we can then correctly set the appropriate shop context based on the shop constraint
        $this->contextStateManager->setShop($this->getLegacyShop());

        // Now we properly set the context
        if ($this->shopConstraint->forAllShops()) {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_ALL);
        } elseif (!empty($this->shopConstraint->getShopGroupId())) {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_GROUP, $this->shopConstraint->getShopGroupId()->getValue());
        } else {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_SHOP, $this->shopConstraint->getShopId()->getValue());
        }
    }

    public function setShopId(int $shopId): self
    {
        $this->shopId = $shopId;

        return $this;
    }

    public function setShopConstraint(ShopConstraint $shopConstraint): self
    {
        $this->shopConstraint = $shopConstraint;

        return $this;
    }

    private function assertArguments(): void
    {
        if (null === $this->shopConstraint) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build shop context as no shopConstraint has been defined you need to call %s::setShopConstraint to define it before building the shop context',
                self::class
            ));
        }

        if (null === $this->shopId) {
            throw new InvalidArgumentException(sprintf(
                'Cannot build shop context as no shopId has been defined you need to call %s::setShopId to define it before building the shop context',
                self::class
            ));
        }
    }

    private function getLegacyShop(): LegacyShop
    {
        if (!$this->legacyShop) {
            $this->legacyShop = $this->shopRepository->get(new ShopId($this->shopId));
        }

        return $this->legacyShop;
    }
}
