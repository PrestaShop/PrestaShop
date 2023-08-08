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
use PrestaShop\PrestaShop\Adapter\Shop\Context;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/multistore_header.html.twig')]
class MultistoreHeader
{
    public bool $lockedToAllShopContext = false;
    private bool $isMultistoreUsed;
    private bool $isAllShopContext;
    private bool $isTitleDark;
    private bool $isShopContext;
    private bool $isGroupContext;
    private ShopGroup $currentContext;
    private string|false $colorConfigLink;
    private array $groupList = [];
    private Link $link;

    public function __construct(
        private readonly MultistoreFeature $multistoreFeature, //todo removed internal using ?
        private readonly ColorBrightnessCalculator $colorBrightnessCalculator,
        private readonly Context $multiStoreContext,
        private readonly EntityManagerInterface $entityManager,
        private readonly LegacyContext $legacyContext,
        private readonly TranslatorInterface $translator,
    ) {
    }

    public function mount(): void
    {
        $this->isMultistoreUsed = $this->multistoreFeature->isUsed();

        if (!$this->isMultistoreUsed) {
            return;
        }

        $isAllShopContext = $this->multiStoreContext->isAllShopContext();
        $isShopContext = $this->multiStoreContext->isShopContext();
        $colorConfigLink = false;

        if ($isShopContext) {
            $currentContext = $this->entityManager->getRepository(Shop::class)->findOneBy(['id' => $this->multiStoreContext->getContextShopID()]);
            $colorConfigLink = $this->legacyContext->getAdminLink('AdminShop', extraParams: ['shop_id' => $currentContext->getId(), 'updateshop' => true]);
        } elseif (!$isAllShopContext) {
            $shopGroupLegacy = $this->multiStoreContext->getContextShopGroup();
            $currentContext = $this->entityManager->getRepository(ShopGroup::class)->findOneBy(['id' => $shopGroupLegacy->id]);
            $colorConfigLink = $this->legacyContext->getAdminLink('AdminShopGroup', extraParams: ['id_shop_group' => $currentContext->getId(), 'updateshop_group' => true]);
        } else {
            // use ShopGroup object as the container for "all shops" context so that it can be used transparently in twig
            $currentContext = new ShopGroup();
            $currentContext->setName($this->translator->trans('All stores', domain: 'Admin.Global'));
            $currentContext->setColor('');
        }

        if (!$this->lockedToAllShopContext) {
            $this->groupList = array_filter(
                $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]),
                static fn (ShopGroup $shopGroup) => !$shopGroup->getShops()->isEmpty()
            );
        }
        $this->currentContext = $currentContext;
        $this->isShopContext = $isShopContext;
        $this->link = $this->legacyContext->getContext()->link;
        $this->isTitleDark = empty($currentContext->getColor()) ? true : $this->colorBrightnessCalculator->isBright($currentContext->getColor());
        $this->isAllShopContext = $isAllShopContext;
        $this->isGroupContext = $this->multiStoreContext->isGroupShopContext();
        $this->lockedToAllShopContext = false;
        $this->colorConfigLink = !$isAllShopContext && empty($currentContext->getColor()) ? $colorConfigLink : false;
    }

    public function isLockedToAllShopContext(): bool
    {
        return $this->lockedToAllShopContext;
    }

    public function isMultistoreUsed(): bool
    {
        return $this->isMultistoreUsed;
    }

    public function isAllShopContext(): bool
    {
        return $this->isAllShopContext;
    }

    public function getCurrentContextId(): int
    {
        return $this->currentContext->getId();
    }

    public function getCurrentContextName(): string
    {
        return $this->currentContext->getName();
    }

    public function getCurrentContextColor(): string
    {
        return $this->currentContext->getColor();
    }

    public function isTitleDark(): bool
    {
        return $this->isTitleDark;
    }

    public function isShopContext(): bool
    {
        return $this->isShopContext;
    }

    public function isGroupContext(): bool
    {
        return $this->isGroupContext;
    }

    public function getColorConfigLink(): string|false
    {
        return $this->colorConfigLink;
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
