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

namespace PrestaShopBundle\Twig\Component;

use Doctrine\ORM\EntityManagerInterface;
use Link;
use PrestaShop\PrestaShop\Adapter\Feature\MultistoreFeature;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/multistore_header.html.twig')]
class MultistoreHeader
{
    private bool $isMultistoreUsed;
    private string $contextColor = '';
    private string $contextName = '';
    private array $groupList = [];
    private Link $link;

    public function __construct(
        private readonly MultistoreFeature $multistoreFeature, //todo removed internal using ?
        private readonly ColorBrightnessCalculator $colorBrightnessCalculator,
        private readonly EntityManagerInterface $entityManager,
        private readonly LegacyContext $legacyContext,
        private readonly TranslatorInterface $translator,
        private readonly MenuBuilder $menuBuilder,
        private readonly ShopContext $shopContext,
        private readonly array $controllersLockedToAllShopContext
    ) {
    }

    public function mount(): void
    {
        $this->isMultistoreUsed = $this->multistoreFeature->isUsed();

        if (!$this->isMultistoreUsed) {
            return;
        }

        if ($this->shopContext->getShopConstraint()->getShopId()) {
            $shop = $this->entityManager->getRepository(Shop::class)->findOneBy(['id' => $this->shopContext->getShopConstraint()->getShopId()->getValue()]);
            $this->contextColor = $shop->getColor();
            $this->contextName = $shop->getName();
        } elseif ($this->shopContext->getShopConstraint()->getShopGroupId()) {
            $shopGroup = $this->entityManager->getRepository(ShopGroup::class)->findOneBy(['id' => $this->shopContext->getShopConstraint()->getShopGroupId()->getValue()]);
            $this->contextColor = $shopGroup->getColor();
            $this->contextName = $shopGroup->getName();
        } else {
            $this->contextName = $this->translator->trans('All stores', domain: 'Admin.Global');
        }

        if (!$this->isLockedToAllShopContext()) {
            $this->groupList = array_filter(
                $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]),
                static fn (ShopGroup $shopGroup) => !$shopGroup->getShops()->isEmpty()
            );
        }
        $this->link = $this->legacyContext->getContext()->link;
    }

    public function isLockedToAllShopContext(): bool
    {
        $controllerName = $this->menuBuilder->getLegacyControllerClassName();

        return in_array($controllerName, $this->controllersLockedToAllShopContext);
    }

    public function isMultistoreUsed(): bool
    {
        return $this->isMultistoreUsed;
    }

    public function isAllShopContext(): bool
    {
        return $this->shopContext->getShopConstraint()->forAllShops();
    }

    public function getContextShopId(): ?int
    {
        return $this->shopContext->getShopConstraint()->getShopId()?->getValue();
    }

    public function getContextShopGroupId(): ?int
    {
        return $this->shopContext->getShopConstraint()->getShopGroupId()?->getValue();
    }

    public function getContextName(): string
    {
        return $this->contextName;
    }

    public function getContextColor(): string
    {
        return $this->contextColor;
    }

    public function isTitleDark(): bool
    {
        return empty($this->contextColor) || $this->colorBrightnessCalculator->isBright($this->contextColor);
    }

    public function getColorConfigLink(): string
    {
        if ($this->shopContext->getShopConstraint()->getShopId()) {
            $this->legacyContext->getAdminLink('AdminShop', extraParams: ['shop_id' => $this->shopContext->getShopConstraint()->getShopId()->getValue(), 'updateshop' => true]);
        } elseif ($this->shopContext->getShopConstraint()->getShopGroupId()) {
            return $this->legacyContext->getAdminLink('AdminShopGroup', extraParams: ['id_shop_group' => $this->shopContext->getShopConstraint()->getShopGroupId()->getValue(), 'updateshop_group' => true]);
        }

        return '';
    }

    public function getLink(): Link
    {
        return $this->link;
    }

    public function getGroupList(): array
    {
        return $this->groupList;
    }
}
