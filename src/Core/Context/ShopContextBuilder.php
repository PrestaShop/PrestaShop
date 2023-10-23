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
class ShopContextBuilder
{
    private ?ShopConstraint $shopConstraint = null;
    private ?int $shopId = null;

    public function __construct(
        private readonly ShopRepository $shopRepository,
        private readonly ContextStateManager $contextStateManager
    ) {
    }

    public function build(): ShopContext
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

        $shop = $this->shopRepository->get(new ShopId($this->shopId));

        return new ShopContext(
            $this->shopConstraint,
            new Shop(
                id: $shop->getId(),
                name: $shop->getName(),
                shopGroupId: $shop->getShopGroupId(),
                categoryId: $shop->getCategoryId(),
                themeName: $shop->getThemeName(),
                color: $shop->getColor(),
                physicalUri: $shop->getPhysicalUri(),
                virtualUri: $shop->getVirtualUri(),
                domain: $shop->getDomain(),
                domainSSL: $shop->getDomainSSL(),
                active: $shop->isActive()
            )
        );
    }

    public function buildLegacyContext(ShopConstraint $shopConstraint): self
    {
        if ($shopConstraint->forAllShops()) {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_ALL);
        } elseif (empty($shopConstraint->getShopId())) {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_GROUP, $shopConstraint->getShopGroupId()->getValue());
        } else {
            $this->contextStateManager->setShopContext(LegacyShop::CONTEXT_SHOP, $shopConstraint->getShopId()->getValue());
        }

        $this->contextStateManager->setShop(new LegacyShop($this->shopId));

        return $this;
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

    public function getShopId(): ?int
    {
        return $this->shopId;
    }

    public function getShopConstraint(): ?ShopConstraint
    {
        return $this->shopConstraint;
    }
}
