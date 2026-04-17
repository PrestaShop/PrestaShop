<?php
/**
 * For the full copyright and license information, please view the
 * docs/licenses/LICENSE.txt file that was distributed with this source code.
 */

namespace PrestaShopBundle\Twig\Component;

use Doctrine\ORM\EntityManagerInterface;
use PrestaShop\PrestaShop\Adapter\LegacyContext;
use PrestaShop\PrestaShop\Adapter\Product\Repository\ProductRepository;
use PrestaShop\PrestaShop\Core\Context\EmployeeContext;
use PrestaShop\PrestaShop\Core\Context\ShopContext;
use PrestaShop\PrestaShop\Core\Domain\Product\ValueObject\ProductId;
use PrestaShop\PrestaShop\Core\Domain\Shop\ValueObject\ShopId;
use PrestaShop\PrestaShop\Core\Util\ColorBrightnessCalculator;
use PrestaShopBundle\Entity\Shop;
use PrestaShopBundle\Entity\ShopGroup;
use Symfony\Contracts\Translation\TranslatorInterface;
use Symfony\UX\TwigComponent\Attribute\AsTwigComponent;

#[AsTwigComponent(template: '@PrestaShop/Admin/Component/Layout/multistore_product_header.html.twig')]
class MultistoreProductHeader extends AbstractMultistoreHeader
{
    private int $productId;

    public function __construct(
        protected readonly ProductRepository $productRepository,
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

    public function mount(int $productId): void
    {
        $this->productId = $productId;
        if (!$this->isMultistoreUsed()) {
            return;
        }

        parent::doMount();
        $this->groupList = [];
        $groupList = $this->entityManager->getRepository(ShopGroup::class)->findBy(['active' => true]);

        // Filter shops that are not associated to product
        $productShops = $this->productRepository->getAssociatedShopIds(new ProductId($productId));

        if (!empty($productShops)) {
            $productShopIds = array_map(function (ShopId $shopId) {
                return $shopId->getValue();
            }, $productShops);

            /** @var ShopGroup $shopGroup */
            foreach ($groupList as $shopGroup) {
                if (!$this->employeeContext->hasAuthorizationOnShopGroup($shopGroup->getId())) {
                    continue;
                }

                /** @var Shop $shop */
                foreach ($shopGroup->getShops() as $shop) {
                    if (!in_array($shop->getId(), $productShopIds)) {
                        $shopGroup->getShops()->removeElement($shop);
                    }
                }
                if (!$shopGroup->getShops()->isEmpty()) {
                    $this->groupList[] = $shopGroup;
                }
            }

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

    public function getProductId(): int
    {
        return $this->productId;
    }
}
