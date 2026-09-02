<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Component;

use Doctrine\ORM\EntityManagerInterface;
use Link;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Contracts\Translation\TranslatorInterface;

abstract class AbstractMultistoreHeader
{
    protected string $contextColor = '';
    protected string $contextName = '';
    protected array $groupList = [];

    protected Link $link;

    public function __construct(
        protected readonly ShopContext $shopContext,
        protected readonly EntityManagerInterface $entityManager,
        protected readonly TranslatorInterface $translator,
        protected readonly ColorBrightnessCalculator $colorBrightnessCalculator,
        protected readonly LegacyContext $legacyContext,
        protected readonly EmployeeContext $employeeContext,
    ) {
    }

    protected function doMount(): void
    {
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
        $this->link = $this->legacyContext->getContext()->link;
    }

    public function isMultistoreUsed(): bool
    {
        return $this->shopContext->isMultiShopUsed();
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

    public function isAllShopsAllowed(): bool
    {
        return $this->employeeContext->hasAuthorizationForAllShops();
    }
}
