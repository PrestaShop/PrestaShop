<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

declare(strict_types=1);

namespace PrestaShopBundle\Twig\Component;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\ShopGroup;
use PrestaShopBundle\Twig\Layout\MenuBuilder;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/multistore_header.html.twig')]
class MultistoreHeader extends AbstractMultistoreHeader
{
    public function __construct(
        protected readonly MenuBuilder $menuBuilder,
        protected readonly array $controllersLockedToAllShopContext,
        ShopContext $shopContext,
        EntityManagerInterface $entityManager,
        TranslatorInterface $translator,
        ColorBrightnessCalculator $colorBrightnessCalculator,
        LegacyContext $legacyContext,
        EmployeeContext $employeeContext,
    ) {
        parent::__construct(
            $shopContext,
            $entityManager,
            $translator,
            $colorBrightnessCalculator,
            $legacyContext,
            $employeeContext,
        );
    }

    public function mount(): void
    {
        if (!$this->isMultistoreUsed()) {
            return;
        }

        parent::doMount();
        if (!$this->isLockedToAllShopContext()) {
            $this->groupList = array_filter(
                $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]),
                function (ShopGroup $shopGroup) {
                    return !$shopGroup->getShops()->isEmpty() && $this->employeeContext->hasAuthorizationOnShopGroup($shopGroup->getId());
                },
            );

            // Filter not allowed shops
            foreach ($this->groupList as $group) {
                foreach ($group->getShops() as $shop) {
                    if (!$this->employeeContext->hasAuthorizationOnShop($shop->getId())) {
                        $group->getshops()->removeElement($shop);
                    }
                }
            }
        }
    }

    public function isLockedToAllShopContext(): bool
    {
        $controllerName = $this->menuBuilder->getLegacyControllerClassName();

        return in_array($controllerName, $this->controllersLockedToAllShopContext);
    }
}
